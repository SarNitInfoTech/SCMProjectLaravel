<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>All Indents Report</title>
    <style>
        @page {
            margin: 25px 25px 35px 25px;
        }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 9px;
            color: #1E293B;
            line-height: 1.3;
        }
        .header {
            margin-bottom: 15px;
            border-bottom: 2px solid #2563EB;
            padding-bottom: 8px;
        }
        .header h1 {
            font-size: 16px;
            color: #0F172A;
            margin: 0 0 4px 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .header .meta {
            font-size: 8px;
            color: #64748B;
        }
        .kpi-container {
            width: 100%;
            margin-bottom: 14px;
        }
        .kpi-table {
            width: 100%;
            border-collapse: collapse;
        }
        .kpi-box {
            background-color: #F8FAFC;
            border: 1px solid #E2E8F0;
            padding: 6px 10px;
            text-align: center;
        }
        .kpi-box .val {
            font-size: 12px;
            font-weight: bold;
            color: #0F172A;
        }
        .kpi-box .lbl {
            font-size: 7.5px;
            color: #64748B;
            text-transform: uppercase;
            font-weight: bold;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8px;
            page-break-inside: auto;
        }
        table.data-table tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }
        table.data-table th {
            background-color: #0F172A;
            color: #FFFFFF;
            font-weight: bold;
            text-align: left;
            padding: 5px 6px;
            font-size: 7.5px;
            text-transform: uppercase;
            border: 1px solid #0F172A;
        }
        table.data-table td {
            padding: 5px 6px;
            border: 1px solid #E2E8F0;
            vertical-align: middle;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 7.5px;
            font-weight: bold;
        }
        .badge-pending { background: #FEF3C7; color: #92400E; }
        .badge-received { background: #F3E8FF; color: #6B21A8; }
        .badge-completed { background: #DCFCE7; color: #166534; }
        .badge-cancelled { background: #FEE2E2; color: #991B1B; }
        .footer {
            position: fixed;
            bottom: -20px;
            left: 0px;
            right: 0px;
            height: 20px;
            font-size: 7.5px;
            color: #94A3B8;
            text-align: right;
            border-top: 1px solid #E2E8F0;
            padding-top: 4px;
        }
    </style>
</head>
<body>
    <div class="header">
        <table style="width: 100%;">
            <tr>
                <td>
                    <h1>NITRA - ALL INDENTS REPORT</h1>
                    <div class="meta">
                        Generated on: <strong>{{ now()->format('d-M-Y H:i A') }}</strong>
                        @if(!empty($filterText))
                            &nbsp;|&nbsp; Filters: <em>{{ $filterText }}</em>
                        @endif
                    </div>
                </td>
                <td style="text-align: right; vertical-align: top;">
                    <div style="font-size: 11px; font-weight: bold; color: #2563EB;">NITRA RESEARCH & ACADEMIC</div>
                    <div style="font-size: 8px; color: #64748B;">Management Information System</div>
                </td>
            </tr>
        </table>
    </div>

    @if(!empty($kpis))
        <div class="kpi-container">
            <table class="kpi-table">
                <tr>
                    <td class="kpi-box">
                        <div class="val">{{ $kpis['total_indents'] ?? 0 }}</div>
                        <div class="lbl">Total Indents</div>
                    </td>
                    <td class="kpi-box">
                        <div class="val">{{ $kpis['total_req'] ?? 0 }}</div>
                        <div class="lbl">Total Requested Qty</div>
                    </td>
                    <td class="kpi-box">
                        <div class="val">{{ $kpis['total_po'] ?? 0 }}</div>
                        <div class="lbl">Total PO Qty</div>
                    </td>
                    <td class="kpi-box">
                        <div class="val" style="color: #16A34A;">{{ $kpis['total_rec'] ?? 0 }}</div>
                        <div class="lbl">Total Received Qty</div>
                    </td>
                    <td class="kpi-box">
                        <div class="val" style="color: #DC2626;">{{ $kpis['total_canc'] ?? 0 }}</div>
                        <div class="lbl">Total Cancelled Qty</div>
                    </td>
                    <td class="kpi-box">
                        <div class="val" style="color: #D97706;">{{ $kpis['total_bal'] ?? 0 }}</div>
                        <div class="lbl">Remaining Balance</div>
                    </td>
                    <td class="kpi-box">
                        <div class="val" style="color: #2563EB;">₹{{ number_format($kpis['total_amount'] ?? 0, 2) }}</div>
                        <div class="lbl">Total PO Value</div>
                    </td>
                </tr>
            </table>
        </div>
    @endif

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 55px;">Indent ID</th>
                <th style="width: 55px;">Date</th>
                <th style="width: 80px;">Department</th>
                <th style="width: 75px;">Project</th>
                <th>Items & Fulfillment (Req / PO / Rec / Canc / Bal)</th>
                <th style="width: 55px; text-align: center;">Status</th>
                <th style="width: 100px;">Linked PO / Vendor</th>
                <th style="width: 60px; text-align: right;">Amount (₹)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($records as $rec)
                @php
                    $items = $rec['items'] ?? [];
                    $pos = $rec['pos'] ?? [];
                    $poDetails = [];
                    foreach($pos as $p) {
                        $pNo = $p->po_wo_no ?: ('PO #' . $p->id);
                        $pParty = $p->party_name ?: '';
                        $poDetails[] = $pParty ? "$pNo ($pParty)" : $pNo;
                    }
                    $poAmount = collect($pos)->sum(fn($p) => (float)($p->po_amount ?? 0));
                    
                    $stClass = match(strtolower($rec['status'] ?? '')) {
                        'close', 'completed' => 'badge-completed',
                        'cancel', 'cancelled' => 'badge-cancelled',
                        'partially received' => 'badge-received',
                        default => 'badge-pending',
                    };
                @endphp
                <tr>
                    <td style="font-weight: bold; color: #0F172A;">{{ $rec['indent_id'] }}</td>
                    <td style="white-space: nowrap;">{{ $rec['indent_date'] }}</td>
                    <td>{{ $rec['department'] }}</td>
                    <td>{{ $rec['project'] }}</td>
                    <td>
                        @if(!empty($items))
                            @foreach($items as $it)
                                <div style="margin-bottom: 3px; padding-bottom: 2px; border-bottom: 1px dashed #F1F5F9;">
                                    <strong>{{ $it['description'] }}</strong>
                                    @if(!empty($it['unit']))
                                        <span style="color: #64748B;">({{ $it['unit'] }})</span>
                                    @endif
                                    - 
                                    <span style="color: #334155;">Req: <strong>{{ $it['quantity_required'] }}</strong></span> | 
                                    <span style="color: #2563EB;">PO: {{ $it['purchased_order'] }}</span> | 
                                    <span style="color: #16A34A;">Rec: {{ $it['quantity_received'] }}</span>
                                    @if(($it['quantity_cancelled'] ?? 0) > 0)
                                        | <span style="color: #DC2626; font-weight: bold;">Canc: {{ $it['quantity_cancelled'] }}</span>
                                    @endif
                                    | <span style="color: #D97706; font-weight: bold;">Bal: {{ $it['quantity_balance'] }}</span>
                                    &nbsp;
                                    <span style="font-size: 7px; color: #64748B;">[{{ $it['status'] }}]</span>
                                </div>
                            @endforeach
                        @else
                            <span style="color: #94A3B8;">-</span>
                        @endif
                    </td>
                    <td style="text-align: center;">
                        <span class="badge {{ $stClass }}">{{ $rec['status'] }}</span>
                    </td>
                    <td>
                        @if(!empty($poDetails))
                            {!! implode('<br>', $poDetails) !!}
                        @else
                            <span style="color: #94A3B8;">No PO Yet</span>
                        @endif
                    </td>
                    <td style="text-align: right; font-weight: bold;">
                        {{ $poAmount > 0 ? number_format($poAmount, 2) : '-' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center; padding: 20px; color: #64748B;">
                        No indent records found.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        NITRA Information Management System &bull; Confidential &bull; All Indents Report
    </div>
</body>
</html>
