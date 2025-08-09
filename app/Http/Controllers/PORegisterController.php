<?php

namespace App\Http\Controllers;

use App\Enums\POStatus;
use App\Exports\PORegisterExport;
use App\Models\Department;
use App\Models\DepartmentHead;
use App\Models\Notification;
use App\Models\Vendor;
use App\Models\PORegister;
use App\Models\IndentRegister;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class PORegisterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $title = 'PO Register List';

        $columns = [
            ['key' => 'po_date', 'label' => 'PO Date'],
            ['key' => 'indent_id', 'label' => 'Indent ID'],
            ['key' => 'department_name', 'label' => 'Department'],
            ['key' => 'party_name', 'label' => 'Party'],
            ['key' => 'po_amount', 'label' => 'Amount'],
            ['key' => 'status', 'label' => 'Status', 'type' => 'status'],
            // ['key' => 'created_at', 'label' => 'Created At'],
            ['key' => 'action', 'label' => 'Action', 'type' => 'action'],
        ];

        // Subquery to get latest PO ID per indent_id
        $latestPoIds = DB::table('po_registers')
            ->select(DB::raw('MAX(id) as id'))
            ->groupBy('indent_id');

        // Main query using those latest PO IDs
        $poRegisters = DB::table('po_registers')
            ->joinSub($latestPoIds, 'latest_pos', function ($join) {
                $join->on('po_registers.id', '=', 'latest_pos.id');
            })
            ->leftJoin('departments', 'departments.id', '=', 'po_registers.department_id')
            ->select('po_registers.*', 'departments.name as department_name')
            ->orderByDesc('po_registers.created_at')
            ->paginate(10);

        $rows = $poRegisters->map(function ($po) {
            $action = [
                'viewPage' => route('po-register.viewByIndent', [
                    'indent_id' => $po->indent_id,
                    'department_id' => $po->department_id,
                ]),
            ];

            // Add 'close' button only if status is not 'close'
            if (strtolower($po->status) !== 'close') {
                $action['close'] = route('po-register.edit', $po->id);
            }

            // Add 'cancel' button only if status is not 'cancel'
            if (strtolower($po->status) !== 'cancel') {
                $action['cancel'] = route('po-register.edit', $po->id);
            }
            return [
                'po_date' => Carbon::parse($po->po_date)->format('d-m-Y'),
                'indent_id' => $po->indent_id,
                'department_name' => $po->department_name ?? '-',
                'party_name' => $po->party_name,
                'po_amount' => number_format($po->po_amount, 2),
                'status' => $po->status,
                // 'created_at' => Carbon::parse($po->created_at)->format('d-m-Y'),
                'action' => $action,
            ];
        });

        return view('pages.indent.indentPOForm.listIndentPOForm.listIndentPOForm', [
            'title' => $title,
            'columns' => $columns,
            'rows' => $rows,
            'pagination' => $poRegisters,
            'searchPlaceholder' => 'Search PO records...',
            'customButton' => null,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
   public function create(Request $request)
{
    $indent_id      = $request->get('indent_id');
    $department_id  = $request->get('department_id');

    $departmentHeads = DepartmentHead::where('department_id', $department_id)->get();
    $projectList     = Vendor::all();
    $statusList      = POStatus::values();
    $department_name = Department::find($department_id)?->name ?? '';

    // 1) Fetch indent with full items (array of objects)
    $indent = DB::table('indent_registers')
        ->where('indent_id', $indent_id)
        ->where('indent_department', $department_id)
        ->first();

    $itemsFromIndent = collect();
    if ($indent && $indent->items_description) {
        $decoded = json_decode($indent->items_description, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            // Ensure it's a collection of item objects
            $itemsFromIndent = collect($decoded)->filter(fn ($it) => is_array($it));
        }
    }

    // 2) Gather ALL item_description values already used in POs for this indent
    //    (adjust the where() if you also want to scope by department)
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
                return collect(preg_split('/[,|]/', $val))->map(fn ($s) => trim($s))->filter();
            }
            return [];
        })
        ->map(fn ($s) => mb_strtolower(trim($s)))
        ->unique()
        ->values();

    // 4) Keep only indent items whose description is NOT already created
    $items = $itemsFromIndent
        ->filter(function ($item) use ($alreadyCreatedSet) {
            $desc = mb_strtolower(trim((string)($item['description'] ?? '')));
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



public function store(Request $request)
{
    $validated = $request->validate([
        'indent_id'           => 'nullable|integer',
        'department_id'       => 'nullable|string',
        'po_date'             => 'nullable|date',
        'party_name'          => 'nullable|string',
        'po_wo_no'            => 'nullable|string',
        'po_amount'           => 'nullable|numeric',
        'debit_head'          => 'nullable|string',
        'item_description'    => 'nullable|array',
        'item_description.*'  => 'nullable|string',
        'expected_days'       => 'nullable|integer',
        'expected_date'       => 'nullable|date',
        'invoice_date'        => 'nullable|date',
        'receiving_date'      => 'nullable|date',
        'invoice'             => 'nullable|string',
        'delay_in_days'       => 'nullable|integer',
        'store_indent_no'     => 'nullable|string',
        'remarks'             => 'nullable|string',
    ]);

    $fmt = fn($k) => $request->filled($k)
        ? Carbon::parse($request->input($k))->format('Y-m-d')
        : null;

    DB::table('po_registers')->insert([
        'indent_id'        => $request->input('indent_id'),
        'department_id'    => $request->input('department_id'),
        'status'           => 'Pending',
        'po_date'          => $fmt('po_date'),
        'party_name'       => $request->input('party_name'),
        'po_wo_no'         => $request->input('po_wo_no'),
        'po_amount'        => $request->input('po_amount'),
        'debit_head'       => $request->input('debit_head'),
        'item_description' => $request->has('item_description')
                                ? json_encode($request->input('item_description'))
                                : null,
        'expected_days'    => $request->has('expected_days')
                                ? (string) $request->input('expected_days') // column is varchar
                                : null,
        'expected_date'    => $fmt('expected_date'),
        'invoice_date'     => $fmt('invoice_date'),
        'receiving_date'   => $fmt('receiving_date'), // stored as varchar in your table
        'invoice'          => $request->input('invoice'),
        'delay_in_days'    => $request->input('delay_in_days'),
        'store_indent_no'  => $request->input('store_indent_no'),
        'remarks'          => $request->input('remarks'),
        'created_at'       => now(),
        'updated_at'       => now(),
    ]);

    return redirect()->route('po-register.index')
        ->with('success', 'PO Registered Successfully!');
}



    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    public function poEditForm($id)
    {
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
    $title = "PO Records for Indent #$indent_id - Department";

    $allPos = DB::table('po_registers')
        ->leftJoin('departments', 'departments.id', '=', 'po_registers.department_id')
        ->leftJoin('indent_registers', 'indent_registers.id', '=', 'po_registers.indent_id')
        ->leftJoin('projects', 'projects.id', '=', 'indent_registers.indent_project')
        ->select(
            'po_registers.*',
            'departments.name as department_name',
            'indent_registers.indent_id as indent_ticket_no',
            'indent_registers.items_description',
            'projects.name as project_name'
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

    $po = $allPos->first(); // summary

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
        return Excel::download(
            new PORegisterExport($indent_id, $department_id),
            'PO_Indent_' . $indent_id . '_Dept_' . $department_id . '.xlsx'
        );
    }

    public function downloadPORegisterPDF($indent_id, $department_id)
    {
        $allPos = DB::table('po_registers')
            ->leftJoin('indent_registers', 'indent_registers.id', '=', 'po_registers.indent_id')
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
                'indent_registers.item_description as indent_item_description',
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

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
public function updateStatus(Request $request)
{
    $request->validate([
        'indent_id' => 'required',
        'department_id' => 'required|exists:departments,id',
        'status' => 'required|in:Pending,Close,Cancel',
    ]);

    $indentId = $request->indent_id;
    $departmentId = $request->department_id;
    $newStatus = $request->status;

    $poUpdated = 0;
    $indentUpdated = 0;

    // Step 1: Update PORegister if exists and status is not already Close/Cancel
    $existingPOStatus = PORegister::where('indent_id', $indentId)
        ->where('department_id', $departmentId)
        ->value('status');

    if ($existingPOStatus && !in_array($existingPOStatus, ['Close', 'Cancel'])) {
        $poUpdated = PORegister::where('indent_id', $indentId)
            ->where('department_id', $departmentId)
            ->update(['status' => $newStatus]);
    }

    // Step 2: Update IndentRegister if exists and status is not already Close/Cancel
    $existingIndentStatus = IndentRegister::where('indent_id', $indentId)
        ->where('indent_department', $departmentId)
        ->value('status');

    if ($existingIndentStatus && !in_array($existingIndentStatus, ['Close', 'Cancel'])) {
        $indentUpdated = IndentRegister::where('indent_id', $indentId)
            ->where('indent_department', $departmentId)
            ->limit(1)
            ->update(['status' => $newStatus]);
    }

    // Step 3: Feedback
    if ($poUpdated || $indentUpdated) {
        return redirect()->back()->with('success', 'Status updated successfully.');
    }

    return redirect()->back()->with('warning', 'No updates made. Status may already be Close or Cancel.');
}




}
