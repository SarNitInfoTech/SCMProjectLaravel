@extends("layouts.layout")

@section("bodyContent")
<div class="md:flex block items-center justify-between my-6 page-header-breadcrumb">
    <div>
        <h4 class="mb-0 text-defaulttextcolor font-medium">{{ $title }}</h4>
        <p class="-mt-[0.2rem] mb-0 text-textmuted">Report of all indents registered in the system</p>
    </div>
</div>

<div class="card shadow-sm border mb-6 bg-white">
    <div class="card-header p-4 border-b flex flex-wrap justify-between items-center gap-4">
        <div class="flex flex-wrap items-center gap-2">
            <input
                type="text"
                id="indentSearchInput"
                placeholder="Search ID, department, project..."
                class="form-input rounded border px-3 py-1.5 text-sm w-64 bg-gray-50"
            >
            
            <div class="flex items-center gap-1 bg-gray-50 border rounded px-2">
                <i class="ri-calendar-line text-gray-400"></i>
                <input
                    type="text"
                    id="indentDateRange"
                    placeholder="Choose date range"
                    class="form-input bg-transparent border-0 px-2 py-1.5 text-sm w-56 focus:outline-none"
                    readonly
                >
            </div>

            <button type="button" onclick="resetIndentFilters()" class="px-3 py-1.5 text-sm bg-gray-100 hover:bg-gray-200 text-gray-700 rounded">
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

    <div class="table-responsive p-4">
        <table class="table whitespace-nowrap min-w-full" id="indent-report-table">
            <thead>
                <tr class="border-b border-defaultborder">
                    <th scope="col" class="text-start">Indent ID</th>
                    <th scope="col" class="text-start">Department</th>
                    <th scope="col" class="text-start">Project</th>
                    <th scope="col" class="text-start">Items</th>
                    <th scope="col" class="text-start">Status</th>
                    <th scope="col" class="text-start">Indent Date</th>
                </tr>
            </thead>
            <tbody id="indentTableBody">
                @forelse ($rows as $row)
                    @php
                        $status = $row['status'];
                        $badgeClass = match (strtolower($status)) {
                            'pending' => 'bg-yellow-100 text-yellow-800',
                            'cancel', 'cancelled' => 'bg-red-100 text-red-800',
                            'close', 'closed' => 'bg-green-100 text-green-800',
                            default => 'bg-gray-100 text-gray-800'
                        };
                    @endphp
                    <tr class="border-b border-defaultborder hover:bg-gray-50 transition-colors">
                        <td class="font-medium text-gray-900">{{ $row['indent_id'] }}</td>
                        <td>{{ $row['indent_department'] }}</td>
                        <td>{{ $row['indent_project'] }}</td>
                        <td class="max-w-xs truncate" title="{{ $row['items_text'] }}">{{ $row['items_text'] }}</td>
                        <td>
                            <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $badgeClass }}">
                                {{ $status }}
                            </span>
                        </td>
                        <td>{{ $row['indent_date'] }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-gray-500">No records found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div id="paginationContainer" class="p-4 border-t">
        @if($pagination && $pagination->hasPages())
            {{ $pagination->links('pagination::tailwind') }}
        @endif
    </div>
</div>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    let filterUrl = "{{ $filterUrl }}";
    let datePickerInstance = null;

    document.addEventListener("DOMContentLoaded", function () {
        datePickerInstance = flatpickr("#indentDateRange", {
            mode: "range",
            dateFormat: "Y-m-d",
            onChange: function() {
                fetchFilteredData();
            }
        });

        document.getElementById('indentSearchInput').addEventListener('input', debounce(function() {
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

    function resetIndentFilters() {
        document.getElementById('indentSearchInput').value = '';
        if (datePickerInstance) {
            datePickerInstance.clear();
        }
        window.location.reload();
    }

    async function fetchFilteredData() {
        const search = document.getElementById('indentSearchInput').value.trim();
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
            console.error("Failed to filter indents:", error);
        }
    }

    function repaintTable(rows) {
        const tbody = document.getElementById('indentTableBody');
        const paginationContainer = document.getElementById('paginationContainer');
        
        // Hide pagination when filtered results are shown
        paginationContainer.style.display = 'none';

        if (!rows || rows.length === 0) {
            tbody.innerHTML = `<tr><td colspan="6" class="text-center py-4 text-gray-500">No records found.</td></tr>`;
            return;
        }

        tbody.innerHTML = rows.map(row => {
            const status = row.status || 'Pending';
            let badgeClass = 'bg-gray-100 text-gray-800';
            if (status.toLowerCase() === 'pending') badgeClass = 'bg-yellow-100 text-yellow-800';
            else if (status.toLowerCase() === 'cancel') badgeClass = 'bg-red-100 text-red-800';
            else if (status.toLowerCase() === 'close') badgeClass = 'bg-green-100 text-green-800';

            return `
                <tr class="border-b border-defaultborder hover:bg-gray-50 transition-colors">
                    <td class="font-medium text-gray-900">${escapeHtml(row.indent_id)}</td>
                    <td>${escapeHtml(row.indent_department)}</td>
                    <td>${escapeHtml(row.indent_project)}</td>
                    <td class="max-w-xs truncate" title="${escapeHtml(row.items_text)}">${escapeHtml(row.items_text)}</td>
                    <td>
                        <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-semibold ${badgeClass}">
                            ${escapeHtml(status)}
                        </span>
                    </td>
                    <td>${escapeHtml(row.indent_date)}</td>
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
        const table = document.getElementById("indent-report-table");
        
        // Clone table to safely manipulate for export (remove any pagination or action headers if needed)
        const clone = table.cloneNode(true);
        
        const ws = XLSX.utils.table_to_sheet(clone);
        
        // Auto-fit column widths
        const range = XLSX.utils.decode_range(ws['!ref']);
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
        XLSX.utils.book_append_sheet(wb, ws, "Indents Report");
        XLSX.writeFile(wb, "Indent_Report_" + new Date().toISOString().slice(0, 10) + ".xlsx");
    }

    function exportToPDF() {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF('p', 'mm', 'a4'); // Portrait A4
        const pw = doc.internal.pageSize.getWidth();
        const ph = doc.internal.pageSize.getHeight();

        // ── Header Banner ──────────────────────────────────────────────
        doc.setFillColor(37, 99, 235);
        doc.rect(0, 0, pw, 28, 'F');
        doc.setFillColor(99, 102, 241);
        doc.rect(0, 24, pw, 4, 'F');

        doc.setFont("helvetica", "bold");
        doc.setFontSize(16);
        doc.setTextColor(255, 255, 255);
        doc.text("Nitra Purchase Management System", 14, 13);

        doc.setFont("helvetica", "normal");
        doc.setFontSize(8);
        doc.setTextColor(191, 219, 254);
        doc.text("All Indents Report  |  Confidential", 14, 20);
        doc.text("Generated: " + new Date().toLocaleString(), pw - 14, 13, { align: 'right' });
        doc.text("inventory.nitratextile.org", pw - 14, 20, { align: 'right' });

        // ── Metadata row ───────────────────────────────────────────────
        doc.setFillColor(241, 245, 249);
        doc.rect(0, 28, pw, 10, 'F');
        doc.setFont("helvetica", "bold");
        doc.setFontSize(8);
        doc.setTextColor(71, 85, 105);
        doc.text("REPORT:", 14, 35);
        doc.setFont("helvetica", "normal");
        doc.text("All Indents Registered — Complete List", 32, 35);

        // ── Table ──────────────────────────────────────────────────────
        const headers = ["Indent ID", "Department", "Project", "Items Description", "Status", "Indent Date"];
        const rows = [];
        document.querySelectorAll("#indentTableBody tr").forEach(tr => {
            const cells = tr.querySelectorAll("td");
            if (cells.length >= 6) {
                rows.push([
                    cells[0].innerText.trim(), cells[1].innerText.trim(),
                    cells[2].innerText.trim(), cells[3].innerText.trim(),
                    cells[4].innerText.trim(), cells[5].innerText.trim()
                ]);
            }
        });

        doc.autoTable({
            head: [headers],
            body: rows,
            startY: 40,
            theme: 'grid',
            headStyles: {
                fillColor: [37, 99, 235],
                textColor: [255, 255, 255],
                fontStyle: 'bold',
                fontSize: 9,
                cellPadding: 3,
                halign: 'center'
            },
            bodyStyles: {
                fontSize: 8.5,
                textColor: [30, 41, 59],
                cellPadding: 2.5
            },
            columnStyles: {
                0: { halign: 'center', cellWidth: 22 },
                3: { cellWidth: 58 },
                4: { halign: 'center', cellWidth: 22 },
                5: { halign: 'center', cellWidth: 24 }
            },
            alternateRowStyles: { fillColor: [239, 246, 255] },
            tableLineColor: [203, 213, 225],
            tableLineWidth: 0.2,
            didDrawPage: function (data) {
                const y = ph - 14;
                doc.setDrawColor(203, 213, 225);
                doc.setLineWidth(0.3);
                doc.line(14, y, pw - 14, y);
                doc.setFont("helvetica", "normal");
                doc.setFontSize(7);
                doc.setTextColor(148, 163, 184);
                doc.text("Nitra Purchase Management System  •  inventory.nitratextile.org  •  CONFIDENTIAL", 14, ph - 9);
                doc.text(
                    "Page " + doc.internal.getCurrentPageInfo().pageNumber + " of " + doc.internal.getNumberOfPages(),
                    pw - 14, ph - 9, { align: 'right' }
                );
            }
        });

        doc.save("Indent_Report_" + new Date().toISOString().slice(0, 10) + ".pdf");
    }

    function exportToCSV() {
        const table = document.getElementById("indent-report-table");
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
        link.setAttribute("download", "indent_report_" + new Date().toISOString().slice(0, 10) + ".csv");
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
</script>
@endsection
