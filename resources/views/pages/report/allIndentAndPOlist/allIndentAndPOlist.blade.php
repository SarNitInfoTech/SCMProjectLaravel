@extends("layouts.layout")

@section("bodyContent")
<div class="md:flex block items-center justify-between my-6 page-header-breadcrumb">
    <div>
        <h4 class="mb-0 text-defaulttextcolor font-medium">{{ $title }}</h4>
        <p class="-mt-[0.2rem] mb-0 text-textmuted">Combined report tracking all indents and corresponding purchase orders</p>
    </div>
</div>

<div class="card shadow-sm border mb-6 bg-white">
    <div class="card-header p-4 border-b flex flex-wrap justify-between items-center gap-4">
        <div class="flex flex-wrap items-center gap-2">
            <input
                type="text"
                id="combinedSearchInput"
                placeholder="Search ID, department, party, PO..."
                class="form-input rounded border px-3 py-1.5 text-sm w-64 bg-gray-50"
            >
            
            <div class="flex items-center gap-1 bg-gray-50 border rounded px-2">
                <i class="ri-calendar-line text-gray-400"></i>
                <input
                    type="text"
                    id="combinedDateRange"
                    placeholder="Choose date range"
                    class="form-input bg-transparent border-0 px-2 py-1.5 text-sm w-56 focus:outline-none"
                    readonly
                >
            </div>

            <button type="button" onclick="resetCombinedFilters()" class="px-3 py-1.5 text-sm bg-gray-100 hover:bg-gray-200 text-gray-700 rounded">
                Reset
            </button>
        </div>

        <div class="relative inline-block text-left" id="exportDropdownContainer">
            <button type="button" onclick="toggleExportDropdown()" class="px-4 py-1.5 text-sm font-semibold bg-indigo-600 hover:bg-indigo-700 text-white rounded shadow flex items-center gap-2 transition-all">
                <i class="ri-download-cloud-2-line"></i>
                Export Report
                <i class="ri-arrow-down-s-line"></i>
            </button>
            <div id="exportDropdownMenu" class="hidden absolute right-0 mt-2 w-48 rounded-lg shadow-xl bg-white border border-gray-100 divide-y divide-gray-100 z-50 transition-all origin-top-right transform scale-95 opacity-0">
                <div class="py-1">
                    <button type="button" onclick="exportToExcel()" class="flex items-center gap-3 w-full px-4 py-2.5 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-900 transition-colors text-left font-medium">
                        <i class="ri-file-excel-2-line text-emerald-600 text-lg"></i>
                        Excel Spreadsheet
                    </button>
                    <button type="button" onclick="exportToPDF()" class="flex items-center gap-3 w-full px-4 py-2.5 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-900 transition-colors text-left font-medium">
                        <i class="ri-file-pdf-line text-red-600 text-lg"></i>
                        PDF Document
                    </button>
                    <button type="button" onclick="exportToCSV()" class="flex items-center gap-3 w-full px-4 py-2.5 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-900 transition-colors text-left font-medium">
                        <i class="ri-file-text-line text-blue-600 text-lg"></i>
                        CSV File
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="table-responsive p-4 overflow-x-auto">
        <table class="table whitespace-nowrap min-w-full" id="combined-report-table">
            <thead>
                <tr class="border-b border-defaultborder">
                    <th scope="col" class="text-start">Indent ID</th>
                    <th scope="col" class="text-start">Indent Date</th>
                    <th scope="col" class="text-start">Department</th>
                    <th scope="col" class="text-start">Project</th>
                    <th scope="col" class="text-start">Indent Description</th>
                    <th scope="col" class="text-start">Party Name</th>
                    <th scope="col" class="text-start">PO Date</th>
                    <th scope="col" class="text-start">PO/WO No</th>
                    <th scope="col" class="text-start">PO Description</th>
                    <th scope="col" class="text-start">PO Amount</th>
                    <th scope="col" class="text-start">PO Status</th>
                    <th scope="col" class="text-start">Expected Days</th>
                    <th scope="col" class="text-start">Expected Date</th>
                    <th scope="col" class="text-start">Invoice No.</th>
                    <th scope="col" class="text-start">Invoice Date</th>
                    <th scope="col" class="text-start">Receiving Date</th>
                    <th scope="col" class="text-start">Delay in Days</th>
                    <th scope="col" class="text-start">PO Remarks</th>
                    <th scope="col" class="text-start">Indent Remarks</th>
                </tr>
            </thead>
            <tbody id="combinedTableBody">
                @forelse ($rows as $row)
                    @php
                        $status = $row['po_status'];
                        $badgeClass = match (strtolower($status)) {
                            'pending' => 'bg-yellow-100 text-yellow-800',
                            'cancel', 'cancelled' => 'bg-red-100 text-red-800',
                            'close', 'closed' => 'bg-green-100 text-green-800',
                            default => 'bg-gray-100 text-gray-800'
                        };
                    @endphp
                    <tr class="border-b border-defaultborder hover:bg-gray-50 transition-colors">
                        <td class="font-medium text-gray-900">{{ $row['indent_id'] }}</td>
                        <td>{{ $row['indent_date'] }}</td>
                        <td>{{ $row['department'] }}</td>
                        <td>{{ $row['project'] }}</td>
                        <td class="max-w-xs truncate" title="{{ $row['total_description'] }}">{{ $row['total_description'] }}</td>
                        <td>{{ $row['party_name'] }}</td>
                        <td>{{ $row['po_date'] }}</td>
                        <td>{{ $row['po_no'] }}</td>
                        <td class="max-w-xs truncate" title="{{ $row['po_description'] }}">{{ $row['po_description'] }}</td>
                        <td>{{ $row['po_amount'] !== '-' ? '₹'.$row['po_amount'] : '-' }}</td>
                        <td>
                            <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $badgeClass }}">
                                {{ $status }}
                            </span>
                        </td>
                        <td>{{ $row['expected_days'] ?? '—' }}</td>
                        <td>{{ $row['expected_date'] }}</td>
                        <td>{{ $row['invoice_no'] }}</td>
                        <td>{{ $row['invoice_date'] }}</td>
                        <td>{{ $row['receiving_date'] }}</td>
                        <td>{{ $row['invoice_expected_days'] ?? '—' }}</td>
                        <td>{{ $row['remarks'] ?? '—' }}</td>
                        <td>{{ $row['indent_remarks'] ?? '—' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="19" class="text-center py-4 text-gray-500">No records found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
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
            const status = row.po_status || 'Pending';
            let badgeClass = 'bg-gray-100 text-gray-800';
            if (status.toLowerCase() === 'pending') badgeClass = 'bg-yellow-100 text-yellow-800';
            else if (status.toLowerCase() === 'cancel') badgeClass = 'bg-red-100 text-red-800';
            else if (status.toLowerCase() === 'close') badgeClass = 'bg-green-100 text-green-800';

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
