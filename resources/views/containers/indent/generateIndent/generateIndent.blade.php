<div class="w-full px-4 py-6 bg-white shadow rounded">
    <form method="POST" action="{{ route('indent.store') }}" class="grid grid-rows-[auto_1fr] gap-6 h-full">
        @csrf

        <!-- Department Dropdown -->
        <div class="w-full">
            <label for="department_id" class="form-label text-black block mb-1">Select Department</label>
            <select name="department_id" id="department_id" class="form-control w-full" required>
                <option value="">-- Select Department --</option>
                @foreach($departments as $dept)
                    <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                @endforeach
            </select>
            @error('department_id')
                <small class="text-red-600">{{ $message }}</small>
            @enderror
        </div>

        <!-- Generated Indent ID (hidden initially) -->
        <div id="indent_id_wrapper" class="w-full hidden">
            <label for="indent_id" class="form-label text-black block mb-1">Generated Indent ID</label>
            <input
                type="text"
                name="indent_id"
                id="indent_id"
                class="form-control w-full bg-gray-100"
                readonly
                required
            >
            @error('indent_id')
                <small class="text-red-600">{{ $message }}</small>
            @enderror
        </div>

        <div class="flex justify-end">
            <button type="submit" class="ti-btn ti-btn-primary-full">
                File New Indent
            </button>
        </div>
    </form>
</div>

<script>
    const departmentSelect = document.getElementById('department_id');
    const indentIdWrapper = document.getElementById('indent_id_wrapper');
    const indentIdInput = document.getElementById('indent_id');

    departmentSelect.addEventListener('change', function () {
        const department = this.value;
        console.log('🔽 Department selected:', department);

        if (department) {
            console.log('📡 Sending fetch request for indent token...');

            fetch("{{ route('indent.token') }}", {
                method: "POST",
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ department: department })
            })
            .then(response => {
                console.log('📥 Raw response received');
                return response.json();
            })
            .then(data => {
                console.log('✅ Parsed JSON:', data);

                if (data.indent_id) {
                    console.log('🆔 Setting indent ID input to:', data.indent_id);
                    indentIdInput.value = data.indent_id;
                    indentIdWrapper.classList.remove('hidden');
                    console.log('🎯 Displaying indent ID input field');
                } else {
                    console.warn('⚠️ No indent ID received, hiding input field');
                    indentIdInput.value = '';
                    indentIdWrapper.classList.add('hidden');
                }
            })
            .catch(error => {
                console.error('❌ Fetch error:', error);
            });
        } else {
            console.log('🚫 No department selected, hiding indent ID field');
            indentIdWrapper.classList.add('hidden');
            indentIdInput.value = '';
        }
    });
</script>

