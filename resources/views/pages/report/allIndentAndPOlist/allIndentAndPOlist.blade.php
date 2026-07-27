@extends("layouts.layout")

@section("bodyContent")
<div style="width: 100%; max-width: 100%; padding: 24px; box-sizing: border-box; font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">

    <!-- Top Page Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
        <div style="display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 14px; background: #2563EB; color: #FFFFFF; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 12px rgba(37,99,235,0.25);">
                <svg style="width: 24px; height: 24px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <div>
                <h1 style="font-size: 24px; font-weight: 800; color: #0F172A; margin: 0 0 2px 0; letter-spacing: -0.5px;">{{ $title }}</h1>
                <p style="font-size: 13px; color: #64748B; margin: 0; font-weight: 500;">Combined report tracking all indents and corresponding purchase orders</p>
            </div>
        </div>

        <!-- Export Dropdown -->
        <div style="position: relative;" id="exportDropdownContainer">
            <button type="button" onclick="toggleExportDropdown()" 
                    style="background: #2563EB; color: #FFFFFF; font-weight: 700; font-size: 13px; padding: 10px 18px; border-radius: 10px; border: none; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 2px 6px rgba(37,99,235,0.25);">
                <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                <span>Export Report</span>
                <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div id="exportDropdownMenu" style="display: none; position: absolute; right: 0; top: 110%; width: 200px; background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); z-index: 50; padding: 6px;">
                <button type="button" onclick="exportToExcel()" style="width: 100%; text-align: left; padding: 10px 12px; border: none; background: transparent; border-radius: 8px; font-size: 13px; font-weight: 600; color: #334155; cursor: pointer; display: flex; align-items: center; gap: 10px;" onmouseover="this.style.background='#F8FAFC'" onmouseout="this.style.background='transparent'">
                    <span style="color: #059669; font-weight: 800;">📊</span> Excel Spreadsheet
                </button>
                <button type="button" onclick="exportToPDF()" style="width: 100%; text-align: left; padding: 10px 12px; border: none; background: transparent; border-radius: 8px; font-size: 13px; font-weight: 600; color: #334155; cursor: pointer; display: flex; align-items: center; gap: 10px;" onmouseover="this.style.background='#F8FAFC'" onmouseout="this.style.background='transparent'">
                    <span style="color: #DC2626; font-weight: 800;">📄</span> PDF Document
                </button>
                <button type="button" onclick="exportToCSV()" style="width: 100%; text-align: left; padding: 10px 12px; border: none; background: transparent; border-radius: 8px; font-size: 13px; font-weight: 600; color: #334155; cursor: pointer; display: flex; align-items: center; gap: 10px;" onmouseover="this.style.background='#F8FAFC'" onmouseout="this.style.background='transparent'">
                    <span style="color: #2563EB; font-weight: 800;">📑</span> CSV File
                </button>
            </div>
        </div>
    </div>

    <!-- Main Card Container (Full Width) -->
    <div style="background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.03); overflow: hidden; margin-bottom: 24px;">
        
        <!-- Card Header Toolbar -->
        <div style="padding: 20px; border-bottom: 1px solid #F1F5F9; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; background: #FAFAFA;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <div style="width: 34px; height: 34px; border-radius: 10px; background: #EFF6FF; color: #2563EB; display: flex; align-items: center; justify-content: center; border: 1px solid #BFDBFE;">
                    <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <h2 style="font-size: 18px; font-weight: 800; color: #0F172A; margin: 0;">All Indents & POs</h2>
            </div>

            <!-- Search & Right Filter Drawer Controls -->
            <form method="GET" action="{{ route('reports.indentspos.index') }}" style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
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
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search ID, department, party, PO..." 
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
                    <a href="{{ route('reports.indentspos.index') }}" style="padding: 9px 14px; font-size: 13px; font-weight: 600; background: #F1F5F9; color: #475569; border-radius: 10px; text-decoration: none;">Reset</a>
                @endif
            </form>
        </div>

        <!-- Table Responsive Container (Full Width) -->
        <div style="width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch;">
            <table style="width: 100%; min-width: 2200px; border-collapse: collapse; text-align: left; font-size: 13px; table-layout: fixed;" id="combined-report-table">
                <thead>
                    <tr style="background: #F8FAFC; border-bottom: 1px solid #E2E8F0; color: #64748B; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">
                        <th style="padding: 14px 16px; width: 100px; vertical-align: middle;">Indent ID <span style="color:#CBD5E1;">↕</span></th>
                        <th style="padding: 14px 16px; width: 120px; vertical-align: middle;">Indent Date <span style="color:#CBD5E1;">↕</span></th>
                        <th style="padding: 14px 16px; width: 140px; vertical-align: middle;">Department <span style="color:#CBD5E1;">↕</span></th>
                        <th style="padding: 14px 16px; width: 130px; vertical-align: middle;">Project <span style="color:#CBD5E1;">↕</span></th>
                        <th style="padding: 14px 16px; width: 220px; vertical-align: middle;">Indent Description <span style="color:#CBD5E1;">↕</span></th>
                        <th style="padding: 14px 16px; width: 150px; vertical-align: middle;">Party Name <span style="color:#CBD5E1;">↕</span></th>
                        <th style="padding: 14px 16px; width: 120px; vertical-align: middle;">PO Date <span style="color:#CBD5E1;">↕</span></th>
                        <th style="padding: 14px 16px; width: 130px; vertical-align: middle;">PO/WO No <span style="color:#CBD5E1;">↕</span></th>
                        <th style="padding: 14px 16px; width: 180px; vertical-align: middle;">PO Description <span style="color:#CBD5E1;">↕</span></th>
                        <th style="padding: 14px 16px; width: 120px; vertical-align: middle;">PO Amount <span style="color:#CBD5E1;">↕</span></th>
                        <th style="padding: 14px 16px; width: 150px; text-align: center; vertical-align: middle;">PO Status <span style="color:#CBD5E1;">↕</span></th>
                        <th style="padding: 14px 16px; width: 120px; vertical-align: middle;">Expected Days <span style="color:#CBD5E1;">↕</span></th>
                        <th style="padding: 14px 16px; width: 130px; vertical-align: middle;">Expected Date <span style="color:#CBD5E1;">↕</span></th>
                        <th style="padding: 14px 16px; width: 130px; vertical-align: middle;">Invoice No. <span style="color:#CBD5E1;">↕</span></th>
                        <th style="padding: 14px 16px; width: 120px; vertical-align: middle;">Invoice Date <span style="color:#CBD5E1;">↕</span></th>
                        <th style="padding: 14px 16px; width: 130px; vertical-align: middle;">Receiving Date <span style="color:#CBD5E1;">↕</span></th>
                        <th style="padding: 14px 16px; width: 120px; vertical-align: middle;">Delay in Days <span style="color:#CBD5E1;">↕</span></th>
                        <th style="padding: 14px 16px; width: 140px; vertical-align: middle;">Indent Remarks <span style="color:#CBD5E1;">↕</span></th>
                        <th style="padding: 14px 16px; width: 140px; vertical-align: middle;">PO Remarks <span style="color:#CBD5E1;">↕</span></th>
                    </tr>
                </thead>
                <tbody id="combinedTableBody">
                    @forelse ($rows as $row)
                        @php
                            $status = $row['po_status'] ?? 'Pending';
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
                        @endphp
                        <tr style="border-bottom: 1px solid #F1F5F9; transition: background 0.15s ease;" onmouseover="this.style.background='#F8FAFC'" onmouseout="this.style.background='#FFFFFF'">
                            <td style="padding: 16px; vertical-align: middle; font-weight: 800; color: #0F172A; white-space: nowrap;">{{ $row['indent_id'] }}</td>
                            <td style="padding: 16px; vertical-align: middle; color: #64748B; font-family: monospace; white-space: nowrap;">{{ $row['indent_date'] }}</td>
                            <td style="padding: 16px; vertical-align: middle; color: #475569; font-weight: 600; white-space: nowrap;">{{ $row['department'] }}</td>
                            <td style="padding: 16px; vertical-align: middle; color: #0F172A; font-weight: 700; white-space: nowrap;">{{ $row['project'] }}</td>
                            <td style="padding: 16px; vertical-align: middle; color: #334155; font-weight: 500; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $row['total_description'] }}">{{ $row['total_description'] }}</td>
                            <td style="padding: 16px; vertical-align: middle; color: #334155; font-weight: 600; white-space: nowrap;">{{ $row['party_name'] }}</td>
                            <td style="padding: 16px; vertical-align: middle; color: #64748B; font-family: monospace; white-space: nowrap;">{{ $row['po_date'] }}</td>
                            <td style="padding: 16px; vertical-align: middle; color: #2563EB; font-weight: 700; white-space: nowrap;">{{ $row['po_no'] }}</td>
                            <td style="padding: 16px; vertical-align: middle; color: #334155; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $row['po_description'] }}">{{ $row['po_description'] }}</td>
                            <td style="padding: 16px; vertical-align: middle; font-weight: 800; color: #059669; white-space: nowrap;">{{ $row['po_amount'] !== '-' ? '₹' . $row['po_amount'] : '-' }}</td>
                            <td style="padding: 16px; vertical-align: middle; text-align: center; white-space: nowrap;">
                                <span style="background: {{ $badgeBg }}; border: 1px solid {{ $badgeBorder }}; color: {{ $badgeColor }}; font-weight: 700; font-size: 12px; padding: 5px 14px; border-radius: 20px; display: inline-flex; align-items: center; gap: 6px;">
                                    <span style="width: 6px; height: 6px; border-radius: 50%; background: {{ $dotColor }}; display: inline-block;"></span>
                                    {{ ucfirst($status) }}
                                </span>
                            </td>
                            <td style="padding: 16px; vertical-align: middle; color: #475569; white-space: nowrap;">{{ $row['expected_days'] ?? '-' }}</td>
                            <td style="padding: 16px; vertical-align: middle; color: #64748B; font-family: monospace; white-space: nowrap;">{{ $row['expected_date'] }}</td>
                            <td style="padding: 16px; vertical-align: middle; color: #475569; font-weight: 600; white-space: nowrap;">{{ $row['invoice_no'] }}</td>
                            <td style="padding: 16px; vertical-align: middle; color: #64748B; font-family: monospace; white-space: nowrap;">{{ $row['invoice_date'] }}</td>
                            <td style="padding: 16px; vertical-align: middle; color: #64748B; font-family: monospace; white-space: nowrap;">{{ $row['receiving_date'] }}</td>
                            <td style="padding: 16px; vertical-align: middle; color: #475569; white-space: nowrap;">{{ $row['invoice_expected_days'] }}</td>
                            <td style="padding: 16px; vertical-align: middle; color: #94A3B8; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $row['indent_remarks'] }}">{{ $row['indent_remarks'] }}</td>
                            <td style="padding: 16px; vertical-align: middle; color: #94A3B8; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $row['remarks'] }}">{{ $row['remarks'] }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="19" style="text-align: center; padding: 32px; color: #64748B; font-weight: 600;">No records found matching criteria.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Bottom Pagination Bar -->
        @if(isset($paginated))
        <div style="padding: 16px 20px; border-top: 1px solid #F1F5F9; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; font-size: 13px; color: #64748B; font-weight: 500;">
            <div>
                Showing {{ $paginated->firstItem() ?? 0 }} to {{ $paginated->lastItem() ?? 0 }} of {{ $paginated->total() }} entries
            </div>

            <!-- Page Number Controls -->
            <div>
                {{ $paginated->appends(request()->query())->links('pagination::tailwind') }}
            </div>

            <!-- Per Page Selector -->
            <form method="GET" action="{{ route('reports.indentspos.index') }}" style="display: flex; align-items: center; gap: 8px;">
                @foreach(request()->except(['per_page', 'page']) as $k => $v)
                    <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                @endforeach
                <select name="per_page" onchange="this.form.submit()" style="background: #F8FAFC; border: 1px solid #CBD5E1; border-radius: 8px; padding: 4px 8px; font-size: 12px; font-weight: 700; outline: none;">
                    <option value="10" {{ request('per_page', 15) == 10 ? 'selected' : '' }}>10</option>
                    <option value="15" {{ request('per_page', 15) == 15 ? 'selected' : '' }}>15</option>
                    <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                    <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                    <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                </select>
                <span>per page</span>
            </form>
        </div>
        @endif
    </div>
</div>

<!-- Right Slide-over Filter Drawer for All Indents & POs -->
<div id="rightFilterDrawer" style="display: none; position: fixed; inset: 0; z-index: 99999;">
    <!-- Backdrop Overlay -->
    <div onclick="closeRightFilterDrawer()" style="position: fixed; inset: 0; background: rgba(15,23,42,0.4); backdrop-filter: blur(2px);"></div>

    <!-- Drawer Panel sliding from Right -->
    <div style="position: fixed; top: 0; right: 0; bottom: 0; width: 420px; max-width: 90vw; background: #FFFFFF; box-shadow: -10px 0 25px rgba(0,0,0,0.15); display: flex; flex-direction: column; justify-content: space-between; z-index: 100000; animation: slideInRight 0.3s ease-out;">
        
        <!-- Header -->
        <div style="padding: 20px; border-bottom: 1px solid #E2E8F0; display: flex; align-items: center; justify-content: space-between; background: #F8FAFC;">
            <div style="display: flex; align-items: center; gap: 8px; font-weight: 800; font-size: 16px; color: #0F172A;">
                <svg style="width: 20px; height: 20px; color: #2563EB;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                <span>Filter All Indents & POs</span>
            </div>
            <button type="button" onclick="closeRightFilterDrawer()" style="background: transparent; border: none; font-size: 24px; font-weight: 700; cursor: pointer; color: #94A3B8;">&times;</button>
        </div>

        <!-- Body Form -->
        <form id="drawerFilterForm" method="GET" action="{{ route('reports.indentspos.index') }}" style="padding: 20px; overflow-y: auto; flex: 1;">
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
                <label style="display: block; font-size: 11px; font-weight: 700; color: #475569; text-transform: uppercase; margin-bottom: 6px;">PO Status</label>
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
            <a href="{{ route('reports.indentspos.index') }}" style="padding: 10px 18px; border-radius: 10px; font-size: 13px; font-weight: 600; background: #F1F5F9; color: #475569; text-decoration: none;">Reset</a>
            <button type="button" onclick="document.getElementById('drawerFilterForm').submit()" style="padding: 10px 22px; border-radius: 10px; font-size: 13px; font-weight: 700; background: #2563EB; color: #FFFFFF; border: none; cursor: pointer;">Apply Filters</button>
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

  function toggleExportDropdown() {
      const menu = document.getElementById('exportDropdownMenu');
      menu.style.display = menu.style.display === 'none' ? 'block' : 'none';
  }

  document.addEventListener('click', function(e) {
      const container = document.getElementById('exportDropdownContainer');
      if (container && !container.contains(e.target)) {
          const menu = document.getElementById('exportDropdownMenu');
          if (menu) menu.style.display = 'none';
      }
  });

  function exportToExcel() { alert('Exporting combined report to Excel...'); }
  function exportToPDF() { alert('Exporting combined report to PDF...'); }
  function exportToCSV() { alert('Exporting combined report to CSV...'); }
</script>
@endsection
