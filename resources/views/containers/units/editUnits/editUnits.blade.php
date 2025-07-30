<div class="w-full px-4 py-6 bg-white shadow rounded">
    <form method="POST" action="{{ route('units.update', $Unit->id) }}" class="grid grid-rows-[auto_1fr] gap-6 h-full">
        @csrf
        @method('PATCH')

        <div class="w-full">
            <label for="name" class="form-label text-black block mb-1">Unit Name</label>
            <input
                type="text"
                name="name"
                id="name"
                class="form-control w-full"
                placeholder="e.g. Kg, Nos, Ltr"
                value="{{ old('name', $Unit->name) }}"
                required
            >
            @error('name')
                <small class="text-red-600">{{ $message }}</small>
            @enderror
        </div>

        <div class="flex justify-end">
            <button type="submit" class="ti-btn ti-btn-primary-full">
                Update Unit
            </button>
        </div>
    </form>
</div>
