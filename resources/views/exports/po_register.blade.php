@php
    $maxPoItems = 1; // since we’ll merge all into one column
    $maxIndentItems = 1;
@endphp

<table border="1" cellspacing="0" cellpadding="6">
    <thead>
        <tr>
            <th>PO ID</th>
            <th>Indent ID</th>
            <th>Indent Ticket</th>
            <th>Department</th>
            <th>Status</th>
            <th>Invoice</th>
            <th>PO Date</th>
            <th>Party Name</th>
            <th>PO/WO No</th>
            <th>PO Items Description</th>
            <th>PO Amount</th>
            <th>Debit Head</th>
            <th>Expected Days</th>
            <th>Expected Date</th>
            <th>Invoice Date</th>
            <th>Receiving Date</th>
            <th>Delay in Days</th>
            <th>Remarks</th>
            <th>Store Indent No</th>
            <th>PO Created At</th>
            <th>PO Updated At</th>
            <th>Indent Date</th>
            <th>Indent Project</th>
            <th>Project Name</th>
            <th>Indent Items Description</th>
            <th>Unit Name</th>
            <th>Quantity Required</th>
            <th>Purchased Order</th>
            <th>Quantity Received</th>
            <th>Quantity Balance</th>
            <th>Indent Created At</th>
            <th>Indent Updated At</th>
        </tr>
    </thead>
    <tbody>
        @foreach($allPos as $po)
            @php
                $poItems = $po->decoded_po_items ?? [];
                $indentItems = $po->decoded_indent_items ?? [];

                $flatten = function ($arr) {
                    return collect($arr)->map(fn($i) => is_array($i) ? implode(' ', $i) : $i)->implode(', ');
                };
            @endphp
            <tr>
                <td>{{ $po->po_id }}</td>
                <td>{{ $po->indent_id }}</td>
                <td>{{ $po->indent_ticket_no }}</td>
                <td>{{ $po->department_name }}</td>
                <td>{{ $po->status }}</td>
                <td>{{ $po->invoice }}</td>
                <td>{{ $po->po_date }}</td>
                <td>{{ $po->party_name }}</td>
                <td>{{ $po->po_wo_no }}</td>

                {{-- PO Items merged --}}
                <td>{{ $flatten($poItems) }}</td>

                <td>{{ $po->po_amount }}</td>
                <td>{{ $po->debit_head }}</td>
                <td>{{ $po->expected_days }}</td>
                <td>{{ $po->expected_date }}</td>
                <td>{{ $po->invoice_date }}</td>
                <td>{{ $po->receiving_date }}</td>
                <td>{{ $po->delay_in_days }}</td>
                <td>{{ $po->remarks }}</td>
                <td>{{ $po->store_indent_no }}</td>
                <td>{{ $po->po_created_at }}</td>
                <td>{{ $po->po_updated_at }}</td>
                <td>{{ $po->indent_date }}</td>
                <td>{{ $po->indent_project }}</td>
                <td>{{ $po->project_name }}</td>

                {{-- Indent Items merged --}}
                <td>{{ $flatten($indentItems) }}</td>

                <td>{{ $po->unit_name }}</td>
                <td>{{ $po->quantity_required }}</td>
                <td>{{ $po->purchased_order }}</td>
                <td>{{ $po->quantity_received }}</td>
                <td>{{ $po->quantity_balance }}</td>
                <td>{{ $po->indent_created_at }}</td>
                <td>{{ $po->indent_updated_at }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
