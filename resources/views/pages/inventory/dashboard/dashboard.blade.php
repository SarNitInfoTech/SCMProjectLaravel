@extends("layouts.layout")

@section("bodyContent")
<div class="md:flex block items-center justify-between my-6 page-header-breadcrumb">
    <div>
        <h4 class="mb-0 text-defaulttextcolor font-medium">
            Inventory Dashboard
        </h4>
        <p class="-mt-[0.2rem] mb-0 text-textmuted">
            Overview of company-wide stock levels and movements
        </p>
    </div>

    <div class="flex items-center gap-2">
        <a href="{{ route('inventory.stocks.create') }}" class="px-4 py-2 text-sm bg-indigo-600 hover:bg-indigo-700 text-white rounded shadow transition-all">
            <i class="bi bi-plus-lg"></i> Add New Stock
        </a>
        <a href="{{ route('inventory.reports.movements') }}" class="px-4 py-2 text-sm bg-gray-800 hover:bg-gray-900 text-white rounded shadow transition-all">
            <i class="bi bi-journal-text"></i> View Ledger
        </a>
    </div>
</div>

{{-- Stats Grid --}}
<div class="grid grid-cols-12 gap-x-6 mb-6">
    <div class="xl:col-span-3 lg:col-span-6 md:col-span-6 sm:col-span-12 col-span-12">
        <div class="box overflow-hidden sales-box bg-primary-gradient !rounded-sm shadow-sm">
            <div class="px-4 pt-4 pb-2">
                <h6 class="mb-3 text-[.75rem] font-medium text-fixed-white">TOTAL STOCK ITEMS</h6>
                <div class="flex justify-between items-center">
                    <div>
                        <h4 class="font-bold text-[1.5rem] text-fixed-white">
                            {{ $stats['total_items'] }}
                        </h4>
                        <p class="mb-0 text-[.75rem] text-fixed-white opacity-[0.7]">Active tracked items</p>
                    </div>
                    <span class="text-fixed-white text-3xl opacity-75">
                        <i class="bi bi-box-seam"></i>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="xl:col-span-3 lg:col-span-6 md:col-span-6 sm:col-span-12 col-span-12">
        <div class="box overflow-hidden sales-card bg-danger-gradient !rounded-sm shadow-sm">
            <div class="px-4 pt-4 pb-2">
                <h6 class="mb-3 text-[.75rem] font-medium text-fixed-white">OUT OF STOCK</h6>
                <div class="flex justify-between items-center">
                    <div>
                        <h4 class="text-[1.5rem] font-bold text-fixed-white">
                            {{ $stats['out_of_stock'] }}
                        </h4>
                        <p class="mb-0 text-[.75rem] text-fixed-white opacity-[0.7]">Zero balance items</p>
                    </div>
                    <span class="text-fixed-white text-3xl opacity-75">
                        <i class="bi bi-exclamation-octagon"></i>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="xl:col-span-3 lg:col-span-6 md:col-span-6 sm:col-span-12 col-span-12">
        <div class="box overflow-hidden sales-card bg-warning-gradient !rounded-sm shadow-sm">
            <div class="px-4 pt-4 pb-2">
                <h6 class="mb-3 text-[.75rem] font-medium text-fixed-white">LOW STOCK ALERTS</h6>
                <div class="flex justify-between items-center">
                    <div>
                        <h4 class="text-[1.5rem] font-bold text-fixed-white">
                            {{ $stats['low_stock'] }}
                        </h4>
                        <p class="mb-0 text-[.75rem] text-fixed-white opacity-[0.7]">Below minimum threshold</p>
                    </div>
                    <span class="text-fixed-white text-3xl opacity-75">
                        <i class="bi bi-bell"></i>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="xl:col-span-3 lg:col-span-6 md:col-span-6 sm:col-span-12 col-span-12">
        <div class="box overflow-hidden sales-card bg-success-gradient !rounded-sm shadow-sm">
            <div class="px-4 pt-4 pb-2">
                <h6 class="mb-3 text-[.75rem] font-medium text-fixed-white">MOVEMENTS THIS MONTH</h6>
                <div class="flex justify-between items-center">
                    <div>
                        <h4 class="text-[1.5rem] font-bold text-fixed-white">
                            {{ $stats['recent_movements'] }}
                        </h4>
                        <p class="mb-0 text-[.75rem] text-fixed-white opacity-[0.7]">In, Out, Adjust, Transfer</p>
                    </div>
                    <span class="text-fixed-white text-3xl opacity-75">
                        <i class="bi bi-arrow-left-right"></i>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Stock List with Filters --}}
<div class="card shadow-sm border mb-6 bg-white">
    <div class="card-header p-4 border-b">
        <form method="GET" action="{{ route('inventory.dashboard') }}" class="flex flex-wrap items-center justify-between gap-4">
            <h3 class="text-lg font-semibold text-gray-800">Current Stock Levels</h3>
            <div class="flex flex-wrap items-center gap-3">
                <select name="department_id" class="form-select rounded border px-3 py-1.5 text-sm w-48 bg-gray-50" onchange="this.form.submit()">
                    <option value="">All Departments</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                            {{ $dept->name }}
                        </option>
                    @endforeach
                </select>

                <div class="relative">
                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Search item name..."
                        class="form-input rounded border px-3 py-1.5 text-sm w-64 bg-gray-50"
                    >
                </div>

                <button type="submit" class="px-4 py-1.5 text-sm bg-gray-800 hover:bg-gray-900 text-white rounded transition-all">
                    Search
                </button>

                @if(request()->filled('department_id') || request()->filled('search'))
                    <a href="{{ route('inventory.dashboard') }}" class="px-3 py-1.5 text-sm bg-gray-100 hover:bg-gray-200 text-gray-700 rounded transition-all">
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
                    <th scope="col" class="text-start">Item Name</th>
                    <th scope="col" class="text-start">Department</th>
                    <th scope="col" class="text-start">Current Qty</th>
                    <th scope="col" class="text-start">Unit</th>
                    <th scope="col" class="text-start">Location</th>
                    <th scope="col" class="text-start">Status</th>
                    <th scope="col" class="text-center">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($stocks as $stock)
                    @php
                        $isLow = $stock->current_qty <= $stock->min_qty;
                        $isOut = $stock->current_qty <= 0;
                    @endphp
                    <tr class="border-b border-defaultborder hover:bg-gray-50 transition-colors">
                        <td class="font-medium text-gray-900">{{ $stock->item?->name ?? '—' }}</td>
                        <td>{{ $stock->department?->name ?? '—' }}</td>
                        <td class="font-bold">
                            {{ number_format($stock->current_qty, 2) }}
                        </td>
                        <td>{{ $stock->unit?->name ?? '—' }}</td>
                        <td>{{ $stock->location ?? '—' }}</td>
                        <td>
                            @if ($isOut)
                                <span class="inline-block px-2 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                                    Out of Stock
                                </span>
                            @elseif ($isLow)
                                <span class="inline-block px-2 py-0.5 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">
                                    Low Stock
                                </span>
                            @else
                                <span class="inline-block px-2 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                    Good Stock
                                </span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('inventory.stocks.edit', $stock->id) }}"
                                   class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium text-white bg-green-600 hover:bg-green-700 rounded transition-all">
                                    <i class="bi bi-pencil-square"></i> Edit
                                </a>
                                <a href="{{ route('inventory.stocks.movements.create', $stock->id) }}"
                                   class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded transition-all">
                                    <i class="bi bi-plus-slash-minus"></i> Movement
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
                        <td colspan="7" class="text-center py-4 text-gray-500">No stock entries found matching the criteria.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($stocks->hasPages())
        <div class="p-4 border-t">
            {{ $stocks->appends(request()->query())->links('pagination::tailwind') }}
        </div>
    @endif
</div>
@endsection
