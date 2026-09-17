<?php

namespace App\Http\Controllers;

use App\Exports\AllIndentsExport;
use App\Exports\PORegisterExport;
use App\Helpers\SearchHelper;
use App\Models\IndentRegister;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function viewReport(Request $request)
    {
        $query = DB::table('po_registers')
            ->leftJoin('indent_registers', function ($join) {
                $join->on(DB::raw('CAST(indent_registers.indent_id AS CHAR)'), '=', DB::raw('CAST(po_registers.indent_id AS CHAR)'));
            })
            ->leftJoin('departments', 'departments.id', '=', 'po_registers.department_id')
            ->leftJoin('projects', 'projects.id', '=', 'indent_registers.indent_project')
            ->leftJoin('units', 'units.id', '=', 'indent_registers.unit')
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
                'indent_registers.indent_id as indent_ticket_no',
                'indent_registers.indent_date',
                'projects.name as project_name',
                'indent_registers.items_description as indent_item_description',
                'units.name as unit_name',
                'indent_registers.quantity_required',
                'indent_registers.purchased_order',
                'indent_registers.quantity_received',
                'indent_registers.quantity_balance'
            );

        // Optional Filters (Apply only if values are passed)
        if ($request->filled('indent_id')) {
            $query->where('po_registers.indent_id', $request->indent_id);
        }

        if ($request->filled('department')) {
            $query->where('departments.name', $request->get('department'));
        }

        if ($request->filled('project')) {
            $query->where('projects.name', $request->get('project'));
        }

        if ($request->filled('status')) {
            $st = strtolower(trim($request->get('status')));
            if (in_array($st, ['pending', 'open'])) {
                $query->whereIn(DB::raw('LOWER(po_registers.status)'), ['pending', 'open']);
            } elseif ($st === 'partially received') {
                $query->where(DB::raw('LOWER(po_registers.status)'), 'partially received');
            } elseif (in_array($st, ['completed', 'close', 'closed'])) {
                $query->whereIn(DB::raw('LOWER(po_registers.status)'), ['completed', 'close', 'closed']);
            } elseif (in_array($st, ['cancel', 'cancelled'])) {
                $query->whereIn(DB::raw('LOWER(po_registers.status)'), ['cancel', 'cancelled']);
            } else {
                $query->where(DB::raw('LOWER(po_registers.status)'), $st);
            }
        }

        if ($search = $request->input('search')) {
            SearchHelper::applyFuzzySearch($query, $search, [
                'po_registers.party_name',
                'departments.name',
                'po_registers.po_wo_no',
                'projects.name',
                'po_registers.indent_id',
                'po_registers.item_description',
                'po_registers.remarks'
            ]);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('po_registers.created_at', '>=', $request->get('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('po_registers.created_at', '<=', $request->get('date_to'));
        }

        if ($request->filled(['start_date', 'end_date'])) {
            try {
                $start = Carbon::parse($request->start_date)->startOfDay();
                $end = Carbon::parse($request->end_date)->endOfDay();
                $query->whereBetween('po_registers.created_at', [$start, $end]);
            } catch (\Exception $e) {
                // Invalid date format fallback
            }
        }

        $perPage = (int) $request->get('per_page', 15);
        $reports = $query->orderByDesc('po_registers.created_at')->paginate($perPage)->withQueryString();

        $departments = \App\Models\Department::orderBy('name')->get();
        $projects = \App\Models\Project::orderBy('name')->get();

        // Columns for table
        $columns = [
            ['label' => 'Indent Ticket', 'key' => 'indent_ticket_no'],
            ['label' => 'Department', 'key' => 'department_name'],
            ['label' => 'Project', 'key' => 'project_name'],
            ['label' => 'Party Name', 'key' => 'party_name'],
            ['label' => 'PO No.', 'key' => 'po_wo_no'],
            ['label' => 'PO Amount', 'key' => 'po_amount', 'type' => 'number'],
            ['label' => 'Status', 'key' => 'status', 'type' => 'badge'],
            ['label' => 'Created On', 'key' => 'po_created_at', 'type' => 'date'],
        ];

        return view('pages.report.viewReport.viewReport', compact('reports', 'columns', 'departments', 'projects'));
    }

    /**
     * Helper to retrieve enriched indent records, KPI aggregates, and filter metadata.
     */
    protected function getEnrichedIndentData(Request $request, bool $paginate = true, int $perPage = 15)
    {
        $query = IndentRegister::query();
        $filterParts = [];

        if ($request->filled('search')) {
            $search = trim($request->get('search'));
            $filterParts[] = "Search: '{$search}'";
            SearchHelper::applySearch($query, $search, [
                'indent_id',
                'indent_department',
                'indent_project',
                'items_description',
                'remarks'
            ]);
        }

        if ($request->filled('department')) {
            $dept = $request->get('department');
            $filterParts[] = "Department: {$dept}";
            $query->where('indent_department', $dept);
        }

        if ($request->filled('project')) {
            $proj = $request->get('project');
            $filterParts[] = "Project: {$proj}";
            $query->where('indent_project', $proj);
        }

        if ($request->filled('status')) {
            $st = strtolower(trim($request->get('status')));
            $filterParts[] = "Status: " . ucfirst($st);
            if (in_array($st, ['pending', 'open'])) {
                $query->whereIn(DB::raw('LOWER(status)'), ['pending', 'open']);
            } elseif ($st === 'partially received') {
                $query->where(DB::raw('LOWER(status)'), 'partially received');
            } elseif (in_array($st, ['completed', 'close', 'closed'])) {
                $query->whereIn(DB::raw('LOWER(status)'), ['completed', 'close', 'closed']);
            } elseif (in_array($st, ['cancel', 'cancelled'])) {
                $query->whereIn(DB::raw('LOWER(status)'), ['cancel', 'cancelled']);
            } else {
                $query->where(DB::raw('LOWER(status)'), $st);
            }
        }

        if ($request->filled('item_status')) {
            $ist = strtolower(trim($request->get('item_status')));
            $filterParts[] = "Item Status: " . ucfirst($ist);
            if (in_array($ist, ['cancelled', 'cancel'])) {
                $query->where(function ($q) {
                    $q->whereRaw('items_description REGEXP ?', ['"quantity_cancelled":\s*([1-9]|0\.[0-9]*[1-9])'])
                      ->orWhereRaw('LOWER(items_description) LIKE ?', ['%"status":"cancelled"%'])
                      ->orWhereRaw('LOWER(items_description) LIKE ?', ['%"status": "cancelled"%']);
                });
            } elseif (in_array($ist, ['po created', 'ordered'])) {
                $query->where(function ($q) {
                    $q->whereRaw('LOWER(items_description) LIKE ?', ['%"status":"po created"%'])
                      ->orWhereRaw('LOWER(items_description) LIKE ?', ['%"status": "po created"%'])
                      ->orWhereRaw('LOWER(items_description) LIKE ?', ['%"status":"ordered"%']);
                });
            } elseif ($ist === 'partially received') {
                $query->where(function ($q) {
                    $q->whereRaw('LOWER(items_description) LIKE ?', ['%"status":"partially received"%'])
                      ->orWhereRaw('LOWER(items_description) LIKE ?', ['%"status": "partially received"%']);
                });
            } elseif (in_array($ist, ['completed', 'received'])) {
                $query->where(function ($q) {
                    $q->whereRaw('LOWER(items_description) LIKE ?', ['%"status":"completed"%'])
                      ->orWhereRaw('LOWER(items_description) LIKE ?', ['%"status": "completed"%'])
                      ->orWhereRaw('LOWER(items_description) LIKE ?', ['%"status":"received"%']);
                });
            } elseif ($ist === 'pending') {
                $query->where(function ($q) {
                    $q->whereRaw('LOWER(items_description) LIKE ?', ['%"status":"pending"%'])
                      ->orWhereRaw('LOWER(items_description) LIKE ?', ['%"status": "pending"%'])
                      ->orWhere(function ($sub) {
                          $sub->whereRaw('items_description NOT LIKE ?', ['%"status":%'])
                              ->whereRaw('LOWER(status) = "pending"');
                      });
                });
            }
        }

        if ($request->filled('date_from')) {
            $df = $request->get('date_from');
            $filterParts[] = "From: {$df}";
            $query->whereDate('indent_date', '>=', $df);
        }

        if ($request->filled('date_to')) {
            $dt = $request->get('date_to');
            $filterParts[] = "To: {$dt}";
            $query->whereDate('indent_date', '<=', $dt);
        }

        // Clone query to compute aggregate KPI stats across all matching records
        $allMatching = (clone $query)->get();
        $totalIndents = $allMatching->count();
        $totReq = 0;
        $totPO = 0;
        $totRec = 0;
        $totCanc = 0;
        $totBal = 0;

        $matchingIdsForPOs = [];
        foreach ($allMatching as $ind) {
            $matchingIdsForPOs[] = (string) $ind->indent_id;
            $matchingIdsForPOs[] = (string) $ind->id;
            if (!empty($ind->items_description)) {
                $itemsArr = is_string($ind->items_description) ? json_decode($ind->items_description, true) : $ind->items_description;
                if (is_array($itemsArr)) {
                    foreach ($itemsArr as $it) {
                        if (is_array($it)) {
                            $rq = (float)($it['quantity_required'] ?? 0);
                            $po = (float)($it['purchased_order'] ?? $it['already_filed'] ?? 0);
                            $rc = (float)($it['quantity_received'] ?? 0);
                            $cn = (float)($it['quantity_cancelled'] ?? 0);
                            $bl = isset($it['quantity_balance']) ? (float)$it['quantity_balance'] : max(0, round($rq - ($rc + $cn), 4));

                            $totReq  += $rq;
                            $totPO   += $po;
                            $totRec  += $rc;
                            $totCanc += $cn;
                            $totBal  += $bl;
                        }
                    }
                }
            }
        }

        $matchingIdsForPOs = array_values(array_unique(array_filter($matchingIdsForPOs)));
        $matchingPOs = !empty($matchingIdsForPOs)
            ? DB::table('po_registers')->whereIn(DB::raw('CAST(indent_id AS CHAR)'), $matchingIdsForPOs)->get()
            : collect();
        $totalPOAmount = $matchingPOs->sum(fn($p) => (float)($p->po_amount ?? 0));

        $kpis = [
            'total_indents' => $totalIndents,
            'total_req'     => round($totReq, 4),
            'total_po'      => round($totPO, 4),
            'total_rec'     => round($totRec, 4),
            'total_canc'    => round($totCanc, 4),
            'total_bal'     => round($totBal, 4),
            'total_amount'  => $totalPOAmount,
        ];

        // Fetch paginated or all records
        if ($paginate) {
            $registers = $query->orderByDesc('indent_date')->paginate($perPage)->withQueryString();
            $dataset = $registers->items();
        } else {
            $registers = $query->orderByDesc('indent_date')->get();
            $dataset = $registers;
        }

        $unitMap = DB::table('units')->pluck('name', 'id')->toArray();
        $projectMap = DB::table('projects')->pluck('name', 'id')->toArray();

        // Eager-load POs for current dataset
        $currentIndentIds = [];
        foreach ($dataset as $row) {
            $currentIndentIds[] = (string)$row->indent_id;
            $currentIndentIds[] = (string)$row->id;
        }
        $currentIndentIds = array_values(array_unique(array_filter($currentIndentIds)));

        $posByIndent = !empty($currentIndentIds)
            ? DB::table('po_registers')
                ->whereIn(DB::raw('CAST(indent_id AS CHAR)'), $currentIndentIds)
                ->orderByDesc('po_date')
                ->get()
                ->groupBy(fn($p) => (string)$p->indent_id)
            : collect();

        // Format enriched records
        $records = collect($dataset)->map(function ($r) use ($unitMap, $projectMap, $posByIndent) {
            $items = [];
            $indReq = 0;
            $indPO = 0;
            $indRec = 0;
            $indCanc = 0;
            $indBal = 0;

            if (!empty($r->items_description)) {
                $decoded = is_string($r->items_description) ? json_decode($r->items_description, true) : $r->items_description;
                if (is_array($decoded)) {
                    foreach ($decoded as $it) {
                        if (!is_array($it)) continue;
                        $desc = (string)($it['description'] ?? '');
                        $u = (string)($it['unit'] ?? '');
                        if (is_numeric($u) && isset($unitMap[$u])) {
                            $u = $unitMap[$u];
                        }
                        $rq = (float)($it['quantity_required'] ?? 0);
                        $po = (float)($it['purchased_order'] ?? $it['already_filed'] ?? 0);
                        $rc = (float)($it['quantity_received'] ?? 0);
                        $cn = (float)($it['quantity_cancelled'] ?? 0);
                        $bl = isset($it['quantity_balance']) ? (float)$it['quantity_balance'] : max(0, round($rq - ($rc + $cn), 4));

                        $indReq  += $rq;
                        $indPO   += $po;
                        $indRec  += $rc;
                        $indCanc += $cn;
                        $indBal  += $bl;

                        $st = $it['status'] ?? 'Pending';
                        if (empty($st)) {
                            if ($cn >= $rq && $rq > 0) $st = 'Cancelled';
                            elseif ($rc >= $rq && $rq > 0) $st = 'Completed';
                            elseif ($rc > 0) $st = 'Partially Received';
                            elseif ($po > 0) $st = 'PO Created';
                            else $st = 'Pending';
                        }

                        $items[] = [
                            'description'        => $desc,
                            'unit'               => $u ?: '-',
                            'quantity_required'  => round($rq, 4),
                            'purchased_order'    => round($po, 4),
                            'quantity_received'  => round($rc, 4),
                            'quantity_cancelled' => round($cn, 4),
                            'quantity_balance'   => round($bl, 4),
                            'status'             => $st,
                        ];
                    }
                }
            }

            // Find linked POs
            $linkedPOs = $posByIndent->get((string)$r->indent_id, collect())
                ->merge($posByIndent->get((string)$r->id, collect()))
                ->unique('id')
                ->values();

            $poTotalAmount = $linkedPOs->sum(fn($p) => (float)($p->po_amount ?? 0));

            $status = match (strtolower((string) ($r->status ?? ''))) {
                'close', 'closed', 'completed' => 'Close',
                'cancel', 'cancelled' => 'Cancel',
                'partially received' => 'Partially Received',
                default => 'Pending',
            };

            $projName = $r->indent_project;
            if (is_numeric($projName) && isset($projectMap[$projName])) {
                $projName = $projectMap[$projName];
            } elseif (empty($projName) || $projName === '0') {
                $projName = '-';
            }

            return [
                'id'                => $r->id,
                'indent_id'         => (string)$r->indent_id,
                'indent_department' => $r->indent_department ?? '-',
                'department'        => $r->indent_department ?? '-',
                'indent_project'    => $projName,
                'project'           => $projName,
                'status'            => $status,
                'remarks'           => $r->remarks ?? '-',
                'indent_date'       => $r->indent_date ? Carbon::parse($r->indent_date)->format('d-m-Y') : '-',
                'raw_date'          => $r->indent_date,
                'items'             => $items,
                'items_count'       => count($items),
                'items_text'        => !empty($items) ? implode(', ', array_column($items, 'description')) : '-',
                'pos'               => $linkedPOs->all(),
                'po_count'          => $linkedPOs->count(),
                'po_amount'         => $poTotalAmount,
                'totals'            => [
                    'req'  => round($indReq, 4),
                    'po'   => round($indPO, 4),
                    'rec'  => round($indRec, 4),
                    'canc' => round($indCanc, 4),
                    'bal'  => round($indBal, 4),
                ],
                'action'            => [
                    'view'    => route('po-register.viewByIndent', ['indent_id' => $r->indent_id, 'department_id' => $r->indent_department ?? '']),
                    'edit'    => route('indent.edit', $r->id),
                    'file_po' => route('po-register.create', ['indent_id' => $r->indent_id, 'department_id' => $r->indent_department ?? '']),
                ],
            ];
        })->values()->all();

        $departments = \App\Models\Department::orderBy('name')->get();
        $projects = \App\Models\Project::orderBy('name')->get();
        $filterText = implode(' | ', $filterParts);

        return [$records, $kpis, $filterText, $registers, $departments, $projects];
    }

    public function viewAllIndent(Request $request)
    {
        $title = 'All Indents Report';
        $searchPlaceholder = 'Search indents, departments, projects…';
        $perPage = (int) $request->get('per_page', 15);
        if (!in_array($perPage, [10, 15, 25, 50, 100])) {
            $perPage = 15;
        }

        [$records, $kpis, $filterText, $registers, $departments, $projects] = $this->getEnrichedIndentData($request, true, $perPage);

        return view('pages.report.viewAllIndent.viewAllIndent', [
            'title'             => $title,
            'rows'              => $records,
            'kpis'              => $kpis,
            'searchPlaceholder' => $searchPlaceholder,
            'registers'         => $registers,
            'departments'       => $departments,
            'projects'          => $projects,
            'filterText'        => $filterText,
            'filterUrl'         => route('reports.indents.filter'),
            'rowKey'            => 'indent_id',
        ]);
    }

    /**
     * Comprehensive Export for All Indents Report (Excel, PDF, CSV).
     */
    public function exportIndents(Request $request)
    {
        $type = strtolower($request->get('type', 'excel'));
        [$records, $kpis, $filterText] = $this->getEnrichedIndentData($request, false);

        $timestamp = now()->format('Ymd_His');

        if ($type === 'pdf') {
            $pdf = Pdf::loadView('exports.all_indents_pdf', [
                'records'    => $records,
                'kpis'       => $kpis,
                'filterText' => $filterText,
            ])->setPaper('a4', 'landscape');

            return $pdf->download("All_Indents_Report_{$timestamp}.pdf");
        }

        if ($type === 'csv') {
            return Excel::download(
                new AllIndentsExport($records, $kpis, $filterText),
                "All_Indents_Report_{$timestamp}.csv",
                \Maatwebsite\Excel\Excel::CSV
            );
        }

        // Default: Excel XLSX
        return Excel::download(
            new AllIndentsExport($records, $kpis, $filterText),
            "All_Indents_Report_{$timestamp}.xlsx"
        );
    }

    public function exportExcel(Request $request)
    {
        return $this->exportIndents($request);
    }

    public function filterAllIndentAjax(Request $request)
    {
        [$records] = $this->getEnrichedIndentData($request, false);
        return response()->json(['rows' => $records]);
    }

    public function allIndentAndPOlist(Request $request)
    {
        $perPage = (int) $request->get('per_page', 15);

        // Base join: only POs that have indent_id + department_id and match an indent row
        $query = DB::table('po_registers as po')
            ->join('indent_registers as ir', function ($join) {
                $join->on('ir.indent_id', '=', DB::raw('CAST(po.indent_id AS CHAR)'));
            })
            ->leftJoin('departments as d', 'd.id', '=', 'po.department_id')
            ->leftJoin('projects as p', 'p.id', '=', 'ir.indent_project')
            ->whereNotNull('po.indent_id')
            ->whereNotNull('po.department_id')
            ->select([
                // PO
                'po.id                as po_id',
                'po.indent_id         as po_indent_id',
                'po.department_id     as department_id',
                'd.name               as department_name',
                'po.status            as po_status',
                'po.po_date',
                'po.party_name',
                'po.po_wo_no',
                'po.item_description as po_description',
                'po.po_amount',
                'po.expected_date as expected_date',
                'po.expected_days as expected_days',
                'po.invoice_date as invoice_date',
                'po.receiving_date as receiving_date',
                'po.store_indent_no as invoice_no',
                'po.remarks as remarks',
                'po.delay_in_days as invoice_expected_days',
                'po.created_at        as po_created_at',
                // Indent
                'ir.id                as indent_row_id',
                'ir.indent_id         as indent_indent_id',
                'ir.indent_date',
                'ir.indent_department',
                'ir.items_description as total_description',
                'ir.indent_project',
                'p.name               as project_name',
                'ir.status            as indent_status',
                'ir.remarks           as indent_remarks',
                'ir.created_at        as indent_created_at',
            ]);

        if ($request->filled('search')) {
            $search = trim($request->get('search'));
            SearchHelper::applyFuzzySearch($query, $search, [
                'po.indent_id',
                'd.name',
                'p.name',
                'po.party_name',
                'po.po_wo_no',
                'po.item_description',
                'ir.items_description',
                'po.remarks',
                'ir.remarks'
            ]);
        }

        if ($request->filled('department')) {
            $query->where('d.name', $request->get('department'));
        }

        if ($request->filled('project')) {
            $query->where('p.name', $request->get('project'));
        }

        if ($request->filled('status')) {
            $st = strtolower(trim($request->get('status')));
            if (in_array($st, ['pending', 'open'])) {
                $query->whereIn(DB::raw('LOWER(po.status)'), ['pending', 'open']);
            } elseif ($st === 'partially received') {
                $query->where(DB::raw('LOWER(po.status)'), 'partially received');
            } elseif (in_array($st, ['completed', 'close', 'closed'])) {
                $query->whereIn(DB::raw('LOWER(po.status)'), ['completed', 'close', 'closed']);
            } elseif (in_array($st, ['cancel', 'cancelled'])) {
                $query->whereIn(DB::raw('LOWER(po.status)'), ['cancel', 'cancelled']);
            } else {
                $query->where(DB::raw('LOWER(po.status)'), $st);
            }
        }

        if ($request->filled('date_from')) {
            $query->whereDate('po.po_date', '>=', $request->get('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('po.po_date', '<=', $request->get('date_to'));
        }

        $paginated = $query->orderByDesc('po.po_date')->paginate($perPage)->withQueryString();

        $departments = \App\Models\Department::orderBy('name')->get();
        $projects = \App\Models\Project::orderBy('name')->get();

        // Table columns for the reusable component
        $columns = [
            ['label' => 'Indent ID', 'key' => 'indent_id'],
            ['label' => 'Indent Date', 'key' => 'indent_date', 'type' => 'date'],
            ['label' => 'Department', 'key' => 'department'],
            ['label' => 'Project', 'key' => 'project'],
            ['label' => 'Indent Description', 'key' => 'total_description'],
            ['label' => 'Party Name', 'key' => 'party_name'],
            ['label' => 'PO Date', 'key' => 'po_date', 'type' => 'date'],
            ['label' => 'PO/WO No', 'key' => 'po_no'],
            ['label' => 'PO Description', 'key' => 'po_description'],
            ['label' => 'PO Amount', 'key' => 'po_amount'],
            ['label' => 'PO Status', 'key' => 'po_status', 'type' => 'status'],
            ['label' => 'Expected Days', 'key' => 'expected_days'],
            ['label' => 'Expected Date', 'key' => 'expected_date', 'type' => 'date'],
            ['label' => 'Invoice No.', 'key' => 'invoice_no'],
            ['label' => 'Invoice Date', 'key' => 'invoice_date', 'type' => 'date'],
            ['label' => 'Receiving Date', 'key' => 'receiving_date', 'type' => 'date'],
            ['label' => 'Delay in Days', 'key' => 'invoice_expected_days'],
            ['label' => 'Indent Remarks', 'key' => 'indent_remarks'],
            ['label' => 'PO Remarks', 'key' => 'remarks'],
        ];

        // Initial rows mapped to the columns' keys
        $rows = collect($paginated->items())->map(function ($r) {
            $fmtStatus = fn($s) => match (strtolower((string) $s)) {
                'close', 'closed', 'completed' => 'Close',
                'cancel', 'cancelled' => 'Cancel',
                'partially received' => 'Partially Received',
                default => 'Pending',
            };
            return [
                // rowKey will be po_id (unique)
                'po_id' => (string) $r->po_id,
                'indent_id' => (string) $r->po_indent_id,
                'department' => $r->department_name ?? $r->department_id ?? '-',
                'project' => $r->project_name ?? $r->indent_project ?? '-',
                'party_name' => $r->party_name ?? '-',
                'po_no' => $r->po_wo_no ?? '-',
                'total_description' => empty($r->total_description)
                    ? '-'
                    : collect(is_string($r->total_description) ? json_decode($r->total_description, true) : $r->total_description)
                        ->map(function ($i) {
                            $req = (float)($i['quantity_required'] ?? 0);
                            $po  = (float)($i['purchased_order'] ?? $i['already_filed'] ?? 0);
                            $rec = (float)($i['quantity_received'] ?? 0);
                            $canc = (float)($i['quantity_cancelled'] ?? 0);
                            $bal = isset($i['quantity_balance']) ? (float)$i['quantity_balance'] : round(max(0, $req - max($po, $rec + $canc)), 4);
                            $cancStr = $canc > 0 ? ", Canc:{$canc}" : '';
                            return sprintf(
                                '%s (%s) [Req:%s, PO:%s, Rcvd:%s%s, Bal:%s]',
                                $i['description'] ?? '-',
                                $i['unit'] ?? '-',
                                $req,
                                $po,
                                $rec,
                                $cancStr,
                                $bal
                            );
                        })
                        ->implode(' , '),
                'po_description' => empty($r->po_description)
                    ? '-'
                    : collect(is_string($r->po_description)
                            ? (json_decode($r->po_description, true) ?? [$r->po_description])
                            : $r->po_description)
                        ->map(fn($i) => is_array($i) ? ($i['description'] ?? null) : (string) $i)
                        ->filter()
                        ->implode(', '),
                'po_amount' => $r->po_amount !== null ? number_format((float) $r->po_amount, 2) : '-',
                'po_status' => $fmtStatus($r->po_status),
                'expected_days' => $r->expected_days,
                'indent_status' => $fmtStatus($r->indent_status),
                'indent_date' => $r->indent_date ? Carbon::parse($r->indent_date)->format('d-m-Y') : '-',
                'po_date' => $r->po_date ? Carbon::parse($r->po_date)->format('d-m-Y') : '-',
                'expected_date' => $r->expected_date ? Carbon::parse($r->expected_date)->format('d-m-Y') : '-',
                'invoice_date' => $r->invoice_date ? Carbon::parse($r->invoice_date)->format('d-m-Y') : '-',
                'receiving_date' => $r->receiving_date ? Carbon::parse($r->receiving_date)->format('d-m-Y') : '-',
                'invoice_no' => $r->invoice_no ?? '-',
                'invoice_expected_days' => $r->invoice_expected_days ?? '-',
                'remarks' => $r->remarks ?? '-',
                'indent_remarks' => $r->indent_remarks ?? '-',
            ];
        })->values()->all();

        return view('pages.report.allIndentAndPOlist.allIndentAndPOlist', [
            'title' => 'All Indents & POs',
            'columns' => $columns,
            'rows' => $rows,
            'searchPlaceholder' => 'Search ID, department, party, PO...',
            'customButton' => null,
            'paginated' => $paginated,
            'departments' => $departments,
            'projects' => $projects,
            'filterUrl' => route('reports.indentspos.filter'),
            'rowKey' => 'po_id',
        ]);
    }

    /**
     * AJAX endpoint: search + date range across Indents & POs (no pagination).
     * GET params: q, from (YYYY-MM-DD), to (YYYY-MM-DD)
     */
    public function filterAllIndentPOAjax(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $from = $request->query('from');
        $to = $request->query('to');

        $query = DB::table('po_registers as po')
            ->join('indent_registers as ir', function ($join) {
                // ir.indent_id (varchar) == po.indent_id (bigint)  -> cast PO to CHAR for join
                $join->on('ir.indent_id', '=', DB::raw('CAST(po.indent_id AS CHAR)'));
            })
            ->whereNotNull('po.indent_id')
            ->whereNotNull('po.department_id');

        // Text search across common fields
        if ($q !== '') {
            SearchHelper::applyFuzzySearch($query, $q, [
                'po.indent_id',
                'po.department_id',
                'po.party_name',
                'po.po_wo_no',
                'po.status',
                'po.item_description',
                'po.store_indent_no',
                'po.remarks',
                'ir.indent_project',
                'ir.items_description',
                'ir.indent_department',
                'ir.status',
                'ir.remarks'
            ]);
        }

        // Date range: match either PO Date OR Indent Date within range
        if ($from && $to) {
            $query->where(function ($w) use ($from, $to) {
                $w
                    ->whereBetween('po.po_date', [$from, $to])
                    ->orWhereBetween('ir.indent_date', [$from, $to]);
            });
        } elseif ($from) {
            $query->where(function ($w) use ($from) {
                $w
                    ->whereDate('po.po_date', '>=', $from)
                    ->orWhereDate('ir.indent_date', '>=', $from);
            });
        } elseif ($to) {
            $query->where(function ($w) use ($to) {
                $w
                    ->whereDate('po.po_date', '<=', $to)
                    ->orWhereDate('ir.indent_date', '<=', $to);
            });
        }

        $rows = $query
            ->orderByDesc('po.po_date')
            ->select([
                // PO
                'po.id                as po_id',
                'po.indent_id         as po_indent_id',
                'po.department_id     as department_id',
                'po.status            as po_status',
                'po.po_date',
                'po.party_name',
                'po.po_wo_no',
                'po.item_description  as po_description',
                'po.po_amount',
                'po.expected_date     as expected_date',
                'po.expected_days     as expected_days',
                'po.invoice_date      as invoice_date',
                'po.receiving_date    as receiving_date',
                'po.store_indent_no   as invoice_no',
                'po.delay_in_days     as invoice_expected_days',
                'po.remarks as remarks',
                // Indent
                'ir.id                as indent_row_id',
                'ir.indent_id         as indent_indent_id',
                'ir.indent_date',
                'ir.indent_department',
                'ir.items_description as total_description',
                'ir.indent_project',
                'ir.status            as indent_status',
                'ir.remarks           as indent_remarks',
            ])
            ->get()
            ->map(function ($r) {
                $fmtStatus = fn($s) => match (strtolower((string) $s)) {
                    'close' => 'Close',
                    'cancel' => 'Cancel',
                    'pending' => 'Pending',
                    default => 'Pending',
                };

                // Indent items (pretty JSON → lines)
                $totalDescription = '-';
                if (!empty($r->total_description)) {
                    $items = is_string($r->total_description)
                        ? (json_decode($r->total_description, true) ?? $r->total_description)
                        : $r->total_description;

                    if (is_array($items)) {
                        $totalDescription = collect($items)
                            ->map(function ($i) {
                                $req = (float)($i['quantity_required'] ?? 0);
                                $po  = (float)($i['purchased_order'] ?? $i['already_filed'] ?? 0);
                                $rec = (float)($i['quantity_received'] ?? 0);
                                $canc = (float)($i['quantity_cancelled'] ?? 0);
                                $bal = isset($i['quantity_balance']) ? (float)$i['quantity_balance'] : round(max(0, $req - max($po, $rec + $canc)), 4);
                                $cancStr = $canc > 0 ? ", Canc:{$canc}" : '';
                                return sprintf(
                                    '%s (%s) [Req:%s, PO:%s, Rcvd:%s%s, Bal:%s]',
                                    $i['description'] ?? '-',
                                    $i['unit'] ?? '-',
                                    $req,
                                    $po,
                                    $rec,
                                    $cancStr,
                                    $bal
                                );
                            })
                            ->implode(' , ');
                    } else {
                        $totalDescription = (string) $items;
                    }
                }

                // PO description (accept JSON array or plain string)
                $poDescription = '-';
                if (!empty($r->po_description)) {
                    $po = is_string($r->po_description)
                        ? (json_decode($r->po_description, true) ?? $r->po_description)
                        : $r->po_description;

                    if (is_array($po)) {
                        $poDescription = collect($po)
                            ->map(fn($i) => is_array($i) ? ($i['description'] ?? null) : (string) $i)
                            ->filter()
                            ->implode(' , ');
                    } else {
                        $poDescription = (string) $po;
                    }
                }

                return [
                    // keep keys identical to non-AJAX table
                    'po_id' => (string) $r->po_id,
                    'indent_id' => (string) $r->po_indent_id,  // same as indent_indent_id
                    'department' => $r->department_id ?? '-',
                    'project' => $r->indent_project ?? '-',
                    'total_description' => $totalDescription,  // with <br>
                    'party_name' => $r->party_name ?? '-',
                    'po_date' => $r->po_date ? Carbon::parse($r->po_date)->format('d-m-Y') : '-',
                    'po_no' => $r->po_wo_no ?? '-',
                    'po_description' => $poDescription,  // with <br>
                    'po_amount' => $r->po_amount !== null ? number_format((float) $r->po_amount, 2) : '-',
                    'po_status' => $fmtStatus($r->po_status),
                    'expected_days' => $r->expected_days ?? '-',
                    'expected_date' => $r->expected_date ? Carbon::parse($r->expected_date)->format('d-m-Y') : '-',
                    'invoice_no' => $r->invoice_no ?? '-',
                    'invoice_date' => $r->invoice_date ? Carbon::parse($r->invoice_date)->format('d-m-Y') : '-',
                    'receiving_date' => $r->receiving_date ? Carbon::parse($r->receiving_date)->format('d-m-Y') : '-',
                    'invoice_expected_days' => $r->invoice_expected_days ?? '-',
                    'indent_status' => $fmtStatus($r->indent_status),
                    'indent_date' => $r->indent_date ? Carbon::parse($r->indent_date)->format('d-m-Y') : '-',
                    'remarks' => $r->remarks ?? '-',
                    'indent_remarks' => $r->indent_remarks ?? '-',
                ];
            })
            ->values();

        return response()->json(['rows' => $rows]);
    }
}
