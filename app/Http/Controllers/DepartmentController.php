<?php 
namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{


public function index()
{
    $title = 'Department List';

    $columns = [
        ['key' => 'name', 'label' => 'Department Name'],
        ['key' => 'created_at', 'label' => 'Created At'],
        ['key' => 'updated_at', 'label' => 'Updated At'],
        ['key' => 'action', 'label' => 'Action', 'type' => 'action'],
    ];

    // Use paginate instead of get
    $departments = Department::select('id', 'name', 'created_at', 'updated_at')->paginate(10);

    $rows = $departments->map(function ($dept) {
        return [
            'name' => $dept->name,
            'created_at' => $dept->created_at->format('d-m-Y'),
            'updated_at' => $dept->updated_at->format('d-m-Y'),
            'action' => route('departments.edit', $dept->id),
        ];
    });

    $searchPlaceholder = 'Search departments...';
    $redirectUrl = route('departments.create');

    $customButton = <<<HTML
<a href="{$redirectUrl}" class="ti-btn ti-btn-primary-full">
    <i class="bi bi-plus-lg"></i>
    Add New Department
</a>
HTML;

    return view('pages.departments.listDepartments.listDepartments', [
        'title' => $title,
        'columns' => $columns,
        'rows' => $rows,
        'searchPlaceholder' => $searchPlaceholder,
        'customButton' => $customButton,
        'pagination' => $departments, // Add this line
    ]);
}

public function create()
{
    return view('pages.departments.addDepartments.addDepartments');
}

public function store(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
    ]);

    Department::create([
        'name' => $request->name,
    ]);

    return redirect()->route('departments.index')->with('success', 'Department created successfully!');
}
public function edit($id)
{
    $department = Department::findOrFail($id);
    return view('pages.departments.editdepartments.editdepartments', compact('department'));
}

public function update(Request $request, $id)
{
    $request->validate([
        'name' => 'required|string|max:255',
    ]);

    $department = Department::findOrFail($id);
    $department->update([
        'name' => $request->name,
    ]);

    return redirect()->route('departments.index')->with('success', 'Department updated successfully.');
}


}

