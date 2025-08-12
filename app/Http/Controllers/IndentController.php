<?php
namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\IndentRegister;
use App\Models\IndentTicket;
use App\Models\Notification;
use App\Models\Item;
use App\Models\Project;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class IndentController extends Controller
{
  public function index()
{
    $title = 'Indent Register List';

    $columns = [
        ['key' => 'indent_id', 'label' => 'Indent ID'],
        ['key' => 'department_name', 'label' => 'Department'],
        ['key' => 'project', 'label' => 'Project'],
        ['key' => 'item_description', 'label' => 'Description'], // Will hold comma-separated items
        ['key' => 'status', 'label' => 'Status', 'type' => 'status'],
        ['key' => 'date', 'label' => 'Created Date'],
        ['key' => 'action', 'label' => 'Action', 'type' => 'action'],
    ];

    // Join with departments for department name
    $registers = DB::table('indent_registers')
        ->join('departments', 'departments.name', '=', 'indent_registers.indent_department')
        ->select(
            'indent_registers.id',
            'indent_registers.indent_id',
            'departments.name as department_name',
            'indent_registers.items_description',
            'indent_registers.indent_project as project',
            'indent_registers.indent_date as date',
            'indent_registers.indent_department as department_id',
            'indent_registers.status',
            'indent_registers.created_at',
            'indent_registers.updated_at'
        )
        ->orderByDesc('indent_registers.created_at')
        ->paginate(10);

    // Format rows
    $rows = $registers->map(function ($reg) {
        $items = json_decode($reg->items_description, true) ?? [];
        $itemDescriptions = collect($items)->pluck('description')->filter()->implode(', '); // ✅

        return [
            'indent_id' => $reg->indent_id,
            'department_name' => $reg->department_name,
            'department_id' => $reg->department_id,
            'project' => $reg->project,
            'date' => $reg->date,
            'item_description' => $itemDescriptions ?: '-',
            'status' => ucfirst($reg->status ?? 'Pending'),

            'action' => (function () use ($reg) {
    $status = strtolower($reg->status ?? 'pending');
    $actions = [];

    $baseParams = [
        'indent_id'     => $reg->indent_id,
        'department_id' => $reg->department_id,
    ];

    if ($status === 'pending') {
        // usual pending actions
        $actions['edit']    = route('indent-register.edit', $reg->id);
        $actions['file_po'] = route('po-register.create', $baseParams);

        // show Cancel and Close
        $actions['cancel'] = [
            'route'  => route('po-register.statusCancel'),
            'params' => $baseParams,
        ];
        $actions['close'] = [
            'route'  => route('po-register.statusClose'),
            'params' => $baseParams,
        ];
    } elseif ($status === 'close') {
        // when closed, only allow reverting to Pending
        $actions['pending'] = [
            'route'  => route('po-register.statusPending'),
            'params' => $baseParams,
        ];
        $actions['cancel'] = [
            'route'  => route('po-register.statusCancel'),
            'params' => $baseParams,
        ];
    }elseif ($status === 'cancel') {
        // when closed, only allow reverting to Pending
        $actions['close'] = [
            'route'  => route('po-register.statusClose'),
            'params' => $baseParams,
        ];
        $actions['pending'] = [
            'route'  => route('po-register.statusPending'),
            'params' => $baseParams,
        ];
    } else {
        // status is "cancel" (or anything else) → no actions
        // If you want to allow reopen from cancel, uncomment below:
        /*
        $actions['pending'] = [
            'route'  => route('po-register.statusPending'),
            'params' => $baseParams,
        ];
        */
    }

    return $actions;
})(),
        ];
    });

    $searchPlaceholder = 'Search indent records...';
    $redirectUrl = route('indent.create');

    $customButton = <<<HTML
        <a href="{$redirectUrl}" class="ti-btn ti-btn-primary-full">
            <i class="bi bi-plus-lg"></i>
            Add New Indent
        </a>
    HTML;

    return view('pages.indent.indentForm.viewIndentForm.viewIndentForm', [
        'title' => $title,
        'columns' => $columns,
        'rows' => $rows,
        'searchPlaceholder' => $searchPlaceholder,
        'customButton' => $customButton,
        'pagination' => $registers,
    ]);
}


    // public function create()
    // {
    //     $title = 'Draft List';
    //     $departments = Department::all();
    //     $items = Item::all();


    //     $rows = DB::table('indent_tickets')
    //         ->leftJoin('indent_registers', function ($join) {
    //             $join
    //                 ->on('indent_tickets.indent_id', '=', 'indent_registers.indent_id')
    //                 ->whereColumn('indent_tickets.department_id', '=', 'indent_registers.indent_department');
    //         })
    //         ->join('departments', 'departments.id', '=', 'indent_tickets.department_id')
    //         ->whereNull('indent_registers.id')  // Only unmatched entries
    //         ->select(
    //             'indent_tickets.indent_id',
    //             'indent_tickets.department_id',
    //             'departments.name as department_name'
    //         )
    //         ->get();

    //     $columns = [
    //         ['key' => 'indent_id', 'label' => 'Indent ID'],
    //         ['key' => 'department_name', 'label' => 'Department Name'],
    //         ['key' => 'action', 'label' => 'Action', 'type' => 'action'],
    //     ];

    //     $rows = $rows->map(function ($row) {
    //         $row = (array) $row;
    //         $row['action'] = route('indent.create.form', [
    //             'indent_id' => $row['indent_id'],
    //             'department_id' => $row['department_id']
    //         ]);
    //         return $row;
    //     });

    //     return view('pages.indent.generateIndent.generateIndent', compact('departments','items', 'title', 'columns', 'rows'));
    // }
    
public function create()
{
    $title = 'Draft List';
    $departments = Department::all();
    $items = Item::all();

    // it = indent_tickets, d = departments, ir = indent_registers
    $rows = DB::table('indent_tickets as it')
        // 1) Get department name from departments.id
        ->join('departments as d', 'd.id', '=', 'it.department_id')
        // 2) Left join to indent_registers by indent_id + department_name (stored as indent_department)
        ->leftJoin('indent_registers as ir', function ($join) {
            $join->on('ir.indent_id', '=', 'it.indent_id')
                 ->on('ir.indent_department', '=', 'd.name'); // compare to department NAME
        })
        // 3) Keep only tickets not yet present in indent_registers
        ->whereNull('ir.id')
        ->select([
            'it.indent_id',
            'it.department_id',           // numeric id
            'd.name as department_name',  // resolved name
        ])
        ->get();

    $columns = [
        ['key' => 'indent_id', 'label' => 'Indent ID'],
        ['key' => 'department_name', 'label' => 'Department Name'],
        ['key' => 'action', 'label' => 'Action', 'type' => 'action'],
    ];

    // Build action URL (passes both id and name if you want to use either)
    $rows = $rows->map(function ($row) {
        $row = (array) $row;
        $row['action'] = route('indent.create.form', [
            'indent_id'       => $row['indent_id'],
            'department_id'   => $row['department_id'],   // numeric
            'department_name' => $row['department_name'], // optional convenience
        ]);
        return $row;
    });

    return view('pages.indent.generateIndent.generateIndent',
        compact('departments', 'items', 'title', 'columns', 'rows'));
}

    public function store(Request $request)
    {
        // ✅ Validate required fields and unique indent_id per department
        $request->validate([
            'department_id' => 'required|exists:departments,id',
            'indent_id' => [
                'required',
                Rule::unique('indent_registers', 'indent_id')
                    ->where(fn($query) => $query->where('indent_department', $request->department_id)),
            ],
        ]);

        // ✅ Fetch department safely
        $department = Department::findOrFail($request->department_id);

        // ✅ Check for already registered indent (defensive redundancy)
        $alreadyRegistered = IndentTicket::where('indent_id', $request->indent_id)
            ->where('department_id', $request->department_id)
            ->exists();

        if ($alreadyRegistered) {
            return redirect()->route('indent.create')->with('warning', "Indent ID {$request->indent_id} is already registered for this department.");
        }

        // ✅ Create Indent Ticket (prefill use-case)
        $indentTicket = IndentTicket::create([
            'indent_id' => $request->indent_id,
            'department_id' => $department->id,
        ]);

        // ✅ Create Notification for new indent
        Notification::create([
            'title' => 'New Indent Created',
            'link' => route('indent.create.form'),
            'icon' => 'la la-file-alt',
            'bg_color' => 'bg-primary',
            'is_read' => false,
        ]);

        // ✅ Redirect to Indent Register form with flash data
        return redirect()
            ->route('indent.create.form')
            ->with([
                'indent_id' => $indentTicket->indent_id,
                'department_id' => $department->id,
                'success' => 'Indent generated successfully!',
            ]);
    }

    public function createForm(Request $request)
    {
        $departments = Department::all();
        $projects = Project::all();
        $units = Unit::all();
        $items = Item::all();

        $departmentId = $request->get('department_id', session('department_id'));
        $indentId = $request->get('indent_id', session('indent_id'));
        $departmentName = null;

        if ($departmentId) {
            $department = Department::find($departmentId);
            $departmentName = $department ? $department->name : null;
        }

        return view('pages.indent.indentForm.addIndentForm.addIndentForm', [
            'departments' => $departments,
            'projects' => $projects,
            'units' => $units,
            'items' => $items,
            'indent_id' => $indentId,
            'department_id' => $departmentId,
            'department_name' => $departmentName,
            'success' => session('success'),
        ]);
    }

    public function editForm($id)
    {
        $indent = IndentRegister::findOrFail($id);

        $departments = Department::all();
        $projects = Project::all();
        $units = Unit::all();
        $items = Item::all();


        $departmentId = $indent->indent_department;
        $department = Department::find($departmentId);
        $departmentName = $department ? $department->name : null;

        return view('pages.indent.indentForm.editIndentForm.editIndentForm', [
            'indent' => $indent,
            'departments' => $departments,
            'projects' => $projects,
            'units' => $units,
            'items' => $items,
            'department_id' => $departmentId,
            'department_name' => $departmentName,
            'success' => session('success'),
        ]);
    }

    public function generateToken(Request $request)
    {
        $request->validate([
            'department' => 'required|string'
        ]);

        $department = $request->department;

        // Find latest indent for the selected department
        $latest = IndentTicket::where('department_id', $department)
            ->orderByDesc('id')
            ->first();

        // Extract numeric part and increment
        $nextNumber = $latest
            ? ((int) filter_var($latest->indent_id, FILTER_SANITIZE_NUMBER_INT)) + 1
            : 1;

        // Format: CSE-1, IT-2, etc.
        $indentId = $nextNumber;

        return response()->json(['indent_id' => $indentId]);
    }

  public function registerStore(Request $request)
{
    // Check for duplicates
    $alreadyRegistered = IndentRegister::where('indent_id', $request->indent_id)
        ->where('indent_department', $request->indent_department)
        ->exists();

    if ($alreadyRegistered) {
        return redirect()->route('indent.create')
            ->with('warning', "Indent ID {$request->indent_id} is already registered for this department.");
    }

    // Build items JSON array
    $items = [];

    foreach ($request->items as $item) {
        $items[] = [
            'description'       => $item['description'],
            'unit'              => $item['unit'],
            'quantity_required' => (int) $item['required'],
            'quantity_received' => (int) $item['received'],
            'quantity_balance'  => (int) $item['balance'],
        ];
    }

    // Save one row with JSON column
    IndentRegister::create([
        'indent_id'          => $request->indent_id,
        'indent_date'        => $request->indent_date,
        'indent_department'  => $request->indent_department,
        'indent_project'     => $request->indent_project,
        'items_description'  => json_encode($items), // store as JSON
        'status'             => 'Pending',
    ]);

    // Notification
    Notification::create([
        'title'     => "Indent Registered - {$request->indent_id}",
        'link'      => route('indent.index'),
        'icon'      => 'la la-clipboard-list',
        'bg_color'  => 'bg-success',
        'is_read'   => false,
    ]);

    return redirect()->route('indent.index')->with('success', 'Indent registered successfully!');
}


   public function indentRegisterUpdate(Request $request, $id)
{
    // Check if another indent (not the same ID) has same indent_id and department
    $alreadyRegistered = IndentRegister::where('indent_id', $request->indent_id)
        ->where('indent_department', $request->indent_department)
        ->where('id', '!=', $id)
        ->exists();

    if ($alreadyRegistered) {
        return redirect()->back()->with('warning', "Indent ID {$request->indent_id} is already registered for this department.");
    }

    // Prepare the item data from the request
    $items = $request->input('items', []);
    $processedItems = [];

    foreach ($items as $item) {
        $processedItems[] = [
            'description'        => $item['description'] ?? '',
            'unit'               => $item['unit'] ?? '',
            'quantity_required'  => (int)($item['required'] ?? 0),
            'quantity_received'  => (int)($item['received'] ?? 0),
            'quantity_balance'   => (int)($item['balance'] ?? 0),
        ];
    }

    // Proceed with update
    $indent = IndentRegister::findOrFail($id);
    $indent->indent_date        = $request->indent_date;
    $indent->indent_project     = $request->indent_project;
    $indent->items_description  = json_encode($processedItems); // ✅ Save all items as JSON
    $indent->save();

    return redirect()->route('indent.index')->with('success', 'Indent updated successfully!');
}


    
}
