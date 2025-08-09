<div class="w-full px-4 py-6 bg-white shadow rounded">
    <form method="POST" action="{{ route('department-head.update', $departmentHead->id) }}" class="grid grid-rows-[auto_1fr] gap-6 h-full">
        @csrf
        @method('PUT')

        {{-- Row 1: Select Department --}}
        <div class="w-full">
            <label for="department_id" class="form-label text-black block mb-1">Select Department</label>
            <select name="department_id" id="department_id" class="form-control w-full" required>
                <option value="" disabled>Select Department</option>
                @foreach($departments as $dept)
                    <option value="{{ $dept->name }}" {{ $departmentHead->department_id == $dept->id ? 'selected' : '' }}>
                        {{ $dept->name }}
                    </option>
                @endforeach
            </select>
            @error('department_id')
                <small class="text-red-600">{{ $message }}</small>
            @enderror
        </div>

        {{-- Row 2: Department Head Name --}}
        <div class="w-full">
            <label for="department_head" class="form-label text-black block mb-1">Department Head</label>
            <input
                type="text"
                name="department_head"
                id="department_head"
                class="form-control w-full"
                placeholder="e.g. John Doe"
                value="{{ old('department_head', $departmentHead->department_head) }}"
                required
            >
            @error('department_head')
                <small class="text-red-600">{{ $message }}</small>
            @enderror
        </div>

        {{-- Row 3: Submit Button --}}
        <div class="flex justify-end">
            <button type="submit" class="ti-btn ti-btn-primary-full">
                Update
            </button>
        </div>
    </form>
</div>
