@extends("layouts.layout")

@section("bodyContent")
<div style="width: 100%; max-width: 100%; box-sizing: border-box; font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">

    <!-- Top Page Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 16px;">
        <div style="display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 14px; background: #2563EB; color: #FFFFFF; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 12px rgba(37,99,235,0.25);">
                <svg style="width: 24px; height: 24px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <div>
                <h1 style="font-size: 24px; font-weight: 800; color: #0F172A; margin: 0 0 2px 0; letter-spacing: -0.5px;">All Indents Report</h1>
                <p style="font-size: 13px; color: #64748B; margin: 0; font-weight: 500;">Comprehensive lifecycle, fulfillment balances, and purchase order tracking for all registered indents</p>
            </div>
        </div>

        <!-- Top Right Export Dropdown -->
        <div style="position: relative;" id="exportDropdownContainer">
            <button type="button" onclick="toggleExportDropdown()" 
                    style="background: #2563EB; color: #FFFFFF; font-weight: 700; font-size: 13px; padding: 10px 18px; border-radius: 10px; border: none; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 2px 6px rgba(37,99,235,0.25);">
                <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                <span>Export Report</span>
                <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            
            <div id="exportDropdownMenu" style="display: none; position: absolute; right: 0; top: 110%; width: 220px; background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); z-index: 50; padding: 6px;">
                <button type="button" onclick="exportToExcel()" style="width: 100%; text-align: left; padding: 10px 12px; border: none; background: transparent; border-radius: 8px; font-size: 13px; font-weight: 600; color: #334155; cursor: pointer; display: flex; align-items: center; gap: 10px;" onmouseover="this.style.background='#F8FAFC'" onmouseout="this.style.background='transparent'">
                    <span style="color: #059669; font-weight: 800; font-size: 16px;">📊</span>
                    <div>
                        <div style="font-weight: 700;">Excel Spreadsheet</div>
                        <div style="font-size: 11px; color: #94A3B8;">Download .xlsx file</div>
                    </div>
                </button>
                <button type="button" onclick="exportToPDF()" style="width: 100%; text-align: left; padding: 10px 12px; border: none; background: transparent; border-radius: 8px; font-size: 13px; font-weight: 600; color: #334155; cursor: pointer; display: flex; align-items: center; gap: 10px;" onmouseover="this.style.background='#F8FAFC'" onmouseout="this.style.background='transparent'">
                    <span style="color: #DC2626; font-weight: 800; font-size: 16px;">📄</span>
                    <div>
                        <div style="font-weight: 700;">PDF Document</div>
                        <div style="font-size: 11px; color: #94A3B8;">Landscape printable .pdf</div>
                    </div>
                </button>
                <button type="button" onclick="exportToCSV()" style="width: 100%; text-align: left; padding: 10px 12px; border: none; background: transparent; border-radius: 8px; font-size: 13px; font-weight: 600; color: #334155; cursor: pointer; display: flex; align-items: center; gap: 10px;" onmouseover="this.style.background='#F8FAFC'" onmouseout="this.style.background='transparent'">
                    <span style="color: #2563EB; font-weight: 800; font-size: 16px;">📑</span>
                    <div>
                        <div style="font-weight: 700;">CSV File</div>
                        <div style="font-size: 11px; color: #94A3B8;">Standard comma-separated</div>
                    </div>
                </button>
            </div>
        </div>
    </div>

    <!-- Top KPI Stat Cards Bar -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 16px; margin-bottom: 24px;">
        <!-- Total Indents -->
        <div style="background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 14px; padding: 16px; box-shadow: 0 2px 8px rgba(0,0,0,0.02); display: flex; align-items: center; gap: 12px;">
            <div style="width: 42px; height: 42px; border-radius: 10px; background: #F3E8FF; color: #7C3AED; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <div>
                <div style="font-size: 10px; font-weight: 700; color: #64748B; text-transform: uppercase; letter-spacing: 0.5px;">Total Indents</div>
                <div style="font-size: 18px; font-weight: 800; color: #0F172A;">{{ $kpis['total_indents'] ?? 0 }}</div>
            </div>
        </div>

        <!-- Total Requested Qty -->
        <div style="background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 14px; padding: 16px; box-shadow: 0 2px 8px rgba(0,0,0,0.02); display: flex; align-items: center; gap: 12px;">
            <div style="width: 42px; height: 42px; border-radius: 10px; background: #EFF6FF; color: #2563EB; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            </div>
            <div>
                <div style="font-size: 10px; font-weight: 700; color: #64748B; text-transform: uppercase; letter-spacing: 0.5px;">Requested Qty</div>
                <div style="font-size: 18px; font-weight: 800; color: #2563EB;">{{ $kpis['total_req'] ?? 0 }}</div>
            </div>
        </div>

        <!-- Total PO Qty -->
        <div style="background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 14px; padding: 16px; box-shadow: 0 2px 8px rgba(0,0,0,0.02); display: flex; align-items: center; gap: 12px;">
            <div style="width: 42px; height: 42px; border-radius: 10px; background: #FFF7ED; color: #EA580C; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
            </div>
            <div>
                <div style="font-size: 10px; font-weight: 700; color: #64748B; text-transform: uppercase; letter-spacing: 0.5px;">Ordered (PO)</div>
                <div style="font-size: 18px; font-weight: 800; color: #EA580C;">{{ $kpis['total_po'] ?? 0 }}</div>
            </div>
        </div>

        <!-- Total Received Qty -->
        <div style="background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 14px; padding: 16px; box-shadow: 0 2px 8px rgba(0,0,0,0.02); display: flex; align-items: center; gap: 12px;">
            <div style="width: 42px; height: 42px; border-radius: 10px; background: #ECFDF5; color: #059669; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            </div>
            <div>
                <div style="font-size: 10px; font-weight: 700; color: #64748B; text-transform: uppercase; letter-spacing: 0.5px;">Received Qty</div>
                <div style="font-size: 18px; font-weight: 800; color: #059669;">{{ $kpis['total_rec'] ?? 0 }}</div>
            </div>
        </div>

        <!-- Total Cancelled Qty -->
        <div style="background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 14px; padding: 16px; box-shadow: 0 2px 8px rgba(0,0,0,0.02); display: flex; align-items: center; gap: 12px;">
            <div style="width: 42px; height: 42px; border-radius: 10px; background: #FEF2F2; color: #DC2626; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </div>
            <div>
                <div style="font-size: 10px; font-weight: 700; color: #64748B; text-transform: uppercase; letter-spacing: 0.5px;">Cancelled Qty</div>
                <div style="font-size: 18px; font-weight: 800; color: #DC2626;">{{ $kpis['total_canc'] ?? 0 }}</div>
            </div>
        </div>

        <!-- Total Balance Remaining -->
        <div style="background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 14px; padding: 16px; box-shadow: 0 2px 8px rgba(0,0,0,0.02); display: flex; align-items: center; gap: 12px;">
            <div style="width: 42px; height: 42px; border-radius: 10px; background: #FFFBEB; color: #D97706; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <div style="font-size: 10px; font-weight: 700; color: #64748B; text-transform: uppercase; letter-spacing: 0.5px;">Remaining Bal</div>
                <div style="font-size: 18px; font-weight: 800; color: #D97706;">{{ $kpis['total_bal'] ?? 0 }}</div>
            </div>
        </div>

        <!-- Total PO Value -->
        <div style="background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 14px; padding: 16px; box-shadow: 0 2px 8px rgba(0,0,0,0.02); display: flex; align-items: center; gap: 12px;">
            <div style="width: 42px; height: 42px; border-radius: 10px; background: #EEF2FF; color: #4F46E5; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <div style="font-size: 10px; font-weight: 700; color: #64748B; text-transform: uppercase; letter-spacing: 0.5px;">Total PO Value</div>
                <div style="font-size: 18px; font-weight: 800; color: #4F46E5;">₹{{ number_format($kpis['total_amount'] ?? 0, 2) }}</div>
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
                <div>
                    <h2 style="font-size: 17px; font-weight: 800; color: #0F172A; margin: 0;">All Registered Indents</h2>
                    <span style="font-size: 11px; color: #64748B; font-weight: 500;">Showing {{ $registers->firstItem() ?? 0 }} - {{ $registers->lastItem() ?? 0 }} of {{ $registers->total() }} indents</span>
                </div>
            </div>

            <!-- Search & Right Filter Drawer Controls -->
            <form method="GET" action="{{ route('report.viewAllIndent') }}" style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                @if(request('department')) <input type="hidden" name="department" value="{{ request('department') }}"> @endif
                @if(request('project')) <input type="hidden" name="project" value="{{ request('project') }}"> @endif
                @if(request('status')) <input type="hidden" name="status" value="{{ request('status') }}"> @endif
                @if(request('date_from')) <input type="hidden" name="date_from" value="{{ request('date_from') }}"> @endif
                @if(request('date_to')) <input type="hidden" name="date_to" value="{{ request('date_to') }}"> @endif

                <!-- Search Input -->
                <div style="position: relative; width: 280px;">
                    <span style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #94A3B8;">
                        <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search ID, department, item..." 
                           style="width: 100%; padding: 9px 12px 9px 38px; background: #FFFFFF; border: 1px solid #CBD5E1; border-radius: 10px; font-size: 13px; outline: none; box-sizing: border-box;">
                </div>

                <!-- Quick Item Status Dropdown -->
                <select name="item_status" onchange="this.form.submit()" style="padding: 9px 12px; background: #FFFFFF; border: 1px solid #CBD5E1; border-radius: 10px; font-size: 13px; font-weight: 600; color: #334155; outline: none; cursor: pointer;">
                    <option value="">All Item Statuses</option>
                    <option value="pending" {{ strtolower(request('item_status')) === 'pending' ? 'selected' : '' }}>Item: Pending</option>
                    <option value="po created" {{ in_array(strtolower(request('item_status')), ['po created', 'ordered']) ? 'selected' : '' }}>Item: PO Created</option>
                    <option value="partially received" {{ strtolower(request('item_status')) === 'partially received' ? 'selected' : '' }}>Item: Partially Received</option>
                    <option value="completed" {{ in_array(strtolower(request('item_status')), ['completed', 'received']) ? 'selected' : '' }}>Item: Completed</option>
                    <option value="cancelled" {{ in_array(strtolower(request('item_status')), ['cancelled', 'cancel']) ? 'selected' : '' }}>Item: Cancelled</option>
                </select>

                <!-- Right Drawer Filter Button -->
                <button type="button" onclick="openRightFilterDrawer()" 
                        style="background: #FFFFFF; border: 1px solid #DBEAFE; color: #2563EB; font-weight: 700; padding: 9px 16px; border-radius: 10px; font-size: 13px; cursor: pointer; display: inline-flex; align-items: center; gap: 6px;">
                    <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                    <span>Filter</span>
                    @if(request()->anyFilled(['department', 'project', 'status', 'item_status', 'date_from', 'date_to']))
                        <span style="width: 8px; height: 8px; border-radius: 50%; background: #2563EB; display: inline-block;"></span>
                    @endif
                </button>

                <!-- Search Button -->
                <button type="submit" style="background: #0F172A; color: #FFFFFF; font-weight: 700; padding: 9px 18px; border-radius: 10px; font-size: 13px; border: none; cursor: pointer;">
                    Search
                </button>

                @if(request()->anyFilled(['search', 'department', 'project', 'status', 'item_status', 'date_from', 'date_to']))
                    <a href="{{ route('report.viewAllIndent') }}" style="padding: 9px 14px; font-size: 13px; font-weight: 600; background: #F1F5F9; color: #475569; border-radius: 10px; text-decoration: none;">Reset</a>
                @endif
            </form>
        </div>

        <!-- Table Responsive Container (Full Width) -->
        <div style="width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch;">
            <table style="width: 100%; min-width: 1200px; border-collapse: collapse; text-align: left; font-size: 13px; table-layout: fixed;" id="indent-report-table">
                <thead>
                    <tr style="background: #F8FAFC; border-bottom: 1px solid #E2E8F0; color: #64748B; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">
                        <th style="padding: 14px 10px; width: 45px; text-align: center;"></th>
                        <th style="padding: 14px 14px; width: 95px; vertical-align: middle;">INDENT ID</th>
                        <th style="padding: 14px 14px; width: 100px; vertical-align: middle;">DATE</th>
                        <th style="padding: 14px 14px; width: 160px; vertical-align: middle;">DEPARTMENT & PROJECT</th>
                        <th style="padding: 14px 14px; vertical-align: middle;">ITEMS & FULFILLMENT BREAKDOWN</th>
                        <th style="padding: 14px 14px; width: 220px; vertical-align: middle;">LINKED POs & VENDORS</th>
                        <th style="padding: 14px 14px; width: 125px; text-align: center; vertical-align: middle;">STATUS</th>
                        <th style="padding: 14px 14px; width: 145px; text-align: center; vertical-align: middle;">ACTIONS</th>
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

                            $items = $row['items'] ?? [];
                            $pos   = $row['pos'] ?? [];
                            $totals = $row['totals'] ?? [];
                        @endphp
                        <tr style="border-bottom: 1px solid #F1F5F9; transition: background 0.15s ease;" onmouseover="this.style.background='#F8FAFC'" onmouseout="this.style.background='#FFFFFF'">
                            <!-- Expand Button Column -->
                            <td style="padding: 14px 8px; text-align: center; vertical-align: middle;">
                                <button type="button" id="expand-btn-{{ $row['indent_id'] }}" onclick="toggleRowExpand('{{ $row['indent_id'] }}')"
                                        style="width: 26px; height: 26px; border-radius: 6px; background: #F8FAFC; border: 1px solid #CBD5E1; color: #64748B; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; transition: all 0.15s ease;"
                                        title="Click to view detailed item breakdown and PO history">
                                    <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                                </button>
                            </td>

                            <!-- Indent ID -->
                            <td style="padding: 14px; vertical-align: middle; white-space: nowrap;">
                                <a href="{{ $row['action']['view'] }}" style="font-weight: 800; color: #2563EB; text-decoration: none; display: inline-flex; align-items: center; gap: 4px;" title="View Indent Summary">
                                    <span>#{{ $row['indent_id'] }}</span>
                                </a>
                            </td>
                            
                            <!-- Date -->
                            <td style="padding: 14px; vertical-align: middle; color: #64748B; font-family: monospace; white-space: nowrap;">{{ $row['indent_date'] }}</td>

                            <!-- Department & Project -->
                            <td style="padding: 14px; vertical-align: middle;">
                                <div style="font-weight: 700; color: #0F172A; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $row['department'] }}">{{ $row['department'] }}</div>
                                @if(!empty($row['project']) && $row['project'] !== '-')
                                    <div style="font-size: 11px; color: #64748B; font-weight: 500; display: inline-flex; align-items: center; gap: 4px; margin-top: 2px;">
                                        <svg style="width: 12px; height: 12px; color: #94A3B8;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
                                        <span>{{ $row['project'] }}</span>
                                    </div>
                                @endif
                            </td>
                            
                            <!-- Items & Fulfillment Breakdown Column -->
                            <td style="padding: 14px; vertical-align: middle;">
                                @if(!empty($items))
                                    <div style="display: flex; flex-direction: column; gap: 6px;">
                                        @foreach(array_slice($items, 0, 3) as $it)
                                            @php
                                                $iSt = strtolower($it['status'] ?? 'pending');
                                                $iColor = '#475569'; $iBg = '#F1F5F9';
                                                if (in_array($iSt, ['completed', 'received'])) { $iColor = '#059669'; $iBg = '#ECFDF5'; }
                                                elseif (in_array($iSt, ['cancelled', 'cancel'])) { $iColor = '#DC2626'; $iBg = '#FEF2F2'; }
                                                elseif (in_array($iSt, ['po created', 'ordered'])) { $iColor = '#2563EB'; $iBg = '#EFF6FF'; }
                                                elseif (in_array($iSt, ['partially received'])) { $iColor = '#7C3AED'; $iBg = '#F3E8FF'; }
                                            @endphp
                                            <div style="display: flex; align-items: center; justify-content: space-between; gap: 8px; font-size: 12px; border-bottom: 1px dashed #F1F5F9; padding-bottom: 4px;">
                                                <div style="font-weight: 600; color: #1E293B; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 200px;" title="{{ $it['description'] }}">
                                                    {{ $it['description'] }}
                                                    @if(!empty($it['unit']) && $it['unit'] !== '-')
                                                        <span style="color: #94A3B8; font-weight: 500; font-size: 11px;">({{ $it['unit'] }})</span>
                                                    @endif
                                                </div>
                                                <div style="display: flex; align-items: center; gap: 6px; font-size: 11px; white-space: nowrap;">
                                                    <span style="color: #475569;">Req: <strong>{{ $it['quantity_required'] }}</strong></span>
                                                    <span style="color: #059669;">Rec: <strong>{{ $it['quantity_received'] }}</strong></span>
                                                    @if(($it['quantity_cancelled'] ?? 0) > 0)
                                                        <span style="color: #DC2626; font-weight: 700;">Canc: {{ $it['quantity_cancelled'] }}</span>
                                                    @endif
                                                    <span style="color: #D97706; font-weight: 700;">Bal: {{ $it['quantity_balance'] }}</span>
                                                    <span style="background: {{ $iBg }}; color: {{ $iColor }}; font-size: 9px; font-weight: 800; padding: 2px 6px; border-radius: 10px; text-transform: uppercase;">
                                                        {{ $it['status'] }}
                                                    </span>
                                                </div>
                                            </div>
                                        @endforeach

                                        @if(count($items) > 3)
                                            <div style="font-size: 11px; color: #2563EB; font-weight: 700; cursor: pointer;" onclick="toggleRowExpand('{{ $row['indent_id'] }}')">
                                                + {{ count($items) - 3 }} more items (click to expand)
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <span style="color: #94A3B8;">-</span>
                                @endif
                            </td>

                            <!-- Linked POs & Vendors Column -->
                            <td style="padding: 14px; vertical-align: middle;">
                                @if(!empty($pos))
                                    <div style="display: flex; flex-direction: column; gap: 4px;">
                                        @foreach($pos as $p)
                                            <div style="display: flex; align-items: center; justify-content: space-between; gap: 6px; font-size: 12px;">
                                                <div style="font-weight: 700; color: #0F172A; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $p->party_name ?? 'PO #' . $p->id }}">
                                                    {{ !empty($p->po_wo_no) ? $p->po_wo_no : ('PO #' . $p->id) }}
                                                    @if(!empty($p->party_name))
                                                        <span style="color: #64748B; font-weight: 500;">({{ $p->party_name }})</span>
                                                    @endif
                                                </div>
                                                @if(!empty($p->po_amount))
                                                    <span style="font-weight: 800; color: #16A34A; font-family: monospace; font-size: 11px; white-space: nowrap;">₹{{ number_format((float)$p->po_amount, 2) }}</span>
                                                @endif
                                            </div>
                                        @endforeach
                                        @if(!empty($row['po_amount']) && $row['po_amount'] > 0)
                                            <div style="font-size: 11px; color: #475569; font-weight: 700; border-top: 1px solid #F1F5F9; padding-top: 3px; display: flex; justify-content: space-between;">
                                                <span>Total PO Value:</span>
                                                <span style="color: #4F46E5; font-family: monospace;">₹{{ number_format($row['po_amount'], 2) }}</span>
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <span style="color: #94A3B8; font-size: 12px; font-style: italic;">No PO filed yet</span>
                                @endif
                            </td>
                            
                            <!-- Status Badge -->
                            <td style="padding: 14px; vertical-align: middle; text-align: center; white-space: nowrap;">
                                <span style="background: {{ $badgeBg }}; border: 1px solid {{ $badgeBorder }}; color: {{ $badgeColor }}; font-weight: 700; font-size: 11px; padding: 5px 12px; border-radius: 20px; display: inline-flex; align-items: center; gap: 6px;">
                                    <span style="width: 6px; height: 6px; border-radius: 50%; background: {{ $dotColor }}; display: inline-block;"></span>
                                    {{ ucfirst($status) }}
                                </span>
                            </td>

                            <!-- Action Buttons -->
                            <td style="padding: 14px; vertical-align: middle; text-align: center; white-space: nowrap;">
                                <div style="display: inline-flex; align-items: center; gap: 6px; flex-wrap: wrap; justify-content: center;">
                                    <!-- Indent Summary -->
                                    <a href="{{ $row['action']['view'] }}"
                                       style="background: #EFF6FF; border: 1px solid #BFDBFE; color: #2563EB; font-weight: 700; font-size: 11px; padding: 5px 10px; border-radius: 8px; text-decoration: none; display: inline-flex; align-items: center; gap: 4px;" title="View Indent Summary & PO details">
                                        <svg style="width: 12px; height: 12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        <span>Summary</span>
                                    </a>

                                    <!-- Edit Indent -->
                                    <a href="{{ $row['action']['edit'] }}"
                                       style="background: #F8FAFC; border: 1px solid #CBD5E1; color: #475569; font-weight: 700; font-size: 11px; padding: 5px 10px; border-radius: 8px; text-decoration: none; display: inline-flex; align-items: center; gap: 4px;" title="Edit Indent Ticket">
                                        <svg style="width: 12px; height: 12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        <span>Edit</span>
                                    </a>

                                    <!-- File PO -->
                                    @if(!in_array($normSt, ['close', 'closed', 'cancel', 'cancelled']))
                                        <a href="{{ $row['action']['file_po'] }}"
                                           style="background: #FFF7ED; border: 1px solid #FED7AA; color: #EA580C; font-weight: 700; font-size: 11px; padding: 5px 10px; border-radius: 8px; text-decoration: none; display: inline-flex; align-items: center; gap: 4px;" title="File Purchase Order for remaining items">
                                            <svg style="width: 12px; height: 12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                            <span>PO</span>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>

                        <!-- Expandable Sub-Row for Complete Details -->
                        <tr id="expand-row-{{ $row['indent_id'] }}" style="display: none; background: #F8FAFC; border-bottom: 2px solid #E2E8F0;">
                            <td colspan="8" style="padding: 20px 24px;">
                                <div style="background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 12px; padding: 18px; box-shadow: 0 1px 3px rgba(0,0,0,0.03);">
                                    <!-- Sub-row Header -->
                                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; border-bottom: 1px solid #F1F5F9; padding-bottom: 10px; flex-wrap: wrap; gap: 10px;">
                                        <div style="display: flex; align-items: center; gap: 8px;">
                                            <svg style="width: 18px; height: 18px; color: #2563EB;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                            <span style="font-weight: 800; font-size: 14px; color: #0F172A;">Detailed Lifecycle for Indent #{{ $row['indent_id'] }}</span>
                                            <span style="font-size: 12px; color: #64748B;">({{ $row['department'] }} | {{ $row['project'] }})</span>
                                        </div>
                                        <div style="display: flex; align-items: center; gap: 12px; font-size: 12px; font-weight: 600;">
                                            <span>Requested: <strong style="color: #2563EB;">{{ $totals['req'] ?? 0 }}</strong></span>
                                            <span>Received: <strong style="color: #16A34A;">{{ $totals['rec'] ?? 0 }}</strong></span>
                                            <span>Cancelled: <strong style="color: #DC2626;">{{ $totals['canc'] ?? 0 }}</strong></span>
                                            <span>Balance: <strong style="color: #D97706;">{{ $totals['bal'] ?? 0 }}</strong></span>
                                        </div>
                                    </div>

                                    <!-- 1. All Items Breakdown Table -->
                                    <h4 style="font-size: 12px; font-weight: 800; color: #475569; text-transform: uppercase; letter-spacing: 0.5px; margin: 0 0 8px 0;">Itemized Inventory Breakdown</h4>
                                    @if(!empty($items))
                                        <table style="width: 100%; border-collapse: collapse; font-size: 12px; margin-bottom: 16px; border: 1px solid #E2E8F0; border-radius: 8px; overflow: hidden;">
                                            <thead>
                                                <tr style="background: #F1F5F9; color: #475569; font-size: 11px; font-weight: 700; text-transform: uppercase;">
                                                    <th style="padding: 8px 12px; border: 1px solid #E2E8F0;">Item Description</th>
                                                    <th style="padding: 8px 12px; border: 1px solid #E2E8F0; width: 90px;">Unit</th>
                                                    <th style="padding: 8px 12px; border: 1px solid #E2E8F0; width: 90px; text-align: right;">Req Qty</th>
                                                    <th style="padding: 8px 12px; border: 1px solid #E2E8F0; width: 90px; text-align: right;">PO Qty</th>
                                                    <th style="padding: 8px 12px; border: 1px solid #E2E8F0; width: 90px; text-align: right;">Rec Qty</th>
                                                    <th style="padding: 8px 12px; border: 1px solid #E2E8F0; width: 90px; text-align: right;">Canc Qty</th>
                                                    <th style="padding: 8px 12px; border: 1px solid #E2E8F0; width: 90px; text-align: right;">Balance</th>
                                                    <th style="padding: 8px 12px; border: 1px solid #E2E8F0; width: 120px; text-align: center;">Item Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($items as $it)
                                                    @php
                                                        $iSt = strtolower($it['status'] ?? 'pending');
                                                        $iColor = '#475569'; $iBg = '#F1F5F9';
                                                        if (in_array($iSt, ['completed', 'received'])) { $iColor = '#059669'; $iBg = '#ECFDF5'; }
                                                        elseif (in_array($iSt, ['cancelled', 'cancel'])) { $iColor = '#DC2626'; $iBg = '#FEF2F2'; }
                                                        elseif (in_array($iSt, ['po created', 'ordered'])) { $iColor = '#2563EB'; $iBg = '#EFF6FF'; }
                                                        elseif (in_array($iSt, ['partially received'])) { $iColor = '#7C3AED'; $iBg = '#F3E8FF'; }
                                                    @endphp
                                                    <tr>
                                                        <td style="padding: 8px 12px; border: 1px solid #E2E8F0; font-weight: 600; color: #0F172A;">{{ $it['description'] }}</td>
                                                        <td style="padding: 8px 12px; border: 1px solid #E2E8F0; color: #64748B;">{{ $it['unit'] }}</td>
                                                        <td style="padding: 8px 12px; border: 1px solid #E2E8F0; text-align: right; font-weight: 700;">{{ $it['quantity_required'] }}</td>
                                                        <td style="padding: 8px 12px; border: 1px solid #E2E8F0; text-align: right; color: #2563EB;">{{ $it['purchased_order'] }}</td>
                                                        <td style="padding: 8px 12px; border: 1px solid #E2E8F0; text-align: right; color: #16A34A; font-weight: 700;">{{ $it['quantity_received'] }}</td>
                                                        <td style="padding: 8px 12px; border: 1px solid #E2E8F0; text-align: right; color: #DC2626; font-weight: 700;">{{ $it['quantity_cancelled'] }}</td>
                                                        <td style="padding: 8px 12px; border: 1px solid #E2E8F0; text-align: right; font-weight: 800; color: #D97706;">{{ $it['quantity_balance'] }}</td>
                                                        <td style="padding: 8px 12px; border: 1px solid #E2E8F0; text-align: center;">
                                                            <span style="background: {{ $iBg }}; color: {{ $iColor }}; font-size: 10px; font-weight: 800; padding: 3px 8px; border-radius: 12px; text-transform: uppercase;">
                                                                {{ $it['status'] }}
                                                            </span>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    @else
                                        <p style="font-size: 12px; color: #94A3B8; font-style: italic;">No item records found for this indent.</p>
                                    @endif

                                    <!-- 2. Purchase Orders Table -->
                                    <h4 style="font-size: 12px; font-weight: 800; color: #475569; text-transform: uppercase; letter-spacing: 0.5px; margin: 12px 0 8px 0;">Purchase Order & Goods Receipt Records</h4>
                                    @if(!empty($pos))
                                        <table style="width: 100%; border-collapse: collapse; font-size: 12px; border: 1px solid #E2E8F0; border-radius: 8px; overflow: hidden;">
                                            <thead>
                                                <tr style="background: #F1F5F9; color: #475569; font-size: 11px; font-weight: 700; text-transform: uppercase;">
                                                    <th style="padding: 8px 12px; border: 1px solid #E2E8F0;">PO #</th>
                                                    <th style="padding: 8px 12px; border: 1px solid #E2E8F0;">Vendor / Party Name</th>
                                                    <th style="padding: 8px 12px; border: 1px solid #E2E8F0; width: 95px;">PO Date</th>
                                                    <th style="padding: 8px 12px; border: 1px solid #E2E8F0; width: 110px; text-align: right;">Amount (₹)</th>
                                                    <th style="padding: 8px 12px; border: 1px solid #E2E8F0; width: 110px;">Invoice No</th>
                                                    <th style="padding: 8px 12px; border: 1px solid #E2E8F0; width: 95px;">Receiving Date</th>
                                                    <th style="padding: 8px 12px; border: 1px solid #E2E8F0; width: 95px; text-align: center;">PO Status</th>
                                                    <th style="padding: 8px 12px; border: 1px solid #E2E8F0; width: 85px; text-align: center;">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($pos as $p)
                                                    <tr>
                                                        <td style="padding: 8px 12px; border: 1px solid #E2E8F0; font-weight: 800; color: #0F172A;">
                                                            {{ !empty($p->po_wo_no) ? $p->po_wo_no : ('PO #' . $p->id) }}
                                                        </td>
                                                        <td style="padding: 8px 12px; border: 1px solid #E2E8F0; font-weight: 600; color: #334155;">{{ $p->party_name ?: '-' }}</td>
                                                        <td style="padding: 8px 12px; border: 1px solid #E2E8F0; color: #64748B; font-family: monospace;">{{ !empty($p->po_date) ? date('d-m-Y', strtotime($p->po_date)) : '-' }}</td>
                                                        <td style="padding: 8px 12px; border: 1px solid #E2E8F0; text-align: right; font-weight: 700; color: #16A34A; font-family: monospace;">
                                                            {{ !empty($p->po_amount) ? '₹' . number_format((float)$p->po_amount, 2) : '-' }}
                                                        </td>
                                                        <td style="padding: 8px 12px; border: 1px solid #E2E8F0; color: #64748B;">{{ $p->store_indent_no ?: '-' }}</td>
                                                        <td style="padding: 8px 12px; border: 1px solid #E2E8F0; color: #64748B; font-family: monospace;">{{ !empty($p->receiving_date) ? date('d-m-Y', strtotime($p->receiving_date)) : '-' }}</td>
                                                        <td style="padding: 8px 12px; border: 1px solid #E2E8F0; text-align: center;">
                                                            <span style="font-size: 10px; font-weight: 700; padding: 2px 8px; border-radius: 10px; background: #F1F5F9; color: #475569;">
                                                                {{ ucfirst($p->status ?? 'Open') }}
                                                            </span>
                                                        </td>
                                                        <td style="padding: 8px 12px; border: 1px solid #E2E8F0; text-align: center;">
                                                            <a href="{{ route('indentroview.createInvoiceById', $p->id) }}" style="color: #2563EB; font-weight: 700; text-decoration: none; font-size: 11px;">Invoice / GRN</a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    @else
                                        <p style="font-size: 12px; color: #94A3B8; font-style: italic;">No purchase orders filed for this indent yet.</p>
                                    @endif

                                    <!-- Remarks Section if present -->
                                    @if(!empty($row['remarks']) && $row['remarks'] !== '-')
                                        <div style="margin-top: 14px; padding: 10px 14px; background: #F8FAFC; border-left: 3px solid #2563EB; border-radius: 4px; font-size: 12px; color: #475569;">
                                            <strong>Indent Remarks:</strong> {{ $row['remarks'] }}
                                        </div>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align: center; padding: 36px; color: #64748B; font-weight: 600;">
                                No indent records found matching the active filters.
                            </td>
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
            <form method="GET" action="{{ route('report.viewAllIndent') }}" style="display: flex; align-items: center; gap: 8px;">
                @foreach(request()->except(['per_page', 'page']) as $k => $v)
                    <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                @endforeach
                <select name="per_page" onchange="this.form.submit()" style="background: #F8FAFC; border: 1px solid #CBD5E1; border-radius: 8px; padding: 4px 8px; font-size: 12px; font-weight: 700; outline: none;">
                    <option value="10" {{ request('per_page', 15) == 10 ? 'selected' : '' }}>10</option>
                    <option value="15" {{ request('per_page', 15) == 15 ? 'selected' : '' }}>15</option>
                    <option value="25" {{ request('per_page', 15) == 25 ? 'selected' : '' }}>25</option>
                    <option value="50" {{ request('per_page', 15) == 50 ? 'selected' : '' }}>50</option>
                    <option value="100" {{ request('per_page', 15) == 100 ? 'selected' : '' }}>100</option>
                </select>
                <span>per page</span>
            </form>
        </div>
    </div>
</div>

<!-- Right Slide-over Filter Drawer for All Indents -->
<div id="rightFilterDrawer" style="display: none; position: fixed; inset: 0; z-index: 99999;">
    <!-- Backdrop Overlay -->
    <div onclick="closeRightFilterDrawer()" style="position: fixed; inset: 0; background: rgba(15,23,42,0.4); backdrop-filter: blur(2px);"></div>

    <!-- Drawer Panel sliding from Right -->
    <div style="position: fixed; top: 0; right: 0; bottom: 0; width: 420px; max-width: 90vw; background: #FFFFFF; box-shadow: -10px 0 25px rgba(0,0,0,0.15); display: flex; flex-direction: column; justify-content: space-between; z-index: 100000; animation: slideInRight 0.3s ease-out;">
        
        <!-- Header -->
        <div style="padding: 20px; border-bottom: 1px solid #E2E8F0; display: flex; align-items: center; justify-content: space-between; background: #F8FAFC;">
            <div style="display: flex; align-items: center; gap: 8px; font-weight: 800; font-size: 16px; color: #0F172A;">
                <svg style="width: 20px; height: 20px; color: #2563EB;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                <span>Filter All Indents</span>
            </div>
            <button type="button" onclick="closeRightFilterDrawer()" style="background: transparent; border: none; font-size: 24px; font-weight: 700; cursor: pointer; color: #94A3B8;">&times;</button>
        </div>

        <!-- Body Form -->
        <form id="drawerFilterForm" method="GET" action="{{ route('report.viewAllIndent') }}" style="padding: 20px; overflow-y: auto; flex: 1;">
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

            <!-- Indent Status Filter -->
            <div style="margin-bottom: 18px;">
                <label style="display: block; font-size: 11px; font-weight: 700; color: #475569; text-transform: uppercase; margin-bottom: 6px;">Indent Status</label>
                <select name="status" style="width: 100%; padding: 10px 12px; border: 1px solid #CBD5E1; border-radius: 10px; font-size: 13px; outline: none; background: #F8FAFC;">
                    <option value="">All Indent Statuses</option>
                    <option value="pending" {{ strtolower(request('status')) === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="open" {{ strtolower(request('status')) === 'open' ? 'selected' : '' }}>Open</option>
                    <option value="partially received" {{ strtolower(request('status')) === 'partially received' ? 'selected' : '' }}>Partially received</option>
                    <option value="completed" {{ strtolower(request('status')) === 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="close" {{ strtolower(request('status')) === 'close' ? 'selected' : '' }}>Closed</option>
                    <option value="cancel" {{ strtolower(request('status')) === 'cancel' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>

            <!-- Item Status Filter -->
            <div style="margin-bottom: 18px;">
                <label style="display: block; font-size: 11px; font-weight: 700; color: #475569; text-transform: uppercase; margin-bottom: 6px;">Item Fulfillment Status</label>
                <select name="item_status" style="width: 100%; padding: 10px 12px; border: 1px solid #CBD5E1; border-radius: 10px; font-size: 13px; outline: none; background: #F8FAFC;">
                    <option value="">All Item Statuses</option>
                    <option value="pending" {{ strtolower(request('item_status')) === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="po created" {{ in_array(strtolower(request('item_status')), ['po created', 'ordered']) ? 'selected' : '' }}>PO Created / Ordered</option>
                    <option value="partially received" {{ strtolower(request('item_status')) === 'partially received' ? 'selected' : '' }}>Partially Received</option>
                    <option value="completed" {{ in_array(strtolower(request('item_status')), ['completed', 'received']) ? 'selected' : '' }}>Completed / Received</option>
                    <option value="cancelled" {{ in_array(strtolower(request('item_status')), ['cancelled', 'cancel']) ? 'selected' : '' }}>Cancelled</option>
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
            <a href="{{ route('report.viewAllIndent') }}" style="padding: 10px 18px; border-radius: 10px; font-size: 13px; font-weight: 600; background: #F1F5F9; color: #475569; text-decoration: none;">Reset</a>
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
      menu.style.display = (menu.style.display === 'none' || menu.style.display === '') ? 'block' : 'none';
  }

  document.addEventListener('click', function(e) {
      const container = document.getElementById('exportDropdownContainer');
      if (container && !container.contains(e.target)) {
          const menu = document.getElementById('exportDropdownMenu');
          if (menu) menu.style.display = 'none';
      }
  });

  function toggleRowExpand(id) {
      const row = document.getElementById('expand-row-' + id);
      const btn = document.getElementById('expand-btn-' + id);
      if (!row || !btn) return;

      if (row.style.display === 'none' || row.style.display === '') {
          row.style.display = 'table-row';
          btn.innerHTML = '<svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20 12H4"/></svg>';
          btn.style.background = '#EFF6FF';
          btn.style.borderColor = '#BFDBFE';
          btn.style.color = '#2563EB';
      } else {
          row.style.display = 'none';
          btn.innerHTML = '<svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>';
          btn.style.background = '#F8FAFC';
          btn.style.borderColor = '#CBD5E1';
          btn.style.color = '#64748B';
      }
  }

  function triggerExport(type) {
      const currentParams = new URLSearchParams(window.location.search);
      currentParams.set('type', type);
      const exportUrl = "{{ route('report.indents.export') }}?" + currentParams.toString();
      window.location.href = exportUrl;
  }

  function exportToExcel() {
      triggerExport('excel');
  }

  function exportToPDF() {
      triggerExport('pdf');
  }

  function exportToCSV() {
      triggerExport('csv');
  }
</script>
@endsection
