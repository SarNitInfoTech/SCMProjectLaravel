<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
   
    public function index()
{
    $title = 'User List';

    $columns = [
        ['key' => 'name', 'label' => 'Name'],
        ['key' => 'email', 'label' => 'Email'],
        ['key' => 'role', 'label' => 'Role'],
        ['key' => 'department', 'label' => 'Department'],
        ['key' => 'created_at', 'label' => 'Created At'],
        ['key' => 'updated_at', 'label' => 'Updated At'],
        ['key' => 'action', 'label' => 'Action', 'type' => 'action'],
    ];

    $Users = \App\Models\User::with('department:id,name')
        ->select('id', 'name', 'email', 'role', 'department_id', 'created_at', 'updated_at')
        ->paginate(10);

    $rows = $Users->map(function ($user) {
        return [
            'name' => $user->name,
            'email' => $user->email,
            'role' => ucfirst($user->role),
            'department' => optional($user->department)->name ?? '—',
            'created_at' => $user->created_at->format('d-m-Y'),
            'updated_at' => $user->updated_at->format('d-m-Y'),
            'action' => route('users.edit', $user->id),
        ];
    });

    $searchPlaceholder = 'Search Users...';
    $redirectUrl = route('users.create');

    $customButton = <<<HTML
<a href="{$redirectUrl}" class="ti-btn ti-btn-primary-full">
    <i class="bi bi-plus-lg"></i>
    Add New User
</a>
HTML;

    return view('pages.user.listUser.listUser', [
        'title' => $title,
        'columns' => $columns,
        'rows' => $rows,
        'searchPlaceholder' => $searchPlaceholder,
        'customButton' => $customButton,
        'pagination' => $Users,
    ]);
}

    public function create()
    {
        $departments = Department::all();
        return view('pages.user.addUser.addUser', compact('departments'));
    }

   

public function store(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users',
        'password' => 'required|confirmed|min:6',
        'role' => 'required|string',
        'department_id' => 'required|exists:departments,id',
        'designation' => 'nullable|string',
        'phone' => 'nullable|string',
        'avatar' => 'nullable|image|max:2048',
        'bio' => 'nullable|string',
    ]);

    if ($request->hasFile('avatar')) {
        $path = $request->file('avatar')->store('avatars', 'public');
        $validated['avatar'] = $path;
    }

    $validated['password'] = bcrypt($validated['password']);
    $validated['is_active'] = $request->has('is_active') ? 1 : 0;

    User::create($validated);

    return redirect()->route('dashboard.index')
        ->with('success', 'User created successfully.');
}


public function edit($id)
{
    $user = User::findOrFail($id);
    $departments = Department::all();
    return view('pages.user.editUser.editUser', compact('user', 'departments'));
}

public function update(Request $request, $id)
{
    $user = User::findOrFail($id);

    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email,' . $user->id,
        'password' => 'nullable|confirmed|min:6',
        'role' => 'required|string',
        'department_id' => 'required|exists:departments,id',
        'designation' => 'nullable|string',
        'phone' => 'nullable|string',
        'avatar' => 'nullable|image|max:2048',
        'bio' => 'nullable|string',
    ]);

    if ($request->hasFile('avatar')) {
        $path = $request->file('avatar')->store('avatars', 'public');
        $validated['avatar'] = $path;
    }

    if (!empty($validated['password'])) {
        $validated['password'] = bcrypt($validated['password']);
    } else {
        unset($validated['password']);
    }

    $validated['is_active'] = $request->has('is_active') ? 1 : 0;

    $user->update($validated);

    return redirect()->route('dashboard.index')
        ->with('success', 'User updated successfully.');
}


}
