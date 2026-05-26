@extends("layouts.layout")

@section("bodyContent")
<div class="md:flex block items-center justify-between my-6 page-header-breadcrumb">
    <div>
        <h4 class="mb-0 text-defaulttextcolor font-medium">PO Report by Indent</h4>
        <p class="-mt-[0.2rem] mb-0 text-textmuted">Analyze purchase orders and matching indents</p>
    </div>
</div>

<div class="card shadow-sm border mb-6 bg-white">
    <div class="card-header p-4 border-b">
        <form method="GET" action="{{ route('reports.po') }}" class="flex flex-wrap items-center justify-between gap-4">
            <div class="flex flex-wrap items-center gap-2">
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Search party, PO no, project..."
                    class="form-input rounded border px-3 py-1.5 text-sm w-64 bg-gray-50"
                >
                
                <div class="flex items-center gap-1 bg-gray-50 border rounded px-2">
                    <i class="ri-calendar-line text-gray-400"></i>
                    <input
                        type="text"
                        id="reportDateRange"
                        placeholder="Choose date range"
                        class="form-input bg-transparent border-0 px-2 py-1.5 text-sm w-56 focus:outline-none"
                        readonly
                    >
                    <input type="hidden" name="start_date" id="startDate" value="{{ request('start_date') }}">
                    <input type="hidden" name="end_date" id="endDate" value="{{ request('end_date') }}">
                </div>

                <button type="submit" class="px-4 py-1.5 text-sm bg-gray-800 hover:bg-gray-900 text-white rounded">
                    Filter
                </button>
                @if(request()->filled('search') || request()->filled('start_date'))
                    <a href="{{ route('reports.po') }}" class="px-3 py-1.5 text-sm bg-gray-100 hover:bg-gray-200 text-gray-700 rounded">
                        Reset
                    </a>
                @endif
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
        </form>
    </div>

    <div class="table-responsive p-4">
        <table class="table whitespace-nowrap min-w-full" id="po-report-table">
            <thead>
                <tr class="border-b border-defaultborder">
                    <th scope="col" class="text-start">Indent Ticket</th>
                    <th scope="col" class="text-start">Department</th>
                    <th scope="col" class="text-start">Project</th>
                    <th scope="col" class="text-start">Party Name</th>
                    <th scope="col" class="text-start">PO No.</th>
                    <th scope="col" class="text-start">PO Amount</th>
                    <th scope="col" class="text-start">Status</th>
                    <th scope="col" class="text-start">Created On</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($reports as $row)
                    @php
                        $status = $row->status;
                        $badgeClass = match (strtolower($status)) {
                            'pending' => 'bg-yellow-100 text-yellow-800',
                            'cancel', 'cancelled' => 'bg-red-100 text-red-800',
                            'close', 'closed' => 'bg-green-100 text-green-800',
                            default => 'bg-gray-100 text-gray-800'
                        };
                    @endphp
                    <tr class="border-b border-defaultborder hover:bg-gray-50 transition-colors">
                        <td class="font-medium text-gray-900">{{ $row->indent_ticket_no ?? '—' }}</td>
                        <td>{{ $row->department_name ?? '—' }}</td>
                        <td>{{ $row->project_name ?? '—' }}</td>
                        <td>{{ $row->party_name ?? '—' }}</td>
                        <td>{{ $row->po_wo_no ?? '—' }}</td>
                        <td>₹{{ number_format((float)$row->po_amount, 2) }}</td>
                        <td>
                            <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $badgeClass }}">
                                {{ $status }}
                            </span>
                        </td>
                        <td>{{ $row->po_created_at ? \Carbon\Carbon::parse($row->po_created_at)->format('d-m-Y') : '—' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-gray-500">No records found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($reports->hasPages())
        <div class="p-4 border-t">
            {{ $reports->appends(request()->query())->links('pagination::tailwind') }}
        </div>
    @endif
</div>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const startVal = document.getElementById('startDate').value;
        const endVal = document.getElementById('endDate').value;
        
        flatpickr("#reportDateRange", {
            mode: "range",
            dateFormat: "Y-m-d",
            defaultDate: startVal && endVal ? [startVal, endVal] : null,
            onChange: function(selectedDates) {
                if (selectedDates.length === 2) {
                    const fmt = d => d.toISOString().slice(0, 10);
                    document.getElementById('startDate').value = fmt(selectedDates[0]);
                    document.getElementById('endDate').value = fmt(selectedDates[1]);
                } else {
                    document.getElementById('startDate').value = '';
                    document.getElementById('endDate').value = '';
                }
            }
        });
    });

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
        const table = document.getElementById("po-report-table");
        const clone = table.cloneNode(true);
        
        const ws = XLSX.utils.table_to_sheet(clone);
        const range = XLSX.utils.decode_range(ws['!ref']);
        
        // Convert Amount column (index 5) to true Excel numbers
        for (let R = range.s.r + 1; R <= range.e.r; ++R) {
            const cellRef = XLSX.utils.encode_cell({ r: R, c: 5 });
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
            let maxLen = 12;
            for (let R = range.s.r; R <= range.e.r; ++R) {
                const address = XLSX.utils.encode_cell({ r: R, c: C });
                if (ws[address] && ws[address].v) {
                    maxLen = Math.max(maxLen, String(ws[address].v).length);
                }
            }
            cols.push({ wch: maxLen + 3 });
        }
        ws['!cols'] = cols;
        
        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, "PO Report");
        XLSX.writeFile(wb, "PO_Report_by_Indent_" + new Date().toISOString().slice(0, 10) + ".xlsx");
    }

    function exportToPDF() {
        const { jsPDF } = window.jspdf;
        // PO by Indent has 8 columns; landscape is much better to avoid compression
        const doc = new jsPDF('l', 'mm', 'a4'); // Landscape
        
        doc.setFont("helvetica", "bold");
        doc.setFontSize(18);
        doc.setTextColor(79, 70, 229); // Indigo
        doc.text("Nitra Textile SCM System", 14, 20);
        
        doc.setFont("helvetica", "normal");
        doc.setFontSize(10);
        doc.setTextColor(100, 116, 139); // Slate
        doc.text("Report: PO Report by Indent", 14, 26);
        doc.text("Generated On: " + new Date().toLocaleString(), 14, 31);
        
        doc.setDrawColor(226, 232, 240);
        doc.line(14, 35, 283, 35); // wider line for landscape
        
        const headers = ["Indent Ticket", "Department", "Project", "Party Name", "PO No.", "PO Amount", "Status", "Created On"];
        const rows = [];
        
        document.querySelectorAll("#po-report-table tbody tr").forEach(tr => {
            const cells = tr.querySelectorAll("td");
            if (cells.length >= 8) {
                rows.push([
                    cells[0].innerText.trim(),
                    cells[1].innerText.trim(),
                    cells[2].innerText.trim(),
                    cells[3].innerText.trim(),
                    cells[4].innerText.trim(),
                    cells[5].innerText.trim(),
                    cells[6].innerText.trim(),
                    cells[7].innerText.trim()
                ]);
            }
        });
        
        doc.autoTable({
            head: [headers],
            body: rows,
            startY: 40,
            theme: 'striped',
            headStyles: {
                fillColor: [79, 70, 229],
                textColor: [255, 255, 255],
                fontStyle: 'bold',
                fontSize: 9.5
            },
            bodyStyles: {
                fontSize: 9,
                textColor: [30, 41, 59]
            },
            alternateRowStyles: {
                fillColor: [248, 250, 252]
            },
            didDrawPage: function (data) {
                const pageCount = doc.internal.getNumberOfPages();
                doc.setFont("helvetica", "normal");
                doc.setFontSize(8);
                doc.setTextColor(148, 163, 184);
                
                doc.text("Page " + doc.internal.getCurrentPageInfo().pageNumber + " of " + pageCount, 283, doc.internal.pageSize.height - 10, { align: 'right' });
                doc.text("NITRA Supply Chain Management - CONFIDENTIAL", 14, doc.internal.pageSize.height - 10);
            }
        });
        
        doc.save("PO_Report_by_Indent_" + new Date().toISOString().slice(0, 10) + ".pdf");
    }

    function exportToCSV() {
        const table = document.getElementById("po-report-table");
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
        link.setAttribute("download", "po_report_indent_" + new Date().toISOString().slice(0, 10) + ".csv");
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
</script>
@endsection
