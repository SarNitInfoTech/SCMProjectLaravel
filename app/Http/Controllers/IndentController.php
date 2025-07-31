<?php namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\IndentTicket;
use App\Models\IndentRegister;
use App\Models\Project;
use App\Models\Unit;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

use Illuminate\Http\Request;

class IndentController extends Controller
{
   public function index()
{
    $title = 'Indent Register List';

    $columns = [
        ['key' => 'indent_id', 'label' => 'Indent ID'],
        ['key' => 'department_name', 'label' => 'Department'],
        ['key' => 'item_description', 'label' => 'Desciption'],
        ['key' => 'unit', 'label' => 'Unit'],
        ['key' => 'created_at', 'label' => 'Created At'],
        ['key' => 'updated_at', 'label' => 'Updated At'],
        ['key' => 'action', 'label' => 'Action', 'type' => 'action'],
    ];

    // Join with departments for department name
    $registers = DB::table('indent_registers')
        ->join('departments', 'departments.id', '=', 'indent_registers.indent_department')
        ->select(
            'indent_registers.id',
            'indent_registers.indent_id',
            'departments.name as department_name',
            'item_description as item_description',
            'indent_registers.indent_department as department_id',
            'unit as unit',
            'indent_registers.created_at',
            'indent_registers.updated_at'
        )
        ->orderByDesc('indent_registers.created_at')
        ->paginate(10);

    // Format rows
    $rows = $registers->map(function ($reg) {
    return [
        'indent_id' => $reg->indent_id,
        'department_name' => $reg->department_name,
        'department_id' => $reg->department_id,
        'item_description' => $reg->item_description,
        'unit' => $reg->unit,
        'created_at' => \Carbon\Carbon::parse($reg->created_at)->format('Y-m-d H:i'),
        'updated_at' => \Carbon\Carbon::parse($reg->updated_at)->format('Y-m-d H:i'),

        // 👇 Pass both URLs inside an array for 'action'
        'action' => [
            'edit' => route('indent.create', $reg->id),
           'file_po' => route('po-register.create', [
                'indent_id' => $reg->indent_id,
                'department_id' => $reg->department_id,
            ]),// only if not already filed
        ],
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

    return view('pages.indent.indentView.indentView', [
        'title' => $title,
        'columns' => $columns,
        'rows' => $rows,
        'searchPlaceholder' => $searchPlaceholder,
        'customButton' => $customButton,
        'pagination' => $registers,
    ]);
}

    public function create()
{   
    $title = 'Draft List';
    $departments = Department::all();

    $rows = DB::table('indent_tickets')
        ->leftJoin('indent_registers', function ($join) {
            $join->on('indent_tickets.indent_id', '=', 'indent_registers.indent_id')
                 ->whereColumn('indent_tickets.department_id', '=', 'indent_registers.indent_department');
        })
        ->join('departments', 'departments.id', '=', 'indent_tickets.department_id')
        ->whereNull('indent_registers.id') // Only unmatched entries
        ->select(
            'indent_tickets.indent_id',
            'indent_tickets.department_id',
            'departments.name as department_name'
        )
        ->get();

    $columns = [
        ['key' => 'indent_id', 'label' => 'Indent ID'],
    ['key' => 'department_name', 'label' => 'Department Name'],
    ['key' => 'action', 'label' => 'Action', 'type' => 'action'],
];

$rows = $rows->map(function ($row) {
    $row = (array) $row;
    $row['action'] = route('indent.create.form', [
        'indent_id' => $row['indent_id'],
        'department_id' => $row['department_id']
    ]);
    return $row;
});

    return view('pages.indent.generateIndent.generateIndent', compact('departments', 'title','columns', 'rows'));
}

   public function store(Request $request)
{
    // ✅ Validate fields with uniqueness scoped to department
    $request->validate([
        'department_id' => 'required|string',
        'indent_id' => [
            'required',
            Rule::unique('indent_registers')->where(function ($query) use ($request) {
                return $query->where('indent_department', $request->department_id);
            }),
        ],
    ]);

    // ✅ Make sure department exists
    $department = Department::findOrFail($request->department_id);

    // ✅ Create indent ticket
    $indentTicket = IndentTicket::create([
        'indent_id' => $request->indent_id,
        'department_id' => $department->id,
    ]);

    // ✅ Redirect with flash data to prefill next form
    return redirect()->route('indent.create.form')
        ->with([
            'indent_id' => $indentTicket->indent_id,
            'department_id' => $department->id,
            'success' => 'Indent generated successfully!'
        ]);
}

  public function createForm(Request $request)
{
    $departments = Department::all();
    $projects = Project::all();
    $units = Unit::all();

    $departmentId = $request->get('department_id', session('department_id'));
    $indentId = $request->get('indent_id', session('indent_id'));
    $departmentName = null;

    if ($departmentId) {
        $department = Department::find($departmentId);
        $departmentName = $department ? $department->name : null;
    }

    return view('pages.indent.indentForm.indentForm', [
        'departments' => $departments,
        'projects' => $projects,
        'units' => $units,
        'indent_id' => $indentId,
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
    $indent = new IndentRegister();

    $indent->indent_id = $request->indent_id;
    $indent->indent_date = $request->indent_date;
    $indent->indent_department = $request->indent_department;
    $indent->indent_project = $request->indent_project;
    $indent->item_description = $request->item_description;
    $indent->unit = $request->unit;
    $indent->quantity_required = $request->quantity_required;
    $indent->purchased_order = $request->purchased_order;
    $indent->quantity_received = $request->quantity_received;
    $indent->quantity_balance = $request->quantity_balance;

    $indent->save();

    return redirect()->route('indent.index')->with('success', 'Indent registered successfully!');

}


}
