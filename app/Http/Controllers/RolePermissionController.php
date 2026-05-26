<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class RolePermissionController extends Controller
{
    public function index()
    {
        Gate::authorize('roles.manage');

        $title = 'Roles & Permissions';
        $roles = Role::withCount('permissions')->get();
        $permissionsCount = Permission::count();

        return view('pages.roles.index', compact('title', 'roles', 'permissionsCount'));
    }

    public function create()
    {
        Gate::authorize('roles.manage');

        $title = 'Create Role';
        return view('pages.roles.create', compact('title'));
    }

    public function store(Request $request)
    {
        Gate::authorize('roles.manage');

        $validated = $request->validate([
            'name'  => 'required|string|unique:roles,name|max:50|regex:/^[a-zA-Z0-9_]+$/',
            'label' => 'required|string|max:100',
        ], [
            'name.regex' => 'The role name key must only contain letters, numbers, and underscores (e.g. store_manager).'
        ]);

        // Normalize name to lowercase
        $validated['name'] = strtolower($validated['name']);

        Role::create($validated);

        return redirect()->route('roles.index')
            ->with('success', 'Role created successfully.');
    }

    public function edit($id)
    {
        Gate::authorize('roles.manage');

        $role = Role::findOrFail($id);
        $title = 'Edit Access Permissions: ' . $role->label;

        // Group permissions by their module for a clean checkbox layout
        $permissionsByModule = Permission::all()->groupBy('module');
        $rolePermissions = $role->permissions->pluck('id')->toArray();

        return view('pages.roles.edit', compact('title', 'role', 'permissionsByModule', 'rolePermissions'));
    }

    public function update(Request $request, $id)
    {
        Gate::authorize('roles.manage');

        $role = Role::findOrFail($id);

        $validated = $request->validate([
            'label' => 'required|string|max:100',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role->update([
            'label' => $validated['label']
        ]);

        // Sync permissions
        $permissions = $request->input('permissions', []);
        $role->permissions()->sync($permissions);

        return redirect()->route('roles.index')
            ->with('success', 'Role permissions updated successfully.');
    }

    public function destroy($id)
    {
        Gate::authorize('roles.manage');

        $role = Role::findOrFail($id);

        if (in_array($role->name, ['super_admin', 'admin', 'user'])) {
            return redirect()->route('roles.index')
                ->with('error', 'Default system roles cannot be deleted.');
        }

        $role->delete();

        return redirect()->route('roles.index')
            ->with('success', 'Role deleted successfully.');
    }
}
