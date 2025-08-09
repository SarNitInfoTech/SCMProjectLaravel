@props([
    'title' => 'Data Table',
    'columns' => [],
    'rows' => [],
    'searchPlaceholder' => 'Search...',
    'customButton' => null,
    'pagination' => null
])

<div class="card shadow-sm border mb-6 bg-white">
    {{-- Title and Header Controls --}}
    <div class="card-header flex justify-between items-center p-4 border-b">
        <h3 class="text-xl font-semibold text-gray-800">{{ $title }}</h3>
        <div class="flex items-center gap-2">
            <input
                type="text"
                placeholder="{{ $searchPlaceholder }}"
                class="form-input rounded-md border px-3 py-1.5 text-sm w-64"
                onkeyup="filterTable(this)"
            >
            {!! $customButton !!}
        </div>
    </div>

    {{-- Table Section --}}
    <div class="table-responsive p-4">
        <table class="table whitespace-nowrap min-w-full">
            <thead>
                <tr class="border-b border-defaultborder">
                    <th scope="col">
                        <input class="form-check-input" type="checkbox" aria-label="Select All">
                    </th>
                    @foreach ($columns as $col)
                        <th scope="col" class="text-start">{{ $col['label'] }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @forelse ($rows as $row)
                    <tr class="border-b border-defaultborder">
                        <th scope="row">
                            <input class="form-check-input" type="checkbox" aria-label="Select Row">
                        </th>
                        @foreach ($columns as $col)
                            <td>
                                @php
                                    $value = data_get($row, $col['key'], '-');
                                    $type = $col['type'] ?? null;
                                @endphp

                                @switch($type)
                                    @case('avatar')
                                        <div class="flex items-center">
                                            <span class="avatar avatar-xs me-2 online avatar-rounded">
                                                <img src="{{ $value }}" alt="img">
                                            </span>
                                            {{ data_get($row, $col['label_key'] ?? '') }}
                                        </div>
                                        @break

                                    @case('badge')
                                        <span class="badge bg-primary/10 text-primary">{{ $value }}</span>
                                        @break
                                        
                                    @case('progress')
                                        <div class="progress progress-xs">
                                            <div class="progress-bar bg-primary w-[{{ $value }}%]" aria-valuenow="{{ $value }}" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                        @break

                                    @case('team')
                                        <div class="flex items-center -space-x-2">
                                            @foreach ($value as $teamImage)
                                                <span class="avatar avatar-sm avatar-rounded">
                                                    <img src="{{ $teamImage }}" alt="team">
                                                </span>
                                            @endforeach
                                        </div>
                                        @break
                                        @case('status')
    <span class="inline-block px-2 py-1 rounded-full text-xs font-semibold
        @if($value === 'Pending') bg-yellow-100 text-yellow-800
        @elseif($value === 'Cancel') bg-red-100 text-red-800
        @elseif($value === 'Close') bg-green-100 text-green-800
        @else bg-gray-200 text-gray-800
        @endif">
        {{ $value }}
    </span>
    @break

                                  @case('action')
    @php
        $editUrl = is_array($value) ? ($value['edit'] ?? null) : $value;
        $filePoUrl = is_array($value) ? ($value['file_po'] ?? null) : null;
        $viewPageUrl = is_array($value) ? ($value['viewPage'] ?? null) : null;
        $closeData = is_array($value) ? ($value['close'] ?? null) : null;
        $cancelData = is_array($value) ? ($value['cancel'] ?? null) : null;
    @endphp

    <div class="flex flex-wrap gap-2 text-center">
        {{-- Edit --}}
        @if ($editUrl)
            <a href="{{ $editUrl }}"
               class="inline-flex items-center gap-1 px-3 py-1 text-sm font-medium text-white bg-green-600 hover:bg-green-700 rounded-md shadow-sm transition-all duration-150">
                <i class="bi bi-pencil-square text-sm"></i> Edit
            </a>
        @endif

        {{-- File PO --}}
        @if ($filePoUrl)
            <a href="{{ $filePoUrl }}"
               class="inline-flex items-center gap-1 px-3 py-1 text-sm font-medium text-white bg-orange-500 hover:bg-orange-600 rounded-md shadow-sm transition-all duration-150">
                <i class="bi bi-file-earmark-plus text-sm"></i> File PO
            </a>
        @endif

        {{-- View --}}
        @if ($viewPageUrl)
            <a href="{{ $viewPageUrl }}"
               class="inline-flex items-center gap-1 px-3 py-1 text-sm font-medium text-white bg-blue-500 hover:bg-blue-600 rounded-md shadow-sm transition-all duration-150">
                <i class="bi bi-file-text text-sm"></i> View
            </a>
        @endif

        {{-- Close (form button) --}}
        @if (is_array($closeData))
            <form action="{{ $closeData['route'] }}" method="POST" style="display:inline;">
                @csrf
                <input type="hidden" name="indent_id" value="{{ $closeData['params']['indent_id'] }}">
                <input type="hidden" name="department_id" value="{{ $closeData['params']['department_id'] }}">
                <input type="hidden" name="status" value="Close">
                <button type="submit"
                        class="inline-flex items-center gap-1 px-3 py-1 text-sm font-medium text-white bg-gray-700 hover:bg-gray-800 rounded-md shadow-sm transition-all duration-150">
                    <i class="bi bi-x-octagon text-sm"></i> Close
                </button>
            </form>
        @endif

        {{-- Cancel (form button) --}}
        @if (is_array($cancelData))
            <form action="{{ $cancelData['route'] }}" method="POST" style="display:inline;">
                @csrf
                <input type="hidden" name="indent_id" value="{{ $cancelData['params']['indent_id'] }}">
                <input type="hidden" name="department_id" value="{{ $cancelData['params']['department_id'] }}">
                <input type="hidden" name="status" value="Cancel">
                <button type="submit"
                        class="inline-flex items-center gap-1 px-3 py-1 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-md shadow-sm transition-all duration-150">
                    <i class="bi bi-x-circle text-sm"></i> Cancel
                </button>
            </form>
        @endif
    </div>
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

    {{-- Pagination --}}
    @if(isset($pagination) && $pagination instanceof \Illuminate\Contracts\Pagination\Paginator)
        <div class="p-4 border-t">
            {{ $pagination->links('pagination::tailwind') }}
        </div>
    @endif
</div>

{{-- Optional client-side search script --}}
<script>
    function filterTable(input) {
        const filter = input.value.toLowerCase();
        const table = input.closest('.card').querySelector('table');
        const rows = table.querySelectorAll('tbody tr');

        rows.forEach(row => {
            const text = row.innerText.toLowerCase();
            row.style.display = text.includes(filter) ? '' : 'none';
        });
    }
</script>

<script>
function submitStatusChange(data) {
    fetch(data.url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        },
        body: JSON.stringify({
            indent_id: data.indent_id,
            department_id: data.department_id,
            status: data.status
        })
    })
    .then(res => res.ok ? location.reload() : alert("Status update failed."))
    .catch(err => console.error("Error:", err));
}
</script>
