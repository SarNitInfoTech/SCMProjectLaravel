<div class="w-full px-4 py-6 bg-white shadow rounded">
    <form method="POST" action="{{ route('indent-register.indentRegisterUpdate', $indent->id) }}" class="grid grid-cols-1 gap-6">
        @csrf
        @method('PUT')

        <!-- Hidden inputs (actual data submission) -->
        <input type="hidden" name="indent_id" value="{{ $indent->indent_id }}">
        <input type="hidden" name="indent_department" value="{{ $indent->indent_department }}">

        <!-- Top Info Row -->
        <div class="grid grid-cols-4 gap-6">
            <!-- Indent Ticket ID (Display Only) -->
            <div class="w-full col-span-1">
                <label class="form-label text-black block mb-1">Indent Ticket ID</label>
                <input type="text" class="form-control w-full bg-gray-100" value="{{ $indent->indent_id }}" readonly disabled>
            </div>

            <!-- Department (Display Only) -->
            <div class="w-full col-span-1">
                <label class="form-label text-black block mb-1">Department</label>
                <input type="text" class="form-control w-full bg-gray-100" value="{{ $department_name }}" readonly disabled>
            </div>

            <!-- Indent Date -->
            <div class="w-full col-span-1">
                <label for="indent_date" class="form-label text-black block mb-1">Indent Date</label>
                <input type="date" name="indent_date" id="indent_date" class="form-control w-full"
                    value="{{ old('indent_date', $indent->indent_date) }}" required>
                @error('indent_date')
                    <small class="text-red-600">{{ $message }}</small>
                @enderror
            </div>

            <!-- Indent Project -->
            <div class="w-full col-span-1">
                <label for="indent_project" class="form-label text-black block mb-1">Indent Project</label>
                <select name="indent_project" id="indent_project" class="form-control w-full" required>
                    <option value="">-- Select Project --</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}"
                            {{ $indent->indent_project == $project->id ? 'selected' : '' }}>
                            {{ $project->name }}
                        </option>
                    @endforeach
                </select>
                @error('indent_project')
                    <small class="text-red-600">{{ $message }}</small>
                @enderror
            </div>
        </div>

        <!-- Item Description -->
        <div class="w-full">
            <label for="item_description" class="form-label text-black block mb-1">Item Description</label>
            <textarea name="item_description" id="item_description" class="form-control w-full" rows="3"
                required>{{ old('item_description', $indent->item_description) }}</textarea>
            @error('item_description')
                <small class="text-red-600">{{ $message }}</small>
            @enderror
        </div>

        <!-- Unit & Quantity Row -->
        <div class="grid grid-cols-4 gap-6">
            <!-- Unit -->
            <div class="w-full col-span-1">
                <label for="unit" class="form-label text-black block mb-1">Unit</label>
                <select name="unit" id="unit" class="form-control w-full" required>
                    <option value="">-- Select Unit --</option>
                    @foreach($units as $unit)
                        <option value="{{ $unit->id }}"
                            {{ $indent->unit == $unit->id ? 'selected' : '' }}>
                            {{ $unit->name }}
                        </option>
                    @endforeach
                </select>
                @error('unit')
                    <small class="text-red-600">{{ $message }}</small>
                @enderror
            </div>

            <!-- Quantity Required -->
            <div class="w-full col-span-1">
                <label for="quantity_required" class="form-label text-black block mb-1">Quantity Required</label>
                <input type="number" name="quantity_required" id="quantity_required" class="form-control w-full"
                    value="{{ old('quantity_required', $indent->quantity_required) }}" required min="0">
                @error('quantity_required')
                    <small class="text-red-600">{{ $message }}</small>
                @enderror
            </div>

            <!-- Quantity Received -->
            <div class="w-full col-span-1">
                <label for="quantity_received" class="form-label text-black block mb-1">Quantity Received</label>
                <input type="number" name="quantity_received" id="quantity_received" class="form-control w-full"
                    value="{{ old('quantity_received', $indent->quantity_received) }}" required min="0">
                @error('quantity_received')
                    <small class="text-red-600">{{ $message }}</small>
                @enderror
            </div>

            <!-- Quantity Balance -->
            <div class="w-full col-span-1">
                <label for="quantity_balance" class="form-label text-black block mb-1">Quantity Balance</label>
                <input type="number" name="quantity_balance" id="quantity_balance" class="form-control w-full"
                    value="{{ old('quantity_balance', $indent->quantity_balance) }}" required readonly>
                @error('quantity_balance')
                    <small class="text-red-600">{{ $message }}</small>
                @enderror
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex justify-end">
            <button type="submit" class="ti-btn ti-btn-primary-full">
                Update Indent
            </button>
        </div>
    </form>
</div>

<!-- Quantity Balance Script -->
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const qtyRequired = document.getElementById('quantity_required');
        const qtyReceived = document.getElementById('quantity_received');
        const qtyBalance = document.getElementById('quantity_balance');

        function updateBalance() {
            let required = parseFloat(qtyRequired.value) || 0;
            let received = parseFloat(qtyReceived.value) || 0;

            if (required < 0) {
                required = 0;
                qtyRequired.value = 0;
            }
            if (received < 0) {
                received = 0;
                qtyReceived.value = 0;
            }

            const balance = Math.max(required - received, 0);
            qtyBalance.value = balance;
        }

        qtyRequired.addEventListener('input', updateBalance);
        qtyReceived.addEventListener('input', updateBalance);

        [qtyRequired, qtyReceived].forEach(field => {
            field.addEventListener('keypress', (e) => {
                if (e.key === '-' || e.key === '+') {
                    e.preventDefault();
                }
            });
        });

        updateBalance();
    });
</script>
