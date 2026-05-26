<div class="w-full px-4 py-6 bg-white shadow rounded">
    <form method="POST" action="{{ route('users.store') }}" enctype="multipart/form-data"
          class="sm:grid space-y-6 sm:space-y-0 grid-cols-4 gap-4 font-mono text-defaulttextcolor text-sm text-center font-bold rounded-sm">
        @csrf

        <!-- Name -->
        <div>
            <label for="name" class="block text-left mb-1 text-gray-700 font-semibold">
                Name <span class="text-red-500">*</span>
            </label>
            <input type="text" name="name" id="name"
                   class="form-control w-full text-black font-normal rounded border border-gray-300 p-2"
                   placeholder="Full Name" required>
            @error('name') <small class="text-red-600">{{ $message }}</small> @enderror
        </div>

        <!-- Email -->
        <div>
            <label for="email" class="block text-left mb-1 text-gray-700 font-semibold">
                Email <span class="text-red-500">*</span>
            </label>
            <input type="email" name="email" id="email"
                   class="form-control w-full text-black font-normal rounded border border-gray-300 p-2"
                   placeholder="Email" required>
            @error('email') <small class="text-red-600">{{ $message }}</small> @enderror
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="block text-left mb-1 text-gray-700 font-semibold">
                Password <span class="text-red-500">*</span>
            </label>
            <input type="password" name="password" id="password"
                   class="form-control w-full text-black font-normal rounded border border-gray-300 p-2"
                   required>
            @error('password') <small class="text-red-600">{{ $message }}</small> @enderror
        </div>

        <!-- Confirm Password -->
        <div>
            <label for="password_confirmation" class="block text-left mb-1 text-gray-700 font-semibold">
                Confirm Password <span class="text-red-500">*</span>
            </label>
            <input type="password" name="password_confirmation" id="password_confirmation"
                   class="form-control w-full text-black font-normal rounded border border-gray-300 p-2"
                   required>
        </div>

        <!-- Role -->
        <div>
            <label for="role" class="block text-left mb-1 text-gray-700 font-semibold">
                Role <span class="text-red-500">*</span>
            </label>
            <select name="role" id="role"
                    class="form-control w-full text-black font-normal rounded border border-gray-300 p-2"
                    required>
                @foreach($roles as $r)
                    <option value="{{ $r->name }}">{{ $r->label }}</option>
                @endforeach
            </select>
            @error('role') <small class="text-red-600">{{ $message }}</small> @enderror
        </div>

        <!-- Department -->
        <div>
            <label for="department_id" class="block text-left mb-1 text-gray-700 font-semibold">
                Department <span class="text-red-500">*</span>
            </label>
            <select required name="department_id" id="department_id"
                    class="form-control w-full text-black font-normal rounded border border-gray-300 p-2">
                <option value="">Select Department</option>
                @foreach($departments as $dept)
                    <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                @endforeach
            </select>
            @error('department_id') <small class="text-red-600">{{ $message }}</small> @enderror
        </div>

        <!-- Designation -->
        <div>
            <label for="designation" class="block text-left mb-1 text-gray-700 font-semibold">
                Designation
            </label>
            <input type="text" name="designation" id="designation"
                   class="form-control w-full text-black font-normal rounded border border-gray-300 p-2"
                   placeholder="e.g. Developer">
            @error('designation') <small class="text-red-600">{{ $message }}</small> @enderror
        </div>

        <!-- Phone -->
        <div>
            <label for="phone" class="block text-left mb-1 text-gray-700 font-semibold">
                Phone
            </label>
            <input type="text" name="phone" id="phone"
                   class="form-control w-full text-black font-normal rounded border border-gray-300 p-2"
                   placeholder="9876543210">
            @error('phone') <small class="text-red-600">{{ $message }}</small> @enderror
        </div>

        <!-- Avatar -->
        <div class="col-span-4 sm:col-span-2">
            <label for="avatar" class="block text-left mb-1 text-gray-700 font-semibold">
                Avatar
            </label>
            <input type="file" name="avatar" id="avatar"
                   class="form-control w-full text-black font-normal rounded border border-gray-300 p-2">
            @error('avatar') <small class="text-red-600">{{ $message }}</small> @enderror
        </div>

        <!-- Active -->
        <div class="col-span-4 sm:col-span-2">
            <label class="block text-left mb-1 text-gray-700 font-semibold">
                Active
            </label>
            <div class="form-control text-black font-normal rounded border border-gray-300 p-2">
                <input type="checkbox" name="is_active" id="is_active" class="hidden" checked>
                <div id="toggle-active" class="toggle toggle-success on cursor-pointer mb-1">
                    <span></span>
                </div>
            </div>
        </div>

        <!-- Bio -->
        <div class="col-span-4">
            <label for="bio" class="block text-left mb-1 text-gray-700 font-semibold">
                Bio
            </label>
            <textarea name="bio" id="bio" rows="3"
                      class="form-control w-full rounded border-gray-300 p-2"
                      placeholder="Write something..."></textarea>
            @error('bio') <small class="text-red-600">{{ $message }}</small> @enderror
        </div>

        <!-- Submit -->
        <div class="col-span-4 flex justify-end pt-4">
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded shadow">
                Create User
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
