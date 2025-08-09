@props([
    'title' => 'Report Table',
    'columns' => [],
    'rows' => [],
    'searchPlaceholder' => 'Search...',
    'customButton' => null,
    'pagination' => null,
    'enableDateFilter' => false,
    'dateFieldKey' => 'created_at'
])

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@easepick/bundle@1.2.1/dist/index.css" />
@endpush

<div class="card shadow-sm border mb-6 bg-white">
    {{-- Header --}}
    <div class="card-header flex flex-wrap justify-between items-center p-4 gap-3 border-b">
        <h3 class="text-lg font-semibold text-gray-800">{{ $title }}</h3>
        <div class="flex items-center gap-2 flex-wrap">
            {{-- Search --}}
            <input
                type="text"
                placeholder="{{ $searchPlaceholder }}"
                class="form-input rounded-md border px-3 py-1.5 text-sm w-64"
                onkeyup="filterTable(this)"
            >

            {{-- Date Range Picker --}}
            @if($enableDateFilter)
            <input
                type="text"
                id="dateRangeFilter"
                class="form-input border rounded-md px-3 py-1.5 text-sm w-64"
                placeholder="Select date range"
                readonly
            >
            @endif

            {{-- Export Button --}}
            <a href="{{ route('report.export.excel', request()->query()) }}"
               class="inline-flex items-center px-3 py-1.5 text-sm font-semibold text-white bg-green-600 hover:bg-green-700 rounded-md shadow-sm transition-all duration-150">
                <i class="bi bi-download mr-1"></i> Export Excel
            </a>

            {{-- Custom Button --}}
            {!! $customButton !!}
        </div>
    </div>

    {{-- Table --}}
    <div class="table-responsive p-4">
        <table class="table min-w-full whitespace-nowrap" id="reportTable">
            <thead>
                <tr class="border-b">
                    @foreach ($columns as $col)
                        <th class="text-left font-medium text-gray-700">{{ $col['label'] }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @forelse ($rows as $row)
                    <tr class="border-b hover:bg-gray-50" 
                        @if($enableDateFilter) 
                            data-date="{{ \Carbon\Carbon::parse(data_get($row, $dateFieldKey))->format('YYYY-MM-DD') }}"
                        @endif>
                        @foreach ($columns as $col)
                            <td class="py-2">
                                @php
                                    $value = data_get($row, $col['key'], '-');
                                    $type = $col['type'] ?? 'text';
                                @endphp

                                @switch($type)
                                    @case('date')
                                        {{ \Carbon\Carbon::parse($value)->format('d M Y') }}
                                        @break
                                    
                                    @case('number')
                                        {{ number_format($value) }}
                                        @break

                                    @case('badge')
                                        <span class="badge bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs">{{ $value }}</span>
                                        @break

                                    @case('custom')
                                        {!! $value !!}
                                        @break

                                    @default
                                        {{ $value }}
                                @endswitch
                            </td>
                        @endforeach
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ count($columns) }}" class="text-center py-4 text-gray-500">No data available.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if(isset($pagination) && $pagination instanceof \Illuminate\Contracts\Pagination\Paginator)
        <div class="p-4 border-t">
            {{ $pagination->links('pagination::tailwind') }}
        </div>
    @endif
</div>

<script src="https://cdn.jsdelivr.net/npm/@easepick/bundle@1.2.1/dist/index.umd.js"></script>

<script>
    function filterTable(input) {
        const filter = input.value.toLowerCase();
        const table = document.querySelector('#reportTable');
        const rows = table.querySelectorAll('tbody tr');

        rows.forEach(row => {
            const text = row.innerText.toLowerCase();
            row.style.display = text.includes(filter) ? '' : 'none';
        });
    }

    @if($enableDateFilter)
    document.addEventListener("DOMContentLoaded", function () {
        const picker = new easepick.create({
            element: document.getElementById('dateRangeFilter'),
            css: [
                "https://cdn.jsdelivr.net/npm/@easepick/bundle@1.2.1/dist/index.css"
            ],
            plugins: ['RangePlugin'],
            format: 'DD/MM/YYYY',
            setup(picker) {
                picker.on('select', (e) => {
                    const start = picker.getStartDate()?.format('YYYY-MM-DD');
                    const end = picker.getEndDate()?.format('YYYY-MM-DD');
                    if (start && end) {
                        const url = new URL(window.location.href);
                        url.searchParams.set('start_date', start);
                        url.searchParams.set('end_date', end);
                        window.location.href = url;
                    }
                });
            }
        });
    });
    @endif
</script>

