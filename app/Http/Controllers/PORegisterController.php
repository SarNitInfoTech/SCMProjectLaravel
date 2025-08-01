<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Department;
use App\Models\DepartmentHead;
use App\Models\PORegister;
use App\Enums\POStatus;
use App\Enums\Notification;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\PORegisterExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\PurchaseOrder;




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
        ['key' => 'status', 'label' => 'Status'],
        ['key' => 'created_at', 'label' => 'Created At'],
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
        return [
            'po_date' => $po->po_date,
            'indent_id' => $po->indent_id,
            'department_name' => $po->department_name ?? '-',
            'party_name' => $po->party_name,
            'po_amount' => number_format($po->po_amount, 2),
            'status' => $po->status,
            'created_at' => Carbon::parse($po->created_at)->format('Y-m-d H:i'),
            'action' => [
                'edit' => route('po-register.edit', $po->id),
                'viewPage' => route('po-register.viewByIndent', [
    'indent_id' => $po->indent_id,
    'department_id' => $po->department_id
]),

            ],
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
    $indent_id = $request->get('indent_id');
    $department_id = $request->get('department_id');
    $departmentHeads = DepartmentHead::where('department_id', $department_id)->get();
    $statusList = POStatus::values();
    


    // You can also fetch department name from DB if needed
    $department_name = Department::find($department_id)?->name ?? '';

    return view('pages.indent.indentPOForm.addIndentPOForm.addIndentPOForm', compact('indent_id','departmentHeads', 'department_id', 'department_name','statusList'));
}

    /**
     * Store a newly created resource in storage.
     */
   
public function store(Request $request)
{
    $validated = $request->validate([
        'indent_id'        => 'required|integer|exists:indent_registers,indent_id',
        'department_id'    => 'required|integer|exists:departments,id',
        'po_date'          => 'required|date',
        'status'           => 'required|in:' . implode(',', POStatus::values()),
        'party_name'       => 'required|string|max:255',
        'po_wo_no'         => 'required|string|max:100',
        'po_amount'        => 'required|numeric|min:0',
        'debit_head'       => 'nullable|string|max:255',
        'item_description' => 'required|string',
        'expected_days'    => 'nullable|string|max:255',
        'expected_date'    => 'nullable|date',
        'invoice_date'     => 'nullable|date',
        'receiving_date'   => 'nullable|string|max:100',
        'delay_in_days'    => 'nullable|integer',
        'remarks'          => 'nullable|string',
        'store_indent_no'  => 'nullable|string|max:100',
        'invoice'          => 'nullable|string|max:100',
    ]);

    // ✅ Store PO data
    $po = PORegister::create($validated);

    // ✅ Generate Notification
    Notification::create([
        'title' => "New PO Registered: {$po->po_wo_no}",
        'link' => route('po-register.index'),
        'icon' => 'la la-file-invoice',
        'bg_color' => 'bg-primary',
        'is_read' => false,
    ]);

    return redirect()->route('po-register.index')->with('success', 'PO Registered successfully.');
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

    $statusList = ['Pending', 'Close', 'Cancel']; // Or fetch from config/model

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
        ->leftJoin('projects', 'projects.id', '=', 'indent_registers.indent_project') // JOIN PROJECT TABLE
        ->select(
            'po_registers.*',
            'departments.name as department_name',
            'indent_registers.indent_id as indent_ticket_no',
            'indent_registers.item_description as indent_item',
            'indent_registers.unit',
            'projects.name as project_name', // PROJECT NAME from project table
            'indent_registers.quantity_required',
            'indent_registers.quantity_received',
            'indent_registers.quantity_balance'
        )
        ->where('po_registers.indent_id', $indent_id)
        ->where('po_registers.department_id', $department_id)
        ->orderByDesc('po_registers.created_at')
        ->get();

    $po = $allPos->first(); // Used for summary

    return view('pages.indent.indentPOForm.viewDetailIndentPOForm.viewDetailIndentPOForm', compact('title', 'po', 'allPos', 'indent_id', 'department_id'));
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
        ->leftJoin('units', 'units.id', '=', 'indent_registers.unit') // if unit is ID
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
}
