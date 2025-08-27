<div class="w-full px-4 py-6 bg-white shadow rounded">
  <form method="POST" action="{{ route('po-register.updatePObyId', $po->id) }}" class="grid grid-cols-1 gap-6">
    @csrf
    @method('PUT')

    <input type="hidden" name="indent_id" value="{{ $po->indent_id }}">
    <input type="hidden" name="department_id" value="{{ $po->department_id }}">
    {{-- keep status unchanged from here (endpoints handle status changes) --}}
    <input type="hidden" name="status" value="{{ old('status', $po->status) }}">

    <div class="grid grid-cols-4 gap-6">
      <div class="w-full col-span-1">
        <label class="form-label text-black block mb-1">Indent ID <span class="text-red-500">*</span></label>
        <input type="text" class="form-control w-full bg-gray-100" value="{{ $po->indent_id }}" readonly disabled>
      </div>

      <div class="w-full col-span-1">
        <label class="form-label text-black block mb-1">Department  <span class="text-red-500">*</span></label>
        <input type="text" class="form-control w-full bg-gray-100"
               value="{{ $department_id ?? $po->department_id }}" readonly disabled>
      </div>

      <div class="w-full col-span-1">
        <label for="status_show" class="form-label text-black block mb-1">Status</label>
        <input id="status_show" type="text" class="form-control w-full bg-gray-100 cursor-not-allowed"
               value="{{ $po->status }}" readonly disabled>
      </div>

      <div class="w-full col-span-1">
        <label for="po_date" class="form-label text-black block mb-1">PO Date <span class="text-red-500">*</span></label>
        <input type="date" name="po_date" id="po_date" class="form-control w-full"
               value="{{ old('po_date', $po->po_date) }}" required>
      </div>
    </div>

    <div class="grid grid-cols-4 gap-6">
      <div class="w-full col-span-1">
        <label for="party_name" class="form-label text-black block mb-1">Party Name <span class="text-red-500">*</span></label>
        <select name="party_name" id="party_name" class="form-control w-full" required>
          <option value="">Select party</option>
          @foreach ($projectList as $vendor)
            <option value="{{ $vendor->name }}"
              {{ old('party_name', $po->party_name) === $vendor->name ? 'selected' : '' }}>
              {{ $vendor->name }}
            </option>
          @endforeach
          @if($projectList->where('name', $po->party_name)->isEmpty() && $po->party_name)
            <option value="{{ $po->party_name }}" selected>{{ $po->party_name }}</option>
          @endif
        </select>
      </div>

      <div class="w-full col-span-1">
        <label for="po_wo_no" class="form-label text-black block mb-1">PO/WO No. <span class="text-red-500">*</span></label>
        <input type="text" name="po_wo_no" id="po_wo_no" class="form-control w-full"
               value="{{ old('po_wo_no', $po->po_wo_no) }}" required>
      </div>

      <div class="w-full col-span-1">
        <label for="po_amount" class="form-label text-black block mb-1">PO Amount <span class="text-red-500">*</span></label>
        <input
          type="number"
          name="po_amount"
          id="po_amount"
          class="form-control w-full"
          step="0.01"
          min="0"
          inputmode="decimal"
          required
          value="{{ old('po_amount', $po->po_amount) }}"
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
      @php
      // $selectedItems: array of item objects (from controller)
      // $itemsRemaining: array of item objects
      $selected = collect($selectedItems ?? [])->map(fn($i) => (string)($i['description'] ?? ''))->filter()->unique()->values();
      $remaining = collect($itemsRemaining ?? [])->map(fn($i) => (string)($i['description'] ?? ''))->filter()
                    ->reject(fn($d) => $selected->contains($d))->unique()->values();
    @endphp

  
      <div class="w-full col-span-1">
        <label for="item_description" class="form-label text-black block mb-1">
          Item Description <span class="text-red-500">*</span>
        </label>
        <select class="ti-form-select rounded-sm !py-2 !px-3 choices-multiple-remove"
                name="item_description[]" id="item_description" multiple required>
          {{-- Preselected items --}}
          @foreach ($selected as $desc)
            <option value="{{ $desc }}" selected>{{ $desc }}</option>
          @endforeach
          {{-- Remaining items --}}
          @foreach ($remaining as $desc)
            <option value="{{ $desc }}">{{ $desc }}</option>
          @endforeach
        </select>
      </div>
    </div>

    {{-- Items --}}
    
    

    <div class="grid grid-cols-4 gap-6">
      <div class="w-full col-span-1">
        <label for="expected_date" class="form-label text-black block mb-1">Expected Date <span class="text-red-500">*</span></label>
        <input type="date" name="expected_date" id="expected_date" class="form-control w-full"
               value="{{ old('expected_date', $po->expected_date) }}" required>
      </div>

      <div class="w-full col-span-1">
        <label for="expected_days" class="form-label text-black block mb-1">Expected Days</label>
        <input type="text" id="expected_days" class="form-control w-full bg-gray-100" value="{{ $po->expected_days }}" readonly disabled>
        <input type="hidden" name="expected_days" id="expected_days_hidden" value="{{ $po->expected_days }}">
      </div>
    </div>

    <div class="flex justify-end">
      <button type="submit" class="ti-btn ti-btn-primary-full">Update PO</button>
    </div>
  </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const poDateInput        = document.getElementById('po_date');
  const expectedDateInput  = document.getElementById('expected_date');
  const expectedDaysDisplay= document.getElementById('expected_days');
  const expectedDaysHidden = document.getElementById('expected_days_hidden');

  function calcExpectedDays() {
    const poDate = new Date(poDateInput?.value);
    const expDate= new Date(expectedDateInput?.value);
    if (!isNaN(poDate) && !isNaN(expDate)) {
      const days = Math.round((expDate - poDate) / (1000*60*60*24));
      if (expectedDaysDisplay) expectedDaysDisplay.value = days;
      if (expectedDaysHidden) expectedDaysHidden.value = days;
    }
  }

  poDateInput?.addEventListener('change', calcExpectedDays);
  expectedDateInput?.addEventListener('change', calcExpectedDays);

  // Initial compute
  calcExpectedDays();

  // Choices init
  new Choices('#item_description', {
    removeItemButton: true,
    placeholderValue: 'Select item(s)',
    searchPlaceholderValue: 'Search items...',
  });
});
</script>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
