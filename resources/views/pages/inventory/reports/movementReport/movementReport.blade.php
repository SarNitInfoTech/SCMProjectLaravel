@extends("layouts.layout")

@section("bodyContent")
<div class="md:flex block items-center justify-between my-6 page-header-breadcrumb">
    <div>
        <h4 class="mb-0 text-defaulttextcolor font-medium">
            Company Movement Ledger
        </h4>
        <p class="-mt-[0.2rem] mb-0 text-textmuted">
            Detailed ledger of all stock transactions across all departments
        </p>
    </div>

    <div class="flex items-center gap-2">
        <a href="{{ route('inventory.dashboard') }}" class="px-4 py-2 text-sm bg-gray-100 hover:bg-gray-200 text-gray-700 rounded transition-all">
            Back to Dashboard
        </a>
    </div>
</div>

{{-- Filters Card --}}
<div class="card shadow-sm border mb-6 bg-white">
    <div class="card-header p-4 border-b">
        <h3 class="text-sm font-semibold text-gray-800 uppercase tracking-wider">Filter Ledger Records</h3>
    </div>
    <div class="p-4">
        <form method="GET" action="{{ route('inventory.reports.movements') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
            <!-- Department Filter -->
            <div>
                <label for="department_id" class="block text-xs font-semibold text-gray-600 mb-1">Department</label>
                <select name="department_id" id="department_id" class="form-select rounded border px-3 py-2 text-sm w-full bg-white">
                    <option value="">All Departments</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                            {{ $dept->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Type Filter -->
            <div>
                <label for="type" class="block text-xs font-semibold text-gray-600 mb-1">Transaction Type</label>
                <select name="type" id="type" class="form-select rounded border px-3 py-2 text-sm w-full bg-white">
                    <option value="">All Types</option>
                    @foreach($types as $t)
                        <option value="{{ $t }}" {{ request('type') == $t ? 'selected' : '' }}>
                            {{ $t }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Start Date -->
            <div>
                <label for="start_date" class="block text-xs font-semibold text-gray-600 mb-1">Start Date</label>
                <input type="date" name="start_date" id="start_date" value="{{ request('start_date') }}" class="form-input rounded border px-3 py-1.5 text-sm w-full bg-white">
            </div>

            <!-- End Date -->
            <div>
                <label for="end_date" class="block text-xs font-semibold text-gray-600 mb-1">End Date</label>
                <input type="date" name="end_date" id="end_date" value="{{ request('end_date') }}" class="form-input rounded border px-3 py-1.5 text-sm w-full bg-white">
            </div>

            <!-- Actions -->
            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 text-sm bg-gray-800 hover:bg-gray-900 text-white rounded transition-all w-full text-center font-medium">
                    Apply Filter
                </button>
                @if(request()->anyFilled(['department_id', 'type', 'start_date', 'end_date']))
                    <a href="{{ route('inventory.reports.movements') }}" class="px-4 py-2 text-sm bg-gray-100 hover:bg-gray-200 text-gray-700 rounded transition-all text-center font-medium">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

{{-- Ledger Card --}}
<div class="card shadow-sm border mb-6 bg-white">
    <div class="card-header p-4 border-b">
        <h3 class="text-lg font-semibold text-gray-800">Movement Ledger</h3>
    </div>

    <div class="table-responsive p-4">
        <table class="table whitespace-nowrap min-w-full">
            <thead>
                <tr class="border-b border-defaultborder">
                    <th scope="col" class="text-start">Date</th>
                    <th scope="col" class="text-start">Item Name</th>
                    <th scope="col" class="text-start">Department</th>
                    <th scope="col" class="text-start">Type</th>
                    <th scope="col" class="text-end">Qty Changed</th>
                    <th scope="col" class="text-end">Before Qty</th>
                    <th scope="col" class="text-end">After Qty</th>
                    <th scope="col" class="text-start">Unit</th>
                    <th scope="col" class="text-start">Details/Doc No</th>
                    <th scope="col" class="text-start">Recorded By</th>
                    <th scope="col" class="text-start">Remarks</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($movements as $m)
                    @php
                        $type = strtoupper($m->type);
                        $badgeClass = match ($type) {
                            'IN' => 'bg-green-100 text-green-800',
                            'OUT' => 'bg-red-100 text-red-800',
                            'ADJUST' => 'bg-blue-100 text-blue-800',
                            'RETURN' => 'bg-indigo-100 text-indigo-800',
                            'TRANSFER' => 'bg-orange-100 text-orange-800',
                            default => 'bg-gray-100 text-gray-800'
                        };
                        
                        $isPositive = in_array($type, ['IN', 'RETURN']) || ($type === 'ADJUST' && $m->quantity > 0);
                        $qtyPrefix = $isPositive ? '+' : '';
                        $qtyClass = $isPositive ? 'text-green-600 font-semibold' : 'text-red-600 font-semibold';
                    @endphp
                    <tr class="border-b border-defaultborder hover:bg-gray-50 transition-colors">
                        <td>{{ $m->movement_date->format('d-m-Y') }}</td>
                        <td class="font-medium text-gray-900">{{ $m->stock?->item?->name ?? '—' }}</td>
                        <td>{{ $m->stock?->department?->name ?? '—' }}</td>
                        <td>
                            <span class="inline-block px-2 py-0.5 rounded-full text-xs font-semibold {{ $badgeClass }}">
                                {{ $type }}
                            </span>
                        </td>
                        <td class="text-end {{ $qtyClass }}">{{ $qtyPrefix }}{{ number_format($m->quantity, 2) }}</td>
                        <td class="text-end text-gray-500">{{ number_format($m->qty_before, 2) }}</td>
                        <td class="text-end font-medium">{{ number_format($m->qty_after, 2) }}</td>
                        <td>{{ $m->stock?->unit?->name ?? '—' }}</td>
                        <td>
                            @if ($m->vendor)
                                <div class="text-xs">
                                    <span class="text-gray-500">Supplier:</span> {{ $m->vendor->name }}
                                </div>
                            @endif
                            @if ($m->poRegister)
                                <div class="text-xs">
                                    <span class="text-gray-500">PO Ref:</span> {{ $m->poRegister->indent_id }}
                                </div>
                            @endif
                            @if ($m->reference_no)
                                <div class="text-xs">
                                    <span class="text-gray-500">Doc No:</span> {{ $m->reference_no }}
                                </div>
                            @endif
                            @if (!$m->vendor && !$m->poRegister && !$m->reference_no)
                                <span class="text-gray-400">—</span>
                            @endif
                        </td>
                        <td>{{ $m->user?->name ?? 'System' }}</td>
                        <td class="max-w-xs whitespace-normal text-xs text-gray-600">{{ $m->remarks ?? '—' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="11" class="text-center py-6 text-gray-500">No stock movements found matching current filters.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($movements->hasPages())
        <div class="p-4 border-t">
            {{ $movements->appends(request()->query())->links('pagination::tailwind') }}
        </div>
    @endif
</div>
@endsection
