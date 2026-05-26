@extends("layouts.layout")

@section("bodyContent")
<div class="md:flex block items-center justify-between my-6 page-header-breadcrumb">
    <div>
        <h4 class="mb-0 text-defaulttextcolor font-medium">
            Stock Management
        </h4>
        <p class="-mt-[0.2rem] mb-0 text-textmuted">
            Manage, edit, and record transactions for all warehouse items
        </p>
    </div>

    <div class="flex items-center gap-2">
        <a href="{{ route('inventory.stocks.create') }}" class="px-4 py-2 text-sm bg-indigo-600 hover:bg-indigo-700 text-white rounded shadow transition-all">
            <i class="bi bi-plus-lg"></i> Add New Stock
        </a>
        <a href="{{ route('inventory.dashboard') }}" class="px-4 py-2 text-sm bg-gray-100 hover:bg-gray-200 text-gray-700 rounded transition-all">
            Back to Dashboard
        </a>
    </div>
</div>

@if (session('success'))
    <div class="mb-4 p-3 bg-green-100 border-l-4 border-green-500 text-green-700 text-sm">
        {{ session('success') }}
    </div>
@endif

{{-- Custom Stock List with Filters --}}
<div class="card shadow-sm border mb-6 bg-white">
    <div class="card-header p-4 border-b">
        <form method="GET" action="{{ route('inventory.stocks.list') }}" class="flex flex-wrap items-center justify-between gap-4">
            <h3 class="text-lg font-semibold text-gray-800">Tracked Stock Items</h3>
            
            <div class="flex flex-wrap items-center gap-3">
                <!-- Filter by Department -->
                <select name="department_id" class="form-select rounded border px-3 py-1.5 text-sm w-48 bg-gray-50" onchange="this.form.submit()">
                    <option value="">All Departments</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                            {{ $dept->name }}
                        </option>
                    @endforeach
                </select>

                <!-- Search Input -->
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
                    Filter
                </button>

                @if(request()->filled('department_id') || request()->filled('search'))
                    <a href="{{ route('inventory.stocks.list') }}" class="px-3 py-1.5 text-sm bg-gray-100 hover:bg-gray-200 text-gray-700 rounded transition-all">
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
                    <th scope="col" class="text-end">Current Qty</th>
                    <th scope="col" class="text-start">Unit</th>
                    <th scope="col" class="text-start">Location</th>
                    <th scope="col" class="text-start">Threshold Alert (Min)</th>
                    <th scope="col" class="text-start">Status</th>
                    <th scope="col" class="text-center">Action Actions</th>
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
                        <td class="font-bold text-end">
                            {{ number_format($stock->current_qty, 2) }}
                        </td>
                        <td>{{ $stock->unit?->name ?? '—' }}</td>
                        <td>{{ $stock->location ?? '—' }}</td>
                        <td>{{ number_format($stock->min_qty, 2) }}</td>
                        <td>
                            @if (!$stock->is_active)
                                <span class="inline-block px-2 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-600">
                                    Inactive
                                </span>
                            @elseif ($isOut)
                                <span class="inline-block px-2 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                                    Out of Stock
                                </span>
                            @elseif ($isLow)
                                <span class="inline-block px-2 py-0.5 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">
                                    Low Stock Alert
                                </span>
                            @else
                                <span class="inline-block px-2 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                    Active & Tracked
                                </span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('inventory.stocks.edit', $stock->id) }}"
                                   class="inline-flex items-center gap-1 px-3 py-1 text-xs font-medium text-white bg-green-600 hover:bg-green-700 rounded transition-all">
                                    <i class="bi bi-pencil-square"></i> Configure
                                </a>
                                <a href="{{ route('inventory.stocks.movements.create', $stock->id) }}"
                                   class="inline-flex items-center gap-1 px-3 py-1 text-xs font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded transition-all">
                                    <i class="bi bi-plus-slash-minus"></i> Record Transaction
                                </a>
                                <a href="{{ route('inventory.stocks.movements', $stock->id) }}"
                                   class="inline-flex items-center gap-1 px-3 py-1 text-xs font-medium text-white bg-blue-500 hover:bg-blue-600 rounded transition-all">
                                    <i class="bi bi-journal-text"></i> View Ledger
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-6 text-gray-500">No stock records found matching your filters.</td>
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
