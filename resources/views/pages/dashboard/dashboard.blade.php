@extends("layouts.layout")

@section("bodyContent")
<div style="width: 100%; max-width: 100%; padding: 24px; box-sizing: border-box; font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">

    @include("pages.home")

    @if (session('success'))
        <div style="margin-bottom: 20px; padding: 14px 18px; background: #ECFDF5; border: 1px solid #A7F3D0; border-radius: 12px; color: #047857; font-size: 13px; font-weight: 700; display: flex; align-items: center; gap: 8px;">
            <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Recent Indents Card (Full Width) -->
    <div style="background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.03); overflow: hidden; margin-bottom: 24px;">
        
        <!-- Header Toolbar -->
        <div style="padding: 20px; border-bottom: 1px solid #F1F5F9; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; background: #FAFAFA;">
            <div style="display: flex; align-items: center; gap: 12px;">
                <div style="width: 38px; height: 38px; border-radius: 12px; background: #2563EB; color: #FFFFFF; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 12px rgba(37,99,235,0.25);">
                    <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <div>
                    <h3 style="font-size: 18px; font-weight: 800; color: #0F172A; margin: 0; letter-spacing: -0.3px;">{{ $title }}</h3>
                    <p style="font-size: 12px; color: #64748B; margin: 0; font-weight: 500;">Overview of recently filed indents and quick actions</p>
                </div>
            </div>

            <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                <!-- Search Input -->
                <div style="position: relative; width: 280px;">
                    <span style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #94A3B8;">
                        <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </span>
                    <input type="text" placeholder="Search recent indents..." onkeyup="filterDashboardTable(this)"
                           style="width: 100%; padding: 9px 12px 9px 38px; background: #FFFFFF; border: 1px solid #CBD5E1; border-radius: 10px; font-size: 13px; outline: none; box-sizing: border-box;">
                </div>

                <!-- Add New Indent Button -->
                <a href="{{ route('indent.create') }}" 
                   style="background: #2563EB; color: #FFFFFF; font-weight: 700; font-size: 13px; padding: 9px 18px; border-radius: 10px; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; box-shadow: 0 2px 6px rgba(37,99,235,0.25);">
                    <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>Add New Indent</span>
                </a>
            </div>
        </div>

        <!-- Table Responsive Container -->
        <div style="width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch;">
            <table style="width: 100%; min-width: 1100px; border-collapse: collapse; text-align: left; font-size: 13px; table-layout: fixed;" id="dashboard-recent-table">
                <thead>
                    <tr style="background: #F8FAFC; border-bottom: 1px solid #E2E8F0; color: #64748B; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">
                        <th style="padding: 14px 16px; width: 110px; vertical-align: middle;">INDENT ID <span style="color:#CBD5E1;">↕</span></th>
                        <th style="padding: 14px 16px; width: 150px; vertical-align: middle;">DEPARTMENT <span style="color:#CBD5E1;">↕</span></th>
                        <th style="padding: 14px 16px; width: 140px; vertical-align: middle;">PROJECT <span style="color:#CBD5E1;">↕</span></th>
                        <th style="padding: 14px 16px; width: 220px; vertical-align: middle;">DESCRIPTION <span style="color:#CBD5E1;">↕</span></th>
                        <th style="padding: 14px 16px; width: 130px; text-align: center; vertical-align: middle;">STATUS <span style="color:#CBD5E1;">↕</span></th>
                        <th style="padding: 14px 16px; width: 130px; vertical-align: middle;">CREATED DATE <span style="color:#CBD5E1;">↕</span></th>
                        <th style="padding: 14px 16px; width: 260px; text-align: center; vertical-align: middle;">ACTION</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rows as $row)
                        @php
                            $status = $row['status'];
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
                            
                            <!-- Department -->
                            <td style="padding: 16px; vertical-align: middle; color: #475569; font-weight: 600; white-space: nowrap;">{{ $row['department_name'] }}</td>
                            
                            <!-- Project -->
                            <td style="padding: 16px; vertical-align: middle; color: #0F172A; font-weight: 700; white-space: nowrap;">{{ $row['project'] }}</td>

                            <!-- Description -->
                            <td style="padding: 16px; vertical-align: middle; color: #334155; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $row['item_description'] }}">
                                {{ $row['item_description'] }}
                            </td>

                            <!-- Status Badge -->
                            <td style="padding: 16px; vertical-align: middle; text-align: center; white-space: nowrap;">
                                <span style="background: {{ $badgeBg }}; border: 1px solid {{ $badgeBorder }}; color: {{ $badgeColor }}; font-weight: 700; font-size: 12px; padding: 5px 14px; border-radius: 20px; display: inline-flex; align-items: center; gap: 6px;">
                                    <span style="width: 6px; height: 6px; border-radius: 50%; background: {{ $dotColor }}; display: inline-block;"></span>
                                    {{ ucfirst($status) }}
                                </span>
                            </td>

                            <!-- Date -->
                            <td style="padding: 16px; vertical-align: middle; color: #64748B; font-family: monospace; white-space: nowrap;">
                                {{ $row['date'] }}
                            </td>

                            <!-- Action Buttons -->
                            <td style="padding: 16px; vertical-align: middle; text-align: center; white-space: nowrap;">
                                <div style="display: flex; align-items: center; justify-content: center; gap: 6px;">
                                    @if (isset($actions['edit']))
                                        <a href="{{ $actions['edit'] }}" title="Edit Ticket"
                                           style="background: #EFF6FF; border: 1px solid #BFDBFE; color: #2563EB; font-weight: 700; font-size: 12px; padding: 6px 12px; border-radius: 8px; text-decoration: none; display: inline-flex; align-items: center; gap: 4px;">
                                            <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            <span>Edit</span>
                                        </a>
                                    @endif

                                    @if (isset($actions['file_po']))
                                        <a href="{{ $actions['file_po'] }}" title="File PO"
                                           style="background: #FFF7ED; border: 1px solid #FED7AA; color: #EA580C; font-weight: 700; font-size: 12px; padding: 6px 12px; border-radius: 8px; text-decoration: none; display: inline-flex; align-items: center; gap: 4px;">
                                            <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                            <span>File PO</span>
                                        </a>
                                    @endif

                                    @if (isset($actions['pending']))
                                        <form action="{{ $actions['pending']['route'] }}" method="POST" class="js-dashboard-status-form" style="display: inline;">
                                            @csrf
                                            <input type="hidden" name="indent_id" value="{{ $actions['pending']['params']['indent_id'] }}">
                                            <input type="hidden" name="department_id" value="{{ $actions['pending']['params']['department_id'] }}">
                                            <input type="hidden" name="status" value="Pending">
                                            <button type="button" data-action="Pending" onclick="confirmDashboardStatus(this)" title="Re-Open Ticket"
                                                    style="background: #ECFDF5; border: 1px solid #A7F3D0; color: #047857; font-weight: 700; font-size: 12px; padding: 6px 12px; border-radius: 8px; cursor: pointer; display: inline-flex; align-items: center; gap: 4px;">
                                                <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                                <span>Re-Open</span>
                                            </button>
                                        </form>
                                    @endif

                                    @if (isset($actions['close']))
                                        <form action="{{ $actions['close']['route'] }}" method="POST" class="js-dashboard-status-form" style="display: inline;">
                                            @csrf
                                            <input type="hidden" name="indent_id" value="{{ $actions['close']['params']['indent_id'] }}">
                                            <input type="hidden" name="department_id" value="{{ $actions['close']['params']['department_id'] }}">
                                            <input type="hidden" name="status" value="Close">
                                            <button type="button" data-action="Close" onclick="confirmDashboardStatus(this)" title="Close Ticket"
                                                    style="background: #F8FAFC; border: 1px solid #CBD5E1; color: #334155; font-weight: 700; font-size: 12px; padding: 6px 12px; border-radius: 8px; cursor: pointer; display: inline-flex; align-items: center; gap: 4px;">
                                                <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                <span>Close</span>
                                            </button>
                                        </form>
                                    @endif

                                    @if (isset($actions['cancel']))
                                        <form action="{{ $actions['cancel']['route'] }}" method="POST" class="js-dashboard-status-form" style="display: inline;">
                                            @csrf
                                            <input type="hidden" name="indent_id" value="{{ $actions['cancel']['params']['indent_id'] }}">
                                            <input type="hidden" name="department_id" value="{{ $actions['cancel']['params']['department_id'] }}">
                                            <input type="hidden" name="status" value="Cancel">
                                            <button type="button" data-action="Cancel" onclick="confirmDashboardStatus(this)" title="Cancel Ticket"
                                                    style="background: #FEF2F2; border: 1px solid #FECDD3; color: #DC2626; font-weight: 700; font-size: 12px; padding: 6px 12px; border-radius: 8px; cursor: pointer; display: inline-flex; align-items: center; gap: 4px;">
                                                <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                <span>Cancel</span>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 32px; color: #64748B; font-weight: 600;">No recent indents found.</td>
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