<table border="1" cellpadding="4" cellspacing="0">
    <thead>
        <tr style="background-color: #f0f0f0;">
            <th colspan="20" style="text-align: left;">PO Register Fields</th>
            <th colspan="13" style="text-align: left;">Indent Register Fields</th>
        </tr>
        <tr style="background-color: #f9f9f9;">
            {{-- PO Register Fields --}}
            <th>Indent ID</th>
            <th>Department Name</th>
            <th>Status</th>
            <th>Invoice</th>
            <th>PO Date</th>
            <th>Party Name</th>
            <th>PO/WO No</th>
            <th>PO Item Desc</th>
            <th>Amount</th>
            <th>Debit Head</th>
            <th>Expected Days</th>
            <th>Expected Date</th>
            <th>Invoice Date</th>
            <th>Receiving Date</th>
            <th>Delay (Days)</th>
            <th>Remarks</th>
            <th>Store Indent No</th>
            <th>PO Created At</th>
            <th>PO Updated At</th>

            {{-- Indent Register Fields --}}
            <th>Indent Row ID</th>
            <th>Indent Ticket No</th>
            <th>Indent Date</th>
            <th>Indent Department</th>
            <th>Project Name</th>
            <th>Indent Item Desc</th>
            <th>Unit Name</th>
            <th>Qty Required</th>
            <th>Purchased Order</th>
            <th>Qty Received</th>
            <th>Qty Balance</th>
            <th>Indent Created At</th>
            <th>Indent Updated At</th>
        </tr>
    </thead>
    <tbody>
        @foreach($allPos as $row)
        <tr>
            {{-- PO Register --}}
            <td>{{ $row->indent_id }}</td>
            <td>{{ $row->department_name }}</td>
            <td>{{ $row->status }}</td>
            <td>{{ $row->invoice }}</td>
            <td>{{ $row->po_date }}</td>
            <td>{{ $row->party_name }}</td>
            <td>{{ $row->po_wo_no }}</td>
            <td>{{ $row->po_item_description }}</td>
            <td>{{ $row->po_amount }}</td>
            <td>{{ $row->debit_head }}</td>
            <td>{{ $row->expected_days }}</td>
            <td>{{ $row->expected_date }}</td>
            <td>{{ $row->invoice_date }}</td>
            <td>{{ $row->receiving_date }}</td>
            <td>{{ $row->delay_in_days }}</td>
            <td>{{ $row->remarks }}</td>
            <td>{{ $row->store_indent_no }}</td>
            <td>{{ $row->po_created_at }}</td>
            <td>{{ $row->po_updated_at }}</td>

            {{-- Indent Register --}}
            <td>{{ $row->indent_db_id }}</td>
            <td>{{ $row->indent_ticket_no }}</td>
            <td>{{ $row->indent_date }}</td>
            <td>{{ $row->indent_department }}</td>
            <td>{{ $row->project_name }}</td>
            <td>{{ $row->indent_item_description }}</td>
            <td>{{ $row->unit_name }}</td>
            <td>{{ $row->quantity_required }}</td>
            <td>{{ $row->purchased_order }}</td>
            <td>{{ $row->quantity_received }}</td>
            <td>{{ $row->quantity_balance }}</td>
            <td>{{ $row->indent_created_at }}</td>
            <td>{{ $row->indent_updated_at }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
