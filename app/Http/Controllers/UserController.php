<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Department;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
   
    public function index(Request $request)
    {
        $title = 'User List';
        $query = User::with('department:id,name');
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        $users = $query->paginate(10);

        return view('pages.user.listUser.listUser', compact('title', 'users'));
    }

    public function create()
    {
        $departments = Department::all();
        $roles = Role::all();
        return view('pages.user.addUser.addUser', compact('departments', 'roles'));
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
    $roles = Role::all();
    return view('pages.user.editUser.editUser', compact('user', 'departments', 'roles'));
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
