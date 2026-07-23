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
                <input type="text" class="form-control w-full bg-gray-100" value="{{ $department_id}}" readonly disabled>
            </div>
            <div class="w-full col-span-1">
                <label for="is_mandatory" class="form-label text-black block mb-1">Requirement Type <span class="text-red-500">*</span></label>
                <select name="is_mandatory" id="is_mandatory" class="form-control w-full">
                    <option value="Mandatory" {{ old('is_mandatory', 'Mandatory') === 'Mandatory' ? 'selected' : '' }}>Mandatory</option>
                    <option value="Non-Mandatory" {{ old('is_mandatory') === 'Non-Mandatory' ? 'selected' : '' }}>Non-Mandatory</option>
                </select>
            </div>
            <div class="w-full col-span-1">
                <label for="status" class="form-label text-black block mb-1">Status <span class="text-red-500">*</span></label>
                <select name="status" id="status" class="form-control w-full bg-gray-100 cursor-not-allowed" required disabled>
                    <option value="Pending" selected>Pending</option>
                </select>
                <input type="hidden" name="status" value="Pending">
            </div>
        </div>

        <div class="grid grid-cols-4 gap-6">
            <div class="w-full col-span-1">
                <label for="po_date" class="form-label text-black block mb-1">PO Date <span class="text-red-500 required-asterisk">*</span></label>
                <input type="date" name="po_date" id="po_date" class="form-control w-full po-required-field" required>
            </div>
            <div class="w-full col-span-1">
                <label for="party_name" class="form-label text-black block mb-1">Party Name <span class="text-red-500 required-asterisk">*</span></label>
                <select name="party_name" id="party_name" class="form-control w-full po-required-field" required>
                    <option value="">Select party</option>
                    @foreach ($projectList as $vendor)
                        <option value="{{ $vendor->name }}">{{ $vendor->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="w-full col-span-1">
                <label for="po_wo_no" class="form-label text-black block mb-1">PO/WO No. <span class="text-red-500 required-asterisk">*</span></label>
                <input type="text" name="po_wo_no" id="po_wo_no" class="form-control w-full po-required-field" required>
            </div>
            <div class="w-full col-span-1">
                <label for="po_amount" class="form-label text-black block mb-1">PO Amount <span class="text-red-500 required-asterisk">*</span></label>
                <input
                    type="number"
                    name="po_amount"
                    id="po_amount"
                    class="form-control w-full po-required-field"
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
        </div>

        <div class="grid grid-cols-4 gap-6">
            <div class="w-full col-span-1">
                <label for="item_description" class="form-label text-black block mb-1">Item Description <span class="text-red-500 required-asterisk">*</span></label>
                <select class="ti-form-select rounded-sm !py-2 !px-3 choices-multiple-remove po-required-field" name="item_description[]"
                    id="item_description" multiple required>
                    @foreach ($items as $item)
                        @php
                            $req = $item['quantity_required'] ?? 0;
                            $rec = $item['quantity_received'] ?? 0;
                            $bal = $item['quantity_balance'] ?? max(0, $req - $rec);
                            $label = $item['description'] . ($req > 0 ? " (Req: {$req}, Rec: {$rec}, Rem: {$bal})" : '');
                        @endphp
                        <option value="{{ $item['description'] }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="w-full col-span-1">
                <label for="expected_date" class="form-label text-black block mb-1">Expected Date <span class="text-red-500 required-asterisk">*</span></label>
                <input type="date" name="expected_date" id="expected_date" class="form-control w-full po-required-field" required>
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
            if (!poDateInput || !expectedDateInput) return;
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
            if (!expectedDateInput || !receivingDateInput) return;
            const expectedDate = new Date(expectedDateInput.value);
            const receivingDate = new Date(receivingDateInput.value);

            if (!isNaN(expectedDate) && !isNaN(receivingDate)) {
                const delay = Math.round((receivingDate - expectedDate) / (1000 * 60 * 60 * 24));
                if (delayDaysInput) delayDaysInput.value = delay >= 0 ? delay : 0;
            } else {
                if (delayDaysInput) delayDaysInput.value = '';
            }
        }

        poDateInput?.addEventListener('change', () => {
            calculateExpectedDays();
            calculateDelayDays();
        });

        expectedDateInput?.addEventListener('change', () => {
            calculateExpectedDays();
            calculateDelayDays();
        });

        receivingDateInput?.addEventListener('change', calculateDelayDays);

        // Toggle Mandatory / Non-Mandatory requirement fields
        const mandatorySelect = document.getElementById('is_mandatory');

        function updateRequiredState() {
            const isMandatory = mandatorySelect ? mandatorySelect.value === 'Mandatory' : true;
            const requiredFields = document.querySelectorAll('.po-required-field');
            const asterisks = document.querySelectorAll('.required-asterisk');

            requiredFields.forEach(field => {
                if (isMandatory) {
                    field.setAttribute('required', 'required');
                } else {
                    field.removeAttribute('required');
                }
            });

            asterisks.forEach(asterisk => {
                if (isMandatory) {
                    asterisk.style.display = 'inline';
                } else {
                    asterisk.style.display = 'none';
                }
            });
        }

        if (mandatorySelect) {
            mandatorySelect.addEventListener('change', updateRequiredState);
            updateRequiredState();
        }
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