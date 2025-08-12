<div class="w-full px-4 py-6 bg-white shadow rounded">
  <form method="POST" action="{{ route('po-register.updateInvoice', $po->id) }}" class="grid grid-cols-1 gap-6">
    @csrf
    @method('PUT')

    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
      <div class="col-span-1">
        <label for="invoice_date" class="form-label text-black block mb-1">Invoice Date</label>
        <input type="date" name="invoice_date" id="invoice_date" class="form-control w-full"
               value="{{ old('invoice_date', $po->invoice_date) }}">
      </div>

      <div class="col-span-1">
        <label for="receiving_date" class="form-label text-black block mb-1">Receiving Date</label>
        <input type="date" name="receiving_date" id="receiving_date" class="form-control w-full"
               value="{{ old('receiving_date', $po->receiving_date) }}">
      </div>

      <div class="col-span-1">
        <label for="delay_in_days" class="form-label text-black block mb-1">
          Delay (days)
          @if(!empty($po->expected_date))
            <span class="text-xs text-gray-500">(vs expected: {{ $po->expected_date }})</span>
          @endif
        </label>
        <input type="number" name="delay_in_days" id="delay_in_days" class="form-control w-full" min="0"
               value="{{ old('delay_in_days', $po->delay_in_days) }}">
      </div>

      <div class="col-span-1">
        <label for="store_indent_no" class="form-label text-black block mb-1">Invoice No.</label>
        <input type="text" name="store_indent_no" id="store_indent_no" class="form-control w-full"
               value="{{ old('store_indent_no', $po->store_indent_no) }}">
      </div>
    </div>

    <div class="flex justify-end col-span-full">
      <button type="submit" class="ti-btn ti-btn-primary-full">Save changes</button>
    </div>
  </form>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const expected = '{{ $po->expected_date ?? '' }}';
    const expectedDate = expected ? new Date(expected) : null;

    const receivingEl = document.getElementById('receiving_date');
    const delayEl     = document.getElementById('delay_in_days');

    function recalcDelay() {
      if (!expectedDate || !receivingEl?.value) return;
      const recv = new Date(receivingEl.value);
      if (isNaN(recv)) return;
      const diff = Math.round((recv - expectedDate) / (1000 * 60 * 60 * 24));
      delayEl.value = Math.max(0, diff);
    }

    receivingEl?.addEventListener('change', recalcDelay);
  });
</script>
