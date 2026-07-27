<?php

namespace App\Http\Controllers;

use App\Enums\POStatus;
use App\Exports\PORegisterExport;
use App\Helpers\SearchHelper;
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
        $viewBtnTitle = "File Invoice";

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
            SearchHelper::applyFuzzySearch($query, $request->search, [
                'po_registers.indent_id',
                'departments.name',
                'po_registers.party_name',
                'po_registers.item_description',
                'po_registers.po_wo_no',
                'po_registers.status',
                'po_registers.store_indent_no',
                'po_registers.remarks'
            ]);
        }

        if ($request->filled('department_id')) {
            $query->where('po_registers.department_id', $request->department_id);
        }

        if ($request->filled('status')) {
            $query->whereRaw('LOWER(po_registers.status) = ?', [mb_strtolower(trim($request->status))]);
        }

        if ($request->filled('party_name')) {
            $query->where('po_registers.party_name', 'LIKE', '%' . $request->party_name . '%');
        }

        if ($request->filled('date_from')) {
            $query->whereDate('po_registers.po_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('po_registers.po_date', '<=', $request->date_to);
        }

        $perPage = (int) $request->input('per_page', 10);
        if (!in_array($perPage, [10, 25, 50, 100])) {
            $perPage = 10;
        }

        $departments = DB::table('departments')->orderBy('name')->get();

        $poRegisters = $query->select('po_registers.*', 'departments.name as department_name')
            ->orderByDesc('po_registers.created_at')
            ->paginate($perPage);

        $rows = $poRegisters->map(function ($po) {
            $actions = [
                'viewPage' => route('po-register.viewByIndent', [
                    'indent_id'     => $po->indent_id,
                    'department_id' => $po->department_id,
                ]),
            ];

            $status = mb_strtolower(trim((string)($po->status ?? 'open')));

            $baseParams = [
                'indent_id'     => $po->indent_id,
                'department_id' => $po->department_id,
            ];

            $actions['file_invoice'] = route('indentroview.createInvoiceById', $po->id);

            if (!in_array($status, ['closed', 'close', 'cancel', 'cancelled'])) {
                $actions['file_po'] = route('po-register.create', $baseParams);
                $actions['cancel']  = [
                    'route'  => route('po-register.statusCancel'),
                    'params' => $baseParams,
                ];
                $actions['close']   = [
                    'route'  => route('po-register.statusClose'),
                    'params' => $baseParams,
                ];
            } else {
                $actions['pending'] = [
                    'route'  => route('po-register.statusPending'),
                    'params' => $baseParams,
                ];
            }

            $items = [];
            if (!empty($po->item_description) && is_string($po->item_description)) {
                $decoded = json_decode($po->item_description, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    $items = collect($decoded)->map(function ($it) {
                        return is_array($it) ? (string) ($it['description'] ?? '') : (string) $it;
                    })->filter()->values()->all();
                } else {
                    $items = [ $po->item_description ];
                }
            }
            $itemDescriptions = implode(', ', $items);

            return [
                'po_date'          => $po->po_date ? \Carbon\Carbon::parse($po->po_date)->format('d-m-Y') : '-',
                'indent_id'        => $po->indent_id,
                'department_name'  => $po->department_name ?? '-',
                'party_name'       => $po->party_name,
                'item_description' => $itemDescriptions ?: '-',
                'po_amount'        => number_format((float) $po->po_amount, 2),
                'remarks'          => $po->remarks ?? '-',
                'status'           => $po->status,
                'action'           => $actions,
            ];
        });

        return view('pages.indent.indentPOForm.listIndentPOForm.listIndentPOForm', [
            'title' => $title,
            'rows' => $rows,
            'pagination' => $poRegisters,
            'viewBtnTitle' => $viewBtnTitle,
            'departments' => $departments,
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
            ->whereNotIn(DB::raw('LOWER(status)'), ['cancel', 'cancelled'])
            ->pluck('item_description');

        $filedQtyMap = [];
        foreach ($poItemsRaw as $rawJson) {
            if (!empty($rawJson) && is_string($rawJson)) {
                $decoded = json_decode($rawJson, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    foreach ($decoded as $entry) {
                        if (is_array($entry) && isset($entry['description'])) {
                            $descKey   = mb_strtolower(trim($entry['description']));
                            $ordered   = (int)($entry['quantity'] ?? $entry['po_quantity'] ?? 1);
                            $received  = isset($entry['quantity_received']) ? (int)$entry['quantity_received'] : 0;
                            $cancelled = (int)($entry['quantity_cancelled'] ?? 0);

                            // If invoice receiving has been recorded (received > 0 or cancelled > 0),
                            // committed qty for this PO is min(ordered, received + cancelled).
                            // Otherwise, committed qty is ordered.
                            if ($received > 0 || $cancelled > 0) {
                                $committed = min($ordered, $received + $cancelled);
                            } else {
                                $committed = $ordered;
                            }

                            $filedQtyMap[$descKey] = ($filedQtyMap[$descKey] ?? 0) + $committed;
                        } elseif (is_string($entry)) {
                            $descKey = mb_strtolower(trim($entry));
                            $filedQtyMap[$descKey] = ($filedQtyMap[$descKey] ?? 0) + 1;
                        }
                    }
                }
            }
        }

        // Keep indent items that still have remaining quantity to file (>0)
        $items = $itemsFromIndent
            ->map(function ($item) use ($filedQtyMap) {
                $descKey = mb_strtolower(trim((string) ($item['description'] ?? '')));
                if ($descKey === '') return null;

                $req  = (int)($item['quantity_required'] ?? 1);
                $alreadyCommitted = (int)($filedQtyMap[$descKey] ?? 0);
                $remainingToFile  = max(0, $req - $alreadyCommitted);

                $item['quantity_required'] = $req;
                $item['already_filed']     = $alreadyCommitted;
                $item['remaining_to_file'] = $remainingToFile;
                $item['quantity_balance']  = $remainingToFile;

                return $remainingToFile > 0 ? $item : null;
            })
            ->filter()
            ->values()
            ->all();

        // Now $items contains ONLY options with remaining balance or not yet created
        return view(
            'pages.indent.indentPOForm.addIndentPOForm.addIndentPOForm',
            compact('indent_id', 'departmentHeads', 'department_id', 'department_name', 'statusList', 'projectList', 'items')
        );
    }

    public function createInvoiceById(Request $request, int $id)
    {
        Gate::authorize('pos.edit');
        $po = DB::table('po_registers')->where('id', $id)->firstOrFail();

        $indent = DB::table('indent_registers')
            ->where('indent_id', $po->indent_id)
            ->first();

        // Extract items specifically ordered in THIS PO
        $poItems = [];
        if (!empty($po->item_description)) {
            $decoded = json_decode($po->item_description, true);
            if (is_array($decoded)) {
                foreach ($decoded as $entry) {
                    if (is_array($entry) && isset($entry['description'])) {
                        $desc = $entry['description'];
                        $ordered = (int)($entry['quantity'] ?? $entry['po_quantity'] ?? 1);
                        $received = (int)($entry['quantity_received'] ?? 0);
                        $cancelled = (int)($entry['quantity_cancelled'] ?? 0);

                        $unit = $entry['unit'] ?? '';
                        $req = (int)($entry['quantity_required'] ?? $ordered);

                        if ($indent && !empty($indent->items_description)) {
                            $indDecoded = json_decode($indent->items_description, true);
                            if (is_array($indDecoded)) {
                                foreach ($indDecoded as $indIt) {
                                    if (is_array($indIt) && isset($indIt['description']) && mb_strtolower(trim($indIt['description'])) === mb_strtolower(trim($desc))) {
                                        if (empty($unit)) $unit = $indIt['unit'] ?? '';
                                        if (empty($req) || $req < $ordered) $req = (int)($indIt['quantity_required'] ?? $ordered);
                                        break;
                                    }
                                }
                            }
                        }

                        $poItems[] = [
                            'description'        => $desc,
                            'unit'               => $unit,
                            'po_quantity'        => $ordered,
                            'quantity_required'  => $req,
                            'quantity_received'  => $received,
                            'quantity_cancelled' => $cancelled,
                            'quantity_balance'   => max(0, $ordered - ($received + $cancelled)),
                        ];
                    }
                }
            }
        }

        return view(
            'pages.indent.indentPOForm.addInvoiceIndentPOForm.addInvoiceIndentPOForm', [
                'po'          => $po,
                'indent'      => $indent,
                'indentItems' => $poItems,
            ]
        );
    }

    public function updateInvoice(Request $request, int $id)
    {
        Gate::authorize('pos.edit');

        $po = DB::table('po_registers')->where('id', $id)->first();
        if (!$po) {
            return back()->with('warning', 'Purchase Order not found.');
        }

        $currentStatus = mb_strtolower(trim((string)$po->status));
        if (in_array($currentStatus, ['closed', 'close', 'cancel', 'cancelled'])) {
            return back()->with('warning', "Cannot update invoice or receive goods for a {$po->status} Purchase Order.");
        }

        $validated = $request->validate([
            'invoice_date'   => 'nullable|date',
            'receiving_date' => 'nullable|date',
            'delay_in_days'  => 'nullable|integer|min:0',
            'store_indent_no'=> 'nullable|string|max:255',
            'items'          => 'nullable|array',
        ]);

        $fmt = fn($k) => $request->filled($k)
            ? Carbon::parse($request->input($k))->format('Y-m-d')
            : null;

        $data = ['updated_at' => now()];

        if ($request->has('invoice_date'))   $data['invoice_date']   = $fmt('invoice_date');
        if ($request->has('receiving_date')) $data['receiving_date'] = $fmt('receiving_date');
        if ($request->has('delay_in_days'))  $data['delay_in_days']  = $request->input('delay_in_days');
        if ($request->has('store_indent_no'))$data['store_indent_no']= $request->input('store_indent_no');

        // Update item_description JSON in po_registers for THIS PO with received & cancelled quantities
        $updatedPoItems = [];
        if ($request->has('items') && is_array($request->input('items'))) {
            $submittedItems = $request->input('items');
            if (!empty($po->item_description)) {
                $poItemDesc = json_decode($po->item_description, true);
                if (is_array($poItemDesc)) {
                    foreach ($poItemDesc as $pItem) {
                        $pDesc = is_array($pItem) ? ($pItem['description'] ?? '') : (string)$pItem;
                        $foundMatch = null;
                        foreach ($submittedItems as $sub) {
                            if (isset($sub['description']) && mb_strtolower(trim($sub['description'])) === mb_strtolower(trim($pDesc))) {
                                $foundMatch = $sub;
                                break;
                            }
                        }
                        if (is_array($pItem)) {
                            if ($foundMatch) {
                                $req = (int)($pItem['quantity'] ?? $pItem['po_quantity'] ?? 1);
                                $recRaw = (int)($foundMatch['received'] ?? 0);
                                $cancRaw = (int)($foundMatch['cancelled'] ?? 0);
                                $rec = $req > 0 ? min($req, max(0, $recRaw)) : max(0, $recRaw);
                                $canc = $req > 0 ? min(max(0, $req - $rec), max(0, $cancRaw)) : max(0, $cancRaw);

                                $pItem['quantity_received']  = $rec;
                                $pItem['quantity_cancelled'] = $canc;
                            }
                            $updatedPoItems[] = $pItem;
                        } else {
                            $updatedPoItems[] = [
                                'description'        => $pDesc,
                                'quantity'           => 1,
                                'quantity_received'  => $foundMatch ? (int)($foundMatch['received'] ?? 0) : 0,
                                'quantity_cancelled' => $foundMatch ? (int)($foundMatch['cancelled'] ?? 0) : 0,
                            ];
                        }
                    }
                    $data['item_description'] = json_encode($updatedPoItems);
                }
            }
        }

        // Calculate PO status based on received vs ordered quantities
        $totOrd = 0;
        $totRec = 0;
        $totCanc = 0;
        $itemsToEvaluate = !empty($updatedPoItems) ? $updatedPoItems : (!empty($po->item_description) ? (json_decode($po->item_description, true) ?? []) : []);
        if (is_array($itemsToEvaluate)) {
            foreach ($itemsToEvaluate as $pi) {
                if (is_array($pi)) {
                    $totOrd  += (int)($pi['quantity'] ?? $pi['po_quantity'] ?? 1);
                    $totRec  += (int)($pi['quantity_received'] ?? 0);
                    $totCanc += (int)($pi['quantity_cancelled'] ?? 0);
                }
            }
        }

        $prevStatus = $po->status;
        $newPoStatus = 'Open';
        if ($totRec > 0 || $totCanc > 0) {
            if ($totOrd > 0 && ($totRec + $totCanc) >= $totOrd) {
                $newPoStatus = 'Completed';
            } else {
                $newPoStatus = 'Partially Received';
            }
        }

        $data['status'] = $newPoStatus;
        DB::table('po_registers')->where('id', $id)->update($data);

        self::logPOAction($id, 'receipt_added', $prevStatus, $newPoStatus, null, ['items' => $itemsToEvaluate]);

        // Sync item received quantities to indent_registers
        if ($po && $po->indent_id && $request->has('items') && is_array($request->input('items'))) {
            $indent = DB::table('indent_registers')
                ->where('indent_id', $po->indent_id)
                ->first();

            if ($indent && !empty($indent->items_description)) {
                $existingItems = json_decode($indent->items_description, true) ?? [];
                $submittedItems = $request->input('items');

                $updatedItems = [];
                $allCompleted = true;

                foreach ($existingItems as $ex) {
                    $desc = $ex['description'] ?? '';
                    $foundMatch = null;
                    foreach ($submittedItems as $sub) {
                        if (isset($sub['description']) && mb_strtolower(trim($sub['description'])) === mb_strtolower(trim($desc))) {
                            $foundMatch = $sub;
                            break;
                        }
                    }

                    $req = (int)($ex['quantity_required'] ?? ($foundMatch['required'] ?? 0));
                    $recRaw = $foundMatch ? (int)($foundMatch['received'] ?? 0) : (int)($ex['quantity_received'] ?? 0);
                    $cancRaw = $foundMatch ? (int)($foundMatch['cancelled'] ?? 0) : (int)($ex['quantity_cancelled'] ?? 0);

                    // Clamp received and cancelled so they never exceed required quantity
                    $rec = $req > 0 ? min($req, max(0, $recRaw)) : max(0, $recRaw);
                    $canc = $req > 0 ? min(max(0, $req - $rec), max(0, $cancRaw)) : max(0, $cancRaw);
                    $bal = max(0, $req - ($rec + $canc));

                    if ($bal > 0) {
                        $allCompleted = false;
                    }

                    $updatedItems[] = [
                        'description'        => $desc,
                        'unit'               => $ex['unit'] ?? ($foundMatch['unit'] ?? ''),
                        'quantity_required'  => $req,
                        'quantity_received'  => $rec,
                        'quantity_cancelled' => $canc,
                        'quantity_balance'   => $bal,
                    ];
                }

                $indentData = [
                    'items_description' => json_encode($updatedItems),
                    'updated_at'        => now()
                ];

                if ($allCompleted && count($updatedItems) > 0) {
                    $indentData['status'] = 'Close';
                    DB::table('po_registers')->where('indent_id', $po->indent_id)->update(['status' => 'Close']);
                }

                DB::table('indent_registers')->where('id', $indent->id)->update($indentData);
                self::syncIndentItemsBalance($po->indent_id);
            }
        }

        return redirect()->route('po-register.viewByIndent', [
            'indent_id'     => $po->indent_id,
            'department_id' => $po->department_id,
        ])->with('success', 'Invoice & Item receiving info saved successfully.');
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

        // 4) Remaining indent items with remaining balance (>0) or not used by other POs
        $itemsRemaining = $itemsFromIndent
            ->filter(function ($item) use ($alreadyCreatedSet) {
                $desc = mb_strtolower(trim((string) ($item['description'] ?? '')));
                if ($desc === '') return false;
                $req  = (int)($item['quantity_required'] ?? 1);
                $rec  = (int)($item['quantity_received'] ?? 0);
                $canc = (int)($item['quantity_cancelled'] ?? 0);
                $bal  = max(0, $req - ($rec + $canc));
                return $bal > 0 || !$alreadyCreatedSet->contains($desc);
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
            'is_mandatory' => 'nullable|string',
        ]);

        $fmt = fn($k) => $request->filled($k)
            ? Carbon::parse($request->input($k))->format('Y-m-d')
            : null;

        $poItems = [];
        if ($request->has('po_items') && is_array($request->input('po_items'))) {
            foreach ($request->input('po_items') as $it) {
                if (!empty($it['selected'])) {
                    $req = (int)($it['quantity_required'] ?? 1);
                    $filingQty = (int)($it['po_quantity'] ?? $req);
                    $rec = (int)($it['quantity_received'] ?? 0);
                    $bal = max(0, $req - ($rec + $filingQty));
                    $poItems[] = [
                        'description'       => $it['description'] ?? '',
                        'unit'              => $it['unit'] ?? '',
                        'quantity'          => $filingQty,
                        'quantity_required' => $req,
                        'quantity_received' => $rec,
                        'quantity_balance'  => $bal,
                    ];
                }
            }
        }

        if (empty($poItems) && $request->has('item_description')) {
            $rawDescs = (array) $request->input('item_description');
            foreach ($rawDescs as $d) {
                $poItems[] = [
                    'description' => $d,
                    'quantity'    => 1,
                ];
            }
        }

        $poId = DB::table('po_registers')->insertGetId([
            'indent_id' => $request->input('indent_id'),
            'department_id' => $request->input('department_id'),
            'status' => 'Open',
            'is_mandatory' => $request->input('is_mandatory', 'Mandatory'),
            'po_date' => $fmt('po_date'),
            'party_name' => $request->input('party_name'),
            'po_wo_no' => $request->input('po_wo_no'),
            'po_amount' => $request->input('po_amount'),
            'debit_head' => $request->input('debit_head'),
            'item_description' => !empty($poItems) ? json_encode($poItems) : null,
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

        self::logPOAction($poId, 'created', null, 'Open', null, ['items' => $poItems]);

        // Sync item counts and remaining balances across ALL POs to indent_registers
        $indentId = $request->input('indent_id');
        if ($indentId) {
            self::syncIndentItemsBalance($indentId);
        }

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

        $indent = DB::table('indent_registers')
            ->leftJoin('departments', 'departments.id', '=', 'indent_registers.indent_department')
            ->select('indent_registers.*', 'departments.name as department_name')
            ->where('indent_registers.indent_id', $indent_id)
            ->first();

        if (!$indent) {
            $indent = DB::table('indent_registers')
                ->where('indent_id', $indent_id)
                ->first();
        }

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
                'indent_registers.items_description as indent_items_description',
                'indent_registers.indent_project as project_name',
            )
            ->where('po_registers.indent_id', $indent_id)
            ->where('po_registers.department_id', $department_id)
            ->orderByDesc('po_registers.created_at')
            ->get();

        // Add decoded items_description for each PO record
        $allPos->transform(function ($record) {
            $record->items = !empty($record->item_description) ? json_decode($record->item_description, true) : [];
            return $record;
        });

        $po = $allPos->first();  // summary

        return view('pages.indent.indentPOForm.viewDetailIndentPOForm.viewDetailIndentPOForm', compact(
            'title',
            'po',
            'indent',
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
                'indent_registers.remarks as indent_remarks',
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
            'is_mandatory' => 'nullable|string',
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
            'store_indent_no', 'remarks', 'is_mandatory'
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

        $po = DB::table('po_registers')->where('id', $id)->first();
        if ($po && $po->indent_id) {
            self::syncIndentItemsBalance($po->indent_id);
        }

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

        self::syncIndentItemsBalance($indentId);

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

        self::syncIndentItemsBalance($indentId);

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

    /**
     * Recalculates and syncs purchased PO quantities and remaining balances across all active POs for an indent.
     */
    public static function syncIndentItemsBalance($indentId)
    {
        if (!$indentId) return;

        $indent = DB::table('indent_registers')->where('indent_id', $indentId)->first();
        if (!$indent || empty($indent->items_description)) return;

        $existingItems = json_decode($indent->items_description, true);
        if (!is_array($existingItems)) return;

        // Fetch all active (non-cancelled) POs for this indent
        $allPoItemsRaw = DB::table('po_registers')
            ->where('indent_id', $indentId)
            ->whereNotIn(DB::raw('LOWER(status)'), ['cancel', 'cancelled'])
            ->pluck('item_description');

        $allPoQtyMap   = [];
        $allRecQtyMap  = [];
        $allCancQtyMap = [];

        foreach ($allPoItemsRaw as $rawJson) {
            if (!empty($rawJson) && is_string($rawJson)) {
                $decoded = json_decode($rawJson, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    foreach ($decoded as $entry) {
                        if (is_array($entry) && isset($entry['description'])) {
                            $dk   = mb_strtolower(trim($entry['description']));
                            $qty  = (int)($entry['quantity'] ?? $entry['po_quantity'] ?? 1);
                            $rec  = (int)($entry['quantity_received'] ?? 0);
                            $canc = (int)($entry['quantity_cancelled'] ?? 0);

                            $allPoQtyMap[$dk]   = ($allPoQtyMap[$dk]   ?? 0) + $qty;
                            $allRecQtyMap[$dk]  = ($allRecQtyMap[$dk]  ?? 0) + $rec;
                            $allCancQtyMap[$dk] = ($allCancQtyMap[$dk] ?? 0) + $canc;
                        } elseif (is_string($entry)) {
                            $dk = mb_strtolower(trim($entry));
                            $allPoQtyMap[$dk] = ($allPoQtyMap[$dk] ?? 0) + 1;
                        }
                    }
                } else {
                    foreach (preg_split('/[,|]/', $rawJson) as $p) {
                        $dk = mb_strtolower(trim($p));
                        if ($dk !== '') {
                            $allPoQtyMap[$dk] = ($allPoQtyMap[$dk] ?? 0) + 1;
                        }
                    }
                }
            }
        }

        $updatedIndentItems = [];
        $allBalZero = true;

        foreach ($existingItems as $ex) {
            $desc = $ex['description'] ?? '';
            $dk   = mb_strtolower(trim($desc));
            $req  = (int)($ex['quantity_required'] ?? 0);
            $rec  = max((int)($ex['quantity_received'] ?? 0), (int)($allRecQtyMap[$dk] ?? 0));
            $canc = max((int)($ex['quantity_cancelled'] ?? 0), (int)($allCancQtyMap[$dk] ?? 0));
            $po   = (int)($allPoQtyMap[$dk] ?? 0);

            // Remaining balance required for this item
            $bal = max(0, $req - ($rec + $canc));

            if ($bal > 0) {
                $allBalZero = false;
            }

            $updatedIndentItems[] = [
                'description'        => $desc,
                'unit'               => $ex['unit'] ?? '',
                'quantity_required'  => $req,
                'purchased_order'    => $po,
                'quantity_received'  => $rec,
                'quantity_cancelled' => $canc,
                'quantity_balance'   => $bal,
            ];
        }

        $updateData = [
            'items_description' => json_encode($updatedIndentItems),
            'updated_at'        => now(),
        ];

        if (count($updatedIndentItems) > 0) {
            $updateData['status'] = $allBalZero ? 'Close' : 'Pending';
        }

        DB::table('indent_registers')->where('id', $indent->id)->update($updateData);
    }

    /**
     * Log an action in po_audit_logs.
     */
    public static function logPOAction($poId, $action, $prevStatus = null, $newStatus = null, $reason = null, $details = null)
    {
        try {
            DB::table('po_audit_logs')->insert([
                'po_id'           => $poId,
                'user_id'         => auth()->id(),
                'user_name'       => auth()->user()?->name ?? 'System',
                'action'          => $action,
                'previous_status' => $prevStatus,
                'new_status'      => $newStatus,
                'reason'          => $reason,
                'details'         => is_array($details) || is_object($details) ? json_encode($details) : $details,
                'created_at'      => now(),
                'updated_at'      => now(),
            ]);
        } catch (\Throwable $e) {
            Log::error("Failed to write PO audit log: " . $e->getMessage());
        }
    }

    /**
     * Manually Close a PO with an optional reason.
     */
    public function closePO(Request $request, $id)
    {
        Gate::authorize('pos.edit');
        $po = DB::table('po_registers')->where('id', $id)->first();
        if (!$po) {
            return back()->with('warning', 'Purchase Order not found.');
        }

        $prevStatus = $po->status;
        $reason = $request->input('close_reason') ?? $request->input('reason');

        DB::table('po_registers')->where('id', $id)->update([
            'status'       => 'Closed',
            'closed_at'    => now(),
            'closed_by'    => auth()->id(),
            'close_reason' => $reason,
            'updated_at'   => now(),
        ]);

        self::logPOAction($id, 'closed', $prevStatus, 'Closed', $reason);

        if ($po->indent_id) {
            self::syncIndentItemsBalance($po->indent_id);
        }

        return back()->with('success', "PO #{$po->id} has been manually closed.");
    }

    /**
     * Manually Reopen a previously closed PO.
     */
    public function reopenPO(Request $request, $id)
    {
        Gate::authorize('pos.edit');
        $po = DB::table('po_registers')->where('id', $id)->first();
        if (!$po) {
            return back()->with('warning', 'Purchase Order not found.');
        }

        $prevStatus = $po->status;

        // Calculate current received vs ordered
        $items = !empty($po->item_description) ? json_decode($po->item_description, true) : [];
        $totalOrdered = 0;
        $totalReceived = 0;
        if (is_array($items)) {
            foreach ($items as $it) {
                if (is_array($it)) {
                    $totalOrdered += (int)($it['quantity'] ?? $it['po_quantity'] ?? 0);
                    $totalReceived += (int)($it['quantity_received'] ?? 0);
                }
            }
        }

        $newStatus = ($totalReceived > 0) ? 'Partially Received' : 'Reopened';

        DB::table('po_registers')->where('id', $id)->update([
            'status'      => $newStatus,
            'reopened_at' => now(),
            'reopened_by' => auth()->id(),
            'updated_at'  => now(),
        ]);

        self::logPOAction($id, 'reopened', $prevStatus, $newStatus, $request->input('reason'));

        if ($po->indent_id) {
            self::syncIndentItemsBalance($po->indent_id);
        }

        return back()->with('success', "PO #{$po->id} has been reopened.");
    }

    /**
     * Get JSON audit logs for a PO.
     */
    public function getAuditLogs($id)
    {
        try {
            $logs = DB::table('po_audit_logs')
                ->where('po_id', $id)
                ->orderByDesc('created_at')
                ->get();
            return response()->json($logs);
        } catch (\Throwable $e) {
            return response()->json([]);
        }
    }
}

