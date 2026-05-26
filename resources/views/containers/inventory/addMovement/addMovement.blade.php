<div class="w-full px-4 py-6 bg-white shadow rounded">
    <div class="mb-4 pb-2 border-b">
        <h3 class="text-lg font-semibold text-gray-800">Record Stock Transaction</h3>
        <p class="text-sm text-gray-500">Log stock increment, consumption, adjustments, returns, or department transfers.</p>
    </div>

    @if (session('warning'))
        <div class="mb-4 p-3 bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 text-sm">
            {{ session('warning') }}
        </div>
    @endif

    <form method="POST" action="{{ route('inventory.stocks.movements.store', $stock->id) }}" class="space-y-6">
        @csrf

        {{-- Stock Info Panel --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 bg-gray-50 p-4 rounded border">
            <div>
                <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Item Name</label>
                <span class="text-base font-bold text-gray-900">{{ $stock->item?->name }}</span>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Department</label>
                <span class="text-base font-medium text-gray-800">{{ $stock->department?->name }}</span>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Current Qty</label>
                <span class="text-base font-bold text-gray-900">{{ number_format($stock->current_qty, 2) }} {{ $stock->unit?->name }}</span>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Location</label>
                <span class="text-base text-gray-700">{{ $stock->location ?? '—' }}</span>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Movement Type -->
            <div>
                <label for="type" class="form-label text-gray-700 block mb-1 font-semibold">
                    Transaction Type <span class="text-red-500">*</span>
                </label>
                <select name="type" id="type" class="form-control w-full rounded border border-gray-300 p-2.5 bg-white text-black font-normal" required>
                    <option value="">-- Select Type --</option>
                    @foreach($types as $type)
                        <option value="{{ $type->value }}" {{ old('type') == $type->value ? 'selected' : '' }}>
                            {{ $type->name }}
                        </option>
                    @endforeach
                </select>
                @error('type')
                    <small class="text-red-600">{{ $message }}</small>
                @enderror
            </div>

            <!-- Quantity -->
            <div>
                <label for="quantity" class="form-label text-gray-700 block mb-1 font-semibold">
                    Quantity <span class="text-red-500">*</span>
                </label>
                <input
                    type="number"
                    step="0.0001"
                    name="quantity"
                    id="quantity"
                    value="{{ old('quantity') }}"
                    class="form-control w-full rounded border border-gray-300 p-2.5 text-black font-normal"
                    placeholder="Enter quantity amount"
                    required
                >
                @error('quantity')
                    <small class="text-red-600">{{ $message }}</small>
                @enderror
            </div>

            <!-- Date -->
            <div>
                <label for="movement_date" class="form-label text-gray-700 block mb-1 font-semibold">
                    Date of Transaction <span class="text-red-500">*</span>
                </label>
                <input
                    type="date"
                    name="movement_date"
                    id="movement_date"
                    value="{{ old('movement_date', date('Y-m-d')) }}"
                    class="form-control w-full rounded border border-gray-300 p-2.5 text-black font-normal"
                    required
                >
                @error('movement_date')
                    <small class="text-red-600">{{ $message }}</small>
                @enderror
            </div>
        </div>

        {{-- Conditional Inputs based on Transaction Type --}}
        
        <!-- IN fields: Vendor & PO -->
        <div id="in-fields" class="grid grid-cols-1 md:grid-cols-2 gap-6 hidden">
            <div>
                <label for="vendor_id" class="form-label text-gray-700 block mb-1 font-semibold">Supplier/Vendor</label>
                <select name="vendor_id" id="vendor_id" class="form-control w-full rounded border border-gray-300 p-2.5 bg-white text-black font-normal">
                    <option value="">-- Choose Supplier (Optional) --</option>
                    @foreach($vendors as $vendor)
                        <option value="{{ $vendor->id }}" {{ old('vendor_id') == $vendor->id ? 'selected' : '' }}>
                            {{ $vendor->name }}
                        </option>
                    @endforeach
                </select>
                @error('vendor_id')
                    <small class="text-red-600">{{ $message }}</small>
                @enderror
            </div>

            <div>
                <label for="po_register_id" class="form-label text-gray-700 block mb-1 font-semibold">Purchase Order (PO)</label>
                <select name="po_register_id" id="po_register_id" class="form-control w-full rounded border border-gray-300 p-2.5 bg-white text-black font-normal">
                    <option value="">-- Link to PO (Optional) --</option>
                    @foreach($pos as $po)
                        <option value="{{ $po->id }}" {{ old('po_register_id') == $po->id ? 'selected' : '' }}>
                            Indent ID: {{ $po->indent_id }} ({{ $po->party_name }} - {{ number_format($po->po_amount, 2) }})
                        </option>
                    @endforeach
                </select>
                @error('po_register_id')
                    <small class="text-red-600">{{ $message }}</small>
                @enderror
            </div>
        </div>

        <!-- TRANSFER fields: Destination Department -->
        <div id="transfer-fields" class="hidden">
            <div class="w-full md:w-1/3">
                <label for="destination_department_id" class="form-label text-gray-700 block mb-1 font-semibold">
                    Destination Department <span class="text-red-500">*</span>
                </label>
                <select name="destination_department_id" id="destination_department_id" class="form-control w-full rounded border border-gray-300 p-2.5 bg-white text-black font-normal">
                    <option value="">-- Select Destination Department --</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}" {{ old('destination_department_id') == $dept->id ? 'selected' : '' }}>
                            {{ $dept->name }}
                        </option>
                    @endforeach
                </select>
                @error('destination_department_id')
                    <small class="text-red-600">{{ $message }}</small>
                @enderror
            </div>
        </div>

        <!-- ADJUST fields: Adjustment direction -->
        <div id="adjust-fields" class="hidden">
            <div class="flex items-center gap-3 p-3 bg-blue-50 border border-blue-200 rounded md:w-1/3">
                <input type="checkbox" name="adjust_decrease" id="adjust_decrease" value="1" {{ old('adjust_decrease') ? 'checked' : '' }} class="h-4 w-4 text-blue-600 rounded">
                <label for="adjust_decrease" class="text-sm font-semibold text-blue-900 cursor-pointer">
                    Decrease Stock Balance (Deduct)
                </label>
                <p class="text-xs text-blue-700 block mt-0.5">(Leave unchecked to increase stock balance)</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Reference No -->
            <div>
                <label for="reference_no" class="form-label text-gray-700 block mb-1 font-semibold">
                    Reference Document / Challan No
                </label>
                <input
                    type="text"
                    name="reference_no"
                    id="reference_no"
                    value="{{ old('reference_no') }}"
                    class="form-control w-full rounded border border-gray-300 p-2.5 text-black font-normal"
                    placeholder="e.g. Invoice-1002, Challan-550"
                >
                @error('reference_no')
                    <small class="text-red-600">{{ $message }}</small>
                @enderror
            </div>

            <!-- Remarks -->
            <div>
                <label for="remarks" class="form-label text-gray-700 block mb-1 font-semibold">Remarks</label>
                <textarea
                    name="remarks"
                    id="remarks"
                    rows="2"
                    class="form-control w-full rounded border border-gray-300 p-2 text-black font-normal"
                    placeholder="Provide additional details or reasons"
                >{{ old('remarks') }}</textarea>
                @error('remarks')
                    <small class="text-red-600">{{ $message }}</small>
                @enderror
            </div>
        </div>

        <div class="flex justify-end gap-3 pt-4 border-t">
            <a href="{{ route('inventory.stocks.movements', $stock->id) }}" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded transition-all">
                Cancel
            </a>
            <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded shadow transition-all">
                Submit Transaction
            </button>
        </div>
    </form>
</div>

<!-- Type Toggling Javascript -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const typeSelect = document.getElementById('type');
        const inFields = document.getElementById('in-fields');
        const transferFields = document.getElementById('transfer-fields');
        const adjustFields = document.getElementById('adjust-fields');

        const destDept = document.getElementById('destination_department_id');

        function toggleFields() {
            const val = typeSelect.value;
            
            // Reset visibility
            inFields.classList.add('hidden');
            transferFields.classList.add('hidden');
            adjustFields.classList.add('hidden');
            
            destDept.required = false;

            if (val === 'IN') {
                inFields.classList.remove('hidden');
            } else if (val === 'TRANSFER') {
                transferFields.classList.remove('hidden');
                destDept.required = true;
            } else if (val === 'ADJUST') {
                adjustFields.classList.remove('hidden');
            }
        }

        typeSelect.addEventListener('change', toggleFields);
        toggleFields(); // Initial call on page load
    });
</script>
