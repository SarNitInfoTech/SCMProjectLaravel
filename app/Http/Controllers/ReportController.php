<?php

namespace App\Http\Controllers;

use App\Exports\PORegisterExport;
use App\Models\IndentRegister;
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

        if ($request->filled('department_id')) {
            $query->where('po_registers.department_id', $request->department_id);
        }

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q
                    ->where('po_registers.party_name', 'like', "%{$search}%")
                    ->orWhere('departments.name', 'like', "%{$search}%")
                    ->orWhere('po_registers.po_wo_no', 'like', "%{$search}%")
                    ->orWhere('projects.name', 'like', "%{$search}%");
            });
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

        // Fetch paginated results
        $reports = $query->orderByDesc('po_registers.created_at')->paginate(15)->withQueryString();

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

        return view('pages.report.viewReport.viewReport', compact('reports', 'columns'));
    }

    public function viewAllIndent()
    {
        $title = 'All Indents';
        $searchPlaceholder = 'Search indents…';

        $pagination = $registers = IndentRegister::orderByDesc('indent_date')->paginate(15);

        $columns = [
            ['label' => 'Indent ID', 'key' => 'indent_id'],
            ['label' => 'Department', 'key' => 'indent_department'],
            ['label' => 'Project', 'key' => 'indent_project'],
            ['label' => 'Items', 'key' => 'items_text'],
            ['key' => 'status', 'label' => 'Status', 'type' => 'status'],
            ['label' => 'Indent Date', 'key' => 'indent_date', 'type' => 'date'],
        ];

        $rows = $registers->getCollection()->map(function ($r) {
            $items = [];
            if (!empty($r->items_description) && is_string($r->items_description)) {
                $decoded = json_decode($r->items_description, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    $items = collect($decoded)->map(function ($it) {
                        return is_array($it) ? (string) ($it['description'] ?? '') : (string) $it;
                    })->filter()->values()->all();
                }
            }
            $status = match (strtolower((string) ($r->status ?? ''))) {
                'close' => 'Close',
                'cancel' => 'Cancel',
                'pending' => 'Pending',
                default => 'Pending',
            };
            return [
                'indent_id' => $r->indent_id,
                'indent_department' => $r->indent_department,
                'indent_project' => $r->indent_project ?? '-',
                'items_text' => $items ? implode(', ', $items) : '-',
                'status' => $status,
                'indent_date' => $r->indent_date ? \Carbon\Carbon::parse($r->indent_date)->format('d-m-Y') : '-',
            ];
        })->values()->all();

        return view('pages.report.viewAllIndent.viewAllIndent', [
            'title' => $title,
            'columns' => $columns,
            'rows' => $rows,
            'searchPlaceholder' => $searchPlaceholder,
            'customButton' => null,
            'pagination' => $pagination,
            // AJAX endpoint you already created earlier
            'filterUrl' => route('reports.indents.filter'),
            // Optionally specify a unique row key (defaults to first column key)
            'rowKey' => 'indent_id',
        ]);
    }

    public function filterAllIndentAjax(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $from = $request->query('from');  // 'YYYY-MM-DD'
        $to = $request->query('to');  // 'YYYY-MM-DD'

        $query = IndentRegister::query();

        // Date range (no pagination)
        if ($from && $to) {
            $query->whereBetween('indent_date', [$from, $to]);
        } elseif ($from) {
            $query->whereDate('indent_date', '>=', $from);
        } elseif ($to) {
            $query->whereDate('indent_date', '<=', $to);
        }

        // Text search across common fields
        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $like = '%' . $q . '%';
                $sub
                    ->where('indent_id', 'like', $like)
                    ->orWhere('indent_department', 'like', $like)
                    ->orWhere('indent_project', 'like', $like)
                    ->orWhere('status', 'like', $like)
                    ->orWhere('items_description', 'like', $like);
            });
        }

        $registers = $query->orderByDesc('indent_date')->get();

        // Build rows in the SAME SHAPE your component expects
        $rows = $registers->map(function ($r) {
            // Parse items_description -> list of descriptions
            $items = [];
            if (!empty($r->items_description) && is_string($r->items_description)) {
                $decoded = json_decode($r->items_description, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    $items = collect($decoded)->map(function ($it) {
                        if (is_array($it))
                            return (string) ($it['description'] ?? '');
                        if (is_string($it))
                            return $it;
                        return '';
                    })->filter()->values()->all();
                }
            }

            // Normalize status to title case
            $status = match (strtolower((string) ($r->status ?? ''))) {
                'close' => 'Close',
                'cancel' => 'Cancel',
                'pending' => 'Pending',
                default => 'Pending',
            };

            return [
                'indent_id' => $r->indent_id,
                'indent_department' => $r->indent_department,
                'indent_project' => $r->indent_project ?? '-',
                'items_text' => $items ? implode(', ', $items) : '-',
                'status' => $status,
                'indent_date' => $r->indent_date
                    ? Carbon::parse($r->indent_date)->format('d-m-Y')
                    : '-',
            ];
        })->values();

        return response()->json([
            'rows' => $rows,
        ]);
    }

    public function allIndentAndPOlist(Request $request)
    {
        // Base join: only POs that have indent_id + department_id and match an indent row
        $joined = DB::table('po_registers as po')
            ->join('indent_registers as ir', function ($join) {
                // ir.indent_id (varchar) == po.indent_id (bigint)  -> cast PO to CHAR for join
                $join->on('ir.indent_id', '=', DB::raw('CAST(po.indent_id AS CHAR)'));
            })
            ->whereNotNull('po.indent_id')
            ->whereNotNull('po.department_id')
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
                'ir.status            as indent_status',
                'ir.created_at        as indent_created_at',
            ])
            ->limit(100)  // optional: cap initial payload; AJAX will fetch all matches
            ->get();

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
            ['label' => 'Remarks', 'key' => 'remarks'],
        ];

        // Initial rows mapped to the columns' keys
        $rows = $joined->map(function ($r) {
            $fmtStatus = fn($s) => match (strtolower((string) $s)) {
                'close' => 'Close',
                'cancel' => 'Cancel',
                'pending' => 'Pending',
                default => 'Pending',
            };
            return [
                // rowKey will be po_id (unique)
                'po_id' => (string) $r->po_id,
                'indent_id' => (string) $r->po_indent_id,  // same as $r->indent_indent_id
                'department' => $r->department_id ?? '-',
                'project' => $r->indent_project ?? '-',
                'party_name' => $r->party_name ?? '-',
                'po_no' => $r->po_wo_no ?? '-',
                'total_description' => empty($r->total_description)
                    ? '-'
                    : collect(is_string($r->total_description) ? json_decode($r->total_description, true) : $r->total_description)
                        ->map(fn($i) => sprintf(
                            '%s (%s) [Req:%s, Rcvd:%s, Bal:%s]',
                            $i['description'] ?? '-',
                            $i['unit'] ?? '-',
                            $i['quantity_required'] ?? 0,
                            $i['quantity_received'] ?? 0,
                            $i['quantity_balance'] ?? 0
                        ))
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
            ];
        })->values()->all();

        return view('pages.report.allIndentAndPOlist.allIndentAndPOlist', [
            'title' => 'All Indents & POs',
            'columns' => $columns,
            'rows' => $rows,
            'searchPlaceholder' => 'Search indents / POs…',
            'customButton' => null,
            'pagination' => null,  // not used here
            'filterUrl' => route('reports.indentspos.filter'),
            'rowKey' => 'po_id',  // unique per row
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
            $like = "%{$q}%";
            $query->where(function ($w) use ($like) {
                $w
                    ->where('po.indent_id', 'like', $like)
                    ->orWhere('po.department_id', 'like', $like)
                    ->orWhere('po.party_name', 'like', $like)
                    ->orWhere('po.po_wo_no', 'like', $like)
                    ->orWhere('po.status', 'like', $like)
                    ->orWhere('po.item_description', 'like', $like)
                    ->orWhere('ir.indent_project', 'like', $like)
                    ->orWhere('ir.items_description', 'like', $like)
                    ->orWhere('ir.status', 'like', $like);
            });
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
                            ->map(fn($i) => sprintf(
                                '%s (%s) [Req:%s, Rcvd:%s, Bal:%s]',
                                $i['description'] ?? '-',
                                $i['unit'] ?? '-',
                                $i['quantity_required'] ?? 0,
                                $i['quantity_received'] ?? 0,
                                $i['quantity_balance'] ?? 0
                            ))
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
                ];
            })
            ->values();

        return response()->json(['rows' => $rows]);
    }
}
