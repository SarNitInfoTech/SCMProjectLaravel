<div class="w-full px-4 py-6 bg-white shadow rounded space-y-6">

  @php
      $poStatusStr = is_object($po->status) ? $po->status->value : (string)($po->status ?? 'Open');
      $normStatus  = mb_strtolower(trim($poStatusStr));
      $isReadOnly  = in_array($normStatus, ['closed', 'close', 'cancel', 'cancelled', 'completed']);

      // Calculate summary metrics for this PO
      $itemsDecoded = !empty($po->item_description) ? (is_array($po->item_description) ? $po->item_description : json_decode($po->item_description, true)) : [];
      $totalOrdered = 0;
      $totalReceived = 0;
      if (is_array($itemsDecoded)) {
          foreach ($itemsDecoded as $it) {
              if (is_array($it)) {
                  $totalOrdered  += (int)($it['quantity'] ?? $it['po_quantity'] ?? 1);
                  $totalReceived += (int)($it['quantity_received'] ?? 0);
              }
          }
      }
      $totalPending = max(0, $totalOrdered - $totalReceived);
      $progressPct  = $totalOrdered > 0 ? min(100, round(($totalReceived / $totalOrdered) * 100)) : 0;

      // Badge color mapping
      $badgeClasses = match($normStatus) {
          'completed'          => 'bg-emerald-100 text-emerald-800 border-emerald-300',
          'partially received' => 'bg-blue-100 text-blue-800 border-blue-300',
          'reopened'           => 'bg-purple-100 text-purple-800 border-purple-300',
          'closed', 'close'    => 'bg-gray-100 text-gray-800 border-gray-300',
          'cancel', 'cancelled'=> 'bg-red-100 text-red-800 border-red-300',
          default              => 'bg-green-100 text-green-800 border-green-300',
      };
  @endphp

  <!-- Executive Summary Cards & Progress Bar -->
  <div class="bg-gray-50 border rounded-lg p-5">
    <div class="flex flex-wrap justify-between items-center mb-4 gap-4">
      <div>
        <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
          <span>PO #{{ $po->id }} Lifecycle & Goods Receipt</span>
          <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold border {{ $badgeClasses }}">
            {{ ucfirst($poStatusStr) }}
          </span>
        </h3>
        <p class="text-xs text-gray-500">Party: {{ $po->party_name ?? 'N/A' }} | Indent #{{ $po->indent_id }}</p>
      </div>

      <!-- Action Buttons depending on status -->
      <div class="flex items-center gap-2">
        @if(in_array($normStatus, ['open', 'pending', 'partially received', 'reopened']))
          <button type="button" onclick="document.getElementById('closePoModal_{{ $po->id }}').classList.remove('hidden')" class="ti-btn ti-btn-danger text-xs font-semibold px-3 py-1.5">
            🔒 Close PO
          </button>
        @elseif(in_array($normStatus, ['closed', 'close']))
          <form method="POST" action="{{ route('po-register.reopenPO', $po->id) }}" onsubmit="return confirm('Are you sure you want to reopen this PO?');">
            @csrf
            <button type="submit" class="ti-btn ti-btn-secondary text-xs font-semibold px-3 py-1.5">
              🔓 Reopen PO
            </button>
          </form>
        @endif
      </div>
    </div>

    <!-- Metrics Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4 text-center">
      <div class="bg-white p-3 rounded border shadow-sm">
        <span class="text-xs font-medium text-gray-500 block uppercase">Ordered Qty</span>
        <span class="text-xl font-extrabold text-gray-800">{{ $totalOrdered }}</span>
      </div>
      <div class="bg-white p-3 rounded border shadow-sm">
        <span class="text-xs font-medium text-blue-600 block uppercase">Received Qty</span>
        <span class="text-xl font-extrabold text-blue-600">{{ $totalReceived }}</span>
      </div>
      <div class="bg-white p-3 rounded border shadow-sm">
        <span class="text-xs font-medium text-orange-600 block uppercase">Pending Qty</span>
        <span class="text-xl font-extrabold text-orange-600">{{ $totalPending }}</span>
      </div>
      <div class="bg-white p-3 rounded border shadow-sm">
        <span class="text-xs font-medium text-emerald-600 block uppercase">Completion</span>
        <span class="text-xl font-extrabold text-emerald-600">{{ $progressPct }}%</span>
      </div>
    </div>

    <!-- Progress Bar -->
    <div>
      <div class="flex justify-between text-xs font-semibold text-gray-600 mb-1">
        <span>Goods Receipt Progress</span>
        <span>{{ $totalReceived }} of {{ $totalOrdered }} Received ({{ $progressPct }}%)</span>
      </div>
      <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden">
        <div class="bg-emerald-500 h-3 rounded-full transition-all duration-500" style="width: {{ $progressPct }}%"></div>
      </div>
    </div>
  </div>

  @if($isReadOnly)
    <div class="bg-amber-50 border-l-4 border-amber-500 p-4 rounded text-amber-900 text-sm">
      <strong>Notice:</strong> This Purchase Order status is currently <strong>{{ ucfirst($poStatusStr) }}</strong>.
      @if(in_array($normStatus, ['closed', 'close']))
        It was manually closed{{ !empty($po->close_reason) ? ' (Reason: ' . $po->close_reason . ')' : '' }}. Reopen the PO to record additional goods receipts.
      @elseif($normStatus === 'completed')
        All ordered quantities have been 100% received.
      @else
        No modifications are allowed.
      @endif
    </div>
  @endif

  <form method="POST" action="{{ route('po-register.updateInvoice', $po->id) }}" class="grid grid-cols-1 gap-6">
    @csrf
    @method('PUT')

    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
      <div class="col-span-1">
        <label for="invoice_date" class="form-label text-black block mb-1">Invoice Date</label>
        <input type="date" name="invoice_date" id="invoice_date" class="form-control w-full"
               value="{{ old('invoice_date', $po->invoice_date) }}" {{ $isReadOnly ? 'disabled' : '' }}>
      </div>

      <div class="col-span-1">
        <label for="receiving_date" class="form-label text-black block mb-1">Receiving Date</label>
        <input type="date" name="receiving_date" id="receiving_date" class="form-control w-full"
               value="{{ old('receiving_date', $po->receiving_date) }}" {{ $isReadOnly ? 'disabled' : '' }}>
      </div>

      <div class="col-span-1">
        <label for="delay_in_days" class="form-label text-black block mb-1">
          Delay (days)
          @if(!empty($po->expected_date))
            <span class="text-xs text-gray-500">(vs expected: {{ $po->expected_date }})</span>
          @endif
        </label>
        <input type="number" name="delay_in_days" id="delay_in_days" class="form-control w-full" min="0"
               value="{{ old('delay_in_days', $po->delay_in_days) }}" {{ $isReadOnly ? 'disabled' : '' }}>
      </div>

      <div class="col-span-1">
        <label for="store_indent_no" class="form-label text-black block mb-1">Invoice No.</label>
        <input type="text" name="store_indent_no" id="store_indent_no" class="form-control w-full"
               value="{{ old('store_indent_no', $po->store_indent_no) }}" {{ $isReadOnly ? 'disabled' : '' }}>
      </div>
    </div>

    @if(!empty($indentItems) && count($indentItems) > 0)
    <div class="border rounded p-4 bg-gray-50 col-span-full">
        <h4 class="font-semibold text-gray-800 mb-3">Item Received & Remaining Quantity Management for PO #{{ $po->id }}</h4>
        <div class="overflow-x-auto">
            <table class="table min-w-full bg-white border text-sm">
                <thead>
                    <tr class="bg-gray-100 border-b">
                        <th class="p-2 text-start">Item Description</th>
                        <th class="p-2 text-center">PO Qty Ordered</th>
                        <th class="p-2 text-center">Qty Received</th>
                        <th class="p-2 text-center">Qty Cancelled</th>
                        <th class="p-2 text-center">Qty Balance (Remaining)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($indentItems as $idx => $item)
                        @php
                            $req = (int)($item['quantity_required'] ?? 0);
                            $poQty = (int)($item['po_quantity'] ?? $item['quantity'] ?? $req);
                            $rec = (int)($item['quantity_received'] ?? 0);
                            $canc = (int)($item['quantity_cancelled'] ?? 0);
                            $bal = max(0, $poQty - ($rec + $canc));
                        @endphp
                        <tr class="border-b item-qty-row">
                            <td class="p-2 font-medium">
                                {{ $item['description'] ?? '' }}
                                <input type="hidden" name="items[{{ $idx }}][description]" value="{{ $item['description'] ?? '' }}">
                                <input type="hidden" name="items[{{ $idx }}][unit]" value="{{ $item['unit'] ?? '' }}">
                                <input type="hidden" name="items[{{ $idx }}][required]" value="{{ $poQty }}" class="js-qty-req">
                            </td>
                            <td class="p-2 text-center font-bold text-gray-900">
                                {{ $poQty }}
                                @if($req > 0 && $req !== $poQty)
                                    <span class="text-xs text-gray-500 block font-normal">(Indent Req: {{ $req }})</span>
                                @endif
                            </td>
                            <td class="p-2 text-center w-36">
                                <input type="number" 
                                       name="items[{{ $idx }}][received]" 
                                       value="{{ $rec }}" 
                                       min="0" 
                                       max="{{ $poQty > 0 ? $poQty : 999999 }}"
                                       class="form-control text-center js-qty-rec w-full"
                                       {{ $isReadOnly ? 'disabled' : '' }}>
                            </td>
                            <td class="p-2 text-center w-36">
                                <input type="number" 
                                       name="items[{{ $idx }}][cancelled]" 
                                       value="{{ $canc }}" 
                                       min="0" 
                                       max="{{ $poQty > 0 ? $poQty : 999999 }}"
                                       class="form-control text-center js-qty-canc w-full text-red-600 font-semibold"
                                       placeholder="0"
                                       {{ $isReadOnly ? 'disabled' : '' }}>
                            </td>
                            <td class="p-2 text-center">
                                <span class="js-qty-bal font-bold {{ $bal > 0 ? 'text-orange-600' : 'text-green-600' }}">
                                    @if($bal > 0)
                                        {{ $bal }} remaining
                                    @else
                                        0 ({{ $canc > 0 ? 'Closed: ' . $canc . ' Cancelled' : 'Fully Accounted' }})
                                    @endif
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    @if(!$isReadOnly)
      <div class="flex justify-end col-span-full">
        <button type="submit" class="ti-btn ti-btn-primary-full">Save Changes & Update Receipt</button>
      </div>
    @endif
  </form>

  <!-- Audit Trail History Section -->
  @php
    $auditLogs = DB::table('po_audit_logs')->where('po_id', $po->id)->orderByDesc('created_at')->get();
  @endphp
  <div class="border rounded-lg p-5 bg-white">
    <h4 class="font-bold text-gray-800 text-md mb-3 flex items-center gap-2">
      📜 <span>PO Lifecycle Audit Trail</span>
    </h4>
    @if(count($auditLogs) > 0)
      <div class="overflow-x-auto">
        <table class="table min-w-full text-xs text-left text-gray-600 border">
          <thead class="bg-gray-100 uppercase text-gray-700">
            <tr>
              <th class="p-2">Date & Time</th>
              <th class="p-2">User</th>
              <th class="p-2">Action</th>
              <th class="p-2">Prev Status</th>
              <th class="p-2">New Status</th>
              <th class="p-2">Reason / Details</th>
            </tr>
          </thead>
          <tbody>
            @foreach($auditLogs as $log)
              <tr class="border-b hover:bg-gray-50">
                <td class="p-2 font-mono whitespace-nowrap">{{ $log->created_at }}</td>
                <td class="p-2 font-semibold">{{ $log->user_name ?? 'System' }}</td>
                <td class="p-2 capitalize"><span class="font-medium text-gray-800">{{ str_replace('_', ' ', $log->action) }}</span></td>
                <td class="p-2"><span class="text-gray-500">{{ $log->previous_status ?? '-' }}</span></td>
                <td class="p-2"><span class="font-semibold text-blue-700">{{ $log->new_status ?? '-' }}</span></td>
                <td class="p-2 text-gray-600">{{ $log->reason ?? $log->details ?? '-' }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @else
      <p class="text-xs text-gray-500 italic">No audit trail records logged yet.</p>
    @endif
  </div>

</div>

<!-- Close PO Modal -->
<div id="closePoModal_{{ $po->id }}" class="hidden fixed inset-0 z-50 overflow-y-auto bg-black bg-opacity-50 flex items-center justify-center">
  <div class="bg-white rounded-lg p-6 w-full max-w-md shadow-xl border">
    <h3 class="text-lg font-bold text-gray-800 mb-2">Close Purchase Order #{{ $po->id }}</h3>
    <p class="text-xs text-gray-600 mb-4">Are you sure you want to close this PO? Once closed, no further goods receipts or invoice modifications will be allowed.</p>
    <form method="POST" action="{{ route('po-register.closePO', $po->id) }}">
      @csrf
      <div class="mb-4">
        <label for="close_reason" class="block text-xs font-semibold text-gray-700 mb-1">Close Reason (Optional)</label>
        <textarea name="close_reason" id="close_reason" rows="3" class="form-control w-full text-sm" placeholder="Enter reason for closing this PO..."></textarea>
      </div>
      <div class="flex justify-end gap-2">
        <button type="button" onclick="document.getElementById('closePoModal_{{ $po->id }}').classList.add('hidden')" class="ti-btn ti-btn-secondary text-xs">Cancel</button>
        <button type="submit" class="ti-btn ti-btn-danger text-xs">Confirm Close PO</button>
      </div>
    </form>
  </div>
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
        const cancEl = row.querySelector('.js-qty-canc');
        const balEl = row.querySelector('.js-qty-bal');

        function updateBalance() {
            const req = parseInt(reqEl?.value || 0, 10);
            let rec = parseInt(recEl?.value || 0, 10);
            let canc = parseInt(cancEl?.value || 0, 10);

            if (isNaN(rec) || rec < 0) rec = 0;
            if (isNaN(canc) || canc < 0) canc = 0;

            // Clamp received quantity so it cannot exceed required quantity
            if (req > 0 && rec > req) {
                rec = req;
                if (recEl) recEl.value = req;
            }

            // Clamp cancelled quantity so received + cancelled cannot exceed required quantity
            if (req > 0 && (rec + canc) > req) {
                canc = req - rec;
                if (cancEl) cancEl.value = canc;
            }

            const bal = Math.max(0, req - (rec + canc));

            if (balEl) {
                if (bal > 0) {
                    balEl.textContent = bal + ' remaining';
                    balEl.className = 'js-qty-bal font-bold text-orange-600';
                } else {
                    balEl.textContent = '0 (' + (canc > 0 ? 'Closed: ' + canc + ' Cancelled' : 'Fully Accounted') + ')';
                    balEl.className = 'js-qty-bal font-bold text-green-600';
                }
            }
        }

        recEl?.addEventListener('input', updateBalance);
        cancEl?.addEventListener('input', updateBalance);
        recEl?.addEventListener('change', updateBalance);
        cancEl?.addEventListener('change', updateBalance);
    });
  });
</script>
