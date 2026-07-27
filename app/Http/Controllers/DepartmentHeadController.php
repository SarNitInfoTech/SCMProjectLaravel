<?php
namespace App\Http\Controllers;

use App\Helpers\SearchHelper;
use App\Models\Department;
use App\Models\Notification;
use App\Models\DepartmentHead;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DepartmentHeadController extends Controller
{
    public function show(Request $request)
    {
        $title = 'Department Head List';
        $query = DepartmentHead::with('department:id,name');
        if ($request->filled('search')) {
            $search = $request->search;
            SearchHelper::applyFuzzySearch($query, $search, ['department_head']);
        }
        $departmentHeads = $query->paginate(10);

        return view('pages.departmentHeads.listDepartmentHeads.listDepartmentHeads', compact('title', 'departmentHeads'));
    }

    public function create()
    {
        $departments = Department::all();
        return view('pages.departmentHeads.addDepartmentHeads.addDepartmentHeads', compact('departments'));
    }

    public function edit($id)
    {
        $departmentHead = DepartmentHead::findOrFail($id);
        $departments = Department::all();

        return view('pages.departmentHeads.editDepartmentHeads.editDepartmentHeads', compact('departmentHead', 'departments'));
    }

    public function update(Request $request, $id)
    {
        $departmentHead = DepartmentHead::findOrFail($id);

        $request->validate([
            'department_id' => 'required|exists:departments,id',
            'department_head' => [
                'required',
                'string',
                'max:255',
                Rule::unique('department_heads', 'department_head')
                    ->where('department_id', $request->department_id)
                    ->ignore($departmentHead->id)
            ],
        ]);

        $departmentHead->update([
            'department_id' => $request->department_id,
            'department_head' => trim($request->department_head),
        ]);

        return redirect()->route('departmentHead.list')->with('success', 'Department Head updated successfully.');
    }

   public function store(Request $request)
{
    $request->validate([
        'department_id' => 'required|exists:departments,id',
        'department_head' => [
            'required',
            'string',
            'max:255',
            Rule::unique('department_heads', 'department_head')
                ->where('department_id', $request->department_id)
        ],
    ]);

    // Create the Department Head entry
    $head = DepartmentHead::create([
        'department_id' => $request->department_id,
        'department_head' => trim($request->department_head),
    ]);

    // Create a notification
    Notification::create([
        'title' => 'New Department Head Added',
        'description' => "Head '{$head->department_head}' added to department ID {$head->department_id}.",
        'link' => route('departmentHead.list'),
        'icon' => 'la la-user-tie',
        'bg_color' => 'bg-success',
        'is_read' => false,
    ]);

    return redirect()->route('departmentHead.list')->with('success', 'Department Head added successfully.');
}

public function destroy($id)
{
    $departmentHead = DepartmentHead::findOrFail($id);
    $departmentHead->delete();

    return redirect()->route('departmentHead.list')->with('success', 'Department Head deleted successfully.');
}
}
