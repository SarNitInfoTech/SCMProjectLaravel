@props([
    'title' => 'Data Table',
    'columns' => [],                 // e.g. [['label'=>'Indent ID','key'=>'indent_id'], ['label'=>'Indent Date','key'=>'indent_date','type'=>'date'], ...]
    'rows' => [],                    // initial rows (same keys as columns)
    'searchPlaceholder' => 'Search...',
    'customButton' => null,
    'pagination' => null,            // optional Paginator for initial load
    'viewBtnTitle' => 'View',        // if you use 'action' type
    'filterUrl' => null,             // REQUIRED for AJAX filtering, e.g. route('reports.indents.filter')
    'rowKey' => null,                // optional unique key per row (defaults to first column key, or 'id')
])

@php
    // Unique id to scope everything (supports multiple instances on one page)
    $compId = 'ctf_' . uniqid();

    // Column keys & map for quick access
    $columnKeys = collect($columns)->pluck('key')->values();
    $rowKey = $rowKey ?: ($columnKeys[0] ?? 'id');

    // Which column indexes are "action" (to skip in CSV) — optional if you actually add such a type
    $hasActionCol = collect($columns)->firstWhere('type', 'action') !== null;
@endphp

<div id="{{ $compId }}"
     class="card shadow-sm border mb-6 bg-white"
     data-filter-url="{{ $filterUrl }}"
     data-columns='@json($columnKeys)'
     data-row-key="{{ $rowKey }}"
>
    {{-- Title + Controls --}}
    <div class="card-header flex flex-wrap gap-2 justify-between items-center p-4 border-b">
        <h3 class="text-xl font-semibold text-gray-800">{{ $title }}</h3>

        <div class="flex flex-wrap items-center gap-2">
            {{-- Search --}}
            <input
                type="text"
                placeholder="{{ $searchPlaceholder }}"
                class="form-input rounded-md border px-3 py-1.5 text-sm w-64 js-search"
            >

            {{-- Date range (single input with icon) --}}
            <div class="form-group mb-0">
                <div class="input-group">
                    <div class="input-group-text text-[#8c9097]">
                        <i class="ri-calendar-line"></i>
                    </div>
                    <input
                        type="text"
                        id="{{ $compId }}_dateRange"
                        class="form-control !border-s-0 flatpickr-input js-date-range"
                        placeholder="Choose date range"
                        readonly
                    >
                </div>
            </div>

            {{-- Download CSV --}}
            <button type="button"
                    class="ti-btn ti-btn-primary-full !rounded-md js-download">
                Download CSV
            </button>

            {!! $customButton !!}
        </div>
    </div>

    {{-- Table --}}
    <div class="table-responsive p-4">
        <table class="table whitespace-nowrap min-w-full">
            <thead>
            <tr class="border-b border-defaultborder">
                <th scope="col">
                    <input id="{{ $compId }}_selectAll"
                           class="form-check-input js-select-all"
                           type="checkbox"
                           aria-label="Select All">
                </th>
                @foreach ($columns as $col)
                    <th scope="col"
                        class="text-start"
                        @if(isset($col['type'])) data-col-type="{{ $col['type'] }}" @endif>
                        {{ $col['label'] }}
                    </th>
                @endforeach
            </tr>
            </thead>
            <tbody>
            @forelse ($rows as $row)
                @php
                    $rowId = (string) data_get($row, $rowKey, '');
                @endphp
                <tr class="border-b border-defaultborder" data-row-id="{{ $rowId }}">
                    <th scope="row">
                        <input class="form-check-input js-row-check" type="checkbox" aria-label="Select Row">
                    </th>
                    @foreach ($columns as $col)
                        @php
                            $value = data_get($row, $col['key'], '-');
                            $type = $col['type'] ?? null;
                        @endphp
                        <td>
                            @switch($type)
                                @case('status')
                                    <span class="inline-block px-2 py-1 rounded-full text-xs font-semibold
                                        @if($value === 'Pending') bg-yellow-100 text-yellow-800
                                        @elseif($value === 'Cancel') bg-red-100 text-red-800
                                        @elseif($value === 'Close') bg-green-100 text-green-800
                                        @else bg-gray-200 text-gray-800 @endif">
                                        {{ $value }}
                                    </span>
                                    @break

                                @case('action')
                                    {{-- Optional: render any action buttons if you pass them in row["action"] --}}
                                    @php $actionVal = data_get($row, 'action'); @endphp
                                    @if (is_array($actionVal) && !empty($actionVal['viewPage']))
                                        <a href="{{ $actionVal['viewPage'] }}"
                                           class="inline-flex items-center gap-1 px-3 py-1 text-sm font-medium text-white bg-blue-500 hover:bg-blue-600 rounded-md shadow-sm">
                                            <i class="bi bi-file-text text-sm"></i> {{ $viewBtnTitle }}
                                        </a>
                                    @endif
                                    @break

                                @default
                                    {{ $value }}
                            @endswitch
                        </td>
                    @endforeach
                </tr>
            @empty
                <tr>
                    <td colspan="{{ count($columns) + 1 }}" class="text-center py-3">No records found.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination (for initial load, optional) --}}
    @if(isset($pagination) && $pagination instanceof \Illuminate\Contracts\Pagination\Paginator)
        <div class="p-4 border-t">
            {{ $pagination->links('pagination::tailwind') }}
        </div>
    @endif
</div>

{{-- Flatpickr assets (ok if included multiple times) --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<script>
(() => {
  // --- Scoped by component id ---
  const comp = document.getElementById(@json($compId));
  if (!comp) return;

  const filterUrl = comp.dataset.filterUrl || null;
  const rowKey    = comp.dataset.rowKey || 'id';
  const colKeys   = JSON.parse(comp.dataset.columns || '[]');

  const searchEl  = comp.querySelector('.js-search');
  const rangeEl   = document.getElementById(@json($compId) + '_dateRange');
  const table     = comp.querySelector('table');
  const tbody     = table?.querySelector('tbody');
  const selectAll = comp.querySelector('.js-select-all');
  const btnCsv    = comp.querySelector('.js-download');

  // Maintain selected ids across repaints
  const selected = new Set();

  // Init flatpickr range
  if (rangeEl) {
    flatpickr(rangeEl, {
      mode: 'range',
      dateFormat: 'Y-m-d',
      altInput: true,
      altFormat: 'd-m-Y',
      allowInput: false,
      onChange: () => applyFilters(),
      onClose:  () => applyFilters()
    });
  }

  // Debounce helper
  const debounce = (fn, wait=250) => { let t; return (...a) => { clearTimeout(t); t = setTimeout(() => fn(...a), wait); }; };

  // Read range as YYYY-MM-DD
  function getRange() {
    const fp = rangeEl && rangeEl._flatpickr ? rangeEl._flatpickr : null;
    const sel = fp?.selectedDates || [];
    const fmt = d => d ? new Date(d).toISOString().slice(0,10) : null;
    return { from: fmt(sel[0]), to: fmt(sel[1]) };
  }

  // Escape CSV
  const csvCell = (txt) => `"${String(txt ?? '').replace(/"/g,'""')}"`;

  // Render status badge
  function renderStatusBadge(value) {
    const v = String(value || '');
    let cls = 'bg-gray-200 text-gray-800';
    if (v === 'Pending') cls = 'bg-yellow-100 text-yellow-800';
    else if (v === 'Cancel') cls = 'bg-red-100 text-red-800';
    else if (v === 'Close') cls = 'bg-green-100 text-green-800';
    return `<span class="inline-block px-2 py-1 rounded-full text-xs font-semibold ${cls}">${v}</span>`;
  }

  // Escape HTML
  function esc(s) {
    return String(s ?? '')
      .replaceAll('&','&amp;')
      .replaceAll('<','&lt;')
      .replaceAll('>','&gt;')
      .replaceAll('"','&quot;')
      .replaceAll("'",'&#039;');
  }

  // Build a row HTML
  function buildRowHtml(rowObj) {
    const id = String(rowObj[rowKey] ?? '');
    let tr = `<tr class="border-b border-defaultborder" data-row-id="${esc(id)}">`;
    const checked = selected.has(id) ? ' checked' : '';
    tr += `<th scope="row"><input class="form-check-input js-row-check" type="checkbox"${checked} aria-label="Select Row"></th>`;
    colKeys.forEach((key, idx) => {
      const col = @json($columns)[idx] || {};
      const type = col.type || null;
      const value = rowObj[key];

      if (type === 'status') {
        tr += `<td>${renderStatusBadge(value)}</td>`;
      } else if (type === 'action') {
        // If you return "action" data from server, render here. Otherwise empty.
        tr += `<td></td>`;
      } else {
        tr += `<td>${esc(value)}</td>`;
      }
    });
    tr += `</tr>`;
    return tr;
  }

  // Repaint tbody with new rows; keep selections if keys match
  function repaint(rows) {
    if (!tbody) return;

    if (!rows || !rows.length) {
      tbody.innerHTML = `<tr><td colspan="${colKeys.length + 1}" class="text-center py-3">No records found.</td></tr>`;
      // also uncheck master checkbox
      if (selectAll) selectAll.checked = false;
      return;
    }

    let html = '';
    rows.forEach(row => html += buildRowHtml(row));
    tbody.innerHTML = html;

    // Wire row checkboxes
    tbody.querySelectorAll('.js-row-check').forEach(cb => {
      cb.addEventListener('change', () => {
        const rid = cb.closest('tr')?.dataset.rowId || '';
        if (!rid) return;
        if (cb.checked) selected.add(rid);
        else selected.delete(rid);
        // Keep master checkbox in sync if all visible are selected
        syncMasterCheckbox();
      });
    });

    // Sync master checkbox (select all if all visible checked)
    syncMasterCheckbox();
  }

  function syncMasterCheckbox() {
    if (!selectAll) return;
    const rowCbs = Array.from(tbody.querySelectorAll('.js-row-check'));
    if (!rowCbs.length) { selectAll.checked = false; return; }
    selectAll.checked = rowCbs.every(cb => cb.checked);
  }

  // Select-all for visible rows
  if (selectAll) {
    selectAll.addEventListener('change', () => {
      const rowCbs = tbody.querySelectorAll('.js-row-check');
      rowCbs.forEach(cb => {
        cb.checked = selectAll.checked;
        const rid = cb.closest('tr')?.dataset.rowId || '';
        if (!rid) return;
        if (selectAll.checked) selected.add(rid);
        else selected.delete(rid);
      });
    });
  }

  // AJAX filter
  const applyFilters = debounce(() => {
    if (!filterUrl) return;

    const q = searchEl?.value?.trim() || '';
    const { from, to } = getRange();

    const params = new URLSearchParams();
    if (q)   params.set('q', q);
    if (from) params.set('from', from);
    if (to)   params.set('to', to);

    fetch(`${filterUrl}?${params.toString()}`, {
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
      .then(res => res.ok ? res.json() : Promise.reject(res))
      .then(data => {
        const rows = data?.rows || [];
        repaint(rows);
      })
      .catch(err => {
        console.error('Filter AJAX error:', err);
      });
  }, 300);

  // Wire search input
  if (searchEl) searchEl.addEventListener('input', applyFilters);

  // Download CSV: if any selected → selected only; else → all visible
  if (btnCsv) {
    btnCsv.addEventListener('click', () => {
      // which rows to export?
      const rowEls = Array.from(tbody.querySelectorAll('tr')).filter(tr => tr.querySelector('td,th')); // skip empty
      const selectedRows = rowEls.filter(tr => tr.querySelector('.js-row-check')?.checked);
      const source = selectedRows.length ? selectedRows : rowEls;

      // Build column headers (skip the checkbox column, and skip "Action" columns by label)
      const ths = Array.from(table.querySelectorAll('thead th'));
      const includeIdx = ths
        .map((th, i) => ({ i, label: (th.textContent || '').trim().toLowerCase() }))
        .filter(({ i, label }) => i !== 0 && label !== 'action');

      const header = includeIdx.map(({ i }) => (ths[i].textContent || '').trim());

      // Build body from DOM text (innerText gives clean text from status badges)
      const body = source.map(tr =>
        includeIdx.map(({ i }) =>
          (tr.children[i]?.innerText || '').replace(/\s+/g,' ').trim()
        )
      );

      const csv = [header, ...body].map(row => row.map(csvCell).join(',')).join('\r\n');

      const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
      const url  = URL.createObjectURL(blob);
      const a    = document.createElement('a');
      const stamp = new Date().toISOString().slice(0,10);
      a.href = url;
      a.download = `table-${stamp}.csv`;
      document.body.appendChild(a);
      a.click();
      document.body.removeChild(a);
      URL.revokeObjectURL(url);
    });
  }
})();
</script>
