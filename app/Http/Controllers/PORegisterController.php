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
            ->leftJoin('departments', 'departments.id', '=', 'po_registers.department_id')
            ->leftJoin('indent_registers', function ($join) {
                $join->on(DB::raw('CAST(indent_registers.indent_id AS CHAR)'), '=', DB::raw('CAST(po_registers.indent_id AS CHAR)'));
            });

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

        if ($request->filled('item_status')) {
            $st = mb_strtolower(trim($request->item_status));
            $query->where(function ($q) use ($st) {
                $q->whereRaw('LOWER(po_registers.item_description) LIKE ?', ['%"status":"' . $st . '"%'])
                  ->orWhereRaw('LOWER(po_registers.item_description) LIKE ?', ['%"status": "' . $st . '"%'])
                  ->orWhereRaw('LOWER(indent_registers.items_description) LIKE ?', ['%"status":"' . $st . '"%'])
                  ->orWhereRaw('LOWER(indent_registers.items_description) LIKE ?', ['%"status": "' . $st . '"%']);

                if (in_array($st, ['cancelled', 'cancel'])) {
                    $q->orWhereRaw('indent_registers.items_description REGEXP ?', ['"quantity_cancelled":\s*([1-9]|0\.[0-9]*[1-9])'])
                      ->orWhereRaw('po_registers.item_description REGEXP ?', ['"quantity_cancelled":\s*([1-9]|0\.[0-9]*[1-9])']);
                } elseif (in_array($st, ['po created', 'ordered'])) {
                    $q->orWhereRaw('LOWER(indent_registers.items_description) LIKE ?', ['%"status":"ordered"%'])
                      ->orWhereRaw('LOWER(indent_registers.items_description) LIKE ?', ['%"status":"po created"%']);
                } elseif (in_array($st, ['completed', 'received'])) {
                    $q->orWhereRaw('LOWER(indent_registers.items_description) LIKE ?', ['%"status":"completed"%'])
                      ->orWhereRaw('LOWER(indent_registers.items_description) LIKE ?', ['%"status":"received"%']);
                } elseif ($st === 'pending') {
                    $q->orWhere(function ($sub) {
                        $sub->whereRaw('indent_registers.items_description NOT LIKE ?', ['%"status":%'])
                            ->whereRaw('LOWER(indent_registers.status) = "pending"');
                    });
                }
            });
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

        $poRegisters = $query->select(
            'po_registers.*',
            'departments.name as department_name',
            'indent_registers.items_description as indent_items_description'
        )
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
                $actions['edit']    = route('po-register.edit', $po->id);
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

            // Extract item details
            $rawItems = [];
            if (!empty($po->item_description) && is_string($po->item_description)) {
                $decoded = json_decode($po->item_description, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    $rawItems = $decoded;
                }
            }
            if (empty($rawItems) && !empty($po->indent_items_description)) {
                $decodedIndent = json_decode($po->indent_items_description, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decodedIndent)) {
                    $rawItems = $decodedIndent;
                }
            }

            $structuredItems = [];
            if (!empty($rawItems) && is_array($rawItems)) {
                foreach ($rawItems as $it) {
                    if (is_array($it)) {
                        $desc = $it['description'] ?? 'Item';
                        $req  = (float)($it['quantity_required'] ?? $it['quantity'] ?? $it['po_quantity'] ?? 0);
                        $rec  = (float)($it['quantity_received'] ?? 0);
                        $canc = (float)($it['quantity_cancelled'] ?? 0);
                        $iStatus = $it['status'] ?? null;
                        if (!$iStatus) {
                            if ($canc >= $req && $req > 0) {
                                $iStatus = 'Cancelled';
                            } elseif ($rec >= $req && $req > 0) {
                                $iStatus = 'Completed';
                            } elseif ($rec > 0) {
                                $iStatus = 'Partially Received';
                            } else {
                                $iStatus = 'PO Created';
                            }
                        }
                        $structuredItems[] = [
                            'description'        => $desc,
                            'quantity_required'  => $req,
                            'quantity_received'  => $rec,
                            'quantity_cancelled' => $canc,
                            'status'             => $iStatus,
                        ];
                    } elseif (is_string($it)) {
                        $structuredItems[] = [
                            'description'        => $it,
                            'quantity_required'  => 1,
                            'quantity_received'  => 0,
                            'quantity_cancelled' => 0,
                            'status'             => 'PO Created',
                        ];
                    }
                }
            }

            $itemDescriptions = collect($structuredItems)->pluck('description')->filter()->implode(', ');

            return [
                'id'               => $po->id,
                'po_date'          => $po->po_date ? \Carbon\Carbon::parse($po->po_date)->format('d-m-Y') : '-',
                'indent_id'        => $po->indent_id,
                'department_name'  => $po->department_name ?? '-',
                'party_name'       => $po->party_name,
                'items'            => $structuredItems,
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
                            $ordered   = (float)($entry['quantity'] ?? $entry['po_quantity'] ?? 1);
                            $received  = isset($entry['quantity_received']) ? (float)$entry['quantity_received'] : 0;
                            $cancelled = (float)($entry['quantity_cancelled'] ?? 0);

                            // If invoice receiving has been recorded (received > 0 or cancelled > 0),
                            // committed qty for this PO is min($ordered, $received + $cancelled).
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

                $req  = (float)($item['quantity_required'] ?? 1);
                $canc = (float)($item['quantity_cancelled'] ?? 0);
                $rec  = (float)($item['quantity_received'] ?? 0);
                $alreadyCommitted = (float)($filedQtyMap[$descKey] ?? 0);
                $remainingToFile  = round(max(0, $req - ($alreadyCommitted + $canc)), 4);

                $st = $item['status'] ?? null;
                if (!$st) {
                    if ($canc >= $req || ($canc > 0 && $remainingToFile <= 0.0001 && $rec <= 0.0001)) {
                        $st = 'Cancelled';
                    } elseif ($rec >= $req && $req > 0) {
                        $st = 'Completed';
                    } elseif ($rec > 0) {
                        $st = 'Partially Received';
                    } elseif ($alreadyCommitted > 0) {
                        $st = 'PO Created';
                    } else {
                        $st = 'Pending';
                    }
                }

                $item['quantity_required']  = $req;
                $item['quantity_cancelled'] = $canc;
                $item['already_filed']      = $alreadyCommitted;
                $item['remaining_to_file']  = $remainingToFile;
                $item['quantity_balance']   = $remainingToFile;
                $item['status']             = ucfirst($st);

                return $remainingToFile > 0.0001 ? $item : null;
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
                        $ordered = (float)($entry['quantity'] ?? $entry['po_quantity'] ?? 1);
                        $received = (float)($entry['quantity_received'] ?? 0);
                        $cancelled = (float)($entry['quantity_cancelled'] ?? 0);

                        $unit = $entry['unit'] ?? '';
                        $req = (float)($entry['quantity_required'] ?? $ordered);

                        if ($indent && !empty($indent->items_description)) {
                            $indDecoded = json_decode($indent->items_description, true);
                            if (is_array($indDecoded)) {
                                foreach ($indDecoded as $indIt) {
                                    if (is_array($indIt) && isset($indIt['description']) && mb_strtolower(trim($indIt['description'])) === mb_strtolower(trim($desc))) {
                                        if (empty($unit)) $unit = $indIt['unit'] ?? '';
                                        if (empty($req) || $req < $ordered) $req = (float)($indIt['quantity_required'] ?? $ordered);
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
                            'quantity_balance'   => round(max(0, $ordered - ($received + $cancelled)), 4),
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
                                $req = (float)($pItem['quantity'] ?? $pItem['po_quantity'] ?? 1);
                                $recRaw = (float)($foundMatch['received'] ?? 0);
                                $cancRaw = (float)($foundMatch['cancelled'] ?? 0);
                                $rec = $req > 0 ? min($req, max(0, $recRaw)) : max(0, $recRaw);
                                $canc = $req > 0 ? min(max(0, $req - $rec), max(0, $cancRaw)) : max(0, $cancRaw);

                                $pItem['quantity_received']  = round($rec, 4);
                                $pItem['quantity_cancelled'] = round($canc, 4);
                            }
                            $updatedPoItems[] = $pItem;
                        } else {
                            $updatedPoItems[] = [
                                'description'        => $pDesc,
                                'quantity'           => 1,
                                'quantity_received'  => $foundMatch ? (float)($foundMatch['received'] ?? 0) : 0,
                                'quantity_cancelled' => $foundMatch ? (float)($foundMatch['cancelled'] ?? 0) : 0,
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
                    $totOrd  += (float)($pi['quantity'] ?? $pi['po_quantity'] ?? 1);
                    $totRec  += (float)($pi['quantity_received'] ?? 0);
                    $totCanc += (float)($pi['quantity_cancelled'] ?? 0);
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
                    $desc = is_array($ex) ? ($ex['description'] ?? '') : (string)$ex;
                    $foundMatch = null;
                    foreach ($submittedItems as $sub) {
                        if (isset($sub['description']) && mb_strtolower(trim($sub['description'])) === mb_strtolower(trim($desc))) {
                            $foundMatch = $sub;
                            break;
                        }
                    }

                    $req = (float)(is_array($ex) ? ($ex['quantity_required'] ?? ($foundMatch['required'] ?? 1)) : ($foundMatch['required'] ?? 1));
                    $exRec = (float)(is_array($ex) ? ($ex['quantity_received'] ?? 0) : 0);
                    $exCanc = (float)(is_array($ex) ? ($ex['quantity_cancelled'] ?? 0) : 0);
                    $recRaw = max($exRec, $foundMatch ? (float)($foundMatch['received'] ?? 0) : 0);
                    $cancRaw = max($exCanc, $foundMatch ? (float)($foundMatch['cancelled'] ?? 0) : 0);

                    // Clamp received and cancelled so they never exceed required quantity
                    $rec = $req > 0 ? min($req, max(0, $recRaw)) : max(0, $recRaw);
                    $canc = $req > 0 ? min(max(0, $req - $rec), max(0, $cancRaw)) : max(0, $cancRaw);
                    $bal = round(max(0, $req - ($rec + $canc)), 4);

                    if ($bal > 0.0001) {
                        $allCompleted = false;
                    }

                    $updatedItems[] = [
                        'description'        => $desc,
                        'unit'               => is_array($ex) ? ($ex['unit'] ?? ($foundMatch['unit'] ?? '')) : ($foundMatch['unit'] ?? ''),
                        'quantity_required'  => $req,
                        'quantity_received'  => round($rec, 4),
                        'quantity_cancelled' => round($canc, 4),
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
                $req  = (float)($item['quantity_required'] ?? 1);
                $rec  = (float)($item['quantity_received'] ?? 0);
                $canc = (float)($item['quantity_cancelled'] ?? 0);
                $bal  = round(max(0, $req - ($rec + $canc)), 4);
                return $bal > 0.0001 || !$alreadyCreatedSet->contains($desc);
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
        $cancelledIndentItems = [];

        if ($request->has('po_items') && is_array($request->input('po_items'))) {
            foreach ($request->input('po_items') as $it) {
                $desc = $it['description'] ?? '';
                $req = (float)($it['quantity_required'] ?? 1);
                $rec = (float)($it['quantity_received'] ?? 0);
                $alreadyFiled = (float)($it['already_filed'] ?? 0);
                $cancPrev = (float)($it['quantity_cancelled'] ?? 0);
                $remaining = round(max(0, $req - ($alreadyFiled + $cancPrev)), 4);

                // Check if item is marked for cancellation at time of filing PO
                $isCancelled = !empty($it['cancel_item']) || (($it['action'] ?? '') === 'cancel');

                if ($isCancelled) {
                    $cancelQty = isset($it['cancel_qty']) && (float)$it['cancel_qty'] > 0
                        ? min($remaining, (float)$it['cancel_qty'])
                        : $remaining;

                    if ($cancelQty > 0.0001) {
                        $cancelledIndentItems[mb_strtolower(trim($desc))] = [
                            'cancel_qty' => round($cancelQty, 4),
                            'reason'     => $it['cancel_reason'] ?? 'Cancelled at time of filing PO',
                        ];
                    }
                } elseif (!empty($it['selected'])) {
                    $filingQty = (float)($it['po_quantity'] ?? $remaining);
                    $bal = round(max(0, $req - ($rec + $filingQty + $cancPrev)), 4);
                    $poItems[] = [
                        'description'       => $desc,
                        'unit'              => $it['unit'] ?? '',
                        'quantity'          => round($filingQty, 4),
                        'quantity_required' => $req,
                        'quantity_received' => $rec,
                        'quantity_balance'  => $bal,
                    ];
                }
            }
        }

        if (empty($poItems) && empty($cancelledIndentItems) && $request->has('item_description')) {
            $rawDescs = (array) $request->input('item_description');
            foreach ($rawDescs as $d) {
                $poItems[] = [
                    'description' => $d,
                    'quantity'    => 1,
                ];
            }
        }

        // Apply cancellations to indent_registers if any items were cancelled
        $indentId = $request->input('indent_id');
        if (!empty($cancelledIndentItems) && $indentId) {
            $indent = DB::table('indent_registers')->where('indent_id', $indentId)->first();
            if ($indent && !empty($indent->items_description)) {
                $currItems = json_decode($indent->items_description, true) ?? [];
                $newItems = [];
                $allBalZero = true;
                $allCancelled = true;

                foreach ($currItems as $ci) {
                    $cDesc = $ci['description'] ?? '';
                    $cDk = mb_strtolower(trim($cDesc));
                    $cReq = (float)($ci['quantity_required'] ?? 1);
                    $cRec = (float)($ci['quantity_received'] ?? 0);
                    $cCanc = (float)($ci['quantity_cancelled'] ?? 0);

                    if (isset($cancelledIndentItems[$cDk])) {
                        $cAddCanc = (float)$cancelledIndentItems[$cDk]['cancel_qty'];
                        $cCanc = min($cReq, $cCanc + $cAddCanc);
                        $ci['cancel_reason'] = $cancelledIndentItems[$cDk]['reason'];
                    }

                    $cBal = round(max(0, $cReq - ($cRec + $cCanc)), 4);
                    $ci['quantity_cancelled'] = round($cCanc, 4);
                    $ci['quantity_balance'] = $cBal;

                    if ($cBal <= 0.0001 && $cRec <= 0.0001) {
                        $ci['status'] = 'Cancelled';
                    } elseif (($cRec >= $cReq - 0.0001 || ($cBal <= 0.0001 && ($cRec + $cCanc) >= $cReq - 0.0001)) && $cReq > 0) {
                        $ci['status'] = 'Completed';
                    } elseif ($cRec > 0) {
                        $ci['status'] = 'Partially Received';
                    }

                    if ($cBal > 0.0001) {
                        $allBalZero = false;
                    }
                    if (strtolower($ci['status'] ?? '') !== 'cancelled') {
                        $allCancelled = false;
                    }

                    $newItems[] = $ci;
                }

                $indentUp = [
                    'items_description' => json_encode($newItems),
                    'updated_at'        => now(),
                ];
                if ($allCancelled) {
                    $indentUp['status'] = 'Cancel';
                } elseif ($allBalZero) {
                    $indentUp['status'] = 'Close';
                }
                DB::table('indent_registers')->where('id', $indent->id)->update($indentUp);
            }
        }

        // If no PO items were ordered (all submitted items were cancelled or none selected)
        if (empty($poItems)) {
            if (!empty($cancelledIndentItems)) {
                return redirect()
                    ->route('indent.index')
                    ->with('success', 'Selected items have been cancelled successfully.');
            }

            return back()->with('warning', 'Please select at least one item to order or mark for cancellation.');
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

        $po = DB::table('po_registers')->where('id', $id)->first();
        if (!$po) {
            return back()->with('warning', 'Purchase Order not found.');
        }

        $validated = $request->validate([
            'indent_id'          => 'nullable|integer',
            'department_id'      => 'nullable|string',
            'status'             => 'nullable|string',
            'po_date'            => 'nullable|date',
            'party_name'         => 'nullable|string',
            'po_wo_no'           => 'nullable|string',
            'po_amount'          => 'nullable|numeric',
            'debit_head'         => 'nullable|string',
            'item_description'   => 'nullable',
            'po_items'           => 'nullable|array',
            'expected_days'      => 'nullable',
            'expected_date'      => 'nullable|date',
            'invoice_date'       => 'nullable|date',
            'receiving_date'     => 'nullable',
            'invoice'            => 'nullable|string',
            'delay_in_days'      => 'nullable|integer',
            'store_indent_no'    => 'nullable|string',
            'remarks'            => 'nullable|string',
            'is_mandatory'       => 'nullable|string',
        ]);

        $fmt = function (string $k) use ($request) {
            if (!$request->filled($k)) {
                return null;
            }
            try {
                return Carbon::parse($request->input($k))->format('Y-m-d');
            } catch (\Throwable $e) {
                return $request->input($k);
            }
        };

        $data = [];

        foreach ([
            'indent_id', 'department_id', 'status', 'party_name', 'po_wo_no',
            'po_amount', 'debit_head', 'invoice', 'delay_in_days',
            'store_indent_no', 'remarks', 'is_mandatory'
        ] as $k) {
            if ($request->has($k)) {
                $data[$k] = $request->input($k);
            }
        }

        foreach (['po_date', 'expected_date', 'invoice_date', 'receiving_date'] as $k) {
            if ($request->filled($k)) {
                $data[$k] = $fmt($k);
            }
        }

        if ($request->has('expected_days')) {
            $data['expected_days'] = $request->filled('expected_days')
                ? (string) $request->input('expected_days')
                : null;
        }

        // Build structured po_items breakdown array (matching store() behavior)
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
                if (is_array($d) && isset($d['description'])) {
                    $poItems[] = $d;
                } elseif (is_string($d)) {
                    $poItems[] = [
                        'description' => $d,
                        'quantity'    => 1,
                    ];
                }
            }
        }

        if (!empty($poItems)) {
            $data['item_description'] = json_encode($poItems);
        }

        $data['updated_at'] = now();

        $prevStatus = $po->status;
        $affected = DB::table('po_registers')->where('id', $id)->update($data);

        self::logPOAction($id, 'updated', $prevStatus, $data['status'] ?? $prevStatus, null, ['items' => $poItems]);

        $poUpdated = DB::table('po_registers')->where('id', $id)->first();
        if ($poUpdated && $poUpdated->indent_id) {
            try {
                self::syncIndentItemsBalance($poUpdated->indent_id);
            } catch (\Throwable $e) {
                Log::error("Failed to sync indent items balance for indent_id={$poUpdated->indent_id}: " . $e->getMessage());
            }
        }

        if ($affected === 0 && count($data) === 1) {
            return redirect()
                ->route('po-register.index')
                ->with('info', 'No changes submitted.');
        }

        return redirect()
            ->route('po-register.index')
            ->with('success', 'PO updated successfully.');
    }
    public function destroy(string $id)
    {
        //
    }
    public function updateById(Request $request, int $id)
    {
        return $this->updatePObyId($request, $id);
    }

    public function update(Request $request, int $id)
    {
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
                            $qty  = (float)($entry['quantity'] ?? $entry['po_quantity'] ?? 1);
                            $rec  = (float)($entry['quantity_received'] ?? 0);
                            $canc = (float)($entry['quantity_cancelled'] ?? 0);

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
            $desc = is_array($ex) ? ($ex['description'] ?? '') : (string) $ex;
            $dk   = mb_strtolower(trim($desc));
            $req  = (float) (is_array($ex) ? ($ex['quantity_required'] ?? 1) : 1);
            $rec  = max((float) (is_array($ex) ? ($ex['quantity_received'] ?? 0) : 0), (float) ($allRecQtyMap[$dk] ?? 0));
            $canc = max((float) (is_array($ex) ? ($ex['quantity_cancelled'] ?? 0) : 0), (float) ($allCancQtyMap[$dk] ?? 0));
            $po   = (float) ($allPoQtyMap[$dk] ?? 0);

            // Remaining balance required for this item
            $bal = round(max(0, $req - ($rec + $canc)), 4);

            if ($bal > 0.0001) {
                $allBalZero = false;
            }

            // Determine item status
            $itemStatus = 'Pending';
            if ($canc >= $req - 0.0001 || ($canc > 0 && $bal <= 0.0001 && $rec <= 0.0001)) {
                $itemStatus = 'Cancelled';
            } elseif (($rec >= $req - 0.0001 || ($bal <= 0.0001 && ($rec + $canc) >= $req - 0.0001)) && $req > 0) {
                $itemStatus = 'Completed';
            } elseif ($rec > 0) {
                $itemStatus = 'Partially Received';
            } elseif ($po > 0) {
                $itemStatus = 'PO Created';
            }

            $updatedIndentItems[] = [
                'description'        => $desc,
                'unit'               => is_array($ex) ? ($ex['unit'] ?? '') : '',
                'quantity_required'  => $req,
                'purchased_order'    => round($po, 4),
                'quantity_received'  => round($rec, 4),
                'quantity_cancelled' => round($canc, 4),
                'quantity_balance'   => $bal,
                'status'             => $itemStatus,
            ];
        }

        $updateData = [
            'items_description' => json_encode($updatedIndentItems),
            'updated_at'        => now(),
        ];

        if (count($updatedIndentItems) > 0) {
            if ($allBalZero) {
                $allCancelled = collect($updatedIndentItems)->every(fn($it) => ($it['status'] ?? '') === 'Cancelled');
                $updateData['status'] = $allCancelled ? 'Cancel' : 'Close';
            } else {
                $updateData['status'] = 'Pending';
            }
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
     * Manually Cancel a PO with an optional reason.
     */
    public function cancelPO(Request $request, $id)
    {
        Gate::authorize('pos.edit');
        $po = DB::table('po_registers')->where('id', $id)->first();
        if (!$po) {
            return back()->with('warning', 'Purchase Order not found.');
        }

        $prevStatus = $po->status;
        $reason = $request->input('cancel_reason') ?? $request->input('reason');

        DB::table('po_registers')->where('id', $id)->update([
            'status'        => 'Cancel',
            'closed_at'     => now(),
            'closed_by'     => auth()->id(),
            'close_reason'  => $reason,
            'updated_at'    => now(),
        ]);

        self::logPOAction($id, 'cancelled', $prevStatus, 'Cancel', $reason);

        if ($po->indent_id) {
            self::syncIndentItemsBalance($po->indent_id);
        }

        return back()->with('success', "PO #{$po->id} has been cancelled.");
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
                    $totalOrdered += (float)($it['quantity'] ?? $it['po_quantity'] ?? 0);
                    $totalReceived += (float)($it['quantity_received'] ?? 0);
                }
            }
        }

        $newStatus = ($totalReceived > 0.0001) ? 'Partially Received' : 'Reopened';

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

