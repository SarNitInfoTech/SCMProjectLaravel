@extends("layouts.layout")

@section("bodyContent")
<div style="width: 100%; max-width: 100%; box-sizing: border-box; font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">

    <!-- Top Page Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
        <div style="display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 14px; background: #EEF2FF; color: #4F46E5; display: flex; align-items: center; justify-content: center; border: 1px solid #C7D2FE;">
                <svg style="width: 24px; height: 24px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <div>
                <h1 style="font-size: 24px; font-weight: 800; color: #0F172A; margin: 0 0 2px 0; letter-spacing: -0.5px;">PO Register List</h1>
                <p style="font-size: 13px; color: #64748B; margin: 0; font-weight: 500;">Manage and update purchase orders</p>
            </div>
        </div>

        <!-- Bulk Import Button -->
        <a href="{{ route('bulk-upload.index', ['module' => 'po-registers']) }}" 
           style="background: #4F46E5; color: #FFFFFF; font-weight: 700; font-size: 13px; padding: 10px 18px; border-radius: 10px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 2px 6px rgba(79,70,229,0.25);">
            <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
            <span>Bulk Import</span>
            <svg style="width: 12px; height: 12px; margin-left: 2px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
        </a>
    </div>

    @if (session('success'))
        <div style="padding: 14px 18px; background: #ECFDF5; border-left: 4px solid #10B981; color: #065F46; font-size: 13px; font-weight: 600; border-radius: 8px; margin-bottom: 20px;">
            {{ session('success') }}
        </div>
    @endif

    <!-- Main Card Container (Full Width) -->
    <div style="background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.03); overflow: hidden; margin-bottom: 24px;">
        
        <!-- Card Header Toolbar -->
        <div style="padding: 20px; border-bottom: 1px solid #F1F5F9; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; background: #FAFAFA;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <div style="width: 34px; height: 34px; border-radius: 10px; background: #EEF2FF; color: #4F46E5; display: flex; align-items: center; justify-content: center; border: 1px solid #C7D2FE;">
                    <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <h2 style="font-size: 18px; font-weight: 800; color: #0F172A; margin: 0;">PO Register List</h2>
            </div>

            <!-- Search & Right Filter Drawer Controls -->
            <form method="GET" action="{{ route('indentroview.index') }}" style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                @if(request('department_id')) <input type="hidden" name="department_id" value="{{ request('department_id') }}"> @endif
                @if(request('status')) <input type="hidden" name="status" value="{{ request('status') }}"> @endif
                @if(request('party_name')) <input type="hidden" name="party_name" value="{{ request('party_name') }}"> @endif
                @if(request('date_from')) <input type="hidden" name="date_from" value="{{ request('date_from') }}"> @endif
                @if(request('date_to')) <input type="hidden" name="date_to" value="{{ request('date_to') }}"> @endif

                <!-- Search Input -->
                <div style="position: relative; width: 340px;">
                    <span style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #94A3B8;">
                        <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by ID, department, party name, item..." 
                           style="width: 100%; padding: 9px 12px 9px 38px; background: #FFFFFF; border: 1px solid #CBD5E1; border-radius: 10px; font-size: 13px; outline: none; box-sizing: border-box;">
                </div>

                <!-- Right Drawer Filter Button -->
                <button type="button" onclick="openRightFilterDrawer()" 
                        style="background: #FFFFFF; border: 1px solid #C7D2FE; color: #4F46E5; font-weight: 700; padding: 9px 16px; border-radius: 10px; font-size: 13px; cursor: pointer; display: inline-flex; align-items: center; gap: 6px;">
                    <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                    <span>Filter</span>
                    @if(request()->anyFilled(['department_id', 'status', 'party_name', 'date_from', 'date_to']))
                        <span style="width: 8px; height: 8px; border-radius: 50%; background: #4F46E5; display: inline-block;"></span>
                    @endif
                </button>

                <!-- Search Button -->
                <button type="submit" style="background: #0F172A; color: #FFFFFF; font-weight: 700; padding: 9px 20px; border-radius: 10px; font-size: 13px; border: none; cursor: pointer;">
                    Search
                </button>

                @if(request()->anyFilled(['search', 'department_id', 'status', 'party_name', 'date_from', 'date_to']))
                    <a href="{{ route('indentroview.index') }}" style="padding: 9px 14px; font-size: 13px; font-weight: 600; background: #F1F5F9; color: #475569; border-radius: 10px; text-decoration: none;">Reset</a>
                @endif
            </form>
        </div>

        <!-- Table Responsive Container (Full Width) -->
        <div style="width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch;">
            <table style="width: 100%; min-width: 1320px; border-collapse: collapse; text-align: left; font-size: 13px; table-layout: fixed;">
                <thead>
                    <tr style="background: #F8FAFC; border-bottom: 1px solid #E2E8F0; color: #64748B; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">
                        <th style="padding: 14px 16px; width: 90px; vertical-align: middle;">INDENT ID <span style="color:#CBD5E1;">↕</span></th>
                        <th style="padding: 14px 16px; width: 120px; vertical-align: middle;">DEPARTMENT <span style="color:#CBD5E1;">↕</span></th>
                        <th style="padding: 14px 16px; width: 170px; vertical-align: middle;">PARTY NAME <span style="color:#CBD5E1;">↕</span></th>
                        <th style="padding: 14px 16px; width: 220px; vertical-align: middle;">ITEM DESCRIPTION <span style="color:#CBD5E1;">↕</span></th>
                        <th style="padding: 14px 16px; width: 120px; vertical-align: middle;">AMOUNT <span style="color:#CBD5E1;">↕</span></th>
                        <th style="padding: 14px 16px; width: 90px; vertical-align: middle;">REMARKS <span style="color:#CBD5E1;">↕</span></th>
                        <th style="padding: 14px 16px; width: 170px; text-align: center; vertical-align: middle;">STATUS <span style="color:#CBD5E1;">↕</span></th>
                        <th style="padding: 14px 16px; width: 120px; vertical-align: middle;">PO DATE <span style="color:#CBD5E1;">↕</span></th>
                        <th style="padding: 14px 16px; width: 260px; text-align: center; vertical-align: middle;">ACTION</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rows as $row)
                        @php
                            $status = $row['status'] ?? 'Open';
                            $normSt = strtolower(trim($status));
                            
                            // HSL-tailored badge styling without emojis
                            $badgeBg = '#F8FAFC'; $badgeColor = '#475569'; $dotColor = '#64748B'; $badgeBorder = '#E2E8F0';
                            if ($normSt === 'partially received') {
                                $badgeBg = '#F3E8FF'; $badgeColor = '#7C3AED'; $dotColor = '#7C3AED'; $badgeBorder = '#E9D5FF';
                            } elseif ($normSt === 'open') {
                                $badgeBg = '#ECFDF5'; $badgeColor = '#047857'; $dotColor = '#10B981'; $badgeBorder = '#A7F3D0';
                            } elseif ($normSt === 'pending') {
                                $badgeBg = '#FFFBEB'; $badgeColor = '#B45309'; $dotColor = '#F59E0B'; $badgeBorder = '#FDE68A';
                            } elseif ($normSt === 'completed') {
                                $badgeBg = '#F0FDF4'; $badgeColor = '#15803D'; $dotColor = '#22C55E'; $badgeBorder = '#BBF7D0';
                            } elseif (in_array($normSt, ['cancel', 'cancelled'])) {
                                $badgeBg = '#FEF2F2'; $badgeColor = '#B91C1C'; $dotColor = '#EF4444'; $badgeBorder = '#FECDD3';
                            }

                            $actions = $row['action'];
                        @endphp
                        <tr style="border-bottom: 1px solid #F1F5F9; transition: background 0.15s ease;" onmouseover="this.style.background='#F8FAFC'" onmouseout="this.style.background='#FFFFFF'">
                            <!-- Indent ID -->
                            <td style="padding: 16px; vertical-align: middle; font-weight: 800; color: #0F172A; white-space: nowrap;">{{ $row['indent_id'] }}</td>
                            
                            <!-- Department -->
                            <td style="padding: 16px; vertical-align: middle; color: #475569; font-weight: 500; white-space: nowrap;">{{ $row['department_name'] && $row['department_name'] !== '-' ? $row['department_name'] : '-' }}</td>
                            
                            <!-- Party Name -->
                            <td style="padding: 16px; vertical-align: middle; color: #0F172A; font-weight: 700; white-space: nowrap;">{{ $row['party_name'] && $row['party_name'] !== '-' ? $row['party_name'] : '-' }}</td>
                            
                            <!-- Item Description -->
                            <td style="padding: 16px; vertical-align: middle; color: #334155; font-weight: 500; max-width: 220px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $row['item_description'] }}">{{ $row['item_description'] }}</td>
                            
                            <!-- Amount -->
                            <td style="padding: 16px; vertical-align: middle; font-weight: 800; color: #0F172A; white-space: nowrap; font-family: monospace; font-size: 14px;">₹{{ $row['po_amount'] }}</td>
                            
                            <!-- Remarks -->
                            <td style="padding: 16px; vertical-align: middle; color: #94A3B8; max-width: 90px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $row['remarks'] ?? '-' }}">{{ $row['remarks'] ?? '-' }}</td>
                            
                            <!-- Status Badge -->
                            <td style="padding: 16px; vertical-align: middle; text-align: center; white-space: nowrap;">
                                <span style="background: {{ $badgeBg }}; border: 1px solid {{ $badgeBorder }}; color: {{ $badgeColor }}; font-weight: 700; font-size: 11px; padding: 5px 14px; border-radius: 20px; display: inline-flex; align-items: center; gap: 6px;">
                                    <span style="width: 6px; height: 6px; border-radius: 50%; background: {{ $dotColor }}; display: inline-block;"></span>
                                    {{ ucfirst($status) }}
                                </span>
                            </td>
                            
                            <!-- PO Date -->
                            <td style="padding: 16px; vertical-align: middle; color: #64748B; font-family: monospace; white-space: nowrap;">{{ !empty($row['po_date']) && $row['po_date'] !== '-' ? date('d-m-Y', strtotime($row['po_date'])) : '-' }}</td>
                            
                            <!-- Actions Grid Buttons with SVG Google/Material Icons -->
                            <td style="padding: 16px; vertical-align: middle; text-align: center; white-space: nowrap;">
                                <div style="display: inline-flex; align-items: center; gap: 8px;">
                                    <div style="display: grid; grid-template-columns: repeat(2, auto); gap: 6px; align-items: center;">
                                        <!-- File Invoice Button -->
                                        @if (isset($actions['viewPage']))
                                            <a href="{{ $actions['viewPage'] }}"
                                               style="background: #F3E8FF; border: 1px solid #E9D5FF; color: #7C3AED; font-weight: 700; font-size: 11px; padding: 6px 12px; border-radius: 20px; text-decoration: none; display: inline-flex; align-items: center; gap: 5px; white-space: nowrap;">
                                                <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                <span>{{ $viewBtnTitle }}</span>
                                            </a>
                                        @endif

                                        <!-- File PO Button -->
                                        @if (isset($actions['file_po']))
                                            <a href="{{ $actions['file_po'] }}"
                                               style="background: #FFF7ED; border: 1px solid #FED7AA; color: #EA580C; font-weight: 700; font-size: 11px; padding: 6px 12px; border-radius: 20px; text-decoration: none; display: inline-flex; align-items: center; gap: 5px; white-space: nowrap;">
                                                <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                <span>File PO</span>
                                            </a>
                                        @endif

                                        <!-- Reopen Button -->
                                        @if (isset($actions['pending']))
                                            <form action="{{ $actions['pending']['route'] }}" method="POST" class="js-po-status-form" style="display: inline;">
                                                @csrf
                                                <input type="hidden" name="indent_id" value="{{ $actions['pending']['params']['indent_id'] }}">
                                                <input type="hidden" name="department_id" value="{{ $actions['pending']['params']['department_id'] }}">
                                                <input type="hidden" name="status" value="Pending">
                                                <button type="button" data-action="Pending" onclick="confirmPOStatus(this)"
                                                        style="background: #ECFDF5; border: 1px solid #A7F3D0; color: #047857; font-weight: 700; font-size: 11px; padding: 6px 12px; border-radius: 20px; cursor: pointer; display: inline-flex; align-items: center; gap: 5px; white-space: nowrap;">
                                                    <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>
                                                    <span>Re-Open</span>
                                                </button>
                                            </form>
                                        @endif

                                        <!-- Close Button -->
                                        @if (isset($actions['close']))
                                            <form action="{{ $actions['close']['route'] }}" method="POST" class="js-po-status-form" style="display: inline;">
                                                @csrf
                                                <input type="hidden" name="indent_id" value="{{ $actions['close']['params']['indent_id'] }}">
                                                <input type="hidden" name="department_id" value="{{ $actions['close']['params']['department_id'] }}">
                                                <input type="hidden" name="status" value="Close">
                                                <button type="button" data-action="Close" onclick="confirmPOStatus(this)"
                                                        style="background: #F1F5F9; border: 1px solid #CBD5E1; color: #334155; font-weight: 700; font-size: 11px; padding: 6px 12px; border-radius: 20px; cursor: pointer; display: inline-flex; align-items: center; gap: 5px; white-space: nowrap;">
                                                    <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                    <span>Close</span>
                                                </button>
                                            </form>
                                        @endif

                                        <!-- Cancel Button -->
                                        @if (isset($actions['cancel']))
                                            <form action="{{ $actions['cancel']['route'] }}" method="POST" class="js-po-status-form" style="display: inline;">
                                                @csrf
                                                <input type="hidden" name="indent_id" value="{{ $actions['cancel']['params']['indent_id'] }}">
                                                <input type="hidden" name="department_id" value="{{ $actions['cancel']['params']['department_id'] }}">
                                                <input type="hidden" name="status" value="Cancel">
                                                <button type="button" data-action="Cancel" onclick="confirmPOStatus(this)"
                                                        style="background: #FEF2F2; border: 1px solid #FECDD3; color: #DC2626; font-weight: 700; font-size: 11px; padding: 6px 12px; border-radius: 20px; cursor: pointer; display: inline-flex; align-items: center; gap: 5px; white-space: nowrap;">
                                                    <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                    <span>Cancel</span>
                                                </button>
                                            </form>
                                        @endif
                                    </div>

                                    <!-- 3-Dots SVG Icon -->
                                    <button type="button" style="background: transparent; border: none; color: #94A3B8; cursor: pointer; padding: 0 4px; display: inline-flex; align-items: center;">
                                        <svg style="width: 16px; height: 16px;" fill="currentColor" viewBox="0 0 24 24"><path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" style="text-align: center; padding: 32px; color: #64748B; font-weight: 600;">No PO records found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Bottom Pagination Bar -->
        <div style="padding: 16px 20px; border-top: 1px solid #F1F5F9; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; font-size: 13px; color: #64748B; font-weight: 500;">
            <div>
                Showing {{ $pagination->firstItem() ?? 0 }} to {{ $pagination->lastItem() ?? 0 }} of {{ $pagination->total() }} entries
            </div>

            <!-- Page Number Controls -->
            <div>
                {{ $pagination->appends(request()->query())->links('pagination::tailwind') }}
            </div>

            <!-- Per Page Selector -->
            <form method="GET" action="{{ route('indentroview.index') }}" style="display: flex; align-items: center; gap: 8px;">
                @foreach(request()->except(['per_page', 'page']) as $k => $v)
                    <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                @endforeach
                <select name="per_page" onchange="this.form.submit()" style="background: #F8FAFC; border: 1px solid #CBD5E1; border-radius: 8px; padding: 4px 8px; font-size: 12px; font-weight: 700; outline: none;">
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
<div id="rightFilterDrawer" style="display: none; position: fixed; inset: 0; z-index: 99999;">
    <!-- Backdrop Overlay -->
    <div onclick="closeRightFilterDrawer()" style="position: fixed; inset: 0; background: rgba(15,23,42,0.4); backdrop-filter: blur(2px);"></div>

    <!-- Drawer Panel sliding from Right -->
    <div style="position: fixed; top: 0; right: 0; bottom: 0; width: 420px; max-width: 90vw; background: #FFFFFF; box-shadow: -10px 0 25px rgba(0,0,0,0.15); display: flex; flex-direction: column; justify-content: space-between; z-index: 100000; animation: slideInRight 0.3s ease-out;">
        
        <!-- Header -->
        <div style="padding: 20px; border-bottom: 1px solid #E2E8F0; display: flex; align-items: center; justify-content: space-between; background: #F8FAFC;">
            <div style="display: flex; align-items: center; gap: 8px; font-weight: 800; font-size: 16px; color: #0F172A;">
                <svg style="width: 20px; height: 20px; color: #4F46E5;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                <span>Filter PO Registers</span>
            </div>
            <button type="button" onclick="closeRightFilterDrawer()" style="background: transparent; border: none; font-size: 24px; font-weight: 700; cursor: pointer; color: #94A3B8;">&times;</button>
        </div>

        <!-- Body Form -->
        <form id="drawerFilterForm" method="GET" action="{{ route('indentroview.index') }}" style="padding: 20px; overflow-y: auto; flex: 1;">
            @if(request('search')) <input type="hidden" name="search" value="{{ request('search') }}"> @endif

            <!-- Department Filter -->
            <div style="margin-bottom: 18px;">
                <label style="display: block; font-size: 11px; font-weight: 700; color: #475569; text-transform: uppercase; margin-bottom: 6px;">Department</label>
                <select name="department_id" style="width: 100%; padding: 10px 12px; border: 1px solid #CBD5E1; border-radius: 10px; font-size: 13px; outline: none; background: #F8FAFC;">
                    <option value="">All Departments</option>
                    @if(!empty($departments))
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                        @endforeach
                    @endif
                </select>
            </div>

            <!-- Status Filter -->
            <div style="margin-bottom: 18px;">
                <label style="display: block; font-size: 11px; font-weight: 700; color: #475569; text-transform: uppercase; margin-bottom: 6px;">PO Status</label>
                <select name="status" style="width: 100%; padding: 10px 12px; border: 1px solid #CBD5E1; border-radius: 10px; font-size: 13px; outline: none; background: #F8FAFC;">
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
            <div style="margin-bottom: 18px;">
                <label style="display: block; font-size: 11px; font-weight: 700; color: #475569; text-transform: uppercase; margin-bottom: 6px;">Party Name</label>
                <input type="text" name="party_name" value="{{ request('party_name') }}" placeholder="Search party name..." 
                       style="width: 100%; padding: 10px 12px; border: 1px solid #CBD5E1; border-radius: 10px; font-size: 13px; outline: none; background: #F8FAFC; box-sizing: border-box;">
            </div>

            <!-- Date Range -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 18px;">
                <div>
                    <label style="display: block; font-size: 11px; font-weight: 700; color: #475569; text-transform: uppercase; margin-bottom: 6px;">PO Date From</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" 
                           style="width: 100%; padding: 10px 12px; border: 1px solid #CBD5E1; border-radius: 10px; font-size: 12px; outline: none; background: #F8FAFC; box-sizing: border-box;">
                </div>
                <div>
                    <label style="display: block; font-size: 11px; font-weight: 700; color: #475569; text-transform: uppercase; margin-bottom: 6px;">PO Date To</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" 
                           style="width: 100%; padding: 10px 12px; border: 1px solid #CBD5E1; border-radius: 10px; font-size: 12px; outline: none; background: #F8FAFC; box-sizing: border-box;">
                </div>
            </div>
        </form>

        <!-- Footer -->
        <div style="padding: 16px 20px; border-top: 1px solid #E2E8F0; background: #F8FAFC; display: flex; justify-content: flex-end; gap: 10px;">
            <a href="{{ route('indentroview.index') }}" style="padding: 10px 18px; border-radius: 10px; font-size: 13px; font-weight: 600; background: #F1F5F9; color: #475569; text-decoration: none;">Reset</a>
            <button type="button" onclick="document.getElementById('drawerFilterForm').submit()" style="padding: 10px 22px; border-radius: 10px; font-size: 13px; font-weight: 700; background: #4F46E5; color: #FFFFFF; border: none; cursor: pointer;">Apply Filters</button>
        </div>
    </div>
</div>

<!-- Status Confirm Modal -->
<div id="poStatusModal" style="display: none; position: fixed; inset: 0; z-index: 99999; background: rgba(15,23,42,0.5); align-items: center; justify-content: center;">
  <div style="background: #FFFFFF; border-radius: 16px; padding: 24px; width: 100%; max-width: 440px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1);">
    <div style="padding-bottom: 12px; border-bottom: 1px solid #F1F5F9; margin-bottom: 14px;">
      <h4 id="poModalTitle" style="font-size: 18px; font-weight: 800; color: #0F172A; margin: 0;">Confirm Action</h4>
    </div>
    <div style="margin-bottom: 20px;">
      <p id="poModalText" style="font-size: 13px; color: #475569; margin: 0;">Are you sure you want to proceed?</p>
    </div>
    <div style="display: flex; justify-content: flex-end; gap: 10px;">
      <button type="button" onclick="closePOStatusModal()" style="padding: 8px 16px; border-radius: 8px; font-size: 12px; font-weight: 600; background: #F1F5F9; color: #475569; border: 1px solid #CBD5E1; cursor: pointer;">
        No
      </button>
      <button type="button" id="poConfirmBtn" style="padding: 8px 18px; border-radius: 8px; font-size: 12px; font-weight: 700; color: #FFFFFF; background: #0F172A; border: none; cursor: pointer;">
        Yes
      </button>
    </div>
  </div>
</div>

<style>
@keyframes slideInRight {
    from { transform: translateX(100%); }
    to { transform: translateX(0); }
}
</style>

<script>
  function openRightFilterDrawer() {
      document.getElementById('rightFilterDrawer').style.display = 'block';
  }

  function closeRightFilterDrawer() {
      document.getElementById('rightFilterDrawer').style.display = 'none';
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
      close: { title: 'Confirm Close', text: 'This will mark the Indent & PO as Closed. Continue?', bg: '#0F172A' },
      cancel: { title: 'Confirm Cancel', text: 'This will mark the Indent & PO as Cancelled. Continue?', bg: '#DC2626' },
      pending: { title: 'Re-Open (Pending)', text: 'This will set the status back to Pending. Continue?', bg: '#047857' }
    };

    const cfg = config[action] || config.close;
    title.textContent = cfg.title;
    text.textContent = cfg.text;

    confirmBtn.style.background = cfg.bg;
    modal.style.display = 'flex';
  }

  function closePOStatusModal() {
    document.getElementById('poStatusModal').style.display = 'none';
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