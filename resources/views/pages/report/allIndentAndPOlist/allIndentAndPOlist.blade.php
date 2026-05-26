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
                    <th scope="col" class="text-start">Remarks</th>
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
                    </tr>
                @empty
                    <tr>
                        <td colspan="18" class="text-center py-4 text-gray-500">No records found.</td>
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
        // 18 columns: Landscape A4 is mandatory
        const doc = new jsPDF('l', 'mm', 'a4');
        
        doc.setFont("helvetica", "bold");
        doc.setFontSize(16);
        doc.setTextColor(79, 70, 229); // Indigo
        doc.text("Nitra Textile SCM System", 14, 15);
        
        doc.setFont("helvetica", "normal");
        doc.setFontSize(9);
        doc.setTextColor(100, 116, 139); // Slate
        doc.text("Combined Indents & Purchase Orders Report", 14, 21);
        doc.text("Generated: " + new Date().toLocaleString(), 14, 26);
        
        doc.setDrawColor(226, 232, 240);
        doc.line(14, 30, 283, 30);
        
        const headers = [
            "Indent ID", "Indent Date", "Dept", "Project", "Indent Item Description", 
            "Party Name", "PO Date", "PO/WO No", "PO Item Description", "PO Amount", 
            "Status", "Expected Days", "Expected Date", "Invoice No.", "Invoice Date", 
            "Receiving Date", "Delay", "Remarks"
        ];
        const rows = [];
        
        document.querySelectorAll("#combinedTableBody tr").forEach(tr => {
            const cells = tr.querySelectorAll("td");
            if (cells.length >= 18) {
                rows.push([
                    cells[0].innerText.trim(),
                    cells[1].innerText.trim(),
                    cells[2].innerText.trim(),
                    cells[3].innerText.trim(),
                    cells[4].innerText.trim(),
                    cells[5].innerText.trim(),
                    cells[6].innerText.trim(),
                    cells[7].innerText.trim(),
                    cells[8].innerText.trim(),
                    cells[9].innerText.trim(),
                    cells[10].innerText.trim(),
                    cells[11].innerText.trim(),
                    cells[12].innerText.trim(),
                    cells[13].innerText.trim(),
                    cells[14].innerText.trim(),
                    cells[15].innerText.trim(),
                    cells[16].innerText.trim(),
                    cells[17].innerText.trim()
                ]);
            }
        });
        
        doc.autoTable({
            head: [headers],
            body: rows,
            startY: 33,
            theme: 'striped',
            headStyles: {
                fillColor: [79, 70, 229],
                textColor: [255, 255, 255],
                fontStyle: 'bold',
                fontSize: 6.5
            },
            bodyStyles: {
                fontSize: 6,
                textColor: [30, 41, 59]
            },
            columnStyles: {
                4: { cellWidth: 26 }, // Indent item descriptions
                8: { cellWidth: 22 }, // PO item descriptions
                17: { cellWidth: 16 } // Remarks
            },
            alternateRowStyles: {
                fillColor: [248, 250, 252]
            },
            margin: { left: 8, right: 8 },
            didDrawPage: function (data) {
                const pageCount = doc.internal.getNumberOfPages();
                doc.setFont("helvetica", "normal");
                doc.setFontSize(7);
                doc.setTextColor(148, 163, 184);
                
                doc.text("Page " + doc.internal.getCurrentPageInfo().pageNumber + " of " + pageCount, 283, doc.internal.pageSize.height - 10, { align: 'right' });
                doc.text("NITRA Supply Chain Management - CONFIDENTIAL", 8, doc.internal.pageSize.height - 10);
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
