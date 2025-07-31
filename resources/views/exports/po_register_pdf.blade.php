<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>PO Register PDF</title>
    <style>
        body { font-family: sans-serif; font-size: 10px; }
        table { border-collapse: collapse; width: 100%; table-layout: fixed; word-wrap: break-word; }
        th, td { border: 1px solid #ccc; padding: 4px; text-align: left; }
        th { background-color: #f0f0f0; }
    </style>
</head>
<body>

<h2>Purchase Order Register</h2>
<p><strong>Indent ID:</strong> {{ $indent_id }} | <strong>Department ID:</strong> {{ $department_id }}</p>

<table>
    <thead>
        <tr>
            <th>PO ID</th>
            <th>Dept Name</th>
            <th>PO Date</th>
            <th>Party</th>
            <th>PO/WO No</th>
            <th>PO Item</th>
            <th>Amount</th>
            <th>Status</th>
            <th>Invoice</th>
            <th>Expected</th>
            <th>Receiving</th>
            <th>Delay</th>
            <th>Remarks</th>
            <th>Indent Ticket</th>
            <th>Indent Date</th>
            <th>Project</th>
            <th>Item</th>
            <th>Unit</th>
            <th>Qty Req</th>
            <th>Qty Recv</th>
            <th>Qty Bal</th>
        </tr>
    </thead>
    <tbody>
        @foreach($allPos as $row)
        <tr>
            <td>{{ $row->po_id }}</td>
            <td>{{ $row->department_name }}</td>
            <td>{{ $row->po_date }}</td>
            <td>{{ $row->party_name }}</td>
            <td>{{ $row->po_wo_no }}</td>
            <td>{{ $row->po_item_description }}</td>
            <td>{{ $row->po_amount }}</td>
            <td>{{ $row->status }}</td>
            <td>{{ $row->invoice }}</td>
            <td>{{ $row->expected_date }}</td>
            <td>{{ $row->receiving_date }}</td>
            <td>{{ $row->delay_in_days }}</td>
            <td>{{ $row->remarks }}</td>
            <td>{{ $row->indent_ticket_no }}</td>
            <td>{{ $row->indent_date }}</td>
            <td>{{ $row->project_name }}</td>
            <td>{{ $row->indent_item_description }}</td>
            <td>{{ $row->unit_name }}</td>
            <td>{{ $row->quantity_required }}</td>
            <td>{{ $row->quantity_received }}</td>
            <td>{{ $row->quantity_balance }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

</body>
</html>
