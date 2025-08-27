<div>

    {{-- Section 1: Indent Summary --}}
    <div class="{{ $po->status === 'Pending'
    ? 'bg-white border-gray-400'
    : ($po->status === 'Close'
        ? 'bg-green-50 border-green-800'
        : 'bg-red-100 border-red-800') }} border shadow-sm rounded-[4px] p-6 mb-10">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-2xl font-bold text-gray-900 tracking-tight">Indent Summary</h3>
            <div class="flex items-center gap-1">
                <div class="flex flex-wrap items-center gap-2">
                    @php $status = strtolower($po->status ?? 'pending'); @endphp

                    {{-- ========== CANCEL -> Reopen + Close ========== --}}
                    @if ($status === 'cancel')
                        {{-- Re-Open (Pending) --}}
                        <a href="#"
                            onclick="event.preventDefault(); document.getElementById('pending-po-{{ $po->id }}').submit();"
                            class="inline-flex items-center px-3 py-1 rounded-[4px] text-sm font-medium bg-yellow-600 text-white shadow hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500/50 transition">
                            Re-Open
                        </a>
                        <form id="pending-po-{{ $po->id }}" action="{{ route('po-register.statusPending') }}" method="POST"
                            class="hidden">
                            @csrf
                            <input type="hidden" name="indent_id" value="{{ $po->indent_id }}">
                            <input type="hidden" name="department_id" value="{{ $po->department_id }}">
                        </form>

                        {{-- Close --}}
                        <a href="#"
                            onclick="event.preventDefault(); document.getElementById('close-po-{{ $po->id }}').submit();"
                            class="inline-flex items-center px-3 py-1 rounded-[4px] text-sm font-medium bg-green-600 text-white shadow hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500/50 transition">
                            Close
                        </a>
                        <form id="close-po-{{ $po->id }}" action="{{ route('po-register.statusClose') }}" method="POST"
                            class="hidden">
                            @csrf
                            <input type="hidden" name="indent_id" value="{{ $po->indent_id }}">
                            <input type="hidden" name="department_id" value="{{ $po->department_id }}">
                        </form>
                    @endif

                    {{-- ========== PENDING -> Close + Cancel ========== --}}
                    @if ($status === 'pending')
                        {{-- Close --}}
                        <a href="#"
                            onclick="event.preventDefault(); document.getElementById('close-po-{{ $po->id }}').submit();"
                            class="inline-flex items-center px-3 py-1 rounded-[4px] text-sm font-medium bg-green-600 text-white shadow hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500/50 transition">
                            Close
                        </a>
                        <form id="close-po-{{ $po->id }}" action="{{ route('po-register.statusClose') }}" method="POST"
                            class="hidden">
                            @csrf
                            <input type="hidden" name="indent_id" value="{{ $po->indent_id }}">
                            <input type="hidden" name="department_id" value="{{ $po->department_id }}">
                        </form>

                        {{-- Cancel --}}
                        <a href="#"
                            onclick="event.preventDefault(); document.getElementById('cancel-po-{{ $po->id }}').submit();"
                            class="inline-flex items-center px-3 py-1 rounded-[4px] text-sm font-medium bg-red-600 text-white shadow hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500/50 transition">
                            Cancel
                        </a>
                        <form id="cancel-po-{{ $po->id }}" action="{{ route('po-register.statusCancel') }}" method="POST"
                            class="hidden">
                            @csrf
                            <input type="hidden" name="indent_id" value="{{ $po->indent_id }}">
                            <input type="hidden" name="department_id" value="{{ $po->department_id }}">
                        </form>
                    @endif

                    {{-- ========== CLOSE -> Reopen + Cancel ========== --}}
                    @if ($status === 'close')
                        {{-- Re-Open (Pending) --}}
                        <a href="#"
                            onclick="event.preventDefault(); document.getElementById('pending-po-{{ $po->id }}').submit();"
                            class="inline-flex items-center px-3 py-1 rounded-[4px] text-sm font-medium bg-yellow-600 text-white shadow hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500/50 transition">
                            Re-Open
                        </a>
                        <form id="pending-po-{{ $po->id }}" action="{{ route('po-register.statusPending') }}" method="POST"
                            class="hidden">
                            @csrf
                            <input type="hidden" name="indent_id" value="{{ $po->indent_id }}">
                            <input type="hidden" name="department_id" value="{{ $po->department_id }}">
                        </form>

                        {{-- Cancel --}}
                        <a href="#"
                            onclick="event.preventDefault(); document.getElementById('cancel-po-{{ $po->id }}').submit();"
                            class="inline-flex items-center px-3 py-1 rounded-[4px] text-sm font-medium bg-red-600 text-white shadow hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500/50 transition">
                            Cancel
                        </a>
                        <form id="cancel-po-{{ $po->id }}" action="{{ route('po-register.statusCancel') }}" method="POST"
                            class="hidden">
                            @csrf
                            <input type="hidden" name="indent_id" value="{{ $po->indent_id }}">
                            <input type="hidden" name="department_id" value="{{ $po->department_id }}">
                        </form>
                    @endif
                </div>

            </div>

        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-gray-800 text-sm leading-6">
            <div
                class="group rounded-[4px] border border-gray-400 p-4 hover:bg-gray-50 transition flex items-center justify-between gap-3">
                <span class="text-xs uppercase tracking-wider text-gray-800 whitespace-nowrap font-[400]">Indent
                    ID:</span>
                <span class="mt-0 font-bold text-gray-900 whitespace-nowrap">{{ $po->indent_id }}</span>
            </div>


            <div
                class="group rounded-[4px] border border-gray-400 p-4 hover:bg-gray-50 transition flex items-center justify-between gap-3">
                <span
                    class="text-xs uppercase tracking-wider text-gray-800 whitespace-nowrap font-[400]">Department:</span>
                <span class="font-bold text-gray-900 truncate">{{ $po->department_id }}</span>
            </div>

            <div
                class="group rounded-[4px] border border-gray-400 p-4 hover:bg-gray-50 transition flex items-center justify-between gap-3">
                <span
                    class="text-xs uppercase tracking-wider text-gray-800 whitespace-nowrap font-[400]">Project:</span>
                <span class="font-bold text-gray-900 truncate">{{ $po->project_name }}</span>
            </div>
            <div
                class="group rounded-[4px] border border-gray-400 p-4 hover:bg-gray-50 transition flex items-center justify-between gap-3">
                <span class="text-xs uppercase tracking-wider text-gray-800 whitespace-nowrap font-[400]">Indent
                    Date:</span>
                <span class="mt-0 font-bold text-gray-900 whitespace-nowrap">{{ $po->indent_date }}</span>
            </div>

        </div>

        {{-- Item Description One-Line --}}

        <div class="mt-4">
            <div
                class="group rounded-[4px] border border-gray-400 p-4 hover:bg-gray-50 transition flex items-center justify-between gap-3">
                <span class="font-[400] text-gray-900">Items:</span>
                <span class="text-sm text-gray-900 font-bold">
                    {{ collect($po->items ?? [])->map(fn($item) => ($item['description'] ?? '-') . ' — Qty: ' . ($item['quantity_required'] ?? '-'))->implode(', ') }}
                </span>
            </div>

        </div>
    </div>

    {{-- Section 2: PO Header & Action Buttons --}}
    <div class="sticky top-2 z-10">
        <div
            class="bg-white/90 backdrop-blur border shadow-sm rounded-2xl p-4 flex flex-wrap justify-between items-center">
            <h4 class="text-xl font-bold text-gray-900">All Purchase Orders</h4>
            {{-- <div class="flex gap-3">
                <a href="{{ route('po.export.excel', ['indent_id' => $indent_id, 'department_id' => $department_id]) }}"
                    style="background:green;"
                    class="inline-flex items-center px-4 py-2 rounded-[4px] text-sm font-medium bg-green-600 text-white shadow hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500/50 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-2" fill="currentColor"
                        viewBox="0 0 24 24">
                        <path d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h4l2 2h8a2 2 0 012 2v10a2 2 0 01-2 2z" />
                    </svg> Export Excel
                </a>

            </div> --}}
        </div>
    </div>

    {{-- Section 3: PO Entries --}}
    @forelse($allPos as $row)
        @php
            $raw = (string) ($row->status ?? '');
            $status = strtolower(trim($raw));

            // base badge look
            $base = 'display:inline-flex;align-items:center;gap:.375rem;padding:.25rem .625rem;' .
                'border-radius:9999px;font-size:.75rem;line-height:1rem;font-weight:600;' .
                'border:1px solid;';

            // colors (approx Tailwind hues)
            $palette = [
                'pending' => 'background:#FFFBEB;color:#92400E;border-color:#FCD34D;', // amber
                'cancel' => 'background:#FEF2F2;color:#991B1B;border-color:#FCA5A5;', // red/rose
                'close' => 'background:#ECFDF5;color:#065F46;border-color:#A7F3D0;', // emerald
                'closed' => 'background:#ECFDF5;color:#065F46;border-color:#A7F3D0;', // alias
                'default' => 'background:#F5F5F5;color:#1F2937;border-color:#E5E7EB;', // gray
            ];

            $style = $base . ($palette[$status] ?? $palette['default']);

            // icon path per status (SVG inherits currentColor)
            $icons = [
                'pending' => 'M12 6v6l4 2',             // clock-ish
                'cancel' => 'M6 6l12 12M18 6L6 18',    // X
                'close' => 'M5 13l4 4L19 7',          // check
                'closed' => 'M5 13l4 4L19 7',
                'default' => 'M5 12h14',                // dash
            ];
            $iconPath = $icons[$status] ?? $icons['default'];

            // label
            $label = ucfirst($status ?: 'Unknown');
        @endphp




        <div class="{{ $po->status === 'Pending'
    ? 'bg-white border-gray-400'
    : ($po->status === 'Close'
        ? 'bg-green-50 border-green-800'
        : 'bg-red-100 border-red-800') }} border shadow-sm rounded-[4px] p-6 mb-6 hover:shadow-md transition">
            <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
                <div class="space-y-2">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="text-sm text-gray-700 ml-3">PO/WO No.</span>
                        <span
                            class="inline-flex items-center px-2.5 py-1 rounded-full text-sm font-medium bg-gray-50 ring-1 ring-gray-200 text-gray-900">
                            {{ $row->po_wo_no }}
                        </span>

                        <span class="text-gray-300 mx-1 select-none">|</span>

                        <span class="text-sm text-gray-700">PO Date</span>
                        <span
                            class="inline-flex items-center gap-2 px-2.5 py-1 rounded-full text-sm font-medium bg-gray-50 ring-1 ring-gray-200 text-gray-900">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M8 7V3m8 4V3M3 11h18M5 21h14a2 2 0 0 0 2-2v-9a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2z" />
                            </svg>
                            {{ $row->po_date }}
                        </span>

                        <span class="text-gray-300 mx-1 select-none">|</span>

                        <span class="text-sm text-gray-700">Status: </span>
                        <span style="{{ $style }}">
                            {{-- Icon (inherits text color via currentColor) --}}
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" style="stroke-width:2;">
                                <path d="{{ $iconPath }}" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            {{ $label }}
                        </span>
                    </div>


                    @php
                        // Prefer Intl NumberFormatter (₹ 1,23,456.00)
                        if (class_exists(\NumberFormatter::class)) {
                            $fmt = new \NumberFormatter('en_IN', \NumberFormatter::CURRENCY);
                            $poAmountDisplay = $fmt->formatCurrency($row->po_amount ?? 0, 'INR');
                        } else {
                            // Fallback: custom Indian grouping formatter
                            if (!function_exists('format_inr')) {
                                function format_inr($amount): string
                                {
                                    $neg = $amount < 0;
                                    $amount = abs((float) $amount);
                                    [$int, $dec] = explode('.', number_format($amount, 2, '.', ''));
                                    if (strlen($int) > 3) {
                                        $last3 = substr($int, -3);
                                        $rest = substr($int, 0, -3);
                                        $rest = preg_replace('/\B(?=(\d{2})+(?!\d))/', ',', $rest);
                                        $int = $rest . ',' . $last3;
                                    }
                                    return ($neg ? '−' : '') . '₹' . $int . '.' . $dec;
                                }
                            }
                            $poAmountDisplay = format_inr($row->po_amount ?? 0);
                        }
                    @endphp

                    <div class="space-y-2">
                        <div class="flex items-baseline gap-3">
                            <span class="w-40 sm:w-48 text-sm text-gray-600 font-semibold">Party</span>
                            <span class="text-base md:text-lg font-bold text-gray-900 break-words">
                                {{ $row->party_name }}
                            </span>
                        </div>

                        <div class="flex items-baseline gap-3">
                            <span class="w-40 sm:w-48 text-sm text-gray-600 font-semibold">PO Amount</span>
                            <span class="text-base md:text-lg font-bold text-gray-900 font-mono tabular-nums">
                                {{ $poAmountDisplay }}
                            </span>
                        </div>

                        {{-- Example: Items (chips but still aligned) --}}
                        @if(!empty($row->item_description))
                            @php
                                $decoded = json_decode($row->item_description, true);
                                $items = is_array($decoded) ? $decoded : [];
                            @endphp
                            <div class="flex items-start gap-3">
                                <span class="w-40 sm:w-48 text-sm text-gray-600 font-semibold mt-1">Items</span>
                                <div class="flex flex-wrap gap-2">
                                    @foreach ($items as $item)
                                        @php
                                            $label = is_array($item) ? trim(($item['description'] ?? (json_encode($item) ?: 'Item'))) : (string) $item;
                                          @endphp
                                        <span
                                            class="px-2 py-0.5 rounded-md bg-gray-50 ring-1 ring-gray-200 text-gray-900 text-sm font-semibold">
                                            {{ $label }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>



                </div>

                {{-- Quick meta stack on the right --}}
                <div class="flex flex-col items-start md:items-end gap-2 text-sm">
                    @if ($po->status==="pending" || $po->status==="Pending")
                         <form action="{{ route('po-register.edit', $row->id) }}" method="GET" class="inline">
  <button type="submit" class="ti-btn ti-btn-success-full label-ti-btn me-[0.375rem]">
<i class="ri-receipt-line label-ti-btn-icon me-2"></i>
    Update P.O.
  </button>
</form>
<a href="{{ route('indentroview.createInvoiceById', $row->id) }}"
   class="ti-btn ti-btn-primary-full label-ti-btn me-[0.375rem] inline-flex items-center">
  <i class="ri-file-list-3-line label-ti-btn-icon me-2"></i>

{{ $row->invoice_date ? 'Update Invoice' : 'File Invoice' }}

</a>
                    @endif
                 


                </div>
            </div>@php
                $remarksText = $row->remarks ?? 'No remarks available.';
                $remarksPreview = Str::limit(trim(strip_tags($remarksText)), 70); // preview in summary
            @endphp

            <details class="group py-2">
                <summary
                    class="flex items-center gap-2 w-full cursor-pointer select-none rounded-[4px] border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 hover:bg-gray-50">
                    <!-- Left icon (fixed size) -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 opacity-70 shrink-0" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M3 7h18M3 12h18M3 17h18" />
                    </svg>

                    <!-- Label -->
                    <span class="font-medium text-gray-900">Remarks</span>

                    <!-- Chevron -->
                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="w-4 h-4 ml-auto transition group-open:rotate-180 shrink-0" viewBox="0 0 20 20"
                        fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 011.08 1.04l-4.25 4.25a.75.75 0 01-1.06 0L5.21 8.27a.75.75 0 01.02-1.06z"
                            clip-rule="evenodd" />
                    </svg>
                </summary>

                <div
                    class="mt-3 p-3 bg-gray-50 ring-1 ring-gray-200 rounded-md text-sm text-gray-900 whitespace-pre-line break-words">
                    {{ $remarksText }}
                </div>
            </details>


            {{-- Expandable details --}}

        </div>
    @empty
        <div class="bg-white border border-gray-400 shadow-sm rounded-2xl p-10 text-center text-gray-700">
            <div class="mx-auto mb-3 h-10 w-10 rounded-full bg-gray-100 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M12 8v4m0 4h.01M4 6h16M6 6v14h12V6" />
                </svg>
            </div>
            No purchase orders found.
        </div>
    @endforelse

</div>
@if (session('debug'))
    <script>
        console.log('updateStatus debug:', @json(session('debug')));
        console.table(@json(session('debug')));
    </script>
@endif