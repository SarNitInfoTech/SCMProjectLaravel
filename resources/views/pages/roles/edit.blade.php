@extends("layouts.layout")

@section("bodyContent")
<div class="md:flex block items-center justify-between my-6 page-header-breadcrumb">
    <div>
        <h4 class="mb-0 text-defaulttextcolor font-medium">{{ $title }}</h4>
        <p class="-mt-[0.2rem] mb-0 text-textmuted">Manage the specific pages and actions this role is authorized to perform</p>
    </div>
</div>

<form method="POST" action="{{ route('roles.update', $role->id) }}">
    @csrf
    @method('PUT')

    <div class="card shadow-sm border bg-white mb-6">
        <div class="card-header p-4 border-b flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-800">Role Base Info</h3>
        </div>
        <div class="p-6">
            <div class="max-w-md">
                <label for="label" class="block text-sm font-semibold text-gray-700 mb-1">Display Label</label>
                <input
                    type="text"
                    id="label"
                    name="label"
                    value="{{ old('label', $role->label) }}"
                    class="form-input rounded border w-full px-3 py-2 text-sm bg-gray-50 focus:bg-white @error('label') border-red-500 @enderror"
                    required
                >
                @error('label')
                    <span class="text-xs text-red-500 font-medium block mt-1">{{ $message }}</span>
                @enderror
            </div>
        </div>
    </div>

    @if ($role->name === 'super_admin')
        <div class="card shadow-sm border p-6 bg-red-50 border-red-200 text-red-800 rounded mb-6">
            <div class="flex items-start gap-3">
                <i class="bi bi-exclamation-triangle-fill text-2xl text-red-600"></i>
                <div>
                    <h4 class="font-bold text-red-900 mb-1">Super Admin Access Control</h4>
                    <p class="text-sm text-red-700">The <strong>Super Admin</strong> role has hardcoded database-level bypass logic. This user always has access to all pages, resources, actions, settings, and modules in the SCM application, regardless of the permissions checkmarks below.</p>
                </div>
            </div>
        </div>
    @endif

    <div class="card shadow-sm border bg-white mb-6">
        <div class="card-header p-4 border-b flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-800 font-medium">Access Control Permissions Grid</h3>
            <button type="button" onclick="toggleAllPermissions()" class="text-xs font-semibold px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded transition-all">
                Select / Clear All
            </button>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                @foreach ($permissionsByModule as $moduleName => $modulePermissions)
                    <div class="border rounded shadow-sm bg-gray-50 overflow-hidden">
                        <div class="bg-indigo-600 text-white px-4 py-2.5 font-bold flex justify-between items-center text-sm">
                            <span>{{ $moduleName }} Module</span>
                            <button type="button" onclick="toggleModulePermissions('{{ Str::slug($moduleName) }}')" class="text-xs text-indigo-100 hover:text-white underline">
                                Toggle All
                            </button>
                        </div>
                        <div class="p-4 space-y-3 bg-white">
                            @foreach ($modulePermissions as $perm)
                                @php
                                    $checked = in_array($perm->id, $rolePermissions) ? 'checked' : '';
                                    $isDeleteAction = str_contains($perm->name, 'delete');
                                @endphp
                                <label class="flex items-start gap-3 cursor-pointer select-none">
                                    <input
                                        type="checkbox"
                                        name="permissions[]"
                                        value="{{ $perm->id }}"
                                        {{ $checked }}
                                        data-module="{{ Str::slug($moduleName) }}"
                                        class="permission-checkbox rounded mt-1 text-indigo-600 focus:ring-indigo-500 {{ $isDeleteAction ? 'border-red-300' : 'border-gray-300' }}"
                                    >
                                    <div>
                                        <span class="text-sm font-semibold {{ $isDeleteAction ? 'text-red-700' : 'text-gray-800' }}">
                                            {{ $perm->label }}
                                        </span>
                                        <p class="text-xs text-gray-400 font-mono">{{ $perm->name }}</p>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="flex justify-end gap-2 p-4 border-t bg-gray-50">
            <a href="{{ route('roles.index') }}" class="px-4 py-2 text-sm bg-gray-100 hover:bg-gray-200 text-gray-700 rounded transition-all">
                Cancel
            </a>
            <button type="submit" class="px-4 py-2 text-sm bg-indigo-600 hover:bg-indigo-700 text-white rounded shadow transition-all">
                Save Permissions
            </button>
        </div>
    </div>
</form>

<script>
    function toggleAllPermissions() {
        const checkboxes = document.querySelectorAll('.permission-checkbox');
        const anyChecked = Array.from(checkboxes).some(cb => cb.checked);
        checkboxes.forEach(cb => cb.checked = !anyChecked);
    }

    function toggleModulePermissions(moduleSlug) {
        const checkboxes = document.querySelectorAll(`.permission-checkbox[data-module="${moduleSlug}"]`);
        const anyChecked = Array.from(checkboxes).some(cb => cb.checked);
        checkboxes.forEach(cb => cb.checked = !anyChecked);
    }
</script>
@endsection
