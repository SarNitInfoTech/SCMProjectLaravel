<?php

namespace App\Http\Controllers;

use App\Enums\POStatus;
use App\Exports\PORegisterExport;
use App\Models\Department;
use App\Models\DepartmentHead;
use App\Models\IndentRegister;
use App\Models\Notification;
use App\Models\PORegister;
use App\Models\Vendor;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Gate;

class PORegisterController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('pos.view');
        $title = 'PO Register List';
        $viewBtnTitle="File Invoice";

        // Subquery to get latest PO ID per indent_id
        $latestPoIds = DB::table('po_registers')
            ->select(DB::raw('MAX(id) as id'))
            ->groupBy('indent_id');

        $query = DB::table('po_registers')
            ->joinSub($latestPoIds, 'latest_pos', function ($join) {
                $join->on('po_registers.id', '=', 'latest_pos.id');
            })
            ->leftJoin('departments', 'departments.id', '=', 'po_registers.department_id');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('po_registers.indent_id', 'like', "%{$search}%")
                  ->orWhere('departments.name', 'like', "%{$search}%")
                  ->orWhere('po_registers.party_name', 'like', "%{$search}%");
            });
        }

        $poRegisters = $query->select('po_registers.*', 'departments.name as department_name')
            ->orderByDesc('po_registers.created_at')
            ->paginate(10);

        $rows = $poRegisters->map(function ($po) {
            $actions = [
                'viewPage' => route('po-register.viewByIndent', [
                    'indent_id'     => $po->indent_id,
                    'department_id' => $po->department_id,
                ]),
            ];

            $status = strtolower($po->status ?? 'pending');

            $baseParams = [
                'indent_id'     => $po->indent_id,
                'department_id' => $po->department_id,
            ];

            if ($status === 'pending') {
                $actions['file_po'] = route('po-register.create', $baseParams);
                $actions['cancel'] = [
                    'route'  => route('po-register.statusCancel'),
                    'params' => $baseParams,
                ];
                $actions['close'] = [
                    'route'  => route('po-register.statusClose'),
                    'params' => $baseParams,
                ];
            } elseif ($status === 'close') {
                $actions['pending'] = [
                    'route'  => route('po-register.statusPending'),
                    'params' => $baseParams,
                ];
                $actions['cancel'] = [
                    'route'  => route('po-register.statusCancel'),
                    'params' => $baseParams,
                ];
            } elseif ($status === 'cancel') {
                $actions['close'] = [
                    'route'  => route('po-register.statusClose'),
                    'params' => $baseParams,
                ];
                $actions['pending'] = [
                    'route'  => route('po-register.statusPending'),
                    'params' => $baseParams,
                ];
            }

            return [
                'po_date'         => $po->po_date ? \Carbon\Carbon::parse($po->po_date)->format('d-m-Y') : '-',
                'indent_id'       => $po->indent_id,
                'department_name' => $po->department_name ?? '-',
                'party_name'      => $po->party_name,
                'po_amount'       => number_format((float) $po->po_amount, 2),
                'status'          => $po->status,
                'action'          => $actions,
            ];
        });

        return view('pages.indent.indentPOForm.listIndentPOForm.listIndentPOForm', [
            'title' => $title,
            'rows' => $rows,
            'pagination' => $poRegisters,
            'viewBtnTitle' => $viewBtnTitle
        ]);
    }
    public function create(Request $request)
    {
        Gate::authorize('pos.create');
        $indent_id = $request->get('indent_id');
        $department_id = $request->get('department_id');

        $departmentHeads = DepartmentHead::where('department_id', $department_id)->get();
        $projectList = Vendor::all();
        $statusList = POStatus::values();
        $department_name = Department::find($department_id)?->name ?? '';

        // indent_registers stores department NAME, not ID — resolve it
        $department_name_resolved = Department::find($department_id)?->name ?? $department_id;

        $indent = DB::table('indent_registers')
            ->where('indent_id', $indent_id)
            ->where('indent_department', $department_name_resolved)
            ->first();

        $itemsFromIndent = collect();
        if ($indent && $indent->items_description) {
            $decoded = json_decode($indent->items_description, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                // Ensure it's a collection of item objects
                $itemsFromIndent = collect($decoded)->filter(fn($it) => is_array($it));
            }
        }
        $poItemsRaw = DB::table('po_registers')
            ->where('indent_id', $indent_id)
            // ->where('department_id', $department_id) // uncomment if needed
            ->pluck('item_description');

        // 3) Normalize PO items to a lowercase set of description strings
        $alreadyCreatedSet = collect($poItemsRaw)
            ->flatMap(function ($val) {
                // Expect JSON: ["Printer","Mouse"] OR [{"description":"Printer"},...]
                if (is_string($val)) {
                    $decoded = json_decode($val, true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        if (is_array($decoded)) {
                            return collect($decoded)->map(function ($entry) {
                                if (is_array($entry) && isset($entry['description'])) {
                                    return $entry['description'];
                                }
                                if (is_string($entry)) {
                                    return $entry;
                                }
                                return null;
                            })->filter();
                        }
                    }
                    // Fallback: comma/pipe separated string
                    return collect(preg_split('/[,|]/', $val))->map(fn($s) => trim($s))->filter();
                }
                return [];
            })
            ->map(fn($s) => mb_strtolower(trim($s)))
            ->unique()
            ->values();

        // 4) Keep only indent items whose description is NOT already created
        $items = $itemsFromIndent
            ->filter(function ($item) use ($alreadyCreatedSet) {
                $desc = mb_strtolower(trim((string) ($item['description'] ?? '')));
                return $desc !== '' && !$alreadyCreatedSet->contains($desc);
            })
            ->values()
            ->all();

        // Now $items contains ONLY the not-yet-created options (with full object: description, unit, quantities, etc.)
        return view(
            'pages.indent.indentPOForm.addIndentPOForm.addIndentPOForm',
            compact('indent_id', 'departmentHeads', 'department_id', 'department_name', 'statusList', 'projectList', 'items')
        );
    }
    public function createInvoiceById(Request $request,int $id)
    {
        Gate::authorize('pos.edit');
       $po = DB::table('po_registers')->where('id', $id)->firstOrFail();
        return view(
            'pages.indent.indentPOForm.addInvoiceIndentPOForm.addInvoiceIndentPOForm',[
        'po'            => $po,
    ]);
    }
    public function updateInvoice(Request $request, int $id)
{
    Gate::authorize('pos.edit');
    $validated = $request->validate([
        'invoice_date'   => 'nullable|date',
        'receiving_date' => 'nullable|date',
        'delay_in_days'  => 'nullable|integer|min:0',
        'store_indent_no'=> 'nullable|string|max:255',
    ]);

    $fmt = fn($k) => $request->filled($k)
        ? Carbon::parse($request->input($k))->format('Y-m-d')
        : null;

    $data = ['updated_at' => now()];

    if ($request->has('invoice_date'))   $data['invoice_date']   = $fmt('invoice_date');
    if ($request->has('receiving_date')) $data['receiving_date'] = $fmt('receiving_date');
    if ($request->has('delay_in_days'))  $data['delay_in_days']  = $request->input('delay_in_days');
    if ($request->has('store_indent_no'))$data['store_indent_no']= $request->input('store_indent_no');

    $affected = DB::table('po_registers')->where('id', $id)->update($data);

    return back()->with(
        $affected ? 'success' : 'warning',
        $affected ? 'Invoice info updated.' : 'No changes applied.'
    );
}

    public function edit(int $id)
    {
        Gate::authorize('pos.edit');
        $po = DB::table('po_registers')->where('id', $id)->first();
        if (!$po) {
            abort(404, 'PO not found.');
        }

        $indent_id = $po->indent_id;
        $department_id = $po->department_id;

        // Common lookups
        $departmentHeads = DepartmentHead::where('department_id', $department_id)->get();
        $projectList = Vendor::all();
        $statusList = POStatus::values();
        $department_name = Department::find($department_id)?->name ?? '';

        // 1) Fetch indent with full items — indent_department stores NAME, not ID
        $department_name_resolved = Department::find($department_id)?->name ?? $department_id;

        $indent = DB::table('indent_registers')
            ->where('indent_id', $indent_id)
            ->where('indent_department', $department_name_resolved)
            ->first();

        $itemsFromIndent = collect();
        if ($indent && $indent->items_description) {
            $decoded = json_decode($indent->items_description, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                // collection of item objects
                $itemsFromIndent = collect($decoded)->filter(fn($it) => is_array($it));
            }
        }

        // 2) Current PO items (normalize to array of objects with 'description' when possible)
        $selectedItems = collect();
        if (!empty($po->item_description) && is_string($po->item_description)) {
            $currDecoded = json_decode($po->item_description, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($currDecoded)) {
                $selectedItems = collect($currDecoded)->map(function ($entry) {
                    if (is_array($entry)) {
                        return $entry;  // already an object-like array (e.g., ['description'=>..., ...])
                    }
                    if (is_string($entry)) {
                        return ['description' => $entry];
                    }
                    return null;
                })->filter();
            } else {
                // Fallback for comma/pipe separated string
                $selectedItems = collect(preg_split('/[,|]/', $po->item_description))
                    ->map(fn($s) => trim($s))
                    ->filter()
                    ->map(fn($s) => ['description' => $s]);
            }
        }

        // 3) Items already used by OTHER POs for this indent (exclude current PO id)
        $poItemsRaw = DB::table('po_registers')
            ->where('indent_id', $indent_id)
            // ->where('department_id', $department_id) // uncomment if you want to scope by department
            ->where('id', '!=', $id)
            ->pluck('item_description');

        $alreadyCreatedSet = collect($poItemsRaw)
            ->flatMap(function ($val) {
                if (is_string($val)) {
                    $decoded = json_decode($val, true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        if (is_array($decoded)) {
                            return collect($decoded)->map(function ($entry) {
                                if (is_array($entry) && isset($entry['description']))
                                    return $entry['description'];
                                if (is_string($entry))
                                    return $entry;
                                return null;
                            })->filter();
                        }
                    }
                    return collect(preg_split('/[,|]/', $val))->map(fn($s) => trim($s))->filter();
                }
                return [];
            })
            ->map(fn($s) => mb_strtolower(trim($s)))
            ->unique()
            ->values();

        // 4) Remaining indent items NOT used by other POs (you can still keep current PO's items separate)
        $itemsRemaining = $itemsFromIndent
            ->filter(function ($item) use ($alreadyCreatedSet) {
                $desc = mb_strtolower(trim((string) ($item['description'] ?? '')));
                return $desc !== '' && !$alreadyCreatedSet->contains($desc);
            })
            ->values()
            ->all();

        // Return edit view with everything needed
        return view(
            'pages.indent.indentPOForm.editIndentPOForm.editIndentPOForm',
            compact(
                'po',
                'id',
                'indent_id',
                'department_id',
                'department_name',
                'departmentHeads',
                'projectList',
                'statusList',
                'selectedItems',  // current PO's items (normalized)
                'itemsRemaining'  // from indent, excluding items used by other POs
            )
        );
    }
    public function store(Request $request)
    {
        Gate::authorize('pos.create');
        $validated = $request->validate([
            'indent_id' => 'nullable|integer',
            'department_id' => 'nullable|string',
            'po_date' => 'nullable|date',
            'party_name' => 'nullable|string',
            'po_wo_no' => 'nullable|string',
            'po_amount' => 'nullable|numeric',
            'debit_head' => 'nullable|string',
            'item_description' => 'nullable|array',
            'item_description.*' => 'nullable|string',
            'expected_days' => 'nullable|integer',
            'expected_date' => 'nullable|date',
            'invoice_date' => 'nullable|date',
            'receiving_date' => 'nullable|date',
            'invoice' => 'nullable|string',
            'delay_in_days' => 'nullable|integer',
            'store_indent_no' => 'nullable|string',
            'remarks' => 'nullable|string',
        ]);

        $fmt = fn($k) => $request->filled($k)
            ? Carbon::parse($request->input($k))->format('Y-m-d')
            : null;

        DB::table('po_registers')->insert([
            'indent_id' => $request->input('indent_id'),
            'department_id' => $request->input('department_id'),
            'status' => 'Pending',
            'po_date' => $fmt('po_date'),
            'party_name' => $request->input('party_name'),
            'po_wo_no' => $request->input('po_wo_no'),
            'po_amount' => $request->input('po_amount'),
            'debit_head' => $request->input('debit_head'),
            'item_description' => $request->has('item_description')
                ? json_encode($request->input('item_description'))
                : null,
            'expected_days' => $request->has('expected_days')
                ? (string) $request->input('expected_days')  // column is varchar
                : null,
            'expected_date' => $fmt('expected_date'),
            'invoice_date' => $fmt('invoice_date'),
            'receiving_date' => $fmt('receiving_date'),  // stored as varchar in your table
            'invoice' => $request->input('invoice'),
            'delay_in_days' => $request->input('delay_in_days'),
            'store_indent_no' => $request->input('store_indent_no'),
            'remarks' => $request->input('remarks'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()
            ->route('po-register.index')
            ->with('success', 'PO Registered Successfully!');
    }
    public function show(string $id)
    {
        //
    }
    public function poEditForm($id)
    {
        Gate::authorize('pos.edit');
        $po = DB::table('po_registers')->where('id', $id)->first();

        if (!$po) {
            return redirect()->back()->with('error', 'PO not found.');
        }

        $department = DB::table('departments')->where('id', $po->department_id)->first();
        $department_name = $department ? $department->name : 'Unknown';

        $statusList = ['Pending', 'Close', 'Cancel'];  // Or fetch from config/model

        return view('pages.po.edit', compact('po', 'department_name', 'statusList'));
    }
    public function poFormUpdate(Request $request, $id)
    {
        Gate::authorize('pos.edit');
        $validated = $request->validate([
            'po_date' => 'required|date',
            'status' => 'required|string',
            'party_name' => 'required|string|max:255',
            'po_wo_no' => 'required|string|max:255',
            'po_amount' => 'required|numeric',
            'debit_head' => 'nullable|string|max:255',
            'item_description' => 'required|string',
            'expected_date' => 'nullable|date',
            'invoice_date' => 'nullable|date',
            'receiving_date' => 'nullable|string|max:255',
            'invoice' => 'nullable|string|max:255',
            'delay_in_days' => 'nullable|integer',
            'store_indent_no' => 'nullable|string|max:255',
            'remarks' => 'nullable|string',
            'expected_days' => 'nullable|integer',
        ]);

        $updated = DB::table('po_registers')->where('id', $id)->update([
            'po_date' => $validated['po_date'],
            'status' => $validated['status'],
            'party_name' => $validated['party_name'],
            'po_wo_no' => $validated['po_wo_no'],
            'po_amount' => $validated['po_amount'],
            'debit_head' => $validated['debit_head'],
            'item_description' => $validated['item_description'],
            'expected_date' => $validated['expected_date'],
            'invoice_date' => $validated['invoice_date'],
            'receiving_date' => $validated['receiving_date'],
            'invoice' => $validated['invoice'],
            'delay_in_days' => $validated['delay_in_days'],
            'store_indent_no' => $validated['store_indent_no'],
            'remarks' => $validated['remarks'],
            'expected_days' => $validated['expected_days'],
            'updated_at' => now()
        ]);

        return redirect()->route('po-register.index')->with('success', 'Purchase Order updated successfully.');
    }
    public function viewByIndent($indent_id, $department_id)
    {
        Gate::authorize('pos.view');
        $title = "PO Records for Indent #$indent_id - Department";

        $allPos = DB::table('po_registers')
            ->leftJoin('departments', 'departments.id', '=', 'po_registers.department_id')
            ->leftJoin('indent_registers', function ($join) {
                $join->on(DB::raw('CAST(indent_registers.indent_id AS CHAR)'), '=', DB::raw('CAST(po_registers.indent_id AS CHAR)'));
            })
            ->leftJoin('projects', 'projects.id', '=', 'indent_registers.indent_project')
            ->select(
                'po_registers.*',
                'departments.name as department_name',
                'indent_registers.indent_id as indent_ticket_no',
                'indent_registers.indent_date as indent_date',
                'indent_registers.items_description',
                'indent_registers.indent_project as project_name',
            )
            ->where('po_registers.indent_id', $indent_id)
            ->where('po_registers.department_id', $department_id)
            ->orderByDesc('po_registers.created_at')
            ->get();

        // Add decoded items_description for each PO record
        $allPos->transform(function ($record) {
            $record->items = json_decode($record->items_description, true) ?? [];
            return $record;
        });

        $po = $allPos->first();  // summary

        return view('pages.indent.indentPOForm.viewDetailIndentPOForm.viewDetailIndentPOForm', compact(
            'title',
            'po',
            'allPos',
            'indent_id',
            'department_id'
        ));
    }
    public function downloadPORegisterExcel($indent_id, $department_id)
    {
        Gate::authorize('pos.view');
        return Excel::download(
            new PORegisterExport($indent_id, $department_id),
            'PO_Indent_' . $indent_id . '_Dept_' . $department_id . '.xlsx'
        );
    }
    public function downloadPORegisterPDF($indent_id, $department_id)
    {
        Gate::authorize('pos.view');
        $allPos = DB::table('po_registers')
            ->leftJoin('indent_registers', function ($join) {
                $join->on(DB::raw('CAST(indent_registers.indent_id AS CHAR)'), '=', DB::raw('CAST(po_registers.indent_id AS CHAR)'));
            })
            ->leftJoin('departments', 'departments.id', '=', 'po_registers.department_id')
            ->leftJoin('projects', 'projects.id', '=', 'indent_registers.indent_project')
            ->leftJoin('units', 'units.id', '=', 'indent_registers.unit')  // if unit is ID
            ->select(
                'po_registers.id as po_id',
                'po_registers.indent_id',
                'po_registers.department_id',
                'departments.name as department_name',
                'po_registers.status',
                'po_registers.invoice',
                'po_registers.po_date',
                'po_registers.party_name',
                'po_registers.po_wo_no',
                'po_registers.item_description as po_item_description',
                'po_registers.po_amount',
                'po_registers.debit_head',
                'po_registers.expected_days',
                'po_registers.expected_date',
                'po_registers.invoice_date',
                'po_registers.receiving_date',
                'po_registers.delay_in_days',
                'po_registers.remarks',
                'po_registers.store_indent_no',
                'po_registers.created_at as po_created_at',
                'po_registers.updated_at as po_updated_at',
                'indent_registers.id as indent_db_id',
                'indent_registers.indent_id as indent_ticket_no',
                'indent_registers.indent_date',
                'indent_registers.indent_department',
                'indent_registers.indent_project',
                'projects.name as project_name',
                'indent_registers.items_description as indent_item_description',
                'indent_registers.unit as unit_id',
                'units.name as unit_name',
                'indent_registers.quantity_required',
                'indent_registers.purchased_order',
                'indent_registers.quantity_received',
                'indent_registers.quantity_balance',
                'indent_registers.created_at as indent_created_at',
                'indent_registers.updated_at as indent_updated_at'
            )
            ->where('po_registers.indent_id', $indent_id)
            ->where('po_registers.department_id', $department_id)
            ->orderByDesc('po_registers.created_at')
            ->get();

        $pdf = Pdf::loadView('exports.po_register_pdf', compact('allPos', 'indent_id', 'department_id'))
            ->setPaper('a4', 'landscape');

        return $pdf->download("POfile_Indent_{$indent_id}_Dept_{$department_id}.pdf");
    }
    public function updatePObyId(Request $request, int $id)
    {
        Gate::authorize('pos.edit');
        // Same validation rules as store(), all nullable so you can send partial updates
        $validated = $request->validate([
            'indent_id' => 'nullable|integer',
            'department_id' => 'nullable|string',
            'po_date' => 'nullable|date',
            'party_name' => 'nullable|string',
            'po_wo_no' => 'nullable|string',
            'po_amount' => 'nullable|numeric',
            'debit_head' => 'nullable|string',
            'item_description' => 'nullable|array',
            'item_description.*' => 'nullable|string',
            'expected_days' => 'nullable|integer',
            'expected_date' => 'nullable|date',
            'invoice_date' => 'nullable|date',
            'receiving_date' => 'nullable|date',
            'invoice' => 'nullable|string',
            'delay_in_days' => 'nullable|integer',
            'store_indent_no' => 'nullable|string',
            'remarks' => 'nullable|string',
        ]);

        // Helper to format YYYY-MM-DD if present
        $fmt = fn(string $k) => $request->filled($k)
            ? Carbon::parse($request->input($k))->format('Y-m-d')
            : null;

        // Build payload only with provided fields (no accidental nulling)
        $data = [];

        foreach ([
            'indent_id', 'department_id', 'party_name', 'po_wo_no',
            'po_amount', 'debit_head', 'invoice', 'delay_in_days',
            'store_indent_no', 'remarks'
        ] as $k) {
            if ($request->has($k)) {
                $data[$k] = $request->input($k);
            }
        }

        // Dates (only if provided)
        foreach (['po_date', 'expected_date', 'invoice_date', 'receiving_date'] as $k) {
            if ($request->filled($k)) {
                $data[$k] = $fmt($k);
            }
        }

        // expected_days is varchar in your table; cast to string if provided
        if ($request->has('expected_days')) {
            $data['expected_days'] = $request->filled('expected_days')
                ? (string) $request->input('expected_days')
                : null;
        }

        // item_description -> JSON if provided
        if ($request->has('item_description')) {
            $data['item_description'] = $request->has('item_description')
                ? json_encode($request->input('item_description'))
                : null;
        }

        // Always touch updated_at
        $data['updated_at'] = now();

        // If nothing to update, bounce politely
        if (count($data) === 1 && array_key_exists('updated_at', $data)) {
            return back()->with('info', 'No changes submitted.');
        }

        $affected = DB::table('po_registers')->where('id', $id)->update($data);

        if ($affected === 0) {
            // Could be "no row found" or "values identical" — up to you how to message
            return redirect()
                ->route('po-register.index')
                ->with('warning', 'No changes were applied. (Record may not exist or values are unchanged.)');
        }

        return redirect()
            ->route('po-register.index')
            ->with('success', 'PO updated successfully.');
    }
    public function destroy(string $id)
    {
        //
    }
    public function update(Request $request, int $id)
    {
        // delegate to your existing implementation
        return $this->updatePObyId($request, $id);
    }
    public function updateStatus(Request $request)
    {
        Gate::authorize('pos.edit');
        // Expect: id (indent_id), department (department_id), action
        $data = $request->validate([
            'id' => ['required', 'integer'],  // indent_id
            'department' => ['required', 'string'],  // department_id (string in your schema)
            'action' => ['required', Rule::in(['close', 'cancel', 'pending', 'Close', 'Cancel', 'Pending'])],
        ]);

        $indentId = (int) $data['id'];
        $departmentId = (string) $data['department'];
        $newStatus = match (strtolower($data['action'])) {
            'cancel' => 'Cancel',
            'close' => 'Close',
            default => 'Pending',
        };

        DB::beginTransaction();
        try {
            // UPDATE po_registers ... (same as your SQL; parameterized)
            DB::update(
                'UPDATE `po_registers`
             SET `status` = ?, `updated_at` = NOW()
             WHERE `indent_id` = ? AND `department_id` = ?',
                [$newStatus, $indentId, $departmentId]
            );

            // SELECT ROW_COUNT() AS rows_updated;
            $row = DB::selectOne('SELECT ROW_COUNT() AS rows_updated');
            $rowsUpdated = (int) ($row->rows_updated ?? 0);

            DB::commit();

            if ($rowsUpdated > 0) {
                return back()->with(
                    'success',
                    "Updated PO status to {$newStatus} for Indent #{$indentId}, Department {$departmentId}. Rows: {$rowsUpdated}."
                );
            }

            return back()->with('warning', 'No rows matched the given indent_id and department_id.');
        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);
            return back()->with('error', 'Failed to update status. Please try again.');
        }
    }
    public function statusClose(Request $request)
    {
        Gate::authorize('pos.edit');
        $data = $request->validate([
            'indent_id' => ['required', 'integer'],  // indent_id (numeric in your flow)
            'department_id' => ['required', 'string'],  // department code like "NTC"
        ]);

        $indentId = (int) $data['indent_id'];
        $departmentId = is_numeric($data['department_id']) ? (int)$data['department_id'] : $data['department_id'];

        [$poRows, $indentRows] = DB::transaction(function () use ($indentId, $departmentId) {
            // po_registers uses numeric department_id
            $poRows = DB::update(
                'UPDATE `po_registers`
             SET `status` = ?, `updated_at` = ?
             WHERE `indent_id` = ? AND `department_id` = ?',
                ['Close', now(), $indentId, $departmentId]
            );

            // indent_registers stores department NAME — resolve it
            $deptName = Department::find($departmentId)?->name ?? $departmentId;

            $indentRows = DB::update(
                'UPDATE `indent_registers`
             SET `status` = ?, `updated_at` = ?
             WHERE `indent_id` = ? AND `indent_department` = ?',
                ['Close', now(), (string) $indentId, $deptName]
            );

            return [$poRows, $indentRows];
        });

        $total = $poRows + $indentRows;

        return back()->with(
            $total > 0 ? 'success' : 'warning',
            $total > 0
                ? "Closed status applied. PO rows: {$poRows}, Indent rows: {$indentRows}."
                : 'No rows matched the given indent_id and department.'
        );
    }
    public function statusPending(Request $request)
    {
        Gate::authorize('pos.edit');
        $data = $request->validate([
            'indent_id' => ['required', 'integer'],
            'department_id' => ['required', 'string'],
        ]);

        $indentId = (int) $data['indent_id'];
        $departmentId = is_numeric($data['department_id']) ? (int)$data['department_id'] : $data['department_id'];

        [$poRows, $indentRows] = DB::transaction(function () use ($indentId, $departmentId) {
            $poRows = DB::update(
                'UPDATE `po_registers`
             SET `status` = ?, `updated_at` = ?
             WHERE `indent_id` = ? AND `department_id` = ?',
                ['Pending', now(), $indentId, $departmentId]
            );

            // indent_registers stores department NAME — resolve it
            $deptName = Department::find($departmentId)?->name ?? $departmentId;

            $indentRows = DB::update(
                'UPDATE `indent_registers`
             SET `status` = ?, `updated_at` = ?
             WHERE `indent_id` = ? AND `indent_department` = ?',
                ['Pending', now(), (string) $indentId, $deptName]
            );

            return [$poRows, $indentRows];
        });

        $total = $poRows + $indentRows;

        return back()->with(
            $total > 0 ? 'success' : 'warning',
            $total > 0
                ? "Pending status applied. PO rows: {$poRows}, Indent rows: {$indentRows}."
                : 'No rows matched the given indent_id and department.'
        );
    }
    public function statusCancel(Request $request)
    {
        Gate::authorize('pos.edit');
        $data = $request->validate([
            'indent_id' => ['required', 'integer'],
            'department_id' => ['required', 'string'],
        ]);

        $indentId = (int) $data['indent_id'];
        $departmentId = is_numeric($data['department_id']) ? (int)$data['department_id'] : $data['department_id'];

        [$poRows, $indentRows] = DB::transaction(function () use ($indentId, $departmentId) {
            $poRows = DB::update(
                'UPDATE `po_registers`
             SET `status` = ?, `updated_at` = ?
             WHERE `indent_id` = ? AND `department_id` = ?',
                ['Cancel', now(), $indentId, $departmentId]
            );

            // indent_registers stores department NAME — resolve it
            $deptName = Department::find($departmentId)?->name ?? $departmentId;

            $indentRows = DB::update(
                'UPDATE `indent_registers`
             SET `status` = ?, `updated_at` = ?
             WHERE `indent_id` = ? AND `indent_department` = ?',
                ['Cancel', now(), (string) $indentId, $deptName]
            );

            return [$poRows, $indentRows];
        });

        $total = $poRows + $indentRows;

        return back()->with(
            $total > 0 ? 'success' : 'warning',
            $total > 0
                ? "Cancel status applied. PO rows: {$poRows}, Indent rows: {$indentRows}."
                : 'No rows matched the given indent_id and department.'
        );
    }
}
