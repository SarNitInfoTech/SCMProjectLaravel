@extends("layouts.layout")

@section("bodyContent")
<div class="md:flex block items-center justify-between my-6 page-header-breadcrumb">
    <div>
        <h4 class="mb-0 text-defaulttextcolor font-medium">{{ $title }}</h4>
        <p class="-mt-[0.2rem] mb-0 text-textmuted">Manage and update purchase orders</p>
    </div>
</div>

@if (session('success'))
    <div class="mb-4 p-3 bg-green-100 border-l-4 border-green-500 text-green-700 text-sm">
        {{ session('success') }}
    </div>
@endif

<div class="card shadow-sm border mb-6 bg-white">
    <div class="card-header p-4 border-b">
        <form method="GET" action="{{ route('indentroview.index') }}" class="flex flex-wrap items-center justify-between gap-4">
            <h3 class="text-lg font-semibold text-gray-800">PO Register List</h3>
            <div class="flex items-center gap-2">
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Search ID, department, party..."
                    class="form-input rounded border px-3 py-1.5 text-sm w-64 bg-gray-50"
                >
                <button type="submit" class="px-4 py-1.5 text-sm bg-gray-800 hover:bg-gray-900 text-white rounded">
                    Search
                </button>
                @if(request()->filled('search'))
                    <a href="{{ route('indentroview.index') }}" class="px-3 py-1.5 text-sm bg-gray-100 hover:bg-gray-200 text-gray-700 rounded">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <div class="table-responsive p-4">
        <table class="table whitespace-nowrap min-w-full">
            <thead>
                <tr class="border-b border-defaultborder">
                    <th scope="col" class="text-start">Indent ID</th>
                    <th scope="col" class="text-start">Department</th>
                    <th scope="col" class="text-start">Party Name</th>
                    <th scope="col" class="text-start">Amount</th>
                    <th scope="col" class="text-start">Status</th>
                    <th scope="col" class="text-start">PO Date</th>
                    <th scope="col" class="text-center">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($rows as $row)
                    @php
                        $status = $row['status'];
                        $badgeClass = match (strtolower($status)) {
                            'pending' => 'bg-yellow-100 text-yellow-800',
                            'cancel', 'cancelled' => 'bg-red-100 text-red-800',
                            'close', 'closed' => 'bg-green-100 text-green-800',
                            default => 'bg-gray-100 text-gray-800'
                        };
                        $actions = $row['action'];
                    @endphp
                    <tr class="border-b border-defaultborder hover:bg-gray-50 transition-colors">
                        <td class="font-medium text-gray-900">{{ $row['indent_id'] }}</td>
                        <td>{{ $row['department_name'] }}</td>
                        <td>{{ $row['party_name'] }}</td>
                        <td>₹{{ $row['po_amount'] }}</td>
                        <td>
                            <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $badgeClass }}">
                                {{ $status }}
                            </span>
                        </td>
                        <td>{{ $row['po_date'] }}</td>
                        <td class="text-center">
                            <div class="flex flex-wrap gap-2 justify-center">
                                @if (isset($actions['viewPage']))
                                    <a href="{{ $actions['viewPage'] }}"
                                       class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded transition-all">
                                        <i class="bi bi-eye"></i> {{ $viewBtnTitle }}
                                    </a>
                                @endif

                                @if (isset($actions['file_po']))
                                    <a href="{{ $actions['file_po'] }}"
                                       class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium text-white bg-orange-500 hover:bg-orange-600 rounded transition-all">
                                        <i class="bi bi-file-earmark-plus"></i> File PO
                                    </a>
                                @endif

                                @if (isset($actions['pending']))
                                    <form action="{{ $actions['pending']['route'] }}" method="POST" class="js-po-status-form inline">
                                        @csrf
                                        <input type="hidden" name="indent_id" value="{{ $actions['pending']['params']['indent_id'] }}">
                                        <input type="hidden" name="department_id" value="{{ $actions['pending']['params']['department_id'] }}">
                                        <input type="hidden" name="status" value="Pending">
                                        <button type="button" data-action="Pending" onclick="confirmPOStatus(this)"
                                                class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium text-white bg-green-600 hover:bg-green-700 rounded transition-all">
                                            <i class="bi bi-arrow-counterclockwise"></i> Re-Open
                                        </button>
                                    </form>
                                @endif

                                @if (isset($actions['close']))
                                    <form action="{{ $actions['close']['route'] }}" method="POST" class="js-po-status-form inline">
                                        @csrf
                                        <input type="hidden" name="indent_id" value="{{ $actions['close']['params']['indent_id'] }}">
                                        <input type="hidden" name="department_id" value="{{ $actions['close']['params']['department_id'] }}">
                                        <input type="hidden" name="status" value="Close">
                                        <button type="button" data-action="Close" onclick="confirmPOStatus(this)"
                                                class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium text-white bg-gray-700 hover:bg-gray-800 rounded transition-all">
                                            <i class="bi bi-x-octagon"></i> Close
                                        </button>
                                    </form>
                                @endif

                                @if (isset($actions['cancel']))
                                    <form action="{{ $actions['cancel']['route'] }}" method="POST" class="js-po-status-form inline">
                                        @csrf
                                        <input type="hidden" name="indent_id" value="{{ $actions['cancel']['params']['indent_id'] }}">
                                        <input type="hidden" name="department_id" value="{{ $actions['cancel']['params']['department_id'] }}">
                                        <input type="hidden" name="status" value="Cancel">
                                        <button type="button" data-action="Cancel" onclick="confirmPOStatus(this)"
                                                class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium text-white bg-red-600 hover:bg-red-700 rounded transition-all">
                                            <i class="bi bi-x-circle"></i> Cancel
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-gray-500">No PO records found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($pagination->hasPages())
        <div class="p-4 border-t">
            {{ $pagination->appends(request()->query())->links('pagination::tailwind') }}
        </div>
    @endif
</div>

<!-- Status Confirm Modal -->
<div id="poStatusModal" class="fixed inset-0 z-50 hidden">
  <div class="absolute inset-0 bg-black/50" onclick="closePOStatusModal()"></div>
  <div class="relative mx-auto mt-24 w-[90%] max-w-md rounded-2xl bg-white shadow-xl">
    <div class="px-5 py-4 border-b">
      <h4 id="poModalTitle" class="text-lg font-semibold text-gray-800">Confirm Action</h4>
    </div>
    <div class="px-5 py-4">
      <p id="poModalText" class="text-sm text-gray-700">Are you sure you want to proceed?</p>
    </div>
    <div class="px-5 py-4 border-t flex items-center justify-end gap-2">
      <button type="button" onclick="closePOStatusModal()"
              class="px-4 py-2 text-sm font-medium rounded-md border border-gray-300 text-gray-700 hover:bg-gray-50">
        No
      </button>
      <button type="button" id="poConfirmBtn"
              class="px-4 py-2 text-sm font-medium rounded-md text-white bg-gray-700 hover:bg-gray-800">
        Yes
      </button>
    </div>
  </div>
</div>

<script>
  let activePOForm = null;

  function confirmPOStatus(btn) {
    activePOForm = btn.closest('form');
    const action = btn.dataset.action.toLowerCase();
    
    const modal = document.getElementById('poStatusModal');
    const title = document.getElementById('poModalTitle');
    const text = document.getElementById('poModalText');
    const confirmBtn = document.getElementById('poConfirmBtn');

    const config = {
      close: { title: 'Confirm Close', text: 'This will mark the Indent & PO as Closed. Continue?', cls: 'bg-gray-700 hover:bg-gray-800' },
      cancel: { title: 'Confirm Cancel', text: 'This will mark the Indent & PO as Cancelled. Continue?', cls: 'bg-red-600 hover:bg-red-700' },
      pending: { title: 'Re-Open (Pending)', text: 'This will set the status back to Pending. Continue?', cls: 'bg-green-600 hover:bg-green-700' }
    };

    const cfg = config[action] || config.close;
    title.textContent = cfg.title;
    text.textContent = cfg.text;

    confirmBtn.className = 'px-4 py-2 text-sm font-medium rounded-md text-white';
    confirmBtn.classList.add(...cfg.cls.split(' '));
    modal.classList.remove('hidden');
  }

  function closePOStatusModal() {
    document.getElementById('poStatusModal').classList.add('hidden');
    activePOForm = null;
  }

  document.getElementById('poConfirmBtn').addEventListener('click', function() {
    if (activePOForm) {
      activePOForm.submit();
    }
    closePOStatusModal();
  });
</script>
@endsection