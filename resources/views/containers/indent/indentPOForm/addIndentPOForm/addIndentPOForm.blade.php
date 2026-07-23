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
                            $req = (int)($item['quantity_required'] ?? 0);
                            $filed = (int)($item['already_filed'] ?? 0);
                            $rem = (int)($item['remaining_to_file'] ?? max(0, $req - $filed));
                            $label = $item['description'] . ($req > 0 ? " (Req: {$req}, Filed: {$filed}, Rem: {$rem})" : '');
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
            <div class="w-full col-span-1">
                <label for="remarks" class="form-label text-black block mb-1">Remarks</label>
                <textarea name="remarks" id="remarks" rows="1" class="form-control w-full" placeholder="Enter PO remarks (optional)...">{{ old('remarks') }}</textarea>
            </div>
        </div>

        @if(!empty($items) && count($items) > 0)
        <div id="po-items-table-container" class="w-full col-span-full border rounded p-4 bg-gray-50 hidden">
            <label class="form-label text-black block mb-2 font-semibold text-base">
                Item Description & Filing Quantity (Count) Breakdown <span class="text-red-500 required-asterisk">*</span>
            </label>
            <div class="overflow-x-auto">
                <table class="table min-w-full bg-white border text-sm">
                    <thead>
                        <tr class="bg-gray-100 border-b text-gray-700">
                            <th class="p-2 text-center w-12">Select</th>
                            <th class="p-2 text-start">Item Description</th>
                            <th class="p-2 text-center">Unit</th>
                            <th class="p-2 text-center">Qty Required</th>
                            <th class="p-2 text-center">Previously Filed</th>
                            <th class="p-2 text-center w-36">Filing PO Qty (Count)</th>
                            <th class="p-2 text-center">Remaining to File</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($items as $idx => $item)
                            @php
                                $req = (int)($item['quantity_required'] ?? 1);
                                $rec = (int)($item['quantity_received'] ?? 0);
                                $filed = (int)($item['already_filed'] ?? 0);
                                $rem = (int)($item['remaining_to_file'] ?? max(0, $req - $filed));
                                $defaultFiling = $rem > 0 ? $rem : $req;
                            @endphp
                            <tr class="border-b po-item-row" data-item-desc="{{ $item['description'] }}" style="display: none;">
                                <td class="p-2 text-center">
                                    <input type="checkbox" name="po_items[{{ $idx }}][selected]" value="1" class="form-checkbox h-4 w-4 text-indigo-600 po-item-check">
                                </td>
                                <td class="p-2 font-medium">
                                    {{ $item['description'] }}
                                    <input type="hidden" name="po_items[{{ $idx }}][description]" value="{{ $item['description'] }}">
                                    <input type="hidden" name="po_items[{{ $idx }}][unit]" value="{{ $item['unit'] ?? '' }}">
                                    <input type="hidden" name="po_items[{{ $idx }}][quantity_required]" value="{{ $req }}" class="js-po-req">
                                    <input type="hidden" name="po_items[{{ $idx }}][quantity_received]" value="{{ $rec }}" class="js-po-rec">
                                    <input type="hidden" name="po_items[{{ $idx }}][already_filed]" value="{{ $filed }}" class="js-po-filed">
                                </td>
                                <td class="p-2 text-center">{{ $item['unit'] ?? '-' }}</td>
                                <td class="p-2 text-center font-semibold">{{ $req }}</td>
                                <td class="p-2 text-center text-blue-600 font-semibold">{{ $filed }}</td>
                                <td class="p-2 text-center">
                                    <input type="number" 
                                           name="po_items[{{ $idx }}][po_quantity]" 
                                           value="{{ $defaultFiling }}" 
                                           min="1" 
                                           max="{{ $rem }}"
                                           class="form-control text-center js-po-qty w-full po-required-field" disabled required>
                                </td>
                                <td class="p-2 text-center">
                                    <span class="js-po-rem font-bold {{ max(0, $rem - $defaultFiling) > 0 ? 'text-orange-600' : 'text-green-600' }}">
                                        {{ max(0, $rem - $defaultFiling) > 0 ? max(0, $rem - $defaultFiling) . ' remaining' : '0 (Fully Filed)' }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

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

        // Live calculation of PO item remaining to file
        document.querySelectorAll('.po-item-row').forEach(row => {
            const req = parseInt(row.querySelector('.js-po-req')?.value || 0, 10);
            const filed = parseInt(row.querySelector('.js-po-filed')?.value || 0, 10);
            const qtyInput = row.querySelector('.js-po-qty');
            const remSpan = row.querySelector('.js-po-rem');

            function updateRem() {
                const filing = parseInt(qtyInput?.value || 0, 10);
                const rem = Math.max(0, req - (filed + filing));
                if (remSpan) {
                    if (rem > 0) {
                        remSpan.textContent = rem + ' remaining';
                        remSpan.className = 'js-po-rem font-bold text-orange-600';
                    } else {
                        remSpan.textContent = '0 (Fully Filed)';
                        remSpan.className = 'js-po-rem font-bold text-green-600';
                    }
                }
            }

            qtyInput?.addEventListener('input', updateRem);
        });

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

        // Show breakdown table ONLY for items selected in item_description dropdown
        const itemSelect = document.getElementById('item_description');
        const tableContainer = document.getElementById('po-items-table-container');

        function syncTableWithSelection() {
            if (!itemSelect) return;
            const selectedValues = Array.from(itemSelect.selectedOptions).map(opt => opt.value);
            const rows = document.querySelectorAll('.po-item-row');
            let anyVisible = false;

            rows.forEach(row => {
                const desc = row.getAttribute('data-item-desc');
                const check = row.querySelector('.po-item-check');
                const qtyInput = row.querySelector('.js-po-qty');

                if (selectedValues.includes(desc)) {
                    row.style.display = '';
                    if (check) check.checked = true;
                    if (qtyInput) qtyInput.removeAttribute('disabled');
                    anyVisible = true;
                } else {
                    row.style.display = 'none';
                    if (check) check.checked = false;
                    if (qtyInput) qtyInput.setAttribute('disabled', 'disabled');
                }
            });

            if (tableContainer) {
                if (anyVisible) {
                    tableContainer.classList.remove('hidden');
                } else {
                    tableContainer.classList.add('hidden');
                }
            }
        }

        itemSelect?.addEventListener('change', syncTableWithSelection);
        itemSelect?.addEventListener('addItem', syncTableWithSelection);
        itemSelect?.addEventListener('removeItem', syncTableWithSelection);

        syncTableWithSelection();
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