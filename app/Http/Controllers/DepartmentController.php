<?php 
namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{


    public function index(Request $request)
    {
        $title = 'Department List';
        $query = Department::query();
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        $departments = $query->orderBy('name')->paginate(10);

        return view('pages.departments.listDepartments.listDepartments', compact('title', 'departments'));
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

