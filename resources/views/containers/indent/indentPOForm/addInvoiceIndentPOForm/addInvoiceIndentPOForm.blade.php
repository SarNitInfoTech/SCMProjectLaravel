<div class="w-full px-4 py-6 bg-white shadow rounded">
    <form method="POST" action="{{ route('po-register.store') }}" class="grid grid-cols-1 gap-6">
        @csrf

        <input type="hidden" name="indent_id" value="{{ $indent_id }}">
        <input type="hidden" name="department_id" value="{{ $department_id}}">

        <div class="grid grid-cols-4 gap-6">
            <div class="w-full col-span-1">
                <label class="form-label text-black block mb-1">Indent ID <span class="text-red-500">*</span></label>
                <input type="text" class="form-control w-full bg-gray-100" value="{{ $indent_id }}" readonly disabled>
            </div>
            <div class="w-full col-span-1">
                <label class="form-label text-black block mb-1">Department <span class="text-red-500">*</span></label>
                <input type="text" class="form-control w-full bg-gray-100" value="{{ $department_id}}" readonly
                    disabled>
            </div>
            <div class="w-full col-span-1">
                <label for="status" class="form-label text-black block mb-1">Status <span class="text-red-500">*</span></label>
                <select name="status" id="status" class="form-control w-full bg-gray-100 cursor-not-allowed" required
                    disabled>
                    <option value="Pending" selected>Pending</option>
                </select>
                <input type="hidden" name="status" value="Pending">
            </div>
            <div class="w-full col-span-1">
                <label for="po_date" class="form-label text-black block mb-1">PO Date <span class="text-red-500">*</span></label>
                <input type="date" name="po_date" id="po_date" class="form-control w-full" required>
            </div>
        </div>

        <div class="grid grid-cols-4 gap-6">
            <div class="w-full col-span-1">
                <label for="party_name" class="form-label text-black block mb-1">Party Name <span class="text-red-500">*</span></label>
                <select name="party_name" id="party_name" class="form-control w-full" required>
                    <option value="">Select party</option>
                    @foreach ($projectList as $vendor)
                        <option value="{{ $vendor->name }}">{{ $vendor->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="w-full col-span-1">
                <label for="po_wo_no" class="form-label text-black block mb-1">PO/WO No. <span class="text-red-500">*</span></label>
                <input type="text" name="po_wo_no" id="po_wo_no" class="form-control w-full" required>
            </div>
            <div class="w-full col-span-1">
                <label for="po_amount" class="form-label text-black block mb-1">PO Amount <span class="text-red-500">*</span></label>
                <input
  type="number"
  name="po_amount"
  id="po_amount"
  class="form-control w-full"
  step="1"
  min="0"
  inputmode="decimal"
  required
  onkeydown="if (['e','E','+','-'].includes(event.key)) event.preventDefault();"
  oninput="
    this.value = this.value.replace(/[^0-9.]/g,'');
    this.value = this.value.replace(/(\..*)\./g,'$1');
    const p = this.value.split('.');
    if (p[1]) p[1] = p[1].slice(0,2);
    this.value = p.join('.');
    if (this.value.startsWith('.')) this.value = '0' + this.value;
  "
/>

            </div>
            {{-- <div class="w-full col-span-1">
                <label for="debit_head" class="form-label text-black block mb-1">Debit Head</label>
                <select name="debit_head" id="debit_head" class="form-control w-full" required>
                    <option value="" disabled selected>Select Debit Head</option>
                    @foreach ($departmentHeads as $head)
                        <option value="{{ $head->id }}">{{ $head->department_head }}</option>
                    @endforeach
                </select>
            </div> --}}

     

        <div class="w-full col-span-1">
            <label for="item_description" class="form-label text-black block mb-1">Item Description <span class="text-red-500">*</span></label>
            <select class="ti-form-select rounded-sm !py-2 !px-3 choices-multiple-remove" name="item_description[]"
                id="item_description" multiple required>
                @foreach ($items as $item)
                    <option value="{{ $item['description'] }}">{{ $item['description'] }}</option>
                @endforeach
            </select>
        </div>
    </div>

        <div class="grid grid-cols-4 gap-6">
            <div class="w-full col-span-1">
                <label for="expected_date" class="form-label text-black block mb-1">Expected Date <span class="text-red-500">*</span></label>
                <input type="date" name="expected_date" id="expected_date" class="form-control w-full">
            </div>
            <div class="w-full col-span-1">
                <label for="expected_days" class="form-label text-black block mb-1">Expected Days</label>
                <input type="text" id="expected_days" class="form-control w-full bg-gray-100" readonly disabled>
                <input type="hidden" name="expected_days" id="expected_days_hidden">
            </div>
            
        </div>


        <div class="flex justify-end">
            <button type="submit" class="ti-btn ti-btn-primary-full">Submit PO</button>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const poDateInput = document.getElementById('po_date');
        const expectedDateInput = document.getElementById('expected_date');
        const receivingDateInput = document.getElementById('receiving_date');

        const expectedDaysDisplay = document.getElementById('expected_days');
        const expectedDaysHidden = document.getElementById('expected_days_hidden');
        const delayDaysInput = document.getElementById('delay_in_days');

        function calculateExpectedDays() {
            const poDate = new Date(poDateInput.value);
            const expectedDate = new Date(expectedDateInput.value);

            if (!isNaN(poDate) && !isNaN(expectedDate)) {
                const days = Math.round((expectedDate - poDate) / (1000 * 60 * 60 * 24));
                expectedDaysDisplay.value = days;
                expectedDaysHidden.value = days;
            } else {
                expectedDaysDisplay.value = '';
                expectedDaysHidden.value = '';
            }
        }

        function calculateDelayDays() {
            const expectedDate = new Date(expectedDateInput.value);
            const receivingDate = new Date(receivingDateInput.value);

            if (!isNaN(expectedDate) && !isNaN(receivingDate)) {
                const delay = Math.round((receivingDate - expectedDate) / (1000 * 60 * 60 * 24));
                delayDaysInput.value = delay >= 0 ? delay : 0;
            } else {
                delayDaysInput.value = '';
            }
        }

        poDateInput.addEventListener('change', () => {
            calculateExpectedDays();
            calculateDelayDays(); // recalculate delay in case expected date is changed too
        });

        expectedDateInput.addEventListener('change', () => {
            calculateExpectedDays();
            calculateDelayDays();
        });

        receivingDateInput.addEventListener('change', calculateDelayDays);
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        new Choices('#item_description', {
            removeItemButton: true,
            placeholderValue: 'Select item(s)',
            searchPlaceholderValue: 'Search items...',
        });
    });
</script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>