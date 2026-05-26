@extends("layouts.layout")

@section("bodyContent")
<div class="md:flex block items-center justify-between my-6 page-header-breadcrumb">
    <div>
        <h4 class="mb-0 text-defaulttextcolor font-medium">{{ $title }}</h4>
        <p class="-mt-[0.2rem] mb-0 text-textmuted">Manage user roles and page-level permission matrices</p>
    </div>
    <div class="flex items-center gap-2">
        <a href="{{ route('roles.create') }}" class="px-4 py-2 text-sm bg-indigo-600 hover:bg-indigo-700 text-white rounded shadow transition-all">
            <i class="bi bi-plus-lg"></i> Add New Role
        </a>
    </div>
</div>

@if (session('success'))
    <div class="mb-4 p-3 bg-green-100 border-l-4 border-green-500 text-green-700 text-sm rounded">
        {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div class="mb-4 p-3 bg-red-100 border-l-4 border-red-500 text-red-700 text-sm rounded">
        {{ session('error') }}
    </div>
@endif

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <div class="card shadow-sm border p-4 bg-white flex items-center justify-between">
        <div>
            <span class="text-sm text-gray-500 font-medium">Total Defined Roles</span>
            <h3 class="text-2xl font-bold text-gray-800 mt-1">{{ $roles->count() }}</h3>
        </div>
        <div class="p-3 bg-indigo-50 text-indigo-600 rounded-full">
            <i class="bi bi-shield-check text-2xl"></i>
        </div>
    </div>
    <div class="card shadow-sm border p-4 bg-white flex items-center justify-between">
        <div>
            <span class="text-sm text-gray-500 font-medium">System Permissions</span>
            <h3 class="text-2xl font-bold text-gray-800 mt-1">{{ $permissionsCount }}</h3>
        </div>
        <div class="p-3 bg-green-50 text-green-600 rounded-full">
            <i class="bi bi-key text-2xl"></i>
        </div>
    </div>
    <div class="card shadow-sm border p-4 bg-white flex items-center justify-between">
        <div>
            <span class="text-sm text-gray-500 font-medium">Platform Access Security</span>
            <h3 class="text-lg font-bold text-green-600 mt-1">Active (RBAC)</h3>
        </div>
        <div class="p-3 bg-yellow-50 text-yellow-600 rounded-full">
            <i class="bi bi-lock text-2xl"></i>
        </div>
    </div>
</div>

<div class="card shadow-sm border mb-6 bg-white">
    <div class="card-header p-4 border-b">
        <h3 class="text-lg font-semibold text-gray-800">User Roles & Access Levels</h3>
    </div>

    <div class="table-responsive p-4">
        <table class="table whitespace-nowrap min-w-full">
            <thead>
                <tr class="border-b border-defaultborder">
                    <th scope="col" class="text-start">Role Key</th>
                    <th scope="col" class="text-start">Display Label</th>
                    <th scope="col" class="text-start">Assigned Permissions</th>
                    <th scope="col" class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($roles as $role)
                    @php
                        $isSystemRole = in_array($role->name, ['super_admin', 'admin', 'user']);
                        $badgeClass = match ($role->name) {
                            'super_admin' => 'bg-red-100 text-red-800',
                            'admin' => 'bg-indigo-100 text-indigo-800',
                            'user' => 'bg-gray-100 text-gray-800',
                            default => 'bg-blue-100 text-blue-800'
                        };
                    @endphp
                    <tr class="border-b border-defaultborder hover:bg-gray-50 transition-colors">
                        <td class="font-medium text-gray-900">
                            <span class="px-2.5 py-0.5 rounded text-xs font-mono {{ $badgeClass }}">
                                {{ $role->name }}
                            </span>
                        </td>
                        <td class="font-semibold text-gray-700">{{ $role->label }}</td>
                        <td>
                            @if ($role->name === 'super_admin')
                                <span class="text-sm font-medium text-red-600">All (Bypassed / Super Access)</span>
                            @else
                                <span class="text-sm text-gray-600 font-medium">
                                    {{ $role->permissions_count }} permissions assigned
                                </span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="inline-flex gap-2">
                                <a href="{{ route('roles.edit', $role->id) }}" class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium text-white bg-green-600 hover:bg-green-700 rounded transition-all">
                                    <i class="bi bi-shield-lock"></i> Configure Access
                                </a>

                                @if (!$isSystemRole)
                                    <form action="{{ route('roles.destroy', $role->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this role? All users assigned this role will lose its permissions.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium text-white bg-red-600 hover:bg-red-700 rounded transition-all">
                                            <i class="bi bi-trash"></i> Delete
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
