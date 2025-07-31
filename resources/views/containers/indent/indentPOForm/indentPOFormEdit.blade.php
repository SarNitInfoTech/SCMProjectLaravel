<div class="w-full px-4 py-6 bg-white shadow rounded">
    <form method="POST" action="{{ route('po-register.update', $po->id) }}" class="grid grid-cols-1 gap-6">
        @csrf
        @method('PUT')

        <input type="hidden" name="indent_id" value="{{ $po->indent_id }}">
        <input type="hidden" name="department_id" value="{{ $po->department_id }}">
        <input type="hidden" name="expected_days" id="expected_days_hidden" value="{{ $po->expected_days }}">

        <div class="grid grid-cols-4 gap-6">
            <div class="w-full col-span-1">
                <label class="form-label text-black block mb-1">Indent ID</label>
                <input type="text" class="form-control w-full bg-gray-100" value="{{ $po->indent_id }}" readonly disabled>
            </div>
            <div class="w-full col-span-1">
                <label class="form-label text-black block mb-1">Department</label>
                <input type="text" class="form-control w-full bg-gray-100" value="{{ $department_name }}" readonly disabled>
            </div>
            <div class="w-full col-span-1">
                <label for="po_date" class="form-label text-black block mb-1">PO Date</label>
                <input type="date" name="po_date" id="po_date" class="form-control w-full" value="{{ $po->po_date }}" required>
            </div>
            <div class="w-full col-span-1">
                <label for="status" class="form-label text-black block mb-1">Status</label>
                <select name="status" id="status" class="form-control w-full" required>
                    @foreach ($statusList as $status)
                        <option value="{{ $status }}" {{ $po->status === $status ? 'selected' : '' }}>{{ $status }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="grid grid-cols-4 gap-6">
            <div class="w-full col-span-1">
                <label for="party_name" class="form-label text-black block mb-1">Party Name</label>
                <input type="text" name="party_name" id="party_name" class="form-control w-full" value="{{ $po->party_name }}" required>
            </div>
            <div class="w-full col-span-1">
                <label for="po_wo_no" class="form-label text-black block mb-1">PO/WO No.</label>
                <input type="text" name="po_wo_no" id="po_wo_no" class="form-control w-full" value="{{ $po->po_wo_no }}" required>
            </div>
            <div class="w-full col-span-1">
                <label for="po_amount" class="form-label text-black block mb-1">PO Amount</label>
                <input type="number" name="po_amount" id="po_amount" class="form-control w-full" step="0.01" value="{{ $po->po_amount }}" required>
            </div>
            <div class="w-full col-span-1">
                <label for="debit_head" class="form-label text-black block mb-1">Debit Head</label>
                <input type="text" name="debit_head" id="debit_head" class="form-control w-full" value="{{ $po->debit_head }}">
            </div>
        </div>

        <div class="w-full">
            <label for="item_description" class="form-label text-black block mb-1">Item Description</label>
            <textarea name="item_description" id="item_description" class="form-control w-full" rows="3" required>{{ $po->item_description }}</textarea>
        </div>

        <div class="grid grid-cols-4 gap-6">
            <div class="w-full col-span-1">
                <label for="expected_days" class="form-label text-black block mb-1">Expected Days</label>
                <input type="text" id="expected_days" class="form-control w-full bg-gray-100" value="{{ $po->expected_days }}" readonly disabled>
            </div>
            <div class="w-full col-span-1">
                <label for="expected_date" class="form-label text-black block mb-1">Expected Date</label>
                <input type="date" name="expected_date" id="expected_date" class="form-control w-full" value="{{ $po->expected_date }}">
            </div>
            <div class="w-full col-span-1">
                <label for="invoice_date" class="form-label text-black block mb-1">Invoice Date</label>
                <input type="date" name="invoice_date" id="invoice_date" class="form-control w-full" value="{{ $po->invoice_date }}">
            </div>
            <div class="w-full col-span-1">
                <label for="receiving_date" class="form-label text-black block mb-1">Receiving Date</label>
                <input type="text" name="receiving_date" id="receiving_date" class="form-control w-full" value="{{ $po->receiving_date }}">
            </div>
        </div>

        <div class="grid grid-cols-4 gap-6">
            <div class="w-full col-span-2">
                <label for="invoice" class="form-label text-black block mb-1">Invoice Number</label>
                <input type="text" name="invoice" id="invoice" class="form-control w-full" value="{{ $po->invoice }}">
            </div>
            <div class="w-full col-span-1">
                <label for="delay_in_days" class="form-label text-black block mb-1">Delay (Days)</label>
                <input type="number" name="delay_in_days" id="delay_in_days" class="form-control w-full" value="{{ $po->delay_in_days }}">
            </div>
            <div class="w-full col-span-1">
                <label for="store_indent_no" class="form-label text-black block mb-1">Store Indent No.</label>
                <input type="text" name="store_indent_no" id="store_indent_no" class="form-control w-full" value="{{ $po->store_indent_no }}">
            </div>
        </div>

        <div class="w-full">
            <label for="remarks" class="form-label text-black block mb-1">Remarks</label>
            <textarea name="remarks" id="remarks" class="form-control w-full" rows="2">{{ $po->remarks }}</textarea>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="ti-btn ti-btn-primary-full">Update PO</button>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const poDateInput = document.getElementById('po_date');
        const expectedDateInput = document.getElementById('expected_date');
        const expectedDaysDisplay = document.getElementById('expected_days');
        const expectedDaysHidden = document.getElementById('expected_days_hidden');

        function calculateExpectedDays() {
            const poDate = new Date(poDateInput.value);
            const expectedDate = new Date(expectedDateInput.value);

            if (!isNaN(poDate.getTime()) && !isNaN(expectedDate.getTime())) {
                const diffTime = expectedDate - poDate;
                const diffDays = Math.round(diffTime / (1000 * 60 * 60 * 24));
                expectedDaysDisplay.value = diffDays;
                expectedDaysHidden.value = diffDays;
            }
        }

        poDateInput.addEventListener('change', calculateExpectedDays);
        expectedDateInput.addEventListener('change', calculateExpectedDays);

        // Trigger once to fill values if both dates are already set
        calculateExpectedDays();
    });
</script>
