@extends("layouts.layout")

@section("bodyContent")
<div style="width: 100%; max-width: 100%; box-sizing: border-box; font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">

    <!-- Top Page Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
        <div style="display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 14px; background: #2563EB; color: #FFFFFF; display: flex; align-items: center; justify-content: center; shadow: 0 4px 12px rgba(37,99,235,0.25);">
                <svg style="width: 24px; height: 24px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <div>
                <h1 style="font-size: 24px; font-weight: 800; color: #0F172A; margin: 0 0 2px 0; letter-spacing: -0.5px;">Indent Register List</h1>
                <p style="font-size: 13px; color: #64748B; margin: 0; font-weight: 500;">Track, edit and transition status of indent requests</p>
            </div>
        </div>

        <!-- Top Right Action Buttons -->
        <div style="display: flex; align-items: center; gap: 10px;">
            <a href="{{ route('bulk-upload.index', ['module' => 'indents']) }}" 
               style="background: #059669; color: #FFFFFF; font-weight: 700; font-size: 13px; padding: 10px 18px; border-radius: 10px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 2px 6px rgba(5,150,105,0.25);">
                <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                <span>Bulk Import</span>
            </a>
            <a href="{{ route('indent.create') }}" 
               style="background: #2563EB; color: #FFFFFF; font-weight: 700; font-size: 13px; padding: 10px 18px; border-radius: 10px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 2px 6px rgba(37,99,235,0.25);">
                <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                <span>Add New Indent</span>
            </a>
        </div>
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
                <div style="width: 34px; height: 34px; border-radius: 10px; background: #EFF6FF; color: #2563EB; display: flex; align-items: center; justify-content: center; border: 1px solid #BFDBFE;">
                    <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <h2 style="font-size: 18px; font-weight: 800; color: #0F172A; margin: 0;">Indent Register List</h2>
            </div>

            <!-- Search & Right Filter Drawer Controls -->
            <form method="GET" action="{{ route('indent.index') }}" style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                @if(request('department')) <input type="hidden" name="department" value="{{ request('department') }}"> @endif
                @if(request('project')) <input type="hidden" name="project" value="{{ request('project') }}"> @endif
                @if(request('status')) <input type="hidden" name="status" value="{{ request('status') }}"> @endif
                @if(request('date_from')) <input type="hidden" name="date_from" value="{{ request('date_from') }}"> @endif
                @if(request('date_to')) <input type="hidden" name="date_to" value="{{ request('date_to') }}"> @endif

                <!-- Search Input -->
                <div style="position: relative; width: 340px;">
                    <span style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #94A3B8;">
                        <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search ID, department, project, item..." 
                           style="width: 100%; padding: 9px 12px 9px 38px; background: #FFFFFF; border: 1px solid #CBD5E1; border-radius: 10px; font-size: 13px; outline: none; box-sizing: border-box;">
                </div>

                <!-- Right Drawer Filter Button -->
                <button type="button" onclick="openRightFilterDrawer()" 
                        style="background: #FFFFFF; border: 1px solid #DBEAFE; color: #2563EB; font-weight: 700; padding: 9px 16px; border-radius: 10px; font-size: 13px; cursor: pointer; display: inline-flex; align-items: center; gap: 6px;">
                    <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                    <span>Filter</span>
                    @if(request()->anyFilled(['department', 'project', 'status', 'date_from', 'date_to']))
                        <span style="width: 8px; height: 8px; border-radius: 50%; background: #2563EB; display: inline-block;"></span>
                    @endif
                </button>

                <!-- Search Button -->
                <button type="submit" style="background: #0F172A; color: #FFFFFF; font-weight: 700; padding: 9px 20px; border-radius: 10px; font-size: 13px; border: none; cursor: pointer;">
                    Search
                </button>

                @if(request()->anyFilled(['search', 'department', 'project', 'status', 'date_from', 'date_to']))
                    <a href="{{ route('indent.index') }}" style="padding: 9px 14px; font-size: 13px; font-weight: 600; background: #F1F5F9; color: #475569; border-radius: 10px; text-decoration: none;">Reset</a>
                @endif
            </form>
        </div>

        <!-- Table Responsive Container (Full Width) -->
        <div style="width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch;">
            <table style="width: 100%; min-width: 1200px; border-collapse: collapse; text-align: left; font-size: 13px; table-layout: fixed;">
                <thead>
                    <tr style="background: #F8FAFC; border-bottom: 1px solid #E2E8F0; color: #64748B; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">
                        <th style="padding: 14px 16px; width: 100px; vertical-align: middle;">INDENT ID <span style="color:#CBD5E1;">↕</span></th>
                        <th style="padding: 14px 16px; width: 140px; vertical-align: middle;">DEPARTMENT <span style="color:#CBD5E1;">↕</span></th>
                        <th style="padding: 14px 16px; width: 150px; vertical-align: middle;">PROJECT <span style="color:#CBD5E1;">↕</span></th>
                        <th style="padding: 14px 16px; width: 250px; vertical-align: middle;">DESCRIPTION <span style="color:#CBD5E1;">↕</span></th>
                        <th style="padding: 14px 16px; width: 120px; vertical-align: middle;">REMARKS <span style="color:#CBD5E1;">↕</span></th>
                        <th style="padding: 14px 16px; width: 160px; text-align: center; vertical-align: middle;">STATUS <span style="color:#CBD5E1;">↕</span></th>
                        <th style="padding: 14px 16px; width: 130px; vertical-align: middle;">CREATED DATE <span style="color:#CBD5E1;">↕</span></th>
                        <th style="padding: 14px 16px; width: 240px; text-align: center; vertical-align: middle;">ACTION</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rows as $row)
                        @php
                            $status = $row['status'] ?? 'Pending';
                            $normSt = strtolower(trim($status));
                            
                            $badgeBg = '#F8FAFC'; $badgeColor = '#475569'; $dotColor = '#64748B'; $badgeBorder = '#E2E8F0';
                            if (in_array($normSt, ['pending', 'open'])) {
                                $badgeBg = '#FEF3C7'; $badgeColor = '#D97706'; $dotColor = '#D97706'; $badgeBorder = '#FDE68A';
                            } elseif ($normSt === 'partially received') {
                                $badgeBg = '#F3E8FF'; $badgeColor = '#7C3AED'; $dotColor = '#7C3AED'; $badgeBorder = '#E9D5FF';
                            } elseif (in_array($normSt, ['completed', 'close', 'closed'])) {
                                $badgeBg = '#ECFDF5'; $badgeColor = '#047857'; $dotColor = '#10B981'; $badgeBorder = '#A7F3D0';
                            } elseif (in_array($normSt, ['cancel', 'cancelled'])) {
                                $badgeBg = '#FEF2F2'; $badgeColor = '#DC2626'; $dotColor = '#DC2626'; $badgeBorder = '#FECDD3';
                            }

                            $actions = $row['action'];
                        @endphp
                        <tr style="border-bottom: 1px solid #F1F5F9; transition: background 0.15s ease;" onmouseover="this.style.background='#F8FAFC'" onmouseout="this.style.background='#FFFFFF'">
                            <!-- Indent ID -->
                            <td style="padding: 16px; vertical-align: middle; font-weight: 800; color: #0F172A; white-space: nowrap;">{{ $row['indent_id'] }}</td>
                            
                            <!-- Department Name -->
                            <td style="padding: 16px; vertical-align: middle; color: #475569; font-weight: 500; white-space: nowrap;">{{ $row['department_name'] }}</td>
                            
                            <!-- Project -->
                            <td style="padding: 16px; vertical-align: middle; color: #0F172A; font-weight: 700; white-space: nowrap;">{{ $row['project'] }}</td>
                            
                            <!-- Description -->
                            <td style="padding: 16px; vertical-align: middle; color: #334155; font-weight: 500; max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $row['item_description'] }}">{{ $row['item_description'] }}</td>
                            
                            <!-- Remarks -->
                            <td style="padding: 16px; vertical-align: middle; color: #94A3B8; max-width: 120px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $row['remarks'] ?? '-' }}">{{ $row['remarks'] ?? '-' }}</td>
                            
                            <!-- Status Badge -->
                            <td style="padding: 16px; vertical-align: middle; text-align: center; white-space: nowrap;">
                                <span style="background: {{ $badgeBg }}; border: 1px solid {{ $badgeBorder }}; color: {{ $badgeColor }}; font-weight: 700; font-size: 12px; padding: 5px 14px; border-radius: 20px; display: inline-flex; align-items: center; gap: 6px;">
                                    <span style="width: 6px; height: 6px; border-radius: 50%; background: {{ $dotColor }}; display: inline-block;"></span>
                                    {{ ucfirst($status) }}
                                </span>
                            </td>
                            
                            <!-- Date -->
                            <td style="padding: 16px; vertical-align: middle; color: #64748B; font-family: monospace; white-space: nowrap;">{{ !empty($row['date']) && $row['date'] !== '-' ? date('d-m-Y', strtotime($row['date'])) : '-' }}</td>
                            
                            <!-- Actions Grid Buttons with Vector SVG Icons -->
                            <td style="padding: 16px; vertical-align: middle; text-align: center; white-space: nowrap;">
                                <div style="display: inline-flex; align-items: center; gap: 8px;">
                                    <div style="display: grid; grid-template-columns: repeat(2, auto); gap: 6px; align-items: center;">
                                        <!-- Edit Button -->
                                        @if (isset($actions['edit']))
                                            <a href="{{ $actions['edit'] }}"
                                               style="background: #EFF6FF; border: 1px solid #BFDBFE; color: #2563EB; font-weight: 700; font-size: 11px; padding: 6px 12px; border-radius: 20px; text-decoration: none; display: inline-flex; align-items: center; gap: 5px; white-space: nowrap;">
                                                <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                <span>Edit</span>
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
                                            <form action="{{ $actions['pending']['route'] }}" method="POST" class="js-indent-status-form" style="display: inline;">
                                                @csrf
                                                <input type="hidden" name="indent_id" value="{{ $actions['pending']['params']['indent_id'] }}">
                                                <input type="hidden" name="department_id" value="{{ $actions['pending']['params']['department_id'] }}">
                                                <input type="hidden" name="status" value="Pending">
                                                <button type="button" data-action="Pending" onclick="confirmIndentStatus(this)"
                                                        style="background: #ECFDF5; border: 1px solid #A7F3D0; color: #047857; font-weight: 700; font-size: 11px; padding: 6px 12px; border-radius: 20px; cursor: pointer; display: inline-flex; align-items: center; gap: 5px; white-space: nowrap;">
                                                    <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>
                                                    <span>Re-Open</span>
                                                </button>
                                            </form>
                                        @endif

                                        <!-- Close Button -->
                                        @if (isset($actions['close']))
                                            <form action="{{ $actions['close']['route'] }}" method="POST" class="js-indent-status-form" style="display: inline;">
                                                @csrf
                                                <input type="hidden" name="indent_id" value="{{ $actions['close']['params']['indent_id'] }}">
                                                <input type="hidden" name="department_id" value="{{ $actions['close']['params']['department_id'] }}">
                                                <input type="hidden" name="status" value="Close">
                                                <button type="button" data-action="Close" onclick="confirmIndentStatus(this)"
                                                        style="background: #F8FAFC; border: 1px solid #E2E8F0; color: #334155; font-weight: 700; font-size: 11px; padding: 6px 12px; border-radius: 20px; cursor: pointer; display: inline-flex; align-items: center; gap: 5px; white-space: nowrap;">
                                                    <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                    <span>Close</span>
                                                </button>
                                            </form>
                                        @endif

                                        <!-- Cancel Button -->
                                        @if (isset($actions['cancel']))
                                            <form action="{{ $actions['cancel']['route'] }}" method="POST" class="js-indent-status-form" style="display: inline;">
                                                @csrf
                                                <input type="hidden" name="indent_id" value="{{ $actions['cancel']['params']['indent_id'] }}">
                                                <input type="hidden" name="department_id" value="{{ $actions['cancel']['params']['department_id'] }}">
                                                <input type="hidden" name="status" value="Cancel">
                                                <button type="button" data-action="Cancel" onclick="confirmIndentStatus(this)"
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
                            <td colspan="8" style="text-align: center; padding: 32px; color: #64748B; font-weight: 600;">No indent records found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Bottom Pagination Bar -->
        <div style="padding: 16px 20px; border-top: 1px solid #F1F5F9; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; font-size: 13px; color: #64748B; font-weight: 500;">
            <div>
                Showing {{ $registers->firstItem() ?? 0 }} to {{ $registers->lastItem() ?? 0 }} of {{ $registers->total() }} entries
            </div>

            <!-- Page Number Controls -->
            <div>
                {{ $registers->appends(request()->query())->links('pagination::tailwind') }}
            </div>

            <!-- Per Page Selector -->
            <form method="GET" action="{{ route('indent.index') }}" style="display: flex; align-items: center; gap: 8px;">
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

<!-- Right Slide-over Filter Drawer for Indents -->
<div id="rightFilterDrawer" style="display: none; position: fixed; inset: 0; z-index: 99999;">
    <!-- Backdrop Overlay -->
    <div onclick="closeRightFilterDrawer()" style="position: fixed; inset: 0; background: rgba(15,23,42,0.4); backdrop-filter: blur(2px);"></div>

    <!-- Drawer Panel sliding from Right -->
    <div style="position: fixed; top: 0; right: 0; bottom: 0; width: 420px; max-width: 90vw; background: #FFFFFF; box-shadow: -10px 0 25px rgba(0,0,0,0.15); display: flex; flex-direction: column; justify-content: space-between; z-index: 100000; animation: slideInRight 0.3s ease-out;">
        
        <!-- Header -->
        <div style="padding: 20px; border-bottom: 1px solid #E2E8F0; display: flex; align-items: center; justify-content: space-between; background: #F8FAFC;">
            <div style="display: flex; align-items: center; gap: 8px; font-weight: 800; font-size: 16px; color: #0F172A;">
                <svg style="width: 20px; height: 20px; color: #2563EB;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                <span>Filter Indent Registers</span>
            </div>
            <button type="button" onclick="closeRightFilterDrawer()" style="background: transparent; border: none; font-size: 24px; font-weight: 700; cursor: pointer; color: #94A3B8;">&times;</button>
        </div>

        <!-- Body Form -->
        <form id="drawerFilterForm" method="GET" action="{{ route('indent.index') }}" style="padding: 20px; overflow-y: auto; flex: 1;">
            @if(request('search')) <input type="hidden" name="search" value="{{ request('search') }}"> @endif

            <!-- Department Filter -->
            <div style="margin-bottom: 18px;">
                <label style="display: block; font-size: 11px; font-weight: 700; color: #475569; text-transform: uppercase; margin-bottom: 6px;">Department</label>
                <select name="department" style="width: 100%; padding: 10px 12px; border: 1px solid #CBD5E1; border-radius: 10px; font-size: 13px; outline: none; background: #F8FAFC;">
                    <option value="">All Departments</option>
                    @if(!empty($departments))
                        @foreach($departments as $dept)
                            <option value="{{ $dept->name }}" {{ request('department') == $dept->name ? 'selected' : '' }}>{{ $dept->name }}</option>
                        @endforeach
                    @endif
                </select>
            </div>

            <!-- Project Filter -->
            <div style="margin-bottom: 18px;">
                <label style="display: block; font-size: 11px; font-weight: 700; color: #475569; text-transform: uppercase; margin-bottom: 6px;">Project</label>
                <select name="project" style="width: 100%; padding: 10px 12px; border: 1px solid #CBD5E1; border-radius: 10px; font-size: 13px; outline: none; background: #F8FAFC;">
                    <option value="">All Projects</option>
                    @if(!empty($projects))
                        @foreach($projects as $proj)
                            <option value="{{ $proj->name }}" {{ request('project') == $proj->name ? 'selected' : '' }}>{{ $proj->name }}</option>
                        @endforeach
                    @endif
                </select>
            </div>

            <!-- Status Filter -->
            <div style="margin-bottom: 18px;">
                <label style="display: block; font-size: 11px; font-weight: 700; color: #475569; text-transform: uppercase; margin-bottom: 6px;">Indent Status</label>
                <select name="status" style="width: 100%; padding: 10px 12px; border: 1px solid #CBD5E1; border-radius: 10px; font-size: 13px; outline: none; background: #F8FAFC;">
                    <option value="">All Statuses</option>
                    <option value="pending" {{ strtolower(request('status')) === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="open" {{ strtolower(request('status')) === 'open' ? 'selected' : '' }}>Open</option>
                    <option value="partially received" {{ strtolower(request('status')) === 'partially received' ? 'selected' : '' }}>Partially received</option>
                    <option value="completed" {{ strtolower(request('status')) === 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="close" {{ strtolower(request('status')) === 'close' ? 'selected' : '' }}>Closed</option>
                    <option value="cancel" {{ strtolower(request('status')) === 'cancel' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>

            <!-- Date Range -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 18px;">
                <div>
                    <label style="display: block; font-size: 11px; font-weight: 700; color: #475569; text-transform: uppercase; margin-bottom: 6px;">Date From</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" 
                           style="width: 100%; padding: 10px 12px; border: 1px solid #CBD5E1; border-radius: 10px; font-size: 12px; outline: none; background: #F8FAFC; box-sizing: border-box;">
                </div>
                <div>
                    <label style="display: block; font-size: 11px; font-weight: 700; color: #475569; text-transform: uppercase; margin-bottom: 6px;">Date To</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" 
                           style="width: 100%; padding: 10px 12px; border: 1px solid #CBD5E1; border-radius: 10px; font-size: 12px; outline: none; background: #F8FAFC; box-sizing: border-box;">
                </div>
            </div>
        </form>

        <!-- Footer -->
        <div style="padding: 16px 20px; border-top: 1px solid #E2E8F0; background: #F8FAFC; display: flex; justify-content: flex-end; gap: 10px;">
            <a href="{{ route('indent.index') }}" style="padding: 10px 18px; border-radius: 10px; font-size: 13px; font-weight: 600; background: #F1F5F9; color: #475569; text-decoration: none;">Reset</a>
            <button type="button" onclick="document.getElementById('drawerFilterForm').submit()" style="padding: 10px 22px; border-radius: 10px; font-size: 13px; font-weight: 700; background: #2563EB; color: #FFFFFF; border: none; cursor: pointer;">Apply Filters</button>
        </div>
    </div>
</div>

<!-- Status Confirm Modal -->
<div id="indentStatusModal" style="display: none; position: fixed; inset: 0; z-index: 99999; background: rgba(15,23,42,0.5); align-items: center; justify-content: center;">
  <div style="background: #FFFFFF; border-radius: 16px; padding: 24px; width: 100%; max-width: 440px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1);">
    <div style="padding-bottom: 12px; border-bottom: 1px solid #F1F5F9; margin-bottom: 14px;">
      <h4 id="indentModalTitle" style="font-size: 18px; font-weight: 800; color: #0F172A; margin: 0;">Confirm Action</h4>
    </div>
    <div style="margin-bottom: 20px;">
      <p id="indentModalText" style="font-size: 13px; color: #475569; margin: 0;">Are you sure you want to proceed?</p>
    </div>
    <div style="display: flex; justify-content: flex-end; gap: 10px;">
      <button type="button" onclick="closeIndentStatusModal()" style="padding: 8px 16px; border-radius: 8px; font-size: 12px; font-weight: 600; background: #F1F5F9; color: #475569; border: 1px solid #CBD5E1; cursor: pointer;">
        No
      </button>
      <button type="button" id="indentConfirmBtn" style="padding: 8px 18px; border-radius: 8px; font-size: 12px; font-weight: 700; color: #FFFFFF; background: #0F172A; border: none; cursor: pointer;">
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

  let activeIndentForm = null;

  function confirmIndentStatus(btn) {
    activeIndentForm = btn.closest('form');
    const action = btn.dataset.action.toLowerCase();
    
    const modal = document.getElementById('indentStatusModal');
    const title = document.getElementById('indentModalTitle');
    const text = document.getElementById('indentModalText');
    const confirmBtn = document.getElementById('indentConfirmBtn');

    const config = {
      close: { title: 'Confirm Close', text: 'This will mark the Indent as Closed. Continue?', bg: '#0F172A' },
      cancel: { title: 'Confirm Cancel', text: 'This will mark the Indent as Cancelled. Continue?', bg: '#DC2626' },
      pending: { title: 'Re-Open (Pending)', text: 'This will set the status back to Pending. Continue?', bg: '#047857' }
    };

    const cfg = config[action] || config.close;
    title.textContent = cfg.title;
    text.textContent = cfg.text;

    confirmBtn.style.background = cfg.bg;
    modal.style.display = 'flex';
  }

  function closeIndentStatusModal() {
    document.getElementById('indentStatusModal').style.display = 'none';
    activeIndentForm = null;
  }

  document.getElementById('indentConfirmBtn')?.addEventListener('click', function() {
    if (activeIndentForm) {
      activeIndentForm.submit();
    }
    closeIndentStatusModal();
  });
</script>
@endsection