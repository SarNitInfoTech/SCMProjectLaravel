<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
</head>
<body>
    <table>
        <thead>
            <tr>
                <th colspan="16" style="font-size: 16px; font-weight: bold; text-align: center; height: 30px;">
                    NITRA - ALL INDENTS COMPREHENSIVE REPORT
                </th>
            </tr>
            <tr>
                <th colspan="16" style="font-size: 11px; text-align: center; color: #555555;">
                    Generated on: {{ now()->format('d-M-Y H:i A') }}
                </th>
            </tr>
            @if(!empty($filterText))
                <tr>
                    <th colspan="16" style="font-size: 10px; text-align: center; color: #777777;">
                        Applied Filters: {{ $filterText }}
                    </th>
                </tr>
            @endif
            <tr>
                <th colspan="16" style="height: 10px;"></th>
            </tr>
            <tr style="background-color: #1E293B; color: #FFFFFF; font-weight: bold;">
                <th style="background-color: #0F172A; color: #FFFFFF; font-weight: bold; border: 1px solid #000000;">Indent ID</th>
                <th style="background-color: #0F172A; color: #FFFFFF; font-weight: bold; border: 1px solid #000000;">Indent Date</th>
                <th style="background-color: #0F172A; color: #FFFFFF; font-weight: bold; border: 1px solid #000000;">Department</th>
                <th style="background-color: #0F172A; color: #FFFFFF; font-weight: bold; border: 1px solid #000000;">Project</th>
                <th style="background-color: #0F172A; color: #FFFFFF; font-weight: bold; border: 1px solid #000000;">Indent Status</th>
                <th style="background-color: #1E3A8A; color: #FFFFFF; font-weight: bold; border: 1px solid #000000;">Item Description</th>
                <th style="background-color: #1E3A8A; color: #FFFFFF; font-weight: bold; border: 1px solid #000000;">Unit</th>
                <th style="background-color: #1E3A8A; color: #FFFFFF; font-weight: bold; border: 1px solid #000000;">Qty Required</th>
                <th style="background-color: #1E3A8A; color: #FFFFFF; font-weight: bold; border: 1px solid #000000;">PO Qty</th>
                <th style="background-color: #1E3A8A; color: #FFFFFF; font-weight: bold; border: 1px solid #000000;">Qty Received</th>
                <th style="background-color: #1E3A8A; color: #FFFFFF; font-weight: bold; border: 1px solid #000000;">Qty Cancelled</th>
                <th style="background-color: #1E3A8A; color: #FFFFFF; font-weight: bold; border: 1px solid #000000;">Qty Balance</th>
                <th style="background-color: #1E3A8A; color: #FFFFFF; font-weight: bold; border: 1px solid #000000;">Item Status</th>
                <th style="background-color: #14532D; color: #FFFFFF; font-weight: bold; border: 1px solid #000000;">Linked PO / WO No</th>
                <th style="background-color: #14532D; color: #FFFFFF; font-weight: bold; border: 1px solid #000000;">Vendor / Party Name</th>
                <th style="background-color: #14532D; color: #FFFFFF; font-weight: bold; border: 1px solid #000000;">Total PO Amount (₹)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($records as $rec)
                @php
                    $items = $rec['items'] ?? [];
                    $pos = $rec['pos'] ?? [];
                    $poNos = collect($pos)->pluck('po_wo_no')->filter()->implode(', ') ?: '-';
                    $parties = collect($pos)->pluck('party_name')->filter()->unique()->implode(', ') ?: '-';
                    $poAmount = collect($pos)->sum(fn($p) => (float)($p->po_amount ?? 0));
                    $itemCount = count($items);
                @endphp

                @if($itemCount > 0)
                    @foreach($items as $idx => $it)
                        <tr>
                            @if($idx === 0)
                                <td rowspan="{{ $itemCount }}" style="vertical-align: top; border: 1px solid #D1D5DB; font-weight: bold;">{{ $rec['indent_id'] }}</td>
                                <td rowspan="{{ $itemCount }}" style="vertical-align: top; border: 1px solid #D1D5DB;">{{ $rec['indent_date'] }}</td>
                                <td rowspan="{{ $itemCount }}" style="vertical-align: top; border: 1px solid #D1D5DB;">{{ $rec['department'] }}</td>
                                <td rowspan="{{ $itemCount }}" style="vertical-align: top; border: 1px solid #D1D5DB;">{{ $rec['project'] }}</td>
                                <td rowspan="{{ $itemCount }}" style="vertical-align: top; border: 1px solid #D1D5DB; font-weight: bold;">{{ $rec['status'] }}</td>
                            @endif

                            <td style="border: 1px solid #D1D5DB;">{{ $it['description'] ?? '-' }}</td>
                            <td style="border: 1px solid #D1D5DB;">{{ $it['unit'] ?? '-' }}</td>
                            <td style="border: 1px solid #D1D5DB; text-align: right;">{{ $it['quantity_required'] ?? 0 }}</td>
                            <td style="border: 1px solid #D1D5DB; text-align: right;">{{ $it['purchased_order'] ?? 0 }}</td>
                            <td style="border: 1px solid #D1D5DB; text-align: right;">{{ $it['quantity_received'] ?? 0 }}</td>
                            <td style="border: 1px solid #D1D5DB; text-align: right; color: #DC2626;">{{ $it['quantity_cancelled'] ?? 0 }}</td>
                            <td style="border: 1px solid #D1D5DB; text-align: right; font-weight: bold;">{{ $it['quantity_balance'] ?? 0 }}</td>
                            <td style="border: 1px solid #D1D5DB;">{{ $it['status'] ?? 'Pending' }}</td>

                            @if($idx === 0)
                                <td rowspan="{{ $itemCount }}" style="vertical-align: top; border: 1px solid #D1D5DB;">{{ $poNos }}</td>
                                <td rowspan="{{ $itemCount }}" style="vertical-align: top; border: 1px solid #D1D5DB;">{{ $parties }}</td>
                                <td rowspan="{{ $itemCount }}" style="vertical-align: top; border: 1px solid #D1D5DB; text-align: right; font-weight: bold;">{{ $poAmount > 0 ? number_format($poAmount, 2) : '-' }}</td>
                            @endif
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td style="border: 1px solid #D1D5DB; font-weight: bold;">{{ $rec['indent_id'] }}</td>
                        <td style="border: 1px solid #D1D5DB;">{{ $rec['indent_date'] }}</td>
                        <td style="border: 1px solid #D1D5DB;">{{ $rec['department'] }}</td>
                        <td style="border: 1px solid #D1D5DB;">{{ $rec['project'] }}</td>
                        <td style="border: 1px solid #D1D5DB; font-weight: bold;">{{ $rec['status'] }}</td>
                        <td style="border: 1px solid #D1D5DB;">-</td>
                        <td style="border: 1px solid #D1D5DB;">-</td>
                        <td style="border: 1px solid #D1D5DB; text-align: right;">0</td>
                        <td style="border: 1px solid #D1D5DB; text-align: right;">0</td>
                        <td style="border: 1px solid #D1D5DB; text-align: right;">0</td>
                        <td style="border: 1px solid #D1D5DB; text-align: right;">0</td>
                        <td style="border: 1px solid #D1D5DB; text-align: right;">0</td>
                        <td style="border: 1px solid #D1D5DB;">{{ $rec['status'] }}</td>
                        <td style="border: 1px solid #D1D5DB;">{{ $poNos }}</td>
                        <td style="border: 1px solid #D1D5DB;">{{ $parties }}</td>
                        <td style="border: 1px solid #D1D5DB; text-align: right;">{{ $poAmount > 0 ? number_format($poAmount, 2) : '-' }}</td>
                    </tr>
                @endif
            @empty
                <tr>
                    <td colspan="16" style="text-align: center; padding: 15px; border: 1px solid #D1D5DB;">No indent records found matching the criteria.</td>
                </tr>
            @endforelse
        </tbody>
        @if(!empty($kpis))
            <tfoot>
                <tr>
                    <th colspan="7" style="background-color: #F1F5F9; font-weight: bold; border: 1px solid #000000; text-align: right;">OVERALL TOTALS:</th>
                    <th style="background-color: #F1F5F9; font-weight: bold; border: 1px solid #000000; text-align: right;">{{ $kpis['total_req'] ?? 0 }}</th>
                    <th style="background-color: #F1F5F9; font-weight: bold; border: 1px solid #000000; text-align: right;">{{ $kpis['total_po'] ?? 0 }}</th>
                    <th style="background-color: #F1F5F9; font-weight: bold; border: 1px solid #000000; text-align: right;">{{ $kpis['total_rec'] ?? 0 }}</th>
                    <th style="background-color: #F1F5F9; font-weight: bold; border: 1px solid #000000; text-align: right; color: #DC2626;">{{ $kpis['total_canc'] ?? 0 }}</th>
                    <th style="background-color: #F1F5F9; font-weight: bold; border: 1px solid #000000; text-align: right;">{{ $kpis['total_bal'] ?? 0 }}</th>
                    <th style="background-color: #F1F5F9; border: 1px solid #000000;"></th>
                    <th style="background-color: #F1F5F9; border: 1px solid #000000;"></th>
                    <th style="background-color: #F1F5F9; font-weight: bold; border: 1px solid #000000; text-align: right;">Total Value:</th>
                    <th style="background-color: #F1F5F9; font-weight: bold; border: 1px solid #000000; text-align: right;">₹{{ number_format($kpis['total_amount'] ?? 0, 2) }}</th>
                </tr>
            </tfoot>
        @endif
    </table>
</body>
</html>
