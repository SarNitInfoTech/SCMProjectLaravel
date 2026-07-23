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

    @if(!empty($indentItems) && count($indentItems) > 0)
    <div class="border rounded p-4 bg-gray-50 col-span-full">
        <h4 class="font-semibold text-gray-800 mb-3">Item Received & Remaining Quantity Management</h4>
        <div class="overflow-x-auto">
            <table class="table min-w-full bg-white border text-sm">
                <thead>
                    <tr class="bg-gray-100 border-b">
                        <th class="p-2 text-start">Item Description</th>
                        <th class="p-2 text-center">Qty Required</th>
                        <th class="p-2 text-center">Qty Received</th>
                        <th class="p-2 text-center">Qty Balance (Remaining)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($indentItems as $idx => $item)
                        @php
                            $req = (int)($item['quantity_required'] ?? 0);
                            $rec = (int)($item['quantity_received'] ?? 0);
                            $bal = max(0, $req - $rec);
                        @endphp
                        <tr class="border-b item-qty-row">
                            <td class="p-2 font-medium">
                                {{ $item['description'] ?? '' }}
                                <input type="hidden" name="items[{{ $idx }}][description]" value="{{ $item['description'] ?? '' }}">
                                <input type="hidden" name="items[{{ $idx }}][unit]" value="{{ $item['unit'] ?? '' }}">
                                <input type="hidden" name="items[{{ $idx }}][required]" value="{{ $req }}" class="js-qty-req">
                            </td>
                            <td class="p-2 text-center">{{ $req }}</td>
                            <td class="p-2 text-center w-36">
                                <input type="number" 
                                       name="items[{{ $idx }}][received]" 
                                       value="{{ $rec }}" 
                                       min="0" 
                                       max="{{ $req > 0 ? $req : 999999 }}"
                                       class="form-control text-center js-qty-rec w-full">
                            </td>
                            <td class="p-2 text-center">
                                <span class="js-qty-bal font-bold {{ $bal > 0 ? 'text-orange-600' : 'text-green-600' }}">
                                    {{ $bal > 0 ? $bal . ' remaining' : '0 (Fully Received)' }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

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
      if (delayEl) delayEl.value = Math.max(0, diff);
    }

    receivingEl?.addEventListener('change', recalcDelay);

    // Quantity receiving and remaining balance live updater
    document.querySelectorAll('.item-qty-row').forEach(row => {
        const reqEl = row.querySelector('.js-qty-req');
        const recEl = row.querySelector('.js-qty-rec');
        const balEl = row.querySelector('.js-qty-bal');

        function updateBalance() {
            const req = parseInt(reqEl?.value || 0, 10);
            const rec = parseInt(recEl?.value || 0, 10);
            const bal = Math.max(0, req - rec);

            if (balEl) {
                if (bal > 0) {
                    balEl.textContent = bal + ' remaining';
                    balEl.className = 'js-qty-bal font-bold text-orange-600';
                } else {
                    balEl.textContent = '0 (Fully Received)';
                    balEl.className = 'js-qty-bal font-bold text-green-600';
                }
            }
        }

        recEl?.addEventListener('input', updateBalance);
    });
  });
</script>
