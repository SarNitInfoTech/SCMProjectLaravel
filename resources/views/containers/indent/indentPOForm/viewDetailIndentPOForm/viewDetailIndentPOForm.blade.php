<div class="container mx-auto max-w-7xl px-4 py-8">

    {{-- Section 1: Indent Summary --}}
    <div class="bg-white border border-gray-200 shadow-sm rounded-2xl p-6 mb-10">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-2xl font-semibold text-gray-900 tracking-tight">Indent Summary</h3>
            <span class="inline-flex items-center gap-2 text-xs font-medium text-gray-500">
                <span class="h-2 w-2 rounded-full bg-emerald-500"></span> Up to date
            </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-gray-700 text-sm leading-6">
            <div class="group rounded-xl border border-gray-200 p-4 hover:bg-gray-50 transition">
                <div class="text-xs uppercase tracking-wider text-gray-500">Indent ID</div>
                <div class="mt-1 font-semibold text-gray-900">{{ $po->indent_id }}</div>
            </div>
            <div class="group rounded-xl border border-gray-200 p-4 hover:bg-gray-50 transition">
                <div class="text-xs uppercase tracking-wider text-gray-500">Department</div>
                <div class="mt-1 font-semibold text-gray-900">{{ $po->department_name }}</div>
            </div>
            <div class="group rounded-xl border border-gray-200 p-4 hover:bg-gray-50 transition">
                <div class="text-xs uppercase tracking-wider text-gray-500">Project</div>
                <div class="mt-1 font-semibold text-gray-900">{{ $po->project_name }}</div>
            </div>
        </div>

        {{-- Item Description One-Line --}}
        <div class="mt-6">
            <span class="font-semibold text-gray-900">Items:</span>
            <span class="text-sm text-gray-600">
                {{ collect($po->items ?? [])->map(fn($item) => ($item['description'] ?? '-') . ' — Qty: ' . ($item['quantity_required'] ?? '-'))->implode(', ') }}
            </span>
        </div>
    </div>

    {{-- Section 2: PO Header & Action Buttons --}}
    <div class="sticky top-2 z-10">
        <div class="bg-white/90 backdrop-blur border border-gray-200 shadow-sm rounded-2xl p-4 flex flex-wrap justify-between items-center">
            <h4 class="text-xl font-semibold text-gray-900">All Purchase Orders</h4>
            <div class="flex gap-3">
                <a href="{{ route('po.export.excel', ['indent_id' => $indent_id, 'department_id' => $department_id]) }}" style="background:green;"
                   class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium bg-emerald-600 text-white shadow hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500/50 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h4l2 2h8a2 2 0 012 2v10a2 2 0 01-2 2z"/>
                    </svg> Export Excel
                </a>

            </div>
        </div>
    </div>

    {{-- Section 3: PO Entries --}}
    @forelse($allPos as $row)
        @php
    $raw = (string)($row->status ?? '');
    $status = strtolower(trim($raw));

    // base badge look
    $base = 'display:inline-flex;align-items:center;gap:.375rem;padding:.25rem .625rem;'.
            'border-radius:9999px;font-size:.75rem;line-height:1rem;font-weight:600;'.
            'border:1px solid;';

    // colors (approx Tailwind hues)
    $palette = [
        'pending' => 'background:#FFFBEB;color:#92400E;border-color:#FCD34D;', // amber
        'cancel'  => 'background:#FEF2F2;color:#991B1B;border-color:#FCA5A5;', // red/rose
        'close'   => 'background:#ECFDF5;color:#065F46;border-color:#A7F3D0;', // emerald
        'closed'  => 'background:#ECFDF5;color:#065F46;border-color:#A7F3D0;', // alias
        'default' => 'background:#F5F5F5;color:#1F2937;border-color:#E5E7EB;', // gray
    ];

    $style = $base . ($palette[$status] ?? $palette['default']);

    // icon path per status (SVG inherits currentColor)
    $icons = [
        'pending' => 'M12 6v6l4 2',             // clock-ish
        'cancel'  => 'M6 6l12 12M18 6L6 18',    // X
        'close'   => 'M5 13l4 4L19 7',          // check
        'closed'  => 'M5 13l4 4L19 7',
        'default' => 'M5 12h14',                // dash
    ];
    $iconPath = $icons[$status] ?? $icons['default'];

    // label
    $label = ucfirst($status ?: 'Unknown');
@endphp




        <div class="bg-white border border-gray-200 shadow-sm rounded-2xl p-6 mb-6 hover:shadow-md transition">
            <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
                <div class="space-y-2">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="text-sm text-gray-500">PO Date</span>
                        <span class="inline-flex items-center gap-2 px-2.5 py-1 rounded-full text-sm font-medium bg-gray-50 ring-1 ring-gray-200 text-gray-900">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3M3 11h18M5 21h14a2 2 0 0 0 2-2v-9a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2z"/>
                            </svg>
                            {{ $row->po_date }}
                        </span>

                        <span class="text-sm text-gray-500 ml-3">PO/WO No.</span>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-sm font-medium bg-gray-50 ring-1 ring-gray-200 text-gray-900">
                            {{ $row->po_wo_no }}
                        </span>
                    </div>

                    <div class="text-base md:text-lg font-semibold text-gray-900">
                        {{ $row->party_name }}
                    </div>

                    <div class="flex flex-wrap items-center gap-3">
                        <div class="inline-flex items-center gap-1.5 text-sm font-semibold text-gray-900">
                            ₹{{ number_format($row->po_amount, 2) }}
                            <span class="text-xs font-medium text-gray-500">PO Amount</span>
                        </div>

                        <span style="{{ $style }}">
  {{-- Icon (inherits text color via currentColor) --}}
  <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" style="stroke-width:2;">
    <path d="{{ $iconPath }}" stroke-linecap="round" stroke-linejoin="round"/>
  </svg>
  {{ $label }}
</span>
                    </div>
                </div>

                {{-- Quick meta stack on the right --}}
                <div class="flex flex-col items-start md:items-end gap-2 text-sm">
                    <div class="text-gray-500">Invoice: <span class="font-medium text-gray-900">{{ $row->invoice ?? '—' }}</span></div>
                    <div class="text-gray-500">Invoice Date: <span class="font-medium text-gray-900">{{ $row->invoice_date ?? '—' }}</span></div>
                    <div class="text-gray-500">Receiving Date: <span class="font-medium text-gray-900">{{ $row->receiving_date ?? '—' }}</span></div>
                    <div class="text-gray-500">Delay: <span class="font-medium text-gray-900">{{ $row->delay_in_days ?? '0' }} day(s)</span></div>
                </div>
            </div>

            {{-- Expandable details --}}
            <details class="mt-6 group">
                <summary class="flex cursor-pointer select-none items-center justify-between rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-900 hover:bg-gray-100">
                    <div class="inline-flex items-center gap-2 font-medium">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 opacity-70" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 7h18M3 12h18M3 17h18"/>
                        </svg>
                        View items & notes
                    </div>
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 transition group-open:rotate-180" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 011.08 1.04l-4.25 4.25a.75.75 0 01-1.06 0L5.21 8.27a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                    </svg>
                </summary>

                <div class="pt-4">
                    {{-- Items --}}
                    @if(!empty($row->item_description))
                        @php
                            $items = json_decode($row->item_description, true);
                            // Normalize: items can be array of strings or objects
                            $items = is_array($items) ? $items : [];
                        @endphp
                        <div class="mb-4">
                            <div class="text-sm font-semibold text-gray-900 mb-2">Items</div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                                @foreach ($items as $item)
                                    @php
                                        $label = is_array($item)
                                            ? trim(($item['description'] ?? (json_encode($item) ?: 'Item')))  // fallback
                                            : (string) $item;
                                    @endphp
                                    <div class="bg-white border border-gray-200 rounded-xl px-3 py-2 text-sm text-gray-700 shadow-sm">
                                        {{ $label }}
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Remarks --}}
                    <div>
                        <div class="text-sm font-semibold text-gray-900 mb-2">Remarks</div>
                        <div class="p-3 bg-gray-50 border border-gray-200 rounded-xl text-sm text-gray-700">
                            {{ $row->remarks ?? 'No remarks available.' }}
                        </div>
                    </div>
                </div>
            </details>
        </div>
    @empty
        <div class="bg-white border border-gray-200 shadow-sm rounded-2xl p-10 text-center text-gray-500">
            <div class="mx-auto mb-3 h-10 w-10 rounded-full bg-gray-100 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4m0 4h.01M4 6h16M6 6v14h12V6"/>
                </svg>
            </div>
            No purchase orders found.
        </div>
    @endforelse

</div>
