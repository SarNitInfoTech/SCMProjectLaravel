<?php
namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Notification;
use App\Models\DepartmentHead;
use Illuminate\Http\Request;

class DepartmentHeadController extends Controller
{
    public function show(Request $request)
    {
        $title = 'Department Head List';
        $query = DepartmentHead::with('department:id,name');
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('department_head', 'like', "%{$search}%")
                  ->orWhereHas('department', function($dq) use ($search) {
                      $dq->where('name', 'like', "%{$search}%");
                  });
            });
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
        $request->validate([
            'department_id' => 'required|exists:departments,id',
            'department_head' => 'required|string|max:255',
        ]);

        $departmentHead = DepartmentHead::findOrFail($id);
        $departmentHead->update($request->only('department_id', 'department_head'));

        return redirect()->route('departmentHead.list')->with('success', 'Department Head updated successfully.');
    }

   public function store(Request $request)
{
    $request->validate([
        'department_id' => 'required|exists:departments,id',
        'department_head' => 'required|string|max:255',
    ]);

    // Create the Department Head entry
    $head = DepartmentHead::create($request->only('department_id', 'department_head'));

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
}
