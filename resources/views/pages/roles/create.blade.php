@extends("layouts.layout")

@section("bodyContent")
<div class="md:flex block items-center justify-between my-6 page-header-breadcrumb">
    <div>
        <h4 class="mb-0 text-defaulttextcolor font-medium">{{ $title }}</h4>
        <p class="-mt-[0.2rem] mb-0 text-textmuted">Define a new user access role</p>
    </div>
</div>

<div class="max-w-2xl mx-auto">
    <div class="card shadow-sm border bg-white mb-6">
        <div class="card-header p-4 border-b">
            <h3 class="text-lg font-semibold text-gray-800">Add New Role Details</h3>
        </div>
        
        <form method="POST" action="{{ route('roles.store') }}" class="p-6">
            @csrf

            <div class="mb-4">
                <label for="name" class="block text-sm font-semibold text-gray-700 mb-1">Role Key (Unique Identifier)</label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    value="{{ old('name') }}"
                    placeholder="e.g. store_manager"
                    class="form-input rounded border w-full px-3 py-2 text-sm bg-gray-50 focus:bg-white @error('name') border-red-500 @enderror"
                    required
                >
                <p class="text-xs text-gray-400 mt-1">Must be lowercase, with no spaces (use underscores instead, e.g. store_manager).</p>
                @error('name')
                    <span class="text-xs text-red-500 font-medium block mt-1">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-6">
                <label for="label" class="block text-sm font-semibold text-gray-700 mb-1">Display Label</label>
                <input
                    type="text"
                    id="label"
                    name="label"
                    value="{{ old('label') }}"
                    placeholder="e.g. Store Manager"
                    class="form-input rounded border w-full px-3 py-2 text-sm bg-gray-50 focus:bg-white @error('label') border-red-500 @enderror"
                    required
                >
                <p class="text-xs text-gray-400 mt-1">Human readable name shown on the user interface.</p>
                @error('label')
                    <span class="text-xs text-red-500 font-medium block mt-1">{{ $message }}</span>
                @enderror
            </div>

            <div class="flex justify-end gap-2 border-t pt-4">
                <a href="{{ route('roles.index') }}" class="px-4 py-2 text-sm bg-gray-100 hover:bg-gray-200 text-gray-700 rounded transition-all">
                    Cancel
                </a>
                <button type="submit" class="px-4 py-2 text-sm bg-indigo-600 hover:bg-indigo-700 text-white rounded shadow transition-all">
                    Create Role
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
