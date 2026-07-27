@extends("layouts.layout")

@section("bodyContent")
<div class="max-w-7xl mx-auto px-4 py-6 space-y-6">

    <!-- Top Page Header -->
    <div class="flex flex-wrap justify-between items-center gap-4">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center shadow-xs border border-purple-100 font-bold text-xl">
                📄
            </div>
            <div>
                <h1 class="text-2xl font-extrabold text-gray-900 tracking-tight">PO Register List</h1>
                <p class="text-xs text-gray-500 font-medium">Manage and update purchase orders</p>
            </div>
        </div>

        <!-- Bulk Import Button -->
        <a href="{{ route('bulk-upload.index', ['module' => 'po-registers']) }}" 
           class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-xs px-4 py-2.5 rounded-xl shadow-xs transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
            <span>Bulk Import</span>
            <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
        </a>
    </div>

    @if (session('success'))
        <div class="p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 text-xs font-semibold rounded-r-xl shadow-xs">
            {{ session('success') }}
        </div>
    @endif

    <!-- Main Card Container -->
    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
        
        <!-- Card Header Toolbar -->
        <div class="p-5 border-b border-gray-100 flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-sm font-bold">
                    📄
                </div>
                <h2 class="text-base font-extrabold text-gray-900">PO Register List</h2>
            </div>

            <!-- Search, Filter Drawer Trigger & Search Button -->
            <form method="GET" action="{{ route('indentroview.index') }}" class="flex flex-wrap items-center gap-3">
                @if(request('department_id')) <input type="hidden" name="department_id" value="{{ request('department_id') }}"> @endif
                @if(request('status')) <input type="hidden" name="status" value="{{ request('status') }}"> @endif
                @if(request('party_name')) <input type="hidden" name="party_name" value="{{ request('party_name') }}"> @endif
                @if(request('date_from')) <input type="hidden" name="date_from" value="{{ request('date_from') }}"> @endif
                @if(request('date_to')) <input type="hidden" name="date_to" value="{{ request('date_to') }}"> @endif

                <!-- Search Input -->
                <div class="relative min-w-[280px]">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Search by ID, department, party name, item..." 
                           class="w-full pl-10 pr-4 py-2 bg-gray-50/70 border border-gray-200 rounded-xl text-xs font-medium focus:ring-2 focus:ring-indigo-500 focus:bg-white focus:outline-none transition-all">
                </div>

                <!-- Right Drawer Filter Button -->
                <button type="button" onclick="openRightFilterDrawer()" 
                        class="inline-flex items-center gap-1.5 bg-white border border-indigo-200 hover:border-indigo-400 text-indigo-600 font-semibold text-xs px-4 py-2 rounded-xl shadow-xs transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                    <span>Filter</span>
                    @if(request()->anyFilled(['department_id', 'status', 'party_name', 'date_from', 'date_to']))
                        <span class="w-2 h-2 rounded-full bg-indigo-600 inline-block"></span>
                    @endif
                </button>

                <!-- Search Button -->
                <button type="submit" 
                        class="inline-flex items-center gap-1.5 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs px-5 py-2 rounded-xl shadow-xs transition-all">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <span>Search</span>
                </button>

                @if(request()->anyFilled(['search', 'department_id', 'status', 'party_name', 'date_from', 'date_to']))
                    <a href="{{ route('indentroview.index') }}" class="px-3 py-2 text-xs font-semibold bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl transition-all">
                        Reset
                    </a>
                @endif
            </form>
        </div>

        <!-- Table Responsive Container -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-indigo-50/40 border-b border-indigo-100 text-gray-500 uppercase font-bold tracking-wider text-[10px]">
                        <th scope="col" class="py-3.5 px-4 whitespace-nowrap">INDENT ID <span class="text-gray-300">↕</span></th>
                        <th scope="col" class="py-3.5 px-4 whitespace-nowrap">DEPARTMENT <span class="text-gray-300">↕</span></th>
                        <th scope="col" class="py-3.5 px-4 whitespace-nowrap">PARTY NAME <span class="text-gray-300">↕</span></th>
                        <th scope="col" class="py-3.5 px-4 whitespace-nowrap">ITEM DESCRIPTION <span class="text-gray-300">↕</span></th>
                        <th scope="col" class="py-3.5 px-4 whitespace-nowrap">AMOUNT <span class="text-gray-300">↕</span></th>
                        <th scope="col" class="py-3.5 px-4 whitespace-nowrap">REMARKS <span class="text-gray-300">↕</span></th>
                        <th scope="col" class="py-3.5 px-4 whitespace-nowrap text-center">STATUS <span class="text-gray-300">↕</span></th>
                        <th scope="col" class="py-3.5 px-4 whitespace-nowrap">PO DATE <span class="text-gray-300">↕</span></th>
                        <th scope="col" class="py-3.5 px-4 text-center whitespace-nowrap">ACTION</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($rows as $row)
                        @php
                            $status = $row['status'] ?? 'Open';
                            $normSt = strtolower(trim($status));
                            
                            // Badge color & dot styling matching mockup
                            $badgeStyle = match ($normSt) {
                                'partially received' => ['bg' => 'bg-purple-50', 'text' => 'text-purple-700', 'border' => 'border-purple-200', 'dot' => 'bg-purple-600'],
                                'open'               => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'border' => 'border-emerald-200', 'dot' => 'bg-emerald-600'],
                                'pending'            => ['bg' => 'bg-amber-50', 'text' => 'text-amber-700', 'border' => 'border-amber-200', 'dot' => 'bg-amber-600'],
                                'completed'          => ['bg' => 'bg-teal-50', 'text' => 'text-teal-700', 'border' => 'border-teal-200', 'dot' => 'bg-teal-600'],
                                'close', 'closed'    => ['bg' => 'bg-gray-100', 'text' => 'text-gray-700', 'border' => 'border-gray-200', 'dot' => 'bg-gray-500'],
                                'cancel', 'cancelled'=> ['bg' => 'bg-red-50', 'text' => 'text-red-700', 'border' => 'border-red-200', 'dot' => 'bg-red-600'],
                                default              => ['bg' => 'bg-gray-100', 'text' => 'text-gray-700', 'border' => 'border-gray-200', 'dot' => 'bg-gray-500']
                            };

                            $actions = $row['action'];
                        @endphp
                        <tr class="hover:bg-gray-50/80 transition-colors">
                            <!-- Indent ID -->
                            <td class="py-3.5 px-4 font-extrabold text-gray-900 whitespace-nowrap">{{ $row['indent_id'] }}</td>
                            
                            <!-- Department -->
                            <td class="py-3.5 px-4 text-gray-600 font-medium whitespace-nowrap">{{ $row['department_name'] ?? '-' }}</td>
                            
                            <!-- Party Name -->
                            <td class="py-3.5 px-4 text-gray-900 font-semibold whitespace-nowrap">{{ $row['party_name'] && $row['party_name'] !== '-' ? $row['party_name'] : '-' }}</td>
                            
                            <!-- Item Description -->
                            <td class="py-3.5 px-4 text-gray-800 font-medium max-w-xs truncate" title="{{ $row['item_description'] }}">{{ $row['item_description'] }}</td>
                            
                            <!-- Amount -->
                            <td class="py-3.5 px-4 font-extrabold text-gray-900 whitespace-nowrap font-mono">₹{{ $row['po_amount'] }}</td>
                            
                            <!-- Remarks -->
                            <td class="py-3.5 px-4 text-gray-500 max-w-xs truncate" title="{{ $row['remarks'] ?? '-' }}">{{ $row['remarks'] ?? '-' }}</td>
                            
                            <!-- Status Badge -->
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold border {{ $badgeStyle['bg'] }} {{ $badgeStyle['text'] }} {{ $badgeStyle['border'] }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $badgeStyle['dot'] }} inline-block"></span>
                                    {{ ucfirst($status) }}
                                </span>
                            </td>
                            
                            <!-- PO Date -->
                            <td class="py-3.5 px-4 text-gray-600 font-mono whitespace-nowrap">{{ !empty($row['po_date']) && $row['po_date'] !== '-' ? date('d-m-Y', strtotime($row['po_date'])) : '-' }}</td>
                            
                            <!-- Actions Grid Buttons -->
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                <div class="flex items-center justify-center gap-1.5">
                                    <!-- File Invoice Button -->
                                    @if (isset($actions['viewPage']))
                                        <a href="{{ $actions['viewPage'] }}"
                                           class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-semibold bg-purple-50 text-purple-700 border border-purple-200 hover:bg-purple-100 transition-all">
                                            👁️ {{ $viewBtnTitle }}
                                        </a>
                                    @endif

                                    <!-- File PO Button -->
                                    @if (isset($actions['file_po']))
                                        <a href="{{ $actions['file_po'] }}"
                                           class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-semibold bg-orange-50 text-orange-700 border border-orange-200 hover:bg-orange-100 transition-all">
                                            📄 File PO
                                        </a>
                                    @endif

                                    <!-- Reopen Button -->
                                    @if (isset($actions['pending']))
                                        <form action="{{ $actions['pending']['route'] }}" method="POST" class="js-po-status-form inline">
                                            @csrf
                                            <input type="hidden" name="indent_id" value="{{ $actions['pending']['params']['indent_id'] }}">
                                            <input type="hidden" name="department_id" value="{{ $actions['pending']['params']['department_id'] }}">
                                            <input type="hidden" name="status" value="Pending">
                                            <button type="button" data-action="Pending" onclick="confirmPOStatus(this)"
                                                    class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100 transition-all">
                                                🔓 Re-Open
                                            </button>
                                        </form>
                                    @endif

                                    <!-- Close Button -->
                                    @if (isset($actions['close']))
                                        <form action="{{ $actions['close']['route'] }}" method="POST" class="js-po-status-form inline">
                                            @csrf
                                            <input type="hidden" name="indent_id" value="{{ $actions['close']['params']['indent_id'] }}">
                                            <input type="hidden" name="department_id" value="{{ $actions['close']['params']['department_id'] }}">
                                            <input type="hidden" name="status" value="Close">
                                            <button type="button" data-action="Close" onclick="confirmPOStatus(this)"
                                                    class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-semibold bg-gray-50 text-gray-700 border border-gray-200 hover:bg-gray-100 transition-all">
                                                ⊗ Close
                                            </button>
                                        </form>
                                    @endif

                                    <!-- Cancel Button -->
                                    @if (isset($actions['cancel']))
                                        <form action="{{ $actions['cancel']['route'] }}" method="POST" class="js-po-status-form inline">
                                            @csrf
                                            <input type="hidden" name="indent_id" value="{{ $actions['cancel']['params']['indent_id'] }}">
                                            <input type="hidden" name="department_id" value="{{ $actions['cancel']['params']['department_id'] }}">
                                            <input type="hidden" name="status" value="Cancel">
                                            <button type="button" data-action="Cancel" onclick="confirmPOStatus(this)"
                                                    class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-semibold bg-red-50 text-red-700 border border-red-200 hover:bg-red-100 transition-all">
                                                🗑️ Cancel
                                            </button>
                                        </form>
                                    @endif

                                    <!-- 3-Dots Dropdown Trigger -->
                                    <button type="button" class="text-gray-400 hover:text-gray-600 p-1 font-bold text-sm">
                                        ⋮
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-8 text-gray-500 font-medium">No PO records found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Bottom Pagination Bar -->
        <div class="p-4 border-t border-gray-100 flex flex-wrap items-center justify-between gap-4 text-xs font-medium text-gray-600">
            <div>
                Showing {{ $pagination->firstItem() ?? 0 }} to {{ $pagination->lastItem() ?? 0 }} of {{ $pagination->total() }} entries
            </div>

            <!-- Page Number Controls -->
            <div>
                {{ $pagination->appends(request()->query())->links('pagination::tailwind') }}
            </div>

            <!-- Per Page Selector -->
            <form method="GET" action="{{ route('indentroview.index') }}" class="flex items-center gap-2">
                @foreach(request()->except(['per_page', 'page']) as $k => $v)
                    <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                @endforeach
                <select name="per_page" onchange="this.form.submit()" class="bg-gray-50 border border-gray-200 rounded-lg px-2.5 py-1 text-xs font-semibold focus:outline-none">
                    <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                    <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                    <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                    <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                </select>
                <span>per page</span>
            </form>
        </div>
    </div>
</div>

<!-- Right Slide-over Filter Drawer -->
<div id="rightFilterDrawer" class="fixed inset-0 z-50 overflow-hidden hidden" aria-labelledby="slide-over-title" role="dialog" aria-modal="true">
    <!-- Backdrop Overlay -->
    <div class="fixed inset-0 bg-black/40 backdrop-blur-xs transition-opacity duration-300" onclick="closeRightFilterDrawer()"></div>

    <div class="fixed inset-y-0 right-0 max-w-full flex pl-10">
        <!-- Drawer Panel -->
        <div class="w-screen max-w-md bg-white shadow-2xl border-l border-gray-200 flex flex-col justify-between transform transition-transform duration-300 ease-in-out">
            
            <!-- Drawer Header -->
            <div class="p-5 border-b border-gray-100 flex items-center justify-between bg-indigo-50/40">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                    <h3 class="text-base font-extrabold text-gray-900">Filter PO Registers</h3>
                </div>
                <button type="button" onclick="closeRightFilterDrawer()" class="text-gray-400 hover:text-gray-600 p-1.5 rounded-lg hover:bg-gray-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Drawer Body Controls -->
            <form id="drawerFilterForm" method="GET" action="{{ route('indentroview.index') }}" class="p-6 space-y-5 overflow-y-auto flex-1 text-xs">
                @if(request('search')) <input type="hidden" name="search" value="{{ request('search') }}"> @endif

                <!-- Department Filter -->
                <div>
                    <label class="block font-bold text-gray-700 mb-1.5 uppercase text-[10px] tracking-wider">Department</label>
                    <select name="department_id" class="w-full p-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                        <option value="">All Departments</option>
                        @if(!empty($departments))
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <!-- Status Filter -->
                <div>
                    <label class="block font-bold text-gray-700 mb-1.5 uppercase text-[10px] tracking-wider">PO Status</label>
                    <select name="status" class="w-full p-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                        <option value="">All Statuses</option>
                        <option value="open" {{ strtolower(request('status')) === 'open' ? 'selected' : '' }}>Open</option>
                        <option value="partially received" {{ strtolower(request('status')) === 'partially received' ? 'selected' : '' }}>Partially received</option>
                        <option value="completed" {{ strtolower(request('status')) === 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="pending" {{ strtolower(request('status')) === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="closed" {{ strtolower(request('status')) === 'closed' ? 'selected' : '' }}>Closed</option>
                        <option value="cancel" {{ strtolower(request('status')) === 'cancel' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>

                <!-- Party Name Search -->
                <div>
                    <label class="block font-bold text-gray-700 mb-1.5 uppercase text-[10px] tracking-wider">Party Name</label>
                    <input type="text" name="party_name" value="{{ request('party_name') }}" placeholder="Search party name..." 
                           class="w-full p-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                </div>

                <!-- Date Range -->
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-gray-700 mb-1.5 uppercase text-[10px] tracking-wider">PO Date From</label>
                        <input type="date" name="date_from" value="{{ request('date_from') }}" 
                               class="w-full p-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1.5 uppercase text-[10px] tracking-wider">PO Date To</label>
                        <input type="date" name="date_to" value="{{ request('date_to') }}" 
                               class="w-full p-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    </div>
                </div>
            </form>

            <!-- Drawer Footer -->
            <div class="p-5 border-t border-gray-100 flex items-center justify-end gap-3 bg-gray-50/50">
                <a href="{{ route('indentroview.index') }}" class="px-4 py-2.5 rounded-xl text-xs font-semibold bg-gray-100 hover:bg-gray-200 text-gray-700">
                    Reset
                </a>
                <button type="button" onclick="document.getElementById('drawerFilterForm').submit()" 
                        class="px-5 py-2.5 rounded-xl text-xs font-bold bg-indigo-600 hover:bg-indigo-700 text-white shadow-xs">
                    Apply Filters
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Status Confirm Modal -->
<div id="poStatusModal" class="fixed inset-0 z-50 hidden">
  <div class="absolute inset-0 bg-black/50" onclick="closePOStatusModal()"></div>
  <div class="relative mx-auto mt-24 w-[90%] max-w-md rounded-2xl bg-white shadow-xl p-6 border border-gray-100">
    <div class="pb-3 border-b mb-3">
      <h4 id="poModalTitle" class="text-lg font-bold text-gray-900">Confirm Action</h4>
    </div>
    <div class="py-2 mb-4">
      <p id="poModalText" class="text-xs text-gray-600">Are you sure you want to proceed?</p>
    </div>
    <div class="pt-3 border-t flex items-center justify-end gap-2">
      <button type="button" onclick="closePOStatusModal()"
              class="px-4 py-2 text-xs font-semibold rounded-xl border border-gray-300 text-gray-700 hover:bg-gray-50">
        No
      </button>
      <button type="button" id="poConfirmBtn"
              class="px-4 py-2 text-xs font-bold rounded-xl text-white bg-slate-900">
        Yes
      </button>
    </div>
  </div>
</div>

<script>
  function openRightFilterDrawer() {
      document.getElementById('rightFilterDrawer').classList.remove('hidden');
  }

  function closeRightFilterDrawer() {
      document.getElementById('rightFilterDrawer').classList.add('hidden');
  }

  let activePOForm = null;

  function confirmPOStatus(btn) {
    activePOForm = btn.closest('form');
    const action = btn.dataset.action.toLowerCase();
    
    const modal = document.getElementById('poStatusModal');
    const title = document.getElementById('poModalTitle');
    const text = document.getElementById('poModalText');
    const confirmBtn = document.getElementById('poConfirmBtn');

    const config = {
      close: { title: 'Confirm Close', text: 'This will mark the Indent & PO as Closed. Continue?', cls: 'bg-gray-800 hover:bg-gray-900' },
      cancel: { title: 'Confirm Cancel', text: 'This will mark the Indent & PO as Cancelled. Continue?', cls: 'bg-red-600 hover:bg-red-700' },
      pending: { title: 'Re-Open (Pending)', text: 'This will set the status back to Pending. Continue?', cls: 'bg-emerald-600 hover:bg-emerald-700' }
    };

    const cfg = config[action] || config.close;
    title.textContent = cfg.title;
    text.textContent = cfg.text;

    confirmBtn.className = 'px-4 py-2 text-xs font-bold rounded-xl text-white';
    confirmBtn.classList.add(...cfg.cls.split(' '));
    modal.classList.remove('hidden');
  }

  function closePOStatusModal() {
    document.getElementById('poStatusModal').classList.add('hidden');
    activePOForm = null;
  }

  document.getElementById('poConfirmBtn')?.addEventListener('click', function() {
    if (activePOForm) {
      activePOForm.submit();
    }
    closePOStatusModal();
  });
</script>
@endsection