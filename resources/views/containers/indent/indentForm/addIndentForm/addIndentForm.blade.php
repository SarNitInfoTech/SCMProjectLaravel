<div class="w-full px-4 py-6 bg-white shadow rounded">
    <form method="POST" action="{{ route('indent-register.store') }}" class="grid grid-cols-1 gap-6">
        @csrf

        <!-- Hidden inputs (actual data submission) -->
        <input type="hidden" name="indent_id" value="{{ $indent_id }}">
        <input type="hidden" name="indent_department" value="{{ $department_id }}">

        <!-- Top Info Row -->
        <div class="grid grid-cols-4 gap-6">
            <!-- Indent Ticket ID (Display Only) -->
            <div class="w-full col-span-1">
                <label class="form-label text-black block mb-1">Indent Ticket ID</label>
                <input type="text" id="indent_id_display" class="form-control w-full bg-gray-100" value="{{$indent_id}}"
                    readonly disabled>
            </div>

            <!-- Department (Display Only) -->
            <div class="w-full col-span-1">
                <label class="form-label text-black block mb-1">Department</label>
                <input type="text" id="department_display" class="form-control w-full bg-gray-100"
                    value="{{ $department_name }}" readonly disabled>
            </div>

            <!-- Indent Date -->
            <div class="w-full col-span-1">
                <label for="indent_date" class="form-label text-black block mb-1">Indent Date</label>
                <input type="date" name="indent_date" id="indent_date" class="form-control w-full" required>
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
                        <option value="{{ $project->id }}" {{ old('indent_project') == $project->id ? 'selected' : '' }}>
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
                required></textarea>
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
                        <option value="{{ $unit->id }}" {{ old('unit') == $unit->id ? 'selected' : '' }}>
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
                    value="0" required min="0">
                @error('quantity_required')
                    <small class="text-red-600">{{ $message }}</small>
                @enderror
            </div>

            <!-- Quantity Received -->
            <div class="w-full col-span-1">
                <label for="quantity_received" class="form-label text-black block mb-1">Quantity Received</label>
                <input type="number" name="quantity_received" id="quantity_received" class="form-control w-full"
                    value="0" required min="0">
                @error('quantity_received')
                    <small class="text-red-600">{{ $message }}</small>
                @enderror
            </div>

            <!-- Quantity Balance -->
            <div class="w-full col-span-1">
                <label for="quantity_balance" class="form-label text-black block mb-1">Quantity Balance</label>
                <input type="number" name="quantity_balance" id="quantity_balance" class="form-control w-full" value="0"
                    required readonly>
                @error('quantity_balance')
                    <small class="text-red-600">{{ $message }}</small>
                @enderror
            </div>

        </div>

        {{-- <!-- Purchased Order -->
        <div class="w-full">
            <label for="purchased_order" class="form-label text-black block mb-1">Purchased Order</label>
            <textarea name="purchased_order" id="purchased_order" class="form-control w-full" rows="2"></textarea>
            @error('purchased_order')
            <small class="text-red-600">{{ $message }}</small>
            @enderror
        </div> --}}

        <!-- Submit Button -->
        <div class="flex justify-end">
            <button type="submit" class="ti-btn ti-btn-primary-full">
                Submit Indent
            </button>
        </div>
    </form>
</div>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const qtyRequired = document.getElementById('quantity_required');
        const qtyReceived = document.getElementById('quantity_received');
        const qtyBalance = document.getElementById('quantity_balance');

        function updateBalance() {
            let required = parseFloat(qtyRequired.value) || 0;
            let received = parseFloat(qtyReceived.value) || 0;

            // Block negative entries
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

        // Update balance on input
        qtyRequired.addEventListener('input', updateBalance);
        qtyReceived.addEventListener('input', updateBalance);

        // Prevent typing - or + in the fields
        [qtyRequired, qtyReceived].forEach(field => {
            field.addEventListener('keypress', (e) => {
                if (e.key === '-' || e.key === '+') {
                    e.preventDefault();
                }
            });
        });

        // Initial calculation
        updateBalance();
    });
</script>
