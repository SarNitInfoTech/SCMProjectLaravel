@extends("layouts.layout")

@section("bodyContent")
<div class="md:flex block items-center justify-between my-6 page-header-breadcrumb">
    <div>
        <h4 class="mb-0 text-defaulttextcolor font-medium">{{ $title }}</h4>
        <p class="-mt-[0.2rem] mb-0 text-textmuted">Manage external vendors and suppliers list</p>
    </div>
    <div class="flex items-center gap-2">
        <a href="{{ route('vendors.create') }}" class="px-4 py-2 text-sm bg-indigo-600 hover:bg-indigo-700 text-white rounded shadow transition-all">
            <i class="bi bi-plus-lg"></i> Add New Vendor
        </a>
    </div>
</div>

@if (session('success'))
    <div class="mb-4 p-3 bg-green-100 border-l-4 border-green-500 text-green-700 text-sm">
        {{ session('success') }}
    </div>
@endif

<div class="card shadow-sm border mb-6 bg-white">
    <div class="card-header p-4 border-b">
        <form method="GET" action="{{ route('vendors.list') }}" class="flex flex-wrap items-center justify-between gap-4">
            <h3 class="text-lg font-semibold text-gray-800">Vendors Register</h3>
            <div class="flex items-center gap-2">
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Search name, email, phone..."
                    class="form-input rounded border px-3 py-1.5 text-sm w-64 bg-gray-50"
                >
                <button type="submit" class="px-4 py-1.5 text-sm bg-gray-800 hover:bg-gray-900 text-white rounded">
                    Search
                </button>
                @if(request()->filled('search'))
                    <a href="{{ route('vendors.list') }}" class="px-3 py-1.5 text-sm bg-gray-100 hover:bg-gray-200 text-gray-700 rounded">
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
                    <th scope="col" class="text-start">Vendor Name</th>
                    <th scope="col" class="text-start">Email</th>
                    <th scope="col" class="text-start">Phone</th>
                    <th scope="col" class="text-start">GST Number</th>
                    <th scope="col" class="text-start">Bank Name</th>
                    <th scope="col" class="text-start">Status</th>
                    <th scope="col" class="text-start">Created At</th>
                    <th scope="col" class="text-center">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($vendors as $vendor)
                    <tr class="border-b border-defaultborder hover:bg-gray-50 transition-colors">
                        <td class="font-medium text-gray-900">{{ $vendor->name }}</td>
                        <td>{{ $vendor->email ?? '—' }}</td>
                        <td>{{ $vendor->phone ?? '—' }}</td>
                        <td>{{ $vendor->gst_number ?? '—' }}</td>
                        <td>{{ $vendor->bank_name ?? '—' }}</td>
                        <td>
                            @if ($vendor->is_active)
                                <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-800">Active</span>
                            @else
                                <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-800">Inactive</span>
                            @endif
                        </td>
                        <td>{{ $vendor->created_at ? $vendor->created_at->format('d-m-Y') : '—' }}</td>
                        <td class="text-center">
                            <a href="{{ route('vendors.edit', $vendor->id) }}" class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium text-white bg-green-600 hover:bg-green-700 rounded transition-all">
                                <i class="bi bi-pencil-square"></i> Edit
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-gray-500">No vendors found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($vendors->hasPages())
        <div class="p-4 border-t">
            {{ $vendors->appends(request()->query())->links('pagination::tailwind') }}
        </div>
    @endif
</div>
@endsection