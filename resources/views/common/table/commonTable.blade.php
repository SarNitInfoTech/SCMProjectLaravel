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
        $viewTitle =   $viewBtnTitle ?? 'View';

        $closeData = is_array($value) ? ($value['close'] ?? null) : null;
        $cancelData = is_array($value) ? ($value['cancel'] ?? null) : null;
        $pendingData = is_array($value) ? ($value['pending'] ?? null) : null;
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
                <i class="bi bi-file-text text-sm"></i> {{$viewTitle}}
            </a>
        @endif
         {{-- Pending (Re-Open) --}}
@if (is_array($pendingData))
<form action="{{ $pendingData['route'] }}" method="POST" class="js-status-form" style="display:inline;">
  @csrf
  <input type="hidden" name="indent_id" value="{{ $pendingData['params']['indent_id'] }}">
  <input type="hidden" name="department_id" value="{{ $pendingData['params']['department_id'] }}">
  <input type="hidden" name="status" value="Pending">
  <button type="button"
          data-action="Pending"
          onclick="openStatusModal(this)"
          class="inline-flex items-center gap-1 px-3 py-1 text-sm font-medium text-white bg-green-600 hover:bg-green-700 rounded-md shadow-sm transition-all duration-150">
    <i class="bi bi-arrow-counterclockwise text-sm"></i> Re-Open
  </button>
</form>
@endif

{{-- Close --}}
@if (is_array($closeData))
<form action="{{ $closeData['route'] }}" method="POST" class="js-status-form" style="display:inline;">
  @csrf
  <input type="hidden" name="indent_id" value="{{ $closeData['params']['indent_id'] }}">
  <input type="hidden" name="department_id" value="{{ $closeData['params']['department_id'] }}">
  <input type="hidden" name="status" value="Close">
  <button type="button"
          data-action="Close"
          onclick="openStatusModal(this)"
          class="inline-flex items-center gap-1 px-3 py-1 text-sm font-medium text-white bg-gray-700 hover:bg-gray-800 rounded-md shadow-sm transition-all duration-150">
    <i class="bi bi-x-octagon text-sm"></i> Close
  </button>
</form>
@endif

{{-- Cancel --}}
@if (is_array($cancelData))
<form action="{{ $cancelData['route'] }}" method="POST" class="js-status-form" style="display:inline;">
  @csrf
  <input type="hidden" name="indent_id" value="{{ $cancelData['params']['indent_id'] }}">
  <input type="hidden" name="department_id" value="{{ $cancelData['params']['department_id'] }}">
  <input type="hidden" name="status" value="Cancel">
  <button type="button"
          data-action="Cancel"
          onclick="openStatusModal(this)"
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





<!-- Status Confirm Modal -->
<div id="statusConfirmModal" class="fixed inset-0 z-50 hidden">
  <!-- Backdrop -->
  <div class="absolute inset-0 bg-black/50" onclick="closeStatusModal()"></div>

  <!-- Panel -->
  <div class="relative mx-auto mt-24 w-[90%] max-w-md rounded-2xl bg-white shadow-xl">
    <div class="px-5 py-4 border-b">
      <h4 id="statusModalTitle" class="text-lg font-semibold text-gray-800">Confirm Action</h4>
    </div>
    <div class="px-5 py-4">
      <p id="statusModalText" class="text-sm text-gray-700">
        Are you sure you want to proceed?
      </p>
    </div>
    <div class="px-5 py-4 border-t flex items-center justify-end gap-2">
      <button type="button"
              onclick="closeStatusModal()"
              class="px-4 py-2 text-sm font-medium rounded-md border border-gray-300 text-gray-700 hover:bg-gray-50">
        No
      </button>
      <button type="button"
              id="statusConfirmBtn"
              class="px-4 py-2 text-sm font-medium rounded-md text-white bg-gray-700 hover:bg-gray-800">
        Yes
      </button>
    </div>
  </div>
</div>
<script>
  let __activeStatusForm = null;
  let __activeAction = null;

  function openStatusModal(btn) {
    __activeStatusForm = btn.closest('form');
    __activeAction = (btn.dataset.action || '').toLowerCase();

    const modal = document.getElementById('statusConfirmModal');
    const title = document.getElementById('statusModalTitle');
    const text  = document.getElementById('statusModalText');
    const confirmBtn = document.getElementById('statusConfirmBtn');

    // Configure texts/styles per action
    const map = {
      close:   { title: 'Confirm Close',   text: 'This will mark the Indent & PO as Closed for this department. Continue?',  cls: 'bg-gray-700 hover:bg-gray-800' },
      cancel:  { title: 'Confirm Cancel',  text: 'This will mark the Indent & PO as Cancelled for this department. Continue?', cls: 'bg-red-600 hover:bg-red-700' },
      pending: { title: 'Re-Open (Pending)', text: 'This will re-open the Indent & PO (set status to Pending). Continue?',     cls: 'bg-green-600 hover:bg-green-700' },
    };
    const cfg = map[__activeAction] || map.close;

    title.textContent = cfg.title;
    text.textContent  = cfg.text;

    // Reset confirm button classes then apply
    confirmBtn.className = 'px-4 py-2 text-sm font-medium rounded-md text-white';
    confirmBtn.classList.add(...cfg.cls.split(' '));

    // Show modal
    modal.classList.remove('hidden');
  }

  function closeStatusModal() {
    const modal = document.getElementById('statusConfirmModal');
    modal.classList.add('hidden');
    __activeStatusForm = null;
    __activeAction = null;
  }

  document.getElementById('statusConfirmBtn').addEventListener('click', function() {
    if (__activeStatusForm) {
      __activeStatusForm.submit(); // submit the original POST form
    }
    closeStatusModal();
  });

  // Optional: ESC to close
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') closeStatusModal();
  });
</script>
<style>.flex.flex-wrap.gap-2.text-center {
    justify-content: center !important;
}</style>
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


