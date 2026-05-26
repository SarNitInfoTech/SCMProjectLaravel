<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Support\Facades\DB;

class PORegisterExport implements FromView
{
    protected $indent_id;
    protected $department_id;

    public function __construct($indent_id = null, $department_id = null)
    {
        $this->indent_id = $indent_id;
        $this->department_id = $department_id;
    }

    public function view(): View
    {
        $query = DB::table('po_registers')
            ->leftJoin('indent_registers', function ($join) {
                $join->on(DB::raw('CAST(indent_registers.indent_id AS CHAR)'), '=', DB::raw('CAST(po_registers.indent_id AS CHAR)'));
            })
            ->leftJoin('departments', 'departments.id', '=', 'po_registers.department_id')
            ->leftJoin('projects', 'projects.id', '=', 'indent_registers.indent_project')
            ->leftJoin('units', 'units.id', '=', 'indent_registers.unit')
            ->select(
                // PO Register fields
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

                // Indent Register fields
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
            );

        if (!empty($this->indent_id)) {
            $query->where('po_registers.indent_id', $this->indent_id);
        }

        if (!empty($this->department_id)) {
            $query->where('po_registers.department_id', $this->department_id);
        }

        $allPos = $query->orderByDesc('po_registers.created_at')->get();

        // Decode JSON fields
        foreach ($allPos as $po) {
            $po->decoded_po_items = json_decode($po->po_item_description, true) ?? [];
            $po->decoded_indent_items = json_decode($po->indent_item_description, true) ?? [];
        }

        return view('exports.po_register', ['allPos' => $allPos]);
    }
}
