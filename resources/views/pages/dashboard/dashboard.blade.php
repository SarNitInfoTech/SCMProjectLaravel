@extends("layouts.layout")

@section("bodyContent")
<div style="width: 100%; max-width: 100%; padding: 24px; box-sizing: border-box; font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">

    @include("pages.home")

    @if (session('success'))
        <div style="padding: 14px 18px; background: #ECFDF5; border-left: 4px solid #10B981; color: #065F46; font-size: 13px; font-weight: 600; border-radius: 8px; margin-bottom: 20px;">
            {{ session('success') }}
        </div>
    @endif

    <!-- Recent Indent Register Card (Full Width - Identical UI to Indent Register List) -->
    <div style="background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.03); overflow: hidden; margin-bottom: 24px;">
        
        <!-- Card Header Toolbar -->
        <div style="padding: 20px; border-bottom: 1px solid #F1F5F9; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; background: #FAFAFA;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <div style="width: 34px; height: 34px; border-radius: 10px; background: #EFF6FF; color: #2563EB; display: flex; align-items: center; justify-content: center; border: 1px solid #BFDBFE;">
                    <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <h2 style="font-size: 18px; font-weight: 800; color: #0F172A; margin: 0;">Recent Indent Register List</h2>
            </div>

            <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                <!-- Search Input -->
                <div style="position: relative; width: 340px;">
                    <span style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #94A3B8;">
                        <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </span>
                    <input type="text" placeholder="Search recent indents..." onkeyup="filterDashboardTable(this)" 
                           style="width: 100%; padding: 9px 12px 9px 38px; background: #FFFFFF; border: 1px solid #CBD5E1; border-radius: 10px; font-size: 13px; outline: none; box-sizing: border-box;">
                </div>

                <!-- Add New Indent Button -->
                <a href="{{ route('indent.create') }}" 
                   style="background: #2563EB; color: #FFFFFF; font-weight: 700; font-size: 13px; padding: 9px 18px; border-radius: 10px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 2px 6px rgba(37,99,235,0.25);">
                    <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                    <span>Add New Indent</span>
                </a>
            </div>
        </div>

        <!-- Table Responsive Container (Full Width) -->
        <div style="width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch;">
            <table style="width: 100%; min-width: 1200px; border-collapse: collapse; text-align: left; font-size: 13px; table-layout: fixed;" id="dashboard-recent-table">
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
                                            <form action="{{ $actions['pending']['route'] }}" method="POST" class="js-dashboard-status-form" style="display: inline;">
                                                @csrf
                                                <input type="hidden" name="indent_id" value="{{ $actions['pending']['params']['indent_id'] }}">
                                                <input type="hidden" name="department_id" value="{{ $actions['pending']['params']['department_id'] }}">
                                                <input type="hidden" name="status" value="Pending">
                                                <button type="button" data-action="Pending" onclick="confirmDashboardStatus(this)"
                                                        style="background: #ECFDF5; border: 1px solid #A7F3D0; color: #047857; font-weight: 700; font-size: 11px; padding: 6px 12px; border-radius: 20px; cursor: pointer; display: inline-flex; align-items: center; gap: 5px; white-space: nowrap;">
                                                    <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>
                                                    <span>Re-Open</span>
                                                </button>
                                            </form>
                                        @endif

                                        <!-- Close Button -->
                                        @if (isset($actions['close']))
                                            <form action="{{ $actions['close']['route'] }}" method="POST" class="js-dashboard-status-form" style="display: inline;">
                                                @csrf
                                                <input type="hidden" name="indent_id" value="{{ $actions['close']['params']['indent_id'] }}">
                                                <input type="hidden" name="department_id" value="{{ $actions['close']['params']['department_id'] }}">
                                                <input type="hidden" name="status" value="Close">
                                                <button type="button" data-action="Close" onclick="confirmDashboardStatus(this)"
                                                        style="background: #F8FAFC; border: 1px solid #E2E8F0; color: #334155; font-weight: 700; font-size: 11px; padding: 6px 12px; border-radius: 20px; cursor: pointer; display: inline-flex; align-items: center; gap: 5px; white-space: nowrap;">
                                                    <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                    <span>Close</span>
                                                </button>
                                            </form>
                                        @endif

                                        <!-- Cancel Button -->
                                        @if (isset($actions['cancel']))
                                            <form action="{{ $actions['cancel']['route'] }}" method="POST" class="js-dashboard-status-form" style="display: inline;">
                                                @csrf
                                                <input type="hidden" name="indent_id" value="{{ $actions['cancel']['params']['indent_id'] }}">
                                                <input type="hidden" name="department_id" value="{{ $actions['cancel']['params']['department_id'] }}">
                                                <input type="hidden" name="status" value="Cancel">
                                                <button type="button" data-action="Cancel" onclick="confirmDashboardStatus(this)"
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
                            <td colspan="8" style="text-align: center; padding: 32px; color: #64748B; font-weight: 600;">No recent indents found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Confirmation -->
<div id="dashboardStatusModal" style="display: none; position: fixed; inset: 0; z-index: 99999;">
    <div onclick="closeDashboardModal()" style="position: fixed; inset: 0; background: rgba(15,23,42,0.4); backdrop-filter: blur(2px);"></div>
    <div style="position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 400px; max-width: 90vw; background: #FFFFFF; border-radius: 16px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1); padding: 24px; z-index: 100000;">
        <h4 id="dashboardModalTitle" style="font-size: 18px; font-weight: 800; color: #0F172A; margin: 0 0 8px 0;">Confirm Action</h4>
        <p id="dashboardModalText" style="font-size: 13px; color: #475569; margin: 0 0 20px 0;">Are you sure you want to proceed?</p>
        <div style="display: flex; justify-content: flex-end; gap: 10px;">
            <button type="button" onclick="closeDashboardModal()" style="padding: 9px 16px; border-radius: 10px; font-size: 13px; font-weight: 600; background: #F1F5F9; color: #475569; border: none; cursor: pointer;">Cancel</button>
            <button type="button" id="dashboardConfirmBtn" style="padding: 9px 20px; border-radius: 10px; font-size: 13px; font-weight: 700; background: #2563EB; color: #FFFFFF; border: none; cursor: pointer;">Confirm</button>
        </div>
    </div>
</div>

<script>
let currentFormToSubmit = null;

function confirmDashboardStatus(btn) {
    const action = btn.getAttribute('data-action');
    currentFormToSubmit = btn.closest('form');
    
    document.getElementById('dashboardModalTitle').textContent = `Confirm ${action}`;
    document.getElementById('dashboardModalText').textContent = `Are you sure you want to change the status of this indent to "${action}"?`;
    document.getElementById('dashboardStatusModal').style.display = 'block';
}

function closeDashboardModal() {
    document.getElementById('dashboardStatusModal').style.display = 'none';
    currentFormToSubmit = null;
}

document.getElementById('dashboardConfirmBtn').addEventListener('click', function() {
    if (currentFormToSubmit) {
        currentFormToSubmit.submit();
    }
});

function filterDashboardTable(input) {
    const filter = input.value.toLowerCase();
    const rows = document.querySelectorAll('#dashboard-recent-table tbody tr');
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(filter) ? '' : 'none';
    });
}
</script>
@endsection