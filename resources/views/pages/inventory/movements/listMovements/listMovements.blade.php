@extends("layouts.layout")

@section("bodyContent")
<div class="md:flex block items-center justify-between my-6 page-header-breadcrumb">
    <div>
        <h4 class="mb-0 text-defaulttextcolor font-medium">
            Stock Ledger Details
        </h4>
        <p class="-mt-[0.2rem] mb-0 text-textmuted">
            Movement history for <strong>{{ $stock->item?->name }}</strong> ({{ $stock->department?->name }})
        </p>
    </div>

    <div class="flex items-center gap-2">
        <a href="{{ route('inventory.stocks.movements.create', $stock->id) }}" class="px-4 py-2 text-sm bg-indigo-600 hover:bg-indigo-700 text-white rounded shadow transition-all">
            <i class="bi bi-plus-slash-minus"></i> Record Movement
        </a>
        <a href="{{ route('inventory.dashboard') }}" class="px-4 py-2 text-sm bg-gray-100 hover:bg-gray-200 text-gray-700 rounded transition-all">
            Back to Dashboard
        </a>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
    <div class="bg-white p-4 rounded shadow-sm border">
        <span class="text-xs text-gray-500 font-semibold block uppercase">Current Stock</span>
        <span class="text-2xl font-bold text-gray-900">{{ number_format($stock->current_qty, 2) }}</span>
        <span class="text-sm text-gray-500 font-medium ml-1">{{ $stock->unit?->name }}</span>
    </div>
    <div class="bg-white p-4 rounded shadow-sm border">
        <span class="text-xs text-gray-500 font-semibold block uppercase">Min / Max Threshold</span>
        <span class="text-lg font-bold text-gray-900">
            {{ number_format($stock->min_qty, 2) }} / {{ $stock->max_qty ? number_format($stock->max_qty, 2) : 'No Max' }}
        </span>
    </div>
    <div class="bg-white p-4 rounded shadow-sm border">
        <span class="text-xs text-gray-500 font-semibold block uppercase">Storage Location</span>
        <span class="text-lg font-bold text-gray-900">{{ $stock->location ?? 'Not Specified' }}</span>
    </div>
    <div class="bg-white p-4 rounded shadow-sm border">
        <span class="text-xs text-gray-500 font-semibold block uppercase">Status</span>
        @if ($stock->current_qty <= 0)
            <span class="inline-block mt-1 px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">Out of Stock</span>
        @elseif ($stock->current_qty <= $stock->min_qty)
            <span class="inline-block mt-1 px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">Low Stock Alert</span>
        @else
            <span class="inline-block mt-1 px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">In Stock</span>
        @endif
    </div>
</div>

{{-- Movements Table --}}
<div class="card shadow-sm border mb-6 bg-white">
    <div class="card-header p-4 border-b">
        <h3 class="text-lg font-semibold text-gray-800">Transactions Ledger</h3>
    </div>

    <div class="table-responsive p-4">
        <table class="table whitespace-nowrap min-w-full">
            <thead>
                <tr class="border-b border-defaultborder">
                    <th scope="col" class="text-start">Date</th>
                    <th scope="col" class="text-start">Type</th>
                    <th scope="col" class="text-end">Qty Changed</th>
                    <th scope="col" class="text-end">Balance Before</th>
                    <th scope="col" class="text-end">Balance After</th>
                    <th scope="col" class="text-start">Source/Details</th>
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
                        <td>
                            <span class="inline-block px-2 py-0.5 rounded-full text-xs font-semibold {{ $badgeClass }}">
                                {{ $type }}
                            </span>
                        </td>
                        <td class="text-end {{ $qtyClass }}">{{ $qtyPrefix }}{{ number_format($m->quantity, 2) }}</td>
                        <td class="text-end text-gray-500">{{ number_format($m->qty_before, 2) }}</td>
                        <td class="text-end font-medium">{{ number_format($m->qty_after, 2) }}</td>
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
                        <td colspan="8" class="text-center py-4 text-gray-500">No transaction records found for this stock item.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($movements->hasPages())
        <div class="p-4 border-t">
            {{ $movements->links('pagination::tailwind') }}
        </div>
    @endif
</div>
@endsection
