<div class="w-full px-4 py-6 bg-white shadow rounded">
    <form method="POST" action="{{ route('projects.update', $project->id) }}" class="grid grid-rows-[auto_1fr] gap-6 h-full">
        @csrf
        @method('PATCH') {{-- Use PATCH for updating --}}

        {{-- Row 1: Input Field --}}
        <div class="w-full">
            <label for="form-text" class="form-label text-black block mb-1">Project Name</label>
            <input
                type="text"
                name="name"
                id="form-text"
                class="form-control w-full"
                placeholder="e.g. HR, Finance"
                value="{{ old('name', $project->name) }}"
                required
            >
            @error('name')
                <small class="text-red-600">{{ $message }}</small>
            @enderror
        </div>

        {{-- Row 2: Submit Button at Bottom-Right --}}
        <div class="flex justify-end">
            <button type="submit" class="ti-btn ti-btn-primary-full">
                Update Project
            </button>
        </div>
    </form>
</div>
