<div class="max-w-7xl mx-auto px-4 py-6 space-y-6">

    @php
        $indentId    = $indent->indent_id ?? $indent_id;
        $deptName    = $indent->department_name ?? $indent->indent_department ?? $department_id;
        $projectName = $indent->indent_project ?? '-';
        $indentDate  = !empty($indent->indent_date) ? date('d M Y', strtotime($indent->indent_date)) : (!empty($po->indent_date) ? date('d M Y', strtotime($po->indent_date)) : '-');

        // Extract indent items from indent_registers JSON
        $indentItems = [];
        if (!empty($indent->items_description)) {
            $indentItems = json_decode($indent->items_description, true) ?? [];
        } elseif (!empty($po->items_description)) {
            $indentItems = json_decode($po->items_description, true) ?? [];
        }
    @endphp

    <!-- Top Header -->
    <div class="flex flex-wrap justify-between items-center gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-gray-900 tracking-tight">Indent Summary</h1>
            <p class="text-sm text-gray-500">Overview of requested and received items in this indent.</p>
        </div>
    </div>

    <!-- 1. Key Indent Metadata Cards -->
    <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
            <!-- Indent ID -->
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center shrink-0 shadow-sm border border-purple-100">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <div>
                    <span class="text-xs uppercase font-bold text-gray-400 tracking-wider block">INDENT ID</span>
                    <span class="text-xl font-extrabold text-purple-700">{{ $indentId }}</span>
                </div>
            </div>

            <!-- Department -->
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0 shadow-sm border border-blue-100">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                <div>
                    <span class="text-xs uppercase font-bold text-gray-400 tracking-wider block">DEPARTMENT</span>
                    <span class="text-lg font-bold text-gray-900 truncate block max-w-[150px]" title="{{ $deptName }}">{{ $deptName }}</span>
                </div>
            </div>

            <!-- Project -->
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 shadow-sm border border-emerald-100">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                    </svg>
                </div>
                <div>
                    <span class="text-xs uppercase font-bold text-gray-400 tracking-wider block">PROJECT</span>
                    <span class="text-lg font-bold text-gray-900 truncate block max-w-[150px]" title="{{ $projectName }}">{{ $projectName }}</span>
                </div>
            </div>

            <!-- Indent Date -->
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-orange-50 text-orange-600 flex items-center justify-center shrink-0 shadow-sm border border-orange-100">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3M3 11h18M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <div>
                    <span class="text-xs uppercase font-bold text-gray-400 tracking-wider block">INDENT DATE</span>
                    <span class="text-lg font-bold text-orange-600 whitespace-nowrap">{{ $indentDate }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. Requested vs Received (Summary) Cards Grid -->
    <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
        <div class="flex flex-wrap justify-between items-center mb-5 gap-3 border-b border-gray-100 pb-4">
            <h3 class="text-base font-bold text-gray-800 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                <span>Requested vs Received (Summary)</span>
            </h3>
            <div class="flex items-center gap-4 text-xs font-semibold text-gray-500">
                <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500 inline-block"></span> Received</span>
                <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-blue-500 inline-block"></span> Remaining</span>
                <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-gray-400 inline-block"></span> Requested</span>
            </div>
        </div>

        @if(!empty($indentItems) && count($indentItems) > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                @php
                    $colors = [
                        ['bg' => 'bg-purple-50', 'text' => 'text-purple-600', 'border' => 'border-purple-100'],
                        ['bg' => 'bg-amber-50', 'text' => 'text-amber-600', 'border' => 'border-amber-100'],
                        ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-600', 'border' => 'border-emerald-100'],
                        ['bg' => 'bg-sky-50', 'text' => 'text-sky-600', 'border' => 'border-sky-100'],
                        ['bg' => 'bg-rose-50', 'text' => 'text-rose-600', 'border' => 'border-rose-100'],
                        ['bg' => 'bg-teal-50', 'text' => 'text-teal-600', 'border' => 'border-teal-100'],
                        ['bg' => 'bg-indigo-50', 'text' => 'text-indigo-600', 'border' => 'border-indigo-100'],
                        ['bg' => 'bg-orange-50', 'text' => 'text-orange-600', 'border' => 'border-orange-100'],
                    ];
                @endphp

                @foreach($indentItems as $idx => $it)
                    @php
                        $desc = $it['description'] ?? 'Item ' . ($idx + 1);
                        $req  = (int)($it['quantity_required'] ?? 0);
                        $rec  = (int)($it['quantity_received'] ?? 0);
                        $canc = (int)($it['quantity_cancelled'] ?? 0);
                        $rem  = max(0, $req - ($rec + $canc));
                        $cScheme = $colors[$idx % count($colors)];
                    @endphp

                    <div class="bg-gray-50/70 border border-gray-200/80 rounded-xl p-3.5 hover:shadow-md transition-all">
                        <div class="flex items-start gap-3">
                            <div class="w-9 h-9 rounded-xl {{ $cScheme['bg'] }} {{ $cScheme['text'] }} {{ $cScheme['border'] }} border flex items-center justify-center shrink-0 shadow-xs font-bold text-sm">
                                🧪
                            </div>
                            <div class="min-w-0 flex-1">
                                <h4 class="text-sm font-bold text-gray-900 truncate" title="{{ $desc }}">{{ $desc }}</h4>
                                <div class="text-xs font-medium text-gray-500 mt-1 flex flex-wrap items-center gap-1.5 whitespace-nowrap">
                                    <span>Req: <strong class="text-gray-700">{{ $req }}</strong></span>
                                    <span class="text-gray-300">|</span>
                                    <span>Rec: <strong class="text-emerald-600 font-bold">{{ $rec }}</strong></span>
                                    <span class="text-gray-300">|</span>
                                    <span>Rem: <strong class="text-blue-600 font-bold">{{ $rem }}</strong></span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-xs text-gray-500 italic text-center py-4">No items listed on this indent.</p>
        @endif
    </div>

    <!-- 3. All Purchase Orders Section -->
    <div class="space-y-4">
        <div class="flex flex-wrap justify-between items-center gap-4">
            <h2 class="text-xl font-bold text-gray-900 tracking-tight">All Purchase Orders</h2>

            <!-- New PO File Button -->
            <a href="{{ route('po-register.create', ['indent_id' => $indentId, 'department_id' => $department_id]) }}"
               class="inline-flex items-center gap-2 bg-orange-500 hover:bg-orange-600 text-white font-semibold text-sm px-4 py-2 rounded-xl shadow-sm transition-all hover:shadow-md">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                <span>File PO / Remaining Items</span>
            </a>
        </div>

        <!-- Filter & Search Toolbar -->
        <div class="bg-indigo-50/50 border border-indigo-100 rounded-2xl p-3 flex flex-wrap items-center justify-between gap-3">
            <div class="flex flex-wrap items-center gap-3 flex-1 min-w-[280px]">
                <!-- Search Input -->
                <div class="relative flex-1 min-w-[200px]">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </span>
                    <input type="text" id="poSearchInput" placeholder="Search PO/WO No..." 
                           class="w-full pl-9 pr-3 py-2 bg-white border border-gray-200 rounded-xl text-xs font-medium focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                </div>

                <!-- PO Date Picker -->
                <div class="relative w-40">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3M3 11h18M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </span>
                    <input type="date" id="poDateFilter" 
                           class="w-full pl-9 pr-3 py-2 bg-white border border-gray-200 rounded-xl text-xs font-medium focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                </div>

                <!-- Status Filter Dropdown -->
                <div class="relative w-48">
                    <select id="poStatusFilter" 
                            class="w-full px-3 py-2 bg-white border border-gray-200 rounded-xl text-xs font-medium focus:ring-2 focus:ring-indigo-500 focus:outline-none appearance-none">
                        <option value="">Status: All</option>
                        <option value="open">Open</option>
                        <option value="partially received">Partially received</option>
                        <option value="completed">Completed</option>
                        <option value="closed">Closed</option>
                        <option value="reopened">Reopened</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- 4. PO Cards List -->
        <div id="poListContainer" class="space-y-4">
            @forelse($allPos as $row)
                @php
                    $rowStStr   = is_object($row->status) ? $row->status->value : (string)($row->status ?? 'Open');
                    $normSt     = mb_strtolower(trim($rowStStr));
                    $canEdit    = !in_array($normSt, ['closed', 'close', 'cancel', 'cancelled']);

                    // Status Badge Styling
                    $badgeStyle = match($normSt) {
                        'completed'          => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                        'partially received' => 'bg-blue-100 text-blue-800 border-blue-200',
                        'reopened'           => 'bg-purple-100 text-purple-800 border-purple-200',
                        'closed', 'close'    => 'bg-gray-100 text-gray-700 border-gray-200',
                        'cancel', 'cancelled'=> 'bg-red-100 text-red-800 border-red-200',
                        default              => 'bg-green-100 text-green-800 border-green-200',
                    };

                    $poItemsDecoded = !empty($row->item_description) ? (is_array($row->item_description) ? $row->item_description : json_decode($row->item_description, true)) : [];
                    $poItemsCount   = is_array($poItemsDecoded) ? count($poItemsDecoded) : 0;
                    $poWoDisplay    = !empty($row->po_wo_no) ? $row->po_wo_no : ('PO/' . $indentId . '/' . str_pad($row->id, 2, '0', STR_PAD_LEFT));
                    $poDateDisplay  = !empty($row->po_date) ? date('d M Y', strtotime($row->po_date)) : '-';
                @endphp

                <div class="po-card bg-white border border-gray-200 rounded-2xl p-5 shadow-sm hover:shadow-md transition-all space-y-4"
                     data-po-no="{{ strtolower($poWoDisplay) }}"
                     data-po-date="{{ $row->po_date }}"
                     data-po-status="{{ $normSt }}">
                    
                    <!-- PO Card Header -->
                    <div class="flex flex-wrap justify-between items-center gap-4 border-b border-gray-100 pb-3">
                        <div class="flex flex-wrap items-center gap-4 text-xs font-semibold">
                            <!-- PO No -->
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center font-bold">
                                    📄
                                </div>
                                <div>
                                    <span class="text-gray-400 block uppercase text-[10px]">PO/WO No.</span>
                                    <span class="text-sm font-extrabold text-purple-700">{{ $poWoDisplay }}</span>
                                </div>
                            </div>

                            <span class="text-gray-300">|</span>

                            <!-- PO Date -->
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3M3 11h18M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                <div>
                                    <span class="text-gray-400 block uppercase text-[10px]">PO Date</span>
                                    <span class="text-xs font-bold text-gray-800">{{ $poDateDisplay }}</span>
                                </div>
                            </div>

                            <span class="text-gray-300">|</span>

                            <!-- Status Badge -->
                            <div>
                                <span class="text-gray-400 block uppercase text-[10px]">Status</span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold border {{ $badgeStyle }}">
                                    {{ ucfirst($rowStStr) }}
                                </span>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex flex-wrap items-center gap-2">
                            @if($canEdit)
                                <a href="{{ route('po-register.edit', $row->id) }}" 
                                   class="inline-flex items-center gap-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs px-3 py-1.5 rounded-xl shadow-xs transition-all">
                                    📅 Update P.O.
                                </a>

                                <a href="{{ route('indentroview.createInvoiceById', $row->id) }}" 
                                   class="inline-flex items-center gap-1.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs px-3 py-1.5 rounded-xl shadow-xs transition-all">
                                    📑 {{ !empty($row->invoice_date) ? 'Update Invoice' : 'File Invoice / Goods Receipt' }}
                                </a>

                                <button type="button" 
                                        onclick="document.getElementById('closePoModal_{{ $row->id }}').classList.remove('hidden')" 
                                        class="inline-flex items-center gap-1 bg-white hover:bg-rose-50 text-rose-600 border border-rose-200 font-semibold text-xs px-3 py-1.5 rounded-xl shadow-xs transition-all">
                                    🔒 Close PO
                                </button>
                            @elseif(in_array($normSt, ['closed', 'close']))
                                <a href="{{ route('indentroview.createInvoiceById', $row->id) }}" 
                                   class="inline-flex items-center gap-1.5 bg-gray-100 hover:bg-gray-200 text-gray-800 font-semibold text-xs px-3 py-1.5 rounded-xl shadow-xs transition-all">
                                    👁️ View Goods Receipt
                                </a>

                                <form method="POST" action="{{ route('po-register.reopenPO', $row->id) }}" class="inline" onsubmit="return confirm('Are you sure you want to reopen this PO?');">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center gap-1 bg-purple-600 hover:bg-purple-700 text-white font-semibold text-xs px-3 py-1.5 rounded-xl shadow-xs transition-all">
                                        🔓 Reopen PO
                                    </button>
                                </form>
                            @else
                                <a href="{{ route('indentroview.createInvoiceById', $row->id) }}" 
                                   class="inline-flex items-center gap-1.5 bg-gray-100 hover:bg-gray-200 text-gray-800 font-semibold text-xs px-3 py-1.5 rounded-xl shadow-xs transition-all">
                                    👁️ View Details
                                </a>
                            @endif
                        </div>
                    </div>

                    <!-- PO Metadata Row -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
                        <div>
                            <span class="text-gray-400 block font-medium uppercase text-[10px]">Party</span>
                            <span class="font-extrabold text-gray-900 text-sm">{{ $row->party_name ?? '-' }}</span>
                        </div>
                        <div>
                            <span class="text-gray-400 block font-medium uppercase text-[10px]">PO Amount</span>
                            <span class="font-extrabold text-gray-900 text-sm font-mono">₹ {{ number_format($row->po_amount ?? 0, 2) }}</span>
                        </div>
                        <div>
                            <span class="text-gray-400 block font-medium uppercase text-[10px]">Items</span>
                            <span class="inline-block px-2.5 py-0.5 rounded-lg bg-blue-50 text-blue-700 font-extrabold text-xs">
                                {{ $poItemsCount }} {{ Str::plural('Item', $poItemsCount) }}
                            </span>
                        </div>
                    </div>

                    <!-- PO Items List Breakdown -->
                    @if(is_array($poItemsDecoded) && count($poItemsDecoded) > 0)
                        <div class="bg-gray-50/70 border border-gray-100 rounded-xl p-3 space-y-2">
                            @foreach($poItemsDecoded as $pIt)
                                @php
                                    $pDesc = is_array($pIt) ? ($pIt['description'] ?? '') : (string)$pIt;
                                    $pOrd  = is_array($pIt) ? (int)($pIt['quantity'] ?? $pIt['po_quantity'] ?? 1) : 1;
                                    $pRec  = is_array($pIt) ? (int)($pIt['quantity_received'] ?? 0) : 0;
                                    $pCanc = is_array($pIt) ? (int)($pIt['quantity_cancelled'] ?? 0) : 0;
                                    $pRem  = max(0, $pOrd - ($pRec + $pCanc));
                                @endphp

                                <div class="flex flex-wrap items-center justify-between gap-2 text-xs">
                                    <div class="flex items-center gap-2 font-bold text-gray-800">
                                        <span class="w-2 h-2 rounded-full bg-purple-500 inline-block"></span>
                                        <span>{{ $pDesc }}</span>
                                    </div>
                                    <div class="text-gray-500 font-medium whitespace-nowrap">
                                        Req: <strong class="text-gray-700">{{ $pOrd }}</strong>
                                        <span class="text-gray-300 mx-1">|</span>
                                        Rec: <strong class="text-emerald-600 font-bold">{{ $pRec }}</strong>
                                        <span class="text-gray-300 mx-1">|</span>
                                        Rem: <strong class="text-blue-600 font-bold">{{ $pRem }}</strong>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <!-- Expandable Remarks Accordion -->
                    <details class="group bg-indigo-50/30 border border-indigo-100/70 rounded-xl">
                        <summary class="flex items-center justify-between px-3 py-2 text-xs font-semibold text-gray-700 cursor-pointer select-none">
                            <div class="flex items-center gap-2">
                                💬 <span>Remarks</span>
                                <span class="text-gray-400 font-normal italic">
                                    {{ !empty($row->remarks) ? Str::limit($row->remarks, 50) : 'No remarks added' }}
                                </span>
                            </div>
                            <svg class="w-4 h-4 text-gray-400 transition-transform group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </summary>
                        <div class="px-3 py-2 border-t border-indigo-100/50 text-xs text-gray-800 whitespace-pre-line bg-white/80 rounded-b-xl">
                            {{ !empty($row->remarks) ? $row->remarks : 'No remarks recorded for this Purchase Order.' }}
                        </div>
                    </details>

                    <!-- Close PO Modal for this PO -->
                    <div id="closePoModal_{{ $row->id }}" class="hidden fixed inset-0 z-50 overflow-y-auto bg-black/50 flex items-center justify-center p-4">
                        <div class="bg-white rounded-2xl p-6 w-full max-w-md shadow-2xl border border-gray-100">
                            <h3 class="text-lg font-bold text-gray-900 mb-2">Close Purchase Order #{{ $row->id }}</h3>
                            <p class="text-xs text-gray-600 mb-4">Are you sure you want to close this PO? Once closed, no further goods receipts or invoice modifications will be allowed.</p>
                            <form method="POST" action="{{ route('po-register.closePO', $row->id) }}">
                                @csrf
                                <div class="mb-4">
                                    <label for="close_reason_{{ $row->id }}" class="block text-xs font-semibold text-gray-700 mb-1">Close Reason (Optional)</label>
                                    <textarea name="close_reason" id="close_reason_{{ $row->id }}" rows="3" class="form-control w-full text-xs rounded-xl p-2.5 border" placeholder="Enter reason for closing this PO..."></textarea>
                                </div>
                                <div class="flex justify-end gap-2">
                                    <button type="button" onclick="document.getElementById('closePoModal_{{ $row->id }}').classList.add('hidden')" class="px-4 py-2 rounded-xl text-xs font-semibold bg-gray-100 hover:bg-gray-200 text-gray-700">Cancel</button>
                                    <button type="submit" class="px-4 py-2 rounded-xl text-xs font-semibold bg-rose-600 hover:bg-rose-700 text-white shadow-xs">Confirm Close PO</button>
                                </div>
                            </form>
                        </div>
                    </div>

                </div>
            @empty
                <div class="bg-white border border-gray-200 rounded-2xl p-10 text-center text-gray-500 shadow-sm">
                    <div class="w-12 h-12 rounded-full bg-gray-100 text-gray-400 flex items-center justify-center mx-auto mb-3 text-xl">📦</div>
                    <p class="font-semibold text-sm text-gray-700">No purchase orders found for this indent.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Client-side Search & Filtering Script -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('poSearchInput');
        const dateInput   = document.getElementById('poDateFilter');
        const statusInput = document.getElementById('poStatusFilter');
        const poCards     = document.querySelectorAll('.po-card');

        function filterPOCards() {
            const query  = searchInput ? searchInput.value.toLowerCase().trim() : '';
            const date   = dateInput   ? dateInput.value : '';
            const status = statusInput ? statusInput.value.toLowerCase().trim() : '';

            poCards.forEach(card => {
                const cardPoNo   = card.getAttribute('data-po-no') || '';
                const cardDate   = card.getAttribute('data-po-date') || '';
                const cardStatus = card.getAttribute('data-po-status') || '';

                const matchesQuery  = !query || cardPoNo.includes(query);
                const matchesDate   = !date  || cardDate === date;
                const matchesStatus = !status|| cardStatus === status;

                if (matchesQuery && matchesDate && matchesStatus) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        }

        searchInput?.addEventListener('input', filterPOCards);
        dateInput?.addEventListener('change', filterPOCards);
        statusInput?.addEventListener('change', filterPOCards);
    });
</script>