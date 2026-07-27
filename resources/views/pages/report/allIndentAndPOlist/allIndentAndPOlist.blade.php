@extends("layouts.layout")

@section("bodyContent")
<div style="width: 100%; max-width: 100%; padding: 24px; box-sizing: border-box; font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">

    <!-- Top Page Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
        <div style="display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 14px; background: #EEF2FF; color: #4F46E5; display: flex; align-items: center; justify-content: center; border: 1px solid #C7D2FE;">
                <svg style="width: 24px; height: 24px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <div>
                <h1 style="font-size: 24px; font-weight: 800; color: #0F172A; margin: 0 0 2px 0; letter-spacing: -0.5px;">{{ $title }}</h1>
                <p style="font-size: 13px; color: #64748B; margin: 0; font-weight: 500;">Combined report tracking all indents and corresponding purchase orders</p>
            </div>
        </div>

        <!-- Export Dropdown -->
        <div style="position: relative;" id="exportDropdownContainer">
            <button type="button" onclick="toggleExportDropdown()" style="background: #4F46E5; color: #FFFFFF; font-weight: 700; font-size: 13px; padding: 10px 18px; border-radius: 10px; border: none; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 2px 6px rgba(79,70,229,0.25);">
                <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                <span>Export Report</span>
                <svg style="width: 12px; height: 12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div id="exportDropdownMenu" class="hidden" style="position: absolute; right: 0; margin-top: 8px; width: 190px; border-radius: 12px; background: #FFFFFF; border: 1px solid #E2E8F0; box-shadow: 0 10px 25px rgba(0,0,0,0.1); z-index: 50; overflow: hidden;">
                <div style="padding: 4px 0;">
                    <button type="button" onclick="exportToExcel()" style="display: flex; align-items: center; gap: 10px; width: 100%; padding: 10px 16px; border: none; background: transparent; color: #334155; font-size: 13px; font-weight: 600; text-align: left; cursor: pointer;" onmouseover="this.style.background='#F8FAFC'" onmouseout="this.style.background='transparent'">
                        <svg style="width: 16px; height: 16px; color: #10B981;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span>Excel Spreadsheet</span>
                    </button>
                    <button type="button" onclick="exportToPDF()" style="display: flex; align-items: center; gap: 10px; width: 100%; padding: 10px 16px; border: none; background: transparent; color: #334155; font-size: 13px; font-weight: 600; text-align: left; cursor: pointer;" onmouseover="this.style.background='#F8FAFC'" onmouseout="this.style.background='transparent'">
                        <svg style="width: 16px; height: 16px; color: #EF4444;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        <span>PDF Document</span>
                    </button>
                    <button type="button" onclick="exportToCSV()" style="display: flex; align-items: center; gap: 10px; width: 100%; padding: 10px 16px; border: none; background: transparent; color: #334155; font-size: 13px; font-weight: 600; text-align: left; cursor: pointer;" onmouseover="this.style.background='#F8FAFC'" onmouseout="this.style.background='transparent'">
                        <svg style="width: 16px; height: 16px; color: #3B82F6;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span>CSV File</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Card Container (Full Width) -->
    <div style="background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.03); overflow: hidden; margin-bottom: 24px;">
        
        <!-- Card Header Toolbar -->
        <div style="padding: 20px; border-bottom: 1px solid #F1F5F9; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; background: #FAFAFA;">
            <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                <div style="position: relative; width: 300px;">
                    <span style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #94A3B8;">
                        <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </span>
                    <input type="text" id="combinedSearchInput" placeholder="Search ID, department, party, PO..." 
                           style="width: 100%; padding: 9px 12px 9px 38px; background: #FFFFFF; border: 1px solid #CBD5E1; border-radius: 10px; font-size: 13px; outline: none; box-sizing: border-box;">
                </div>
                
                <div style="display: flex; align-items: center; gap: 8px; background: #FFFFFF; border: 1px solid #CBD5E1; border-radius: 10px; padding: 0 12px;">
                    <svg style="width: 16px; height: 16px; color: #94A3B8;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <input type="text" id="combinedDateRange" placeholder="Choose date range" readonly style="background: transparent; border: none; padding: 9px 0; font-size: 13px; color: #334155; outline: none; width: 200px;">
                </div>

                <button type="button" onclick="resetCombinedFilters()" style="padding: 9px 16px; font-size: 13px; font-weight: 600; background: #F1F5F9; color: #475569; border: 1px solid #CBD5E1; border-radius: 10px; cursor: pointer;">
                    Reset
                </button>
            </div>
        </div>

        <!-- Table Responsive Container (Full Width) -->
        <div style="width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch;">
            <table style="width: 100%; min-width: 2000px; border-collapse: collapse; text-align: left; font-size: 13px;" id="combined-report-table">
                <thead>
                    <tr style="background: #F8FAFC; border-bottom: 1px solid #E2E8F0; color: #64748B; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">
                        <th style="padding: 14px 16px; vertical-align: middle;">Indent ID</th>
                        <th style="padding: 14px 16px; vertical-align: middle;">Indent Date</th>
                        <th style="padding: 14px 16px; vertical-align: middle;">Department</th>
                        <th style="padding: 14px 16px; vertical-align: middle;">Project</th>
                        <th style="padding: 14px 16px; vertical-align: middle;">Indent Description</th>
                        <th style="padding: 14px 16px; vertical-align: middle;">Party Name</th>
                        <th style="padding: 14px 16px; vertical-align: middle;">PO Date</th>
                        <th style="padding: 14px 16px; vertical-align: middle;">PO/WO No</th>
                        <th style="padding: 14px 16px; vertical-align: middle;">PO Description</th>
                        <th style="padding: 14px 16px; vertical-align: middle;">PO Amount</th>
                        <th style="padding: 14px 16px; vertical-align: middle; text-align: center;">PO Status</th>
                        <th style="padding: 14px 16px; vertical-align: middle;">Expected Days</th>
                        <th style="padding: 14px 16px; vertical-align: middle;">Expected Date</th>
                        <th style="padding: 14px 16px; vertical-align: middle;">Invoice No.</th>
                        <th style="padding: 14px 16px; vertical-align: middle;">Invoice Date</th>
                        <th style="padding: 14px 16px; vertical-align: middle;">Receiving Date</th>
                        <th style="padding: 14px 16px; vertical-align: middle;">Delay in Days</th>
                        <th style="padding: 14px 16px; vertical-align: middle;">PO Remarks</th>
                        <th style="padding: 14px 16px; vertical-align: middle;">Indent Remarks</th>
                    </tr>
                </thead>
                <tbody id="combinedTableBody">
                    @forelse ($rows as $row)
                        @php
                            $status = $row['po_status'];
                            $normSt = strtolower(trim($status));
                            
                            $badgeBg = '#F8FAFC'; $badgeColor = '#475569'; $dotColor = '#64748B'; $badgeBorder = '#E2E8F0';
                            if ($normSt === 'partially received') {
                                $badgeBg = '#F3E8FF'; $badgeColor = '#7C3AED'; $dotColor = '#7C3AED'; $badgeBorder = '#E9D5FF';
                            } elseif (in_array($normSt, ['open', 'pending'])) {
                                $badgeBg = '#ECFDF5'; $badgeColor = '#047857'; $dotColor = '#10B981'; $badgeBorder = '#A7F3D0';
                            } elseif ($normSt === 'completed') {
                                $badgeBg = '#F0FDF4'; $badgeColor = '#15803D'; $dotColor = '#22C55E'; $badgeBorder = '#BBF7D0';
                            } elseif ($normSt === 'reopened') {
                                $badgeBg = '#F0F9FF'; $badgeColor = '#0369A1'; $dotColor = '#0284C7'; $badgeBorder = '#BAE6FD';
                            } elseif (in_array($normSt, ['cancel', 'cancelled'])) {
                                $badgeBg = '#FEF2F2'; $badgeColor = '#B91C1C'; $dotColor = '#EF4444'; $badgeBorder = '#FECDD3';
                            } elseif (in_array($normSt, ['close', 'closed'])) {
                                $badgeBg = '#F8FAFC'; $badgeColor = '#475569'; $dotColor = '#64748B'; $badgeBorder = '#E2E8F0';
                            }
                        @endphp
                        <tr style="border-bottom: 1px solid #F1F5F9; transition: background 0.15s ease;" onmouseover="this.style.background='#F8FAFC'" onmouseout="this.style.background='#FFFFFF'">
                            <td style="padding: 14px 16px; vertical-align: middle; font-weight: 800; color: #0F172A;">{{ $row['indent_id'] }}</td>
                            <td style="padding: 14px 16px; vertical-align: middle; color: #64748B; font-family: monospace;">{{ $row['indent_date'] }}</td>
                            <td style="padding: 14px 16px; vertical-align: middle; color: #475569; font-weight: 500;">{{ $row['department'] }}</td>
                            <td style="padding: 14px 16px; vertical-align: middle; color: #475569; font-weight: 500;">{{ $row['project'] }}</td>
                            <td style="padding: 14px 16px; vertical-align: middle; color: #334155; max-width: 220px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $row['total_description'] }}">{{ $row['total_description'] }}</td>
                            <td style="padding: 14px 16px; vertical-align: middle; color: #0F172A; font-weight: 700;">{{ $row['party_name'] }}</td>
                            <td style="padding: 14px 16px; vertical-align: middle; color: #64748B; font-family: monospace;">{{ $row['po_date'] }}</td>
                            <td style="padding: 14px 16px; vertical-align: middle; color: #475569; font-weight: 600;">{{ $row['po_no'] }}</td>
                            <td style="padding: 14px 16px; vertical-align: middle; color: #334155; max-width: 220px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $row['po_description'] }}">{{ $row['po_description'] }}</td>
                            <td style="padding: 14px 16px; vertical-align: middle; font-weight: 800; color: #0F172A; font-family: monospace;">{{ $row['po_amount'] !== '-' ? '₹'.$row['po_amount'] : '-' }}</td>
                            <td style="padding: 14px 16px; vertical-align: middle; text-align: center; white-space: nowrap;">
                                <span style="background: {{ $badgeBg }}; border: 1px solid {{ $badgeBorder }}; color: {{ $badgeColor }}; font-weight: 700; font-size: 11px; padding: 5px 14px; border-radius: 20px; display: inline-flex; align-items: center; gap: 6px;">
                                    <span style="width: 6px; height: 6px; border-radius: 50%; background: {{ $dotColor }}; display: inline-block;"></span>
                                    {{ ucfirst($status) }}
                                </span>
                            </td>
                            <td style="padding: 14px 16px; vertical-align: middle; color: #64748B;">{{ $row['expected_days'] ?? '—' }}</td>
                            <td style="padding: 14px 16px; vertical-align: middle; color: #64748B; font-family: monospace;">{{ $row['expected_date'] }}</td>
                            <td style="padding: 14px 16px; vertical-align: middle; color: #475569; font-weight: 600;">{{ $row['invoice_no'] }}</td>
                            <td style="padding: 14px 16px; vertical-align: middle; color: #64748B; font-family: monospace;">{{ $row['invoice_date'] }}</td>
                            <td style="padding: 14px 16px; vertical-align: middle; color: #64748B; font-family: monospace;">{{ $row['receiving_date'] }}</td>
                            <td style="padding: 14px 16px; vertical-align: middle; color: #64748B;">{{ $row['invoice_expected_days'] ?? '—' }}</td>
                            <td style="padding: 14px 16px; vertical-align: middle; color: #94A3B8;">{{ $row['remarks'] ?? '—' }}</td>
                            <td style="padding: 14px 16px; vertical-align: middle; color: #94A3B8;">{{ $row['indent_remarks'] ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="19" style="text-align: center; padding: 32px; color: #64748B; font-weight: 600;">No records found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    let filterUrl = "{{ $filterUrl }}";
    let datePickerInstance = null;

    document.addEventListener("DOMContentLoaded", function () {
        datePickerInstance = flatpickr("#combinedDateRange", {
            mode: "range",
            dateFormat: "Y-m-d",
            onChange: function() {
                fetchFilteredData();
            }
        });

        document.getElementById('combinedSearchInput').addEventListener('input', debounce(function() {
            fetchFilteredData();
        }, 300));
    });

    function debounce(func, wait) {
        let timeout;
        return function(...args) {
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(this, args), wait);
        };
    }

    function resetCombinedFilters() {
        document.getElementById('combinedSearchInput').value = '';
        if (datePickerInstance) {
            datePickerInstance.clear();
        }
        window.location.reload();
    }

    async function fetchFilteredData() {
        const search = document.getElementById('combinedSearchInput').value.trim();
        let from = '';
        let to = '';

        if (datePickerInstance && datePickerInstance.selectedDates.length === 2) {
            const fmt = d => d.toISOString().slice(0, 10);
            from = fmt(datePickerInstance.selectedDates[0]);
            to = fmt(datePickerInstance.selectedDates[1]);
        }

        const params = new URLSearchParams();
        if (search) params.set('q', search);
        if (from) params.set('from', from);
        if (to) params.set('to', to);

        try {
            const res = await fetch(`${filterUrl}?${params.toString()}`, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await res.json();
            repaintTable(data.rows);
        } catch (error) {
            console.error("Failed to filter data:", error);
        }
    }

    function repaintTable(rows) {
        const tbody = document.getElementById('combinedTableBody');

        if (!rows || rows.length === 0) {
            tbody.innerHTML = `<tr><td colspan="18" class="text-center py-4 text-gray-500">No records found.</td></tr>`;
            return;
        }

        tbody.innerHTML = rows.map(row => {
            const status = row.po_status || 'Open';
            const s = status.toLowerCase().trim();
            let badgeClass = 'bg-gray-100 text-gray-800';
            if (s === 'completed') badgeClass = 'bg-emerald-100 text-emerald-800 border border-emerald-300';
            else if (s === 'partially received') badgeClass = 'bg-blue-100 text-blue-800 border border-blue-300';
            else if (s === 'reopened') badgeClass = 'bg-purple-100 text-purple-800 border border-purple-300';
            else if (s === 'close' || s === 'closed') badgeClass = 'bg-gray-100 text-gray-800 border border-gray-300';
            else if (s === 'cancel' || s === 'cancelled') badgeClass = 'bg-red-100 text-red-800 border border-red-300';
            else if (s === 'open' || s === 'pending') badgeClass = 'bg-green-100 text-green-800 border border-green-300';

            const amountText = row.po_amount !== '-' ? '₹' + row.po_amount : '-';

            return `
                <tr class="border-b border-defaultborder hover:bg-gray-50 transition-colors">
                    <td class="font-medium text-gray-900">${escapeHtml(row.indent_id)}</td>
                    <td>${escapeHtml(row.indent_date)}</td>
                    <td>${escapeHtml(row.department)}</td>
                    <td>${escapeHtml(row.project)}</td>
                    <td class="max-w-xs truncate" title="${escapeHtml(row.total_description)}">${escapeHtml(row.total_description)}</td>
                    <td>${escapeHtml(row.party_name)}</td>
                    <td>${escapeHtml(row.po_date)}</td>
                    <td>${escapeHtml(row.po_no)}</td>
                    <td class="max-w-xs truncate" title="${escapeHtml(row.po_description)}">${escapeHtml(row.po_description)}</td>
                    <td>${escapeHtml(amountText)}</td>
                    <td>
                        <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-semibold ${badgeClass}">
                            ${escapeHtml(status)}
                        </span>
                    </td>
                    <td>${escapeHtml(row.expected_days)}</td>
                    <td>${escapeHtml(row.expected_date)}</td>
                    <td>${escapeHtml(row.invoice_no)}</td>
                    <td>${escapeHtml(row.invoice_date)}</td>
                    <td>${escapeHtml(row.receiving_date)}</td>
                    <td>${escapeHtml(row.invoice_expected_days)}</td>
                    <td>${escapeHtml(row.remarks)}</td>
                    <td>${escapeHtml(row.indent_remarks)}</td>
                </tr>
            `;
        }).join('');
    }

    function escapeHtml(str) {
        if (!str) return '—';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function toggleExportDropdown() {
        const menu = document.getElementById('exportDropdownMenu');
        if (menu.classList.contains('hidden')) {
            menu.classList.remove('hidden');
            setTimeout(() => {
                menu.classList.remove('scale-95', 'opacity-0');
                menu.classList.add('scale-100', 'opacity-100');
            }, 10);
        } else {
            menu.classList.remove('scale-100', 'opacity-100');
            menu.classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                menu.classList.add('hidden');
            }, 150);
        }
    }

    document.addEventListener('click', function(e) {
        const container = document.getElementById('exportDropdownContainer');
        const menu = document.getElementById('exportDropdownMenu');
        if (container && !container.contains(e.target) && menu && !menu.classList.contains('hidden')) {
            menu.classList.remove('scale-100', 'opacity-100');
            menu.classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                menu.classList.add('hidden');
            }, 150);
        }
    });

    function exportToExcel() {
        const table = document.getElementById("combined-report-table");
        const clone = table.cloneNode(true);
        
        const ws = XLSX.utils.table_to_sheet(clone);
        const range = XLSX.utils.decode_range(ws['!ref']);
        
        // Convert PO Amount (index 9) to true Excel numbers
        for (let R = range.s.r + 1; R <= range.e.r; ++R) {
            const cellRef = XLSX.utils.encode_cell({ r: R, c: 9 });
            if (ws[cellRef] && ws[cellRef].v) {
                const cleanNum = parseFloat(String(ws[cellRef].v).replace(/[₹,]/g, '').trim());
                if (!isNaN(cleanNum)) {
                    ws[cellRef].v = cleanNum;
                    ws[cellRef].t = 'n';
                    ws[cellRef].z = '"₹"#,##0.00';
                }
            }
        }
        
        // Auto-fit column widths
        const cols = [];
        for (let C = range.s.c; C <= range.e.c; ++C) {
            let maxLen = 10;
            for (let R = range.s.r; R <= range.e.r; ++R) {
                const address = XLSX.utils.encode_cell({ r: R, c: C });
                if (ws[address] && ws[address].v) {
                    maxLen = Math.max(maxLen, String(ws[address].v).length);
                }
            }
            cols.push({ wch: Math.min(maxLen + 3, 40) }); // cap auto-width to 40 to avoid gigantic cells
        }
        ws['!cols'] = cols;
        
        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, "Combined Report");
        XLSX.writeFile(wb, "Combined_Report_" + new Date().toISOString().slice(0, 10) + ".xlsx");
    }

    function exportToPDF() {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF('l', 'mm', 'a4'); // Landscape A4 (18 cols)
        const pw = doc.internal.pageSize.getWidth();
        const ph = doc.internal.pageSize.getHeight();

        // ── Header Banner ──────────────────────────────────────────────
        doc.setFillColor(37, 99, 235);
        doc.rect(0, 0, pw, 26, 'F');
        doc.setFillColor(99, 102, 241);
        doc.rect(0, 22, pw, 4, 'F');

        doc.setFont("helvetica", "bold");
        doc.setFontSize(15);
        doc.setTextColor(255, 255, 255);
        doc.text("Nitra Purchase Management System", 8, 12);

        doc.setFont("helvetica", "normal");
        doc.setFontSize(8);
        doc.setTextColor(191, 219, 254);
        doc.text("Combined Indents & Purchase Orders Report  |  Confidential", 8, 19);
        doc.text("Generated: " + new Date().toLocaleString(), pw - 8, 12, { align: 'right' });
        doc.text("inventory.nitratextile.org", pw - 8, 19, { align: 'right' });

        // ── Metadata row ───────────────────────────────────────────────
        doc.setFillColor(241, 245, 249);
        doc.rect(0, 26, pw, 8, 'F');
        doc.setFont("helvetica", "bold");
        doc.setFontSize(7.5);
        doc.setTextColor(71, 85, 105);
        doc.text("REPORT:", 8, 32);
        doc.setFont("helvetica", "normal");
        doc.text("Combined Indents & PO List — All Departments, All Projects", 25, 32);

        // ── Table ──────────────────────────────────────────────────────
        const headers = [
            "Indent ID", "Indent Date", "Dept", "Project", "Indent Description",
            "Party Name", "PO Date", "PO/WO No", "PO Description", "PO Amount",
            "Status", "Exp. Days", "Exp. Date", "Invoice No.", "Invoice Date",
            "Recv. Date", "Delay", "PO Remarks", "Indent Remarks"
        ];
        const rows = [];
        document.querySelectorAll("#combinedTableBody tr").forEach(tr => {
            const cells = tr.querySelectorAll("td");
            if (cells.length >= 19) {
                rows.push(Array.from({length: 19}, (_, i) => cells[i].innerText.trim()));
            }
        });

        doc.autoTable({
            head: [headers],
            body: rows,
            startY: 36,
            theme: 'grid',
            headStyles: {
                fillColor: [37, 99, 235],
                textColor: [255, 255, 255],
                fontStyle: 'bold',
                fontSize: 6,
                cellPadding: 2,
                halign: 'center',
                valign: 'middle'
            },
            bodyStyles: {
                fontSize: 5.5,
                textColor: [30, 41, 59],
                cellPadding: 1.5
            },
            columnStyles: {
                0:  { halign: 'center', cellWidth: 14 },
                1:  { halign: 'center', cellWidth: 15 },
                2:  { cellWidth: 13 },
                3:  { cellWidth: 14 },
                4:  { cellWidth: 22 },
                5:  { cellWidth: 16 },
                6:  { halign: 'center', cellWidth: 14 },
                7:  { halign: 'center', cellWidth: 15 },
                8:  { cellWidth: 18 },
                9:  { halign: 'right',  cellWidth: 13 },
                10: { halign: 'center', cellWidth: 13 },
                11: { halign: 'center', cellWidth: 11 },
                12: { halign: 'center', cellWidth: 15 },
                13: { halign: 'center', cellWidth: 15 },
                14: { halign: 'center', cellWidth: 15 },
                15: { halign: 'center', cellWidth: 15 },
                16: { halign: 'center', cellWidth: 10 },
                17: { cellWidth: 14 },
                18: { cellWidth: 14 }
            },
            alternateRowStyles: { fillColor: [239, 246, 255] },
            tableLineColor: [203, 213, 225],
            tableLineWidth: 0.15,
            margin: { left: 8, right: 8 },
            didDrawPage: function (data) {
                const y = ph - 12;
                doc.setDrawColor(203, 213, 225);
                doc.setLineWidth(0.3);
                doc.line(8, y, pw - 8, y);
                doc.setFont("helvetica", "normal");
                doc.setFontSize(6.5);
                doc.setTextColor(148, 163, 184);
                doc.text("Nitra Purchase Management System  •  inventory.nitratextile.org  •  CONFIDENTIAL", 8, ph - 7);
                doc.text(
                    "Page " + doc.internal.getCurrentPageInfo().pageNumber + " of " + doc.internal.getNumberOfPages(),
                    pw - 8, ph - 7, { align: 'right' }
                );
            }
        });

        doc.save("Combined_Indent_PO_Report_" + new Date().toISOString().slice(0, 10) + ".pdf");
    }

    function exportToCSV() {
        const table = document.getElementById("combined-report-table");
        let csv = [];
        const rows = table.querySelectorAll("tr");
        
        for (let i = 0; i < rows.length; i++) {
            let row = [], cols = rows[i].querySelectorAll("td, th");
            for (let j = 0; j < cols.length; j++) {
                let cellText = cols[j].innerText.trim().replace(/"/g, '""');
                row.push('"' + cellText + '"');
            }
            csv.push(row.join(","));
        }
        
        const csvContent = "data:text/csv;charset=utf-8," + csv.join("\n");
        const encodedUri = encodeURI(csvContent);
        const link = document.createElement("a");
        link.setAttribute("href", encodedUri);
        link.setAttribute("download", "combined_report_" + new Date().toISOString().slice(0, 10) + ".csv");
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
</script>
@endsection
