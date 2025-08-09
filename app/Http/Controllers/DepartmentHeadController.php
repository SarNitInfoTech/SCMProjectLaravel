<?php
namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Notification;
use App\Models\DepartmentHead;
use Illuminate\Http\Request;

class DepartmentHeadController extends Controller
{
    public function show()
    {
        $title = 'Department Head List';

        $columns = [
            ['key' => 'department_name', 'label' => 'Department'],
            ['key' => 'department_head', 'label' => 'Head Name'],
            ['key' => 'created_at', 'label' => 'Created At'],
            ['key' => 'updated_at', 'label' => 'Updated At'],
            ['key' => 'action', 'label' => 'Action', 'type' => 'action'],
        ];

        $departmentHeads = DepartmentHead::with('department:id,name')
            ->select('id', 'department_id', 'department_head', 'created_at', 'updated_at')
            ->paginate(10);

        $rows = $departmentHeads->map(function ($dh) {
            return [
                'department_name' => $dh->department?->name ?? 'N/A',
                'department_head' => $dh->department_head,
                'created_at' => $dh->created_at->format('d-m-Y'),
                'updated_at' => $dh->updated_at->format('d-m-Y'),
                'action' => route('department-head.edit', $dh->id),
            ];
        });

        $searchPlaceholder = 'Search department heads...';
        $redirectUrl = route('departmentHead.create');

        $customButton = <<<HTML
            <a href="{$redirectUrl}" class="ti-btn ti-btn-primary-full">
                <i class="bi bi-plus-lg"></i>
                Add Department Head
            </a>
            HTML;

        return view('pages.departmentHeads.listDepartmentHeads.listDepartmentHeads', [
            'title' => $title,
            'columns' => $columns,
            'rows' => $rows,
            'searchPlaceholder' => $searchPlaceholder,
            'customButton' => $customButton,
            'pagination' => $departmentHeads,
        ]);
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
