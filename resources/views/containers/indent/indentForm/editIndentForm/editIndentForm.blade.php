<div class="w-full px-4 py-6 bg-white shadow rounded relative">
    <!-- Form Start -->
    <form method="POST" action="{{ route('indent-register.indentRegisterUpdate', $indent->id) }}" class="grid gap-6">
        @csrf
        @method('PUT')

        <div class="sticky top-0 z-10 bg-white grid grid-cols-12 gap-4 border-b pb-4 mb-6">
            <div class="col-span-3">
                <label class="form-label text-black block mb-1">Indent Ticket ID <span
                        class="text-red-500">*</span></label>
                <input type="text" class="form-control w-full bg-gray-100" value="{{ $indent->indent_id }}" readonly
                    disabled>
                <input type="hidden" name="indent_id" value="{{ $indent->indent_id }}">
            </div>
            <div class="col-span-3">
                <label class="form-label text-black block mb-1">Department <span class="text-red-500">*</span></label>
                <input type="text" class="form-control w-full bg-gray-100" value="{{ $department_id }}" readonly
                    disabled>
                <input type="hidden" name="indent_department" value="{{ $indent->indent_department }}">
            </div>
            <div class="col-span-3">
                <label for="indent_date" class="form-label text-black block mb-1">Indent Date <span
                        class="text-red-500">*</span></label>
                <input type="date" name="indent_date" id="indent_date" class="form-control w-full"
                    value="{{ $indent->indent_date }}" required>
            </div>
            <div class="col-span-3">
                <label for="indent_project" class="form-label text-black block mb-1">Indent Project</label>
                <select name="indent_project" id="indent_project" class="form-control choices-js w-full">
                    <option value="">-- Select Project --</option>
                    @foreach($projects as $project)
                        @php
                            $current = old('indent_project', $indent->indent_project ?? null);
                        @endphp
                        <option value="{{ $project->name }}" {{ $current === $project->name ? 'selected' : '' }}>
                            {{ $project->name }}
                        </option>
                    @endforeach
                </select>

            </div>
        </div>

        <!-- Items Section -->
        <div id="items-container" class="space-y-4">
            @php $itemsDecoded = json_decode($indent->items_description, true); @endphp
            @foreach($itemsDecoded as $i => $item)
                <div class="item-row grid grid-cols-12 gap-4 bg-gray-50 p-4 rounded relative">
                    <div class="col-span-4">
                        <label class="form-label block mb-1">Item Description <span class="text-red-500">*</span></label>
                        <select name="items[{{ $i }}][description]" class="form-control choices-js" required>
                            <option value="">-- Select Item --</option>
                            @foreach($items as $it)
                                <option value="{{ $it->name }}" {{ $item['description'] == $it->name ? 'selected' : '' }}>
                                    {{ $it->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-span-2">
                        <label class="form-label block mb-1">Unit <span class="text-red-500">*</span></label>
                        <select name="items[{{ $i }}][unit]" class="form-control w-full" required>
                            <option value="">-- Select Unit --</option>
                            @foreach($units as $unit)
                                <option value="{{ $unit->id }}" {{ $item['unit'] == $unit->id ? 'selected' : '' }}>
                                    {{ $unit->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-span-2">
                        <label class="form-label block mb-1">Quantity Required <span class="text-red-500">*</span></label>
                        <input type="number" name="items[{{ $i }}][required]" class="form-control qty-required w-full"
                            value="{{ $item['quantity_required'] ?? 0 }}" min="0" required>
                    </div>
                    <div class="col-span-2">
                        <label class="form-label block mb-1">Quantity Received</label>
                        <input type="number" name="items[{{ $i }}][received]" class="form-control qty-received w-full"
                            value="{{ $item['quantity_received'] ?? 0 }}" min="0">
                    </div>
                    <div class="col-span-2">
                        <label class="form-label block mb-1">Quantity Balance</label>
                        <input type="number" name="items[{{ $i }}][balance]" class="form-control qty-balance w-full"
                            value="{{ $item['quantity_balance'] ?? 0 }}" readonly>
                    </div>
                    <button type="button" class="absolute top-2 right-2 text-red-500 remove-row">✖</button>
                </div>
            @endforeach
        </div>

        <!-- Add Row -->
        <div>
            <button type="button" id="add-item" class="ti-btn ti-btn-secondary">+ Add Item</button>
        </div>

        <!-- Submit -->
        <div class="flex justify-end">
            <button type="submit" class="ti-btn ti-btn-primary-full">Update Indent</button>
        </div>
    </form>
</div>

<!-- Template for New Item -->
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
                min="0">
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
        let index = {{ count($itemsDecoded) }};

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