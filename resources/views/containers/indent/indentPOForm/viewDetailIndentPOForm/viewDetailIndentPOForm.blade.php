<div class="container mx-auto">

    {{-- Section 1: Indent Summary --}}
    <div class="bg-white shadow rounded-xl p-6 border border-gray-200 mb-10">
        <h3 class="text-xl font-semibold text-gray-700 mb-4">Purchase Order Details</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-gray-800">
            <div><strong>Indent ID:</strong> {{ $po->indent_id }}</div>
            <div><strong>Department:</strong> {{ $po->department_name }}</div>
            <div><strong>Project:</strong> {{ $po->project_name }}</div>
            <div><strong>Item:</strong> {{ $po->indent_item }}</div>
            <div><strong>Unit:</strong> {{ $po->unit }}</div>
            <div><strong>Quantity Required:</strong> {{ $po->quantity_required }}</div>
        </div>
    </div>
    <br>

   {{-- Section 2: Action Buttons & PO Header --}}
<div class=" bg-white shadow rounded-t-xl p-3 border border-gray-200 flex flex-wrap items-center justify-between ">
    <h4 class="text-xl font-semibold text-gray-700">All Purchase Order</h4>

    <div class="flex gap-3">
        <a href="{{ route('po.export.excel', ['indent_id' => $indent_id, 'department_id' => $department_id]) }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-green-500 hover:bg-green-600 text-white text-sm font-medium rounded-md shadow-sm transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                <path d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h4l2 2h8a2 2 0 012 2v10a2 2 0 01-2 2z"/>
            </svg>
            Download Excel
        </a>

        <a href="{{ route('po.export.pdf', ['indent_id' => $indent_id, 'department_id' => $department_id]) }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-red-500 hover:bg-red-600 text-white text-sm font-medium rounded-md shadow-sm transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6zM13 9V3.5L18.5 9H13z"/>
            </svg>
            Download PDF
        </a>
    </div>
</div>


    @foreach($allPos as $row)
    <div class="bg-white shadow rounded-b-xl p-6 border border-gray-200 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-gray-800">
            <div><strong>PO Date:</strong> {{ $row->po_date }}</div>
            <div><strong>Party Name:</strong> {{ $row->party_name }}</div>
            <div><strong>PO/WO No:</strong> {{ $row->po_wo_no }}</div>
            <div><strong>PO Amount:</strong> ₹{{ number_format($row->po_amount, 2) }}</div>
            <div>
                <strong>Status:</strong>
                <span class="inline-block px-2 py-1 rounded-full text-xs font-semibold
                    @if($row->status === 'Pending') bg-yellow-100 text-yellow-800
                    @elseif($row->status === 'Cancel') bg-red-100 text-red-800
                    @elseif($row->status === 'Close') bg-green-100 text-green-800
                    @else bg-gray-200 text-gray-800 @endif">
                    {{ $row->status }}
                </span>
            </div>
            <div><strong>Invoice No:</strong> {{ $row->invoice ?? '—' }}</div>
            <div><strong>Invoice Date:</strong> {{ $row->invoice_date ?? '—' }}</div>
            <div><strong>Receiving Date:</strong> {{ $row->receiving_date ?? '—' }}</div>
            <div><strong>Delay:</strong> {{ $row->delay_in_days ?? '0' }} days</div>
        </div>
        <div class="mt-4">
            <strong>Remarks:</strong>
            <p class="text-sm text-gray-700 bg-gray-50 border border-gray-200 p-3 rounded-md mt-1">
                {{ $row->remarks ?? 'No remarks available.' }}
            </p>
        </div>
    </div>
    @endforeach

</div>
