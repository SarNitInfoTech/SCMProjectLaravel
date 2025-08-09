<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Exports\PORegisterExport;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function viewReport(Request $request)
    {
        $query = DB::table('po_registers')
            ->leftJoin('indent_registers', 'indent_registers.id', '=', 'po_registers.indent_id')
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
                'indent_registers.item_description as indent_item_description',
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
                $q->where('po_registers.party_name', 'like', "%{$search}%")
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
    public function exportExcel(Request $request)
{
    return Excel::download(
        new PORegisterExport($request->indent_id, $request->department_id),
        'po-register-report.xlsx'
    );
}
}

