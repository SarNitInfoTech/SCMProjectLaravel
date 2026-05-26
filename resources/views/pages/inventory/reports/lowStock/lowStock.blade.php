@extends("layouts.layout")

@section("bodyContent")
<div class="md:flex block items-center justify-between my-6 page-header-breadcrumb">
    <div>
        <h4 class="mb-0 text-defaulttextcolor font-medium">
            Low Stock Alerts Report
        </h4>
        <p class="-mt-[0.2rem] mb-0 text-textmuted">
            All active stock items currently at or below their alert thresholds
        </p>
    </div>

    <div class="flex items-center gap-2">
        <a href="{{ route('inventory.dashboard') }}" class="px-4 py-2 text-sm bg-gray-100 hover:bg-gray-200 text-gray-700 rounded transition-all">
            Back to Dashboard
        </a>
    </div>
</div>

<div class="card shadow-sm border mb-6 bg-white">
    <div class="card-header p-4 border-b">
        <h3 class="text-lg font-semibold text-gray-800">Critical Stock Items</h3>
    </div>

    <div class="table-responsive p-4">
        <table class="table whitespace-nowrap min-w-full">
            <thead>
                <tr class="border-b border-defaultborder">
                    <th scope="col" class="text-start">Item Name</th>
                    <th scope="col" class="text-start">Department</th>
                    <th scope="col" class="text-end">Current Qty</th>
                    <th scope="col" class="text-end">Min Alert Qty</th>
                    <th scope="col" class="text-start">Unit</th>
                    <th scope="col" class="text-start">Location</th>
                    <th scope="col" class="text-start">Status</th>
                    <th scope="col" class="text-center">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($stocks as $stock)
                    @php
                        $isOut = $stock->current_qty <= 0;
                    @endphp
                    <tr class="border-b border-defaultborder hover:bg-red-50/30 transition-colors">
                        <td class="font-medium text-gray-900">{{ $stock->item?->name ?? '—' }}</td>
                        <td>{{ $stock->department?->name ?? '—' }}</td>
                        <td class="text-end font-bold text-red-600">
                            {{ number_format($stock->current_qty, 2) }}
                        </td>
                        <td class="text-end text-gray-500 font-semibold">
                            {{ number_format($stock->min_qty, 2) }}
                        </td>
                        <td>{{ $stock->unit?->name ?? '—' }}</td>
                        <td>{{ $stock->location ?? '—' }}</td>
                        <td>
                            @if ($isOut)
                                <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                                    Out of Stock
                                </span>
                            @else
                                <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">
                                    Low Stock
                                </span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('inventory.stocks.movements.create', $stock->id) }}"
                                   class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded transition-all">
                                    <i class="bi bi-plus-slash-minus"></i> Record IN/OUT
                                </a>
                                <a href="{{ route('inventory.stocks.movements', $stock->id) }}"
                                   class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium text-white bg-blue-500 hover:bg-blue-600 rounded transition-all">
                                    <i class="bi bi-journal-text"></i> History
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-6 text-green-600 font-semibold">
                            🎉 Excellent! No stock items are currently below thresholds.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
