<div class="w-full px-4 py-6 bg-white shadow rounded relative">
    <!-- Top Header Section -->
    <form method="POST" action="{{ route('indent-register.store') }}" class="grid gap-6">
        @csrf
        <div class="sticky top-0 z-10 bg-white grid grid-cols-12 gap-4 border-b pb-4 mb-6">
            <div class="col-span-3">
                <label class="form-label text-black block mb-1">Indent Ticket ID <span
                        class="text-red-500">*</span></label>
                <input type="text" class="form-control w-full bg-gray-100" value="{{ $indent_id }}" readonly disabled>
            </div>
            <div class="col-span-3">
                <label class="form-label text-black block mb-1">Department <span class="text-red-500">*</span></label>
                <input type="text" class="form-control w-full bg-gray-100" value="{{ $department_name }}" readonly
                    disabled>
            </div>
            <div class="col-span-3">
                <label for="indent_date" class="form-label text-black block mb-1">Indent Date <span
                        class="text-red-500">*</span></label>
                <input type="date" name="indent_date" id="indent_date" class="form-control w-full" required>
            </div>
            <div class="col-span-3">
                <label for="indent_project" class="form-label block mb-1">
                    Indent Project
                </label>
                <select name="indent_project" id="indent_project" class="form-control choices-js w-full">
                    <option value="">-- Select Project --</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->name }}" {{ old('indent_project') == $project->name ? 'selected' : '' }}>
                            {{ $project->name }}
                        </option>
                    @endforeach
                </select>

            </div>
        </div>

        <!-- Form Start -->

        <input type="hidden" name="indent_id" value="{{ $indent_id }}">
        <input type="hidden" name="indent_department" value="{{ $department_name }}">

        <!-- Item Rows Container -->
        <div id="items-container" class="space-y-4">
            <div class="item-row grid grid-cols-12 gap-4 bg-gray-50 p-4 rounded relative">
                <div class="col-span-4">
                    <label class="form-label block mb-1">Item Description <span class="text-red-500">*</span></label>
                    <select name="items[0][description]" class="form-control choices-js" required>
                        <option value="">-- Select Item --</option>
                        @foreach($items as $item)
                            <option value="{{ $item->name }}">{{ $item->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-span-2">
                    <label class="form-label block mb-1">Unit <span class="text-red-500">*</span></label>
                    <select name="items[0][unit]" class="form-control w-full" required>
                        <option value="">-- Select Unit --</option>
                        @foreach($units as $unit)
                            <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-span-2">
                    <label class="form-label block mb-1">Quantity Required <span class="text-red-500">*</span></label>
                    <input type="number" name="items[0][required]" class="form-control qty-required w-full" value="0"
                        min="0" required>
                </div>
                <div class="col-span-2">
                    <label class="form-label block mb-1">Quantity Received</label>
                    <input type="number" name="items[0][received]" class="form-control qty-received w-full" value="0"
                        min="0" required>
                </div>
                <div class="col-span-2">
                    <label class="form-label block mb-1">Quantity Balance</label>
                    <input type="number" name="items[0][balance]" class="form-control qty-balance w-full" value="0"
                        readonly>
                </div>
                <button type="button" class="absolute top-2 right-2 text-red-500 remove-row">✖</button>
            </div>
        </div>

        <!-- Add Row Button -->
        <div>
            <button type="button" id="add-item" class="ti-btn ti-btn-secondary">+ Add Item</button>
        </div>

        <!-- Submit Button -->
        <div class="flex justify-end">
            <button type="submit" class="ti-btn ti-btn-primary-full">Submit Indent</button>
        </div>
    </form>
</div>

<!-- Hidden Template for Cloning -->
<template id="item-template">
    <div class="item-row grid grid-cols-12 gap-4 bg-gray-50 p-4 rounded relative">
        <div class="col-span-4">
            <label class="form-label block mb-1">Item Description <span class="text-red-500">*</span></label>
            <select name="items[__index__][description]" class="form-control choices-js" required>
                <option value="">-- Select Item --</option>
                @foreach($items as $item)
                    <option value="{{ $item->name }}">{{ $item->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-span-2">
            <label class="form-label block mb-1">Unit <span class="text-red-500">*</span></label>
            <select name="items[__index__][unit]" class="form-control w-full" required>
                <option value="">-- Select Unit --</option>
                @foreach($units as $unit)
                    <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-span-2">
            <label class="form-label block mb-1">Quantity Required <span class="text-red-500">*</span></label>
            <input type="number" name="items[__index__][required]" class="form-control qty-required w-full" value="0"
                min="0" required>
        </div>
        <div class="col-span-2">
            <label class="form-label block mb-1">Quantity Received</label>
            <input type="number" name="items[__index__][received]" class="form-control qty-received w-full" value="0"
                min="0" required>
        </div>
        <div class="col-span-2">
            <label class="form-label block mb-1">Quantity Balance</label>
            <input type="number" name="items[__index__][balance]" class="form-control qty-balance w-full" value="0"
                readonly>
        </div>
        <button type="button" class="absolute top-2 right-2 text-red-500 remove-row">✖</button>
    </div>
</template>

<!-- Choices.js -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css">
<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        let index = 1;

        function initChoices(select) {
            return new Choices(select, {
                searchEnabled: true,
                itemSelectText: '',
                placeholder: true,
                shouldSort: false,
            });
        }

        function updateQtyListeners(row) {
            const qtyRequired = row.querySelector('.qty-required');
            const qtyReceived = row.querySelector('.qty-received');
            const qtyBalance = row.querySelector('.qty-balance');

            function calc() {
                const req = parseFloat(qtyRequired.value) || 0;
                const rec = parseFloat(qtyReceived.value) || 0;
                qtyBalance.value = Math.max(req - rec, 0);
            }

            qtyRequired.addEventListener('input', calc);
            qtyReceived.addEventListener('input', calc);
            [qtyRequired, qtyReceived].forEach(input => {
                input.addEventListener('keypress', e => {
                    if (e.key === '+' || e.key === '-') e.preventDefault();
                });
            });

            calc();
        }

        function addRemoveHandler(row) {
            row.querySelector('.remove-row')?.addEventListener('click', () => {
                if (document.querySelectorAll('.item-row').length > 1) row.remove();
            });
        }

        document.querySelectorAll('.item-row').forEach(row => {
            row.querySelectorAll('.choices-js').forEach(initChoices);
            updateQtyListeners(row);
            addRemoveHandler(row);
        });

        document.getElementById('add-item').addEventListener('click', () => {
            const container = document.getElementById('items-container');
            const template = document.getElementById('item-template').innerHTML.replace(/__index__/g, index);
            const wrapper = document.createElement('div');
            wrapper.innerHTML = template.trim();
            const newRow = wrapper.firstElementChild;

            container.appendChild(newRow);
            newRow.querySelectorAll('.choices-js').forEach(initChoices);
            updateQtyListeners(newRow);
            addRemoveHandler(newRow);

            index++;
        });
    });
</script>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css">
<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.choices-js').forEach(el => {
            new Choices(el, { searchEnabled: true, itemSelectText: '' });
        });
    });
</script>