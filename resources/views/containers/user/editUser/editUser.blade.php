<div class="w-full px-4 py-6 bg-white shadow rounded">
    <form method="POST" action="{{ route('users.update', $user->id) }}" enctype="multipart/form-data"
          class="sm:grid space-y-6 sm:space-y-0 grid-cols-4 gap-4 font-mono text-defaulttextcolor text-sm text-center font-bold rounded-sm">
        @csrf
        @method('PUT')

        <!-- Name -->
        <div>
            <label for="name" class="block text-left mb-1 text-gray-700 font-semibold">Name <span class="text-red-500">*</span></label>
            <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}"
                   class="form-control w-full text-black font-normal rounded border border-gray-300 p-2" required>
            @error('name') <small class="text-red-600">{{ $message }}</small> @enderror
        </div>

        <!-- Email -->
        <div>
            <label for="email" class="block text-left mb-1 text-gray-700 font-semibold">Email <span class="text-red-500">*</span></label>
            <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}"hidden>
             <input type="email"  value="{{ old('email', $user->email) }}"
                   class="form-control w-full text-black font-normal rounded border border-gray-300 p-2" disabled>
            @error('email') <small class="text-red-600">{{ $message }}</small> @enderror
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="block text-left mb-1 text-gray-700 font-semibold">Password</label>
            <input type="password" name="password" id="password"
                   class="form-control w-full text-black font-normal rounded border border-gray-300 p-2"
                   placeholder="Leave blank to keep current password">
            @error('password') <small class="text-red-600">{{ $message }}</small> @enderror
        </div>

        <!-- Confirm Password -->
        <div>
            <label for="password_confirmation" class="block text-left mb-1 text-gray-700 font-semibold">Confirm Password</label>
            <input type="password" name="password_confirmation" id="password_confirmation"
                   class="form-control w-full text-black font-normal rounded border border-gray-300 p-2"
                   placeholder="Leave blank if unchanged">
        </div>

        <!-- Role -->
        <div>
            <label for="role" class="block text-left mb-1 text-gray-700 font-semibold">Role <span class="text-red-500">*</span></label>
            <select name="role" id="role" class="form-control w-full text-black font-normal rounded border border-gray-300 p-2" required>
                @foreach($roles as $r)
                    <option value="{{ $r->name }}" {{ old('role', $user->role) === $r->name ? 'selected' : '' }}>{{ $r->label }}</option>
                @endforeach
            </select>
            @error('role') <small class="text-red-600">{{ $message }}</small> @enderror
        </div>

        <!-- Department -->
        <div>
            <label for="department_id" class="block text-left mb-1 text-gray-700 font-semibold">Department <span class="text-red-500">*</span></label>
            <select name="department_id" id="department_id" class="form-control w-full text-black font-normal rounded border border-gray-300 p-2" required>
                <option value="">Select Department</option>
                @foreach($departments as $dept)
                    <option value="{{ $dept->id }}" {{ old('department_id', $user->department_id) == $dept->id ? 'selected' : '' }}>
                        {{ $dept->name }}
                    </option>
                @endforeach
            </select>
            @error('department_id') <small class="text-red-600">{{ $message }}</small> @enderror
        </div>

        <!-- Designation -->
        <div>
            <label for="designation" class="block text-left mb-1 text-gray-700 font-semibold">Designation</label>
            <input type="text" name="designation" id="designation" value="{{ old('designation', $user->designation) }}"
                   class="form-control w-full text-black font-normal rounded border border-gray-300 p-2">
            @error('designation') <small class="text-red-600">{{ $message }}</small> @enderror
        </div>

        <!-- Phone -->
        <div>
            <label for="phone" class="block text-left mb-1 text-gray-700 font-semibold">Phone</label>
            <input type="text" name="phone" id="phone" value="{{ old('phone', $user->phone) }}"
                   class="form-control w-full text-black font-normal rounded border border-gray-300 p-2">
            @error('phone') <small class="text-red-600">{{ $message }}</small> @enderror
        </div>

        <!-- Avatar -->
        <div class="col-span-4 sm:col-span-2">
            <label for="avatar" class="block text-left mb-1 text-gray-700 font-semibold">Avatar</label>
            <input type="file" name="avatar" id="avatar" class="form-control w-full text-black font-normal rounded border border-gray-300 p-2">
            @error('avatar') <small class="text-red-600">{{ $message }}</small> @enderror
            @if($user->avatar)
                <img src="{{ asset('storage/' . $user->avatar) }}" alt="Avatar" class="mt-2 h-12 w-12 rounded-full object-cover">
            @endif
        </div>

        <!-- Active -->
        <div class="col-span-4 sm:col-span-2">
            <label class="block text-left mb-1 text-gray-700 font-semibold">Active</label>
            <div class="flex items-center gap-3 p-2 rounded border border-gray-300 form-control">
                <input type="checkbox" name="is_active" id="is_active" class="hidden" {{ old('is_active', $user->is_active) ? 'checked' : '' }}>
                <div id="toggle-active" class="toggle toggle-success {{ old('is_active', $user->is_active) ? 'on' : '' }} cursor-pointer">
                    <span></span>
                </div>
                <label for="is_active" class="text-sm text-gray-700 font-normal">Active</label>
            </div>
        </div>

        <!-- Bio -->
        <div class="col-span-4">
            <label for="bio" class="block text-left mb-1 text-gray-700 font-semibold">Bio</label>
            <textarea name="bio" id="bio" rows="3" class="form-control w-full rounded border-gray-300 p-2"
                      placeholder="Write something...">{{ old('bio', $user->bio) }}</textarea>
            @error('bio') <small class="text-red-600">{{ $message }}</small> @enderror
        </div>

        <!-- Submit -->
        <div class="col-span-4 flex justify-end pt-4">
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded shadow">
                Update User
            </button>
        </div>
    </form>
</div>

<!-- Toggle Script -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const toggleDiv = document.getElementById('toggle-active');
        const hiddenCheckbox = document.getElementById('is_active');

        toggleDiv.addEventListener('click', () => {
            toggleDiv.classList.toggle('on');
            hiddenCheckbox.checked = !hiddenCheckbox.checked;
        });
    });
</script>
