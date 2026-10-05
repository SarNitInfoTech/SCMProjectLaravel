@php
    $poStatusStr = is_object($po->status) ? $po->status->value : (string)($po->status ?? 'Open');
    $normStatus  = mb_strtolower(trim($poStatusStr));
    $isReadOnly  = in_array($normStatus, ['cancel', 'cancelled']);

    // Summary calculations
    $totalOrdered = 0;
    $totalReceived = 0;
    $totalCancelled = 0;

    foreach ($indentItems as $it) {
        $poQty = (float)($it['po_quantity'] ?? $it['quantity'] ?? $it['quantity_required'] ?? 1);
        $rec = (float)($it['quantity_received'] ?? 0);
        $canc = (float)($it['quantity_cancelled'] ?? 0);

        $totalOrdered += $poQty;
        $totalReceived += $rec;
        $totalCancelled += $canc;
    }

    $totalPending = max(0, $totalOrdered - ($totalReceived + $totalCancelled));
    $progressPct  = $totalOrdered > 0 ? min(100, round(($totalReceived / $totalOrdered) * 100)) : 0;

    // Status pill style config
    $statusPill = match($normStatus) {
        'completed'          => ['bg' => '#DCFCE7', 'color' => '#15803D', 'border' => '#BBF7D0', 'dot' => '#22C55E', 'label' => 'Completed'],
        'partially received' => ['bg' => '#EFF6FF', 'color' => '#1D4ED8', 'border' => '#BFDBFE', 'dot' => '#3B82F6', 'label' => 'Partially Received'],
        'reopened'           => ['bg' => '#FAF5FF', 'color' => '#7E22CE', 'border' => '#E9D5FF', 'dot' => '#A855F7', 'label' => 'Reopened'],
        'closed', 'close'    => ['bg' => '#F1F5F9', 'color' => '#475569', 'border' => '#CBD5E1', 'dot' => '#64748B', 'label' => 'Closed'],
        'cancel', 'cancelled'=> ['bg' => '#FEF2F2', 'color' => '#B91C1C', 'border' => '#FECACA', 'dot' => '#EF4444', 'label' => 'Cancelled'],
        default              => ['bg' => '#FFFBEB', 'color' => '#B45309', 'border' => '#FDE68A', 'dot' => '#F59E0B', 'label' => ucfirst($poStatusStr)],
    };

    $auditLogs = DB::table('po_audit_logs')->where('po_id', $po->id)->orderByDesc('created_at')->get();
@endphp

<div style="width: 100%; max-width: 100%; box-sizing: border-box; font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
    
    <!-- Top Page Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 14px;">
        <div style="display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 14px; background: #2563EB; color: #FFFFFF; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 12px rgba(37,99,235,0.25);">
                <svg style="width: 24px; height: 24px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <div>
                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 4px;">
                    <h1 style="font-size: 24px; font-weight: 800; color: #0F172A; margin: 0; letter-spacing: -0.5px;">File Invoice & Goods Receipt (PO #{{ $po->id }})</h1>
                    <span style="display: inline-flex; align-items: center; gap: 6px; padding: 4px 10px; border-radius: 20px; background: {{ $statusPill['bg'] }}; border: 1px solid {{ $statusPill['border'] }}; color: {{ $statusPill['color'] }}; font-size: 11px; font-weight: 700;">
                        <span style="width: 6px; height: 6px; border-radius: 50%; background: {{ $statusPill['dot'] }}; display: inline-block;"></span>
                        {{ $statusPill['label'] }}
                    </span>
                </div>
                <p style="font-size: 13px; color: #64748B; margin: 0; font-weight: 500;">Record vendor invoice, receiving dates, and manage received/remaining item quantities.</p>
            </div>
        </div>

        <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
            <a href="{{ route('po-register.edit', $po->id) }}" 
               style="background: #EFF6FF; border: 1px solid #BFDBFE; color: #2563EB; font-weight: 700; font-size: 12px; padding: 8px 16px; border-radius: 10px; text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
                <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                <span>Edit PO</span>
            </a>
            @if(!empty($po->indent_id))
            <a href="{{ route('indent.edit', $indent->id ?? $po->indent_id) }}" 
               style="background: #F8FAFC; border: 1px solid #CBD5E1; color: #475569; font-weight: 700; font-size: 12px; padding: 8px 16px; border-radius: 10px; text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
                <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                <span>Edit Indent</span>
            </a>
            <a href="{{ route('po-register.viewByIndent', ['indent_id' => $po->indent_id, 'department_id' => $po->department_id ?? '1']) }}" 
               style="background: #F8FAFC; border: 1px solid #CBD5E1; color: #475569; font-weight: 700; font-size: 12px; padding: 8px 16px; border-radius: 10px; text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
                <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                <span>Indent Summary</span>
            </a>
            @endif

            @if(in_array($normStatus, ['open', 'pending', 'partially received', 'reopened']))
                <button type="button" onclick="document.getElementById('closePoModal_{{ $po->id }}').style.display='flex'" 
                        style="background: #FEF2F2; border: 1px solid #FECACA; color: #DC2626; font-weight: 700; font-size: 12px; padding: 8px 16px; border-radius: 10px; cursor: pointer; display: inline-flex; align-items: center; gap: 6px;">
                    <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    <span>Close PO</span>
                </button>
            @elseif(in_array($normStatus, ['closed', 'close']))
                <form method="POST" action="{{ route('po-register.reopenPO', $po->id) }}" onsubmit="return confirm('Are you sure you want to reopen this PO?');" style="display: inline;">
                    @csrf
                    <button type="submit" style="background: #F3E8FF; border: 1px solid #E9D5FF; color: #7E22CE; font-weight: 700; font-size: 12px; padding: 8px 16px; border-radius: 10px; cursor: pointer; display: inline-flex; align-items: center; gap: 6px;">
                        <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>
                        <span>Reopen PO</span>
                    </button>
                </form>
            @endif
        </div>
    </div>

    <!-- Notices if special status -->
    @if($isReadOnly)
        <div style="background: #FEF2F2; border-left: 4px solid #EF4444; border-radius: 10px; padding: 14px 18px; margin-bottom: 20px; font-size: 13px; color: #991B1B;">
            <strong>Notice:</strong> This Purchase Order is <strong>Cancelled</strong>. No further goods receipts or invoice updates can be performed.
        </div>
    @elseif(in_array($normStatus, ['closed', 'close']))
        <div style="background: #FFFBEB; border-left: 4px solid #F59E0B; border-radius: 10px; padding: 14px 18px; margin-bottom: 20px; font-size: 13px; color: #92400E;">
            <strong>Notice:</strong> This Purchase Order is currently <strong>Closed</strong>{{ !empty($po->close_reason) ? ' (Reason: ' . $po->close_reason . ')' : '' }}. You can record invoice details or item receipts below.
        </div>
    @elseif($normStatus === 'completed')
        <div style="background: #F0FDF4; border-left: 4px solid #22C55E; border-radius: 10px; padding: 14px 18px; margin-bottom: 20px; font-size: 13px; color: #166534;">
            <strong>Notice:</strong> All ordered items have been 100% received. You can review or update the vendor invoice details below.
        </div>
    @endif

    <!-- Metrics Cards & Progress Header -->
    <div style="background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.03); padding: 24px; margin-bottom: 24px;">
        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 18px; margin-bottom: 20px;">
            <!-- Total Ordered -->
            <div style="background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 12px; padding: 16px 20px;">
                <span style="font-size: 11px; font-weight: 700; color: #64748B; text-transform: uppercase; letter-spacing: 0.5px; display: block; margin-bottom: 4px;">ORDERED QUANTITY</span>
                <span style="font-size: 26px; font-weight: 800; color: #0F172A;">{{ $totalOrdered }}</span>
            </div>

            <!-- Total Received -->
            <div style="background: #EFF6FF; border: 1px solid #BFDBFE; border-radius: 12px; padding: 16px 20px;">
                <span style="font-size: 11px; font-weight: 700; color: #1D4ED8; text-transform: uppercase; letter-spacing: 0.5px; display: block; margin-bottom: 4px;">RECEIVED QUANTITY</span>
                <span style="font-size: 26px; font-weight: 800; color: #2563EB;">{{ $totalReceived }}</span>
            </div>

            <!-- Total Pending -->
            <div style="background: #FFF7ED; border: 1px solid #FED7AA; border-radius: 12px; padding: 16px 20px;">
                <span style="font-size: 11px; font-weight: 700; color: #C2410C; text-transform: uppercase; letter-spacing: 0.5px; display: block; margin-bottom: 4px;">PENDING QUANTITY</span>
                <span style="font-size: 26px; font-weight: 800; color: #EA580C;">{{ $totalPending }}</span>
            </div>

            <!-- Completion Rate -->
            <div style="background: #ECFDF5; border: 1px solid #A7F3D0; border-radius: 12px; padding: 16px 20px;">
                <span style="font-size: 11px; font-weight: 700; color: #047857; text-transform: uppercase; letter-spacing: 0.5px; display: block; margin-bottom: 4px;">RECEIPT COMPLETION</span>
                <span style="font-size: 26px; font-weight: 800; color: #10B981;">{{ $progressPct }}%</span>
            </div>
        </div>

        <!-- Progress Bar -->
        <div>
            <div style="display: flex; justify-content: space-between; font-size: 12px; font-weight: 700; color: #475569; margin-bottom: 8px;">
                <span>Goods Receipt Completion</span>
                <span>{{ $totalReceived }} of {{ $totalOrdered }} Received ({{ $progressPct }}%)</span>
            </div>
            <div style="width: 100%; height: 10px; background: #E2E8F0; border-radius: 999px; overflow: hidden;">
                <div style="width: {{ $progressPct }}%; height: 100%; background: #10B981; border-radius: 999px; transition: width 0.5s ease-in-out;"></div>
            </div>
        </div>
    </div>

    <!-- PO Symmetrical Metadata Section (Read-Only Overview) -->
    <div style="background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.03); padding: 24px; margin-bottom: 24px;">
        <h3 style="font-size: 15px; font-weight: 800; color: #0F172A; margin: 0 0 18px 0; display: flex; align-items: center; gap: 8px;">
            <svg style="width: 18px; height: 18px; color: #2563EB;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span>Purchase Order Information</span>
        </h3>

        <!-- Row 1: 4 Columns -->
        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 18px;">
            <!-- Indent ID -->
            <div>
                <label style="display: block; font-size: 11px; font-weight: 800; color: #64748B; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px;">INDENT ID</label>
                <input type="text" value="{{ $po->indent_id ?? '-' }}" readonly disabled
                       style="width: 100%; padding: 10px 14px; background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 10px; font-size: 13px; font-weight: 700; color: #0F172A; box-sizing: border-box;">
            </div>

            <!-- Department -->
            <div>
                <label style="display: block; font-size: 11px; font-weight: 800; color: #64748B; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px;">DEPARTMENT</label>
                <input type="text" value="{{ $department_name ?? $po->department_id ?? '-' }}" readonly disabled
                       style="width: 100%; padding: 10px 14px; background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 10px; font-size: 13px; font-weight: 700; color: #0F172A; box-sizing: border-box;">
            </div>

            <!-- Party / Vendor -->
            <div>
                <label style="display: block; font-size: 11px; font-weight: 800; color: #64748B; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px;">PARTY / VENDOR</label>
                <input type="text" value="{{ $po->party_name ?? 'N/A' }}" readonly disabled
                       style="width: 100%; padding: 10px 14px; background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 10px; font-size: 13px; font-weight: 700; color: #0F172A; box-sizing: border-box;">
            </div>

            <!-- Requirement Type -->
            <div>
                <label style="display: block; font-size: 11px; font-weight: 800; color: #64748B; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px;">REQUIREMENT TYPE</label>
                <input type="text" value="{{ $po->is_mandatory ?? 'Mandatory' }}" readonly disabled
                       style="width: 100%; padding: 10px 14px; background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 10px; font-size: 13px; font-weight: 700; color: #0F172A; box-sizing: border-box;">
            </div>
        </div>

        <!-- Row 2: 4 Columns -->
        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px;">
            <!-- PO Date -->
            <div>
                <label style="display: block; font-size: 11px; font-weight: 800; color: #64748B; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px;">PO DATE</label>
                <input type="text" value="{{ $po->po_date ? date('d-m-Y', strtotime($po->po_date)) : '-' }}" readonly disabled
                       style="width: 100%; padding: 10px 14px; background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 10px; font-size: 13px; font-weight: 700; color: #0F172A; box-sizing: border-box;">
            </div>

            <!-- PR / WO No. -->
            <div>
                <label style="display: block; font-size: 11px; font-weight: 800; color: #64748B; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px;">PR / WO NO.</label>
                <input type="text" value="{{ $po->po_wo_no ?: '-' }}" readonly disabled
                       style="width: 100%; padding: 10px 14px; background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 10px; font-size: 13px; font-weight: 700; color: #0F172A; box-sizing: border-box;">
            </div>

            <!-- PO Amount -->
            <div>
                <label style="display: block; font-size: 11px; font-weight: 800; color: #64748B; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px;">PO AMOUNT</label>
                <input type="text" value="₹{{ number_format((float)($po->po_amount ?? 0), 2) }}" readonly disabled
                       style="width: 100%; padding: 10px 14px; background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 10px; font-size: 13px; font-weight: 800; color: #0F172A; box-sizing: border-box;">
            </div>

            <!-- Expected Delivery Date -->
            <div>
                <label style="display: block; font-size: 11px; font-weight: 800; color: #64748B; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px;">EXPECTED DATE</label>
                <input type="text" value="{{ $po->expected_date ? date('d-m-Y', strtotime($po->expected_date)) : '-' }}" readonly disabled
                       style="width: 100%; padding: 10px 14px; background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 10px; font-size: 13px; font-weight: 700; color: #0F172A; box-sizing: border-box;">
            </div>
        </div>
    </div>

    <!-- Active Form: Invoice Details & Goods Receipt -->
    <form method="POST" action="{{ route('po-register.updateInvoice', $po->id) }}" style="display: flex; flex-direction: column; gap: 24px;">
        @csrf
        @method('PUT')

        <!-- Invoice & Delivery Information Card -->
        <div style="background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.03); padding: 24px;">
            <h3 style="font-size: 15px; font-weight: 800; color: #0F172A; margin: 0 0 18px 0; display: flex; align-items: center; gap: 8px;">
                <svg style="width: 18px; height: 18px; color: #2563EB;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span>Invoice & Delivery Information</span>
            </h3>

            <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px;">
                <!-- Invoice Date -->
                <div>
                    <label for="invoice_date" style="display: block; font-size: 12px; font-weight: 800; color: #475569; margin-bottom: 8px;">
                        Invoice Date
                    </label>
                    <input type="date" name="invoice_date" id="invoice_date" 
                           value="{{ old('invoice_date', $po->invoice_date) }}" 
                           {{ $isReadOnly ? 'disabled' : '' }}
                           style="width: 100%; padding: 10px 14px; background: #FFFFFF; border: 1px solid #CBD5E1; border-radius: 10px; font-size: 13px; font-weight: 600; color: #0F172A; outline: none; box-sizing: border-box;">
                </div>

                <!-- Receiving Date -->
                <div>
                    <label for="receiving_date" style="display: block; font-size: 12px; font-weight: 800; color: #475569; margin-bottom: 8px;">
                        Receiving Date
                    </label>
                    <input type="date" name="receiving_date" id="receiving_date" 
                           value="{{ old('receiving_date', $po->receiving_date) }}" 
                           {{ $isReadOnly ? 'disabled' : '' }}
                           style="width: 100%; padding: 10px 14px; background: #FFFFFF; border: 1px solid #CBD5E1; border-radius: 10px; font-size: 13px; font-weight: 600; color: #0F172A; outline: none; box-sizing: border-box;">
                </div>

                <!-- Delay in Days -->
                <div>
                    <label for="delay_in_days" style="display: block; font-size: 12px; font-weight: 800; color: #475569; margin-bottom: 8px;">
                        Delay (Days)
                        @if(!empty($po->expected_date))
                            <span style="font-size: 11px; font-weight: normal; color: #94A3B8;">(exp: {{ date('d-m-Y', strtotime($po->expected_date)) }})</span>
                        @endif
                    </label>
                    <input type="number" name="delay_in_days" id="delay_in_days" min="0" step="1"
                           value="{{ old('delay_in_days', $po->delay_in_days) }}" 
                           {{ $isReadOnly ? 'disabled' : '' }}
                           placeholder="0"
                           style="width: 100%; padding: 10px 14px; background: #FFFFFF; border: 1px solid #CBD5E1; border-radius: 10px; font-size: 13px; font-weight: 600; color: #0F172A; outline: none; box-sizing: border-box;">
                </div>

                <!-- Invoice / Bill No. -->
                <div>
                    <label for="store_indent_no" style="display: block; font-size: 12px; font-weight: 800; color: #475569; margin-bottom: 8px;">
                        Invoice / Bill No.
                    </label>
                    <input type="text" name="store_indent_no" id="store_indent_no" 
                           value="{{ old('store_indent_no', $po->store_indent_no ?: $po->invoice) }}" 
                           placeholder="e.g. INV-2026-0089"
                           {{ $isReadOnly ? 'disabled' : '' }}
                           style="width: 100%; padding: 10px 14px; background: #FFFFFF; border: 1px solid #CBD5E1; border-radius: 10px; font-size: 13px; font-weight: 600; color: #0F172A; outline: none; box-sizing: border-box;">
                </div>
            </div>
        </div>

        <!-- Item Receipts & Inventory Balance Breakdown Table -->
        @if(!empty($indentItems) && count($indentItems) > 0)
        <div style="background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.03); padding: 24px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px; flex-wrap: wrap; gap: 10px;">
                <div>
                    <h3 style="font-size: 15px; font-weight: 800; color: #0F172A; margin: 0 0 2px 0; display: flex; align-items: center; gap: 8px;">
                        <svg style="width: 18px; height: 18px; color: #2563EB;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        <span>Goods Receipt & Inventory Balance</span>
                    </h3>
                    <p style="font-size: 12px; color: #64748B; margin: 0;">Enter actual received quantities and any cancelled units. Remaining balances are calculated live.</p>
                </div>
            </div>

            <div style="overflow-x: auto; border: 1px solid #E2E8F0; border-radius: 12px;">
                <table style="width: 100%; border-collapse: separate; border-spacing: 0; font-size: 13px;">
                    <thead>
                        <tr style="background: #F8FAFC; border-bottom: 1px solid #E2E8F0;">
                            <th style="padding: 14px 16px; text-align: left; font-size: 11px; font-weight: 800; color: #475569; text-transform: uppercase; letter-spacing: 0.5px;">ITEM DESCRIPTION</th>
                            <th style="padding: 14px 16px; text-align: center; font-size: 11px; font-weight: 800; color: #475569; text-transform: uppercase; letter-spacing: 0.5px;">PO ORDERED</th>
                            <th style="padding: 14px 16px; text-align: center; font-size: 11px; font-weight: 800; color: #475569; text-transform: uppercase; letter-spacing: 0.5px;">QTY RECEIVED</th>
                            <th style="padding: 14px 16px; text-align: center; font-size: 11px; font-weight: 800; color: #475569; text-transform: uppercase; letter-spacing: 0.5px;">QTY CANCELLED</th>
                            <th style="padding: 14px 16px; text-align: center; font-size: 11px; font-weight: 800; color: #475569; text-transform: uppercase; letter-spacing: 0.5px;">REMAINING BALANCE</th>
                            <th style="padding: 14px 16px; text-align: center; font-size: 11px; font-weight: 800; color: #475569; text-transform: uppercase; letter-spacing: 0.5px;">ITEM STATUS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($indentItems as $idx => $item)
                            @php
                                $req   = (float)($item['quantity_required'] ?? 0);
                                $poQty = (float)($item['po_quantity'] ?? $item['quantity'] ?? $req);
                                $rec   = (float)($item['quantity_received'] ?? 0);
                                $canc  = (float)($item['quantity_cancelled'] ?? 0);
                                $bal   = round(max(0, $poQty - ($rec + $canc)), 4);
                                $unit  = $item['unit'] ?? '';

                                $itemSt = 'Pending';
                                if ($canc >= $poQty && $poQty > 0) {
                                    $itemSt = 'Cancelled';
                                } elseif ($rec >= $poQty && $poQty > 0) {
                                    $itemSt = 'Completed';
                                } elseif ($rec > 0) {
                                    $itemSt = 'Partially Received';
                                }
                            @endphp
                            <tr class="item-qty-row" style="border-bottom: 1px solid #E2E8F0; background: #FFFFFF;">
                                <!-- Description -->
                                <td style="padding: 14px 16px; vertical-align: middle;">
                                    <div style="font-weight: 700; color: #0F172A; font-size: 14px; margin-bottom: 2px;">
                                        {{ $item['description'] ?? 'Item' }}
                                    </div>
                                    @if(!empty($unit))
                                        <span style="font-size: 11px; font-weight: 600; color: #64748B; background: #F1F5F9; padding: 2px 8px; border-radius: 6px; display: inline-block;">Unit: {{ $unit }}</span>
                                    @endif
                                    <input type="hidden" name="items[{{ $idx }}][description]" value="{{ $item['description'] ?? '' }}">
                                    <input type="hidden" name="items[{{ $idx }}][unit]" value="{{ $unit }}">
                                    <input type="hidden" name="items[{{ $idx }}][required]" value="{{ $poQty }}" class="js-qty-req">
                                </td>

                                <!-- PO Ordered -->
                                <td style="padding: 14px 16px; text-align: center; vertical-align: middle;">
                                    <span style="font-size: 15px; font-weight: 800; color: #0F172A;">{{ $poQty }}</span>
                                    @if($req > 0 && $req !== $poQty)
                                        <span style="font-size: 11px; color: #94A3B8; display: block; font-weight: 500;">Indent: {{ $req }}</span>
                                    @endif
                                </td>

                                <!-- Qty Received Input -->
                                <td style="padding: 14px 16px; text-align: center; vertical-align: middle; width: 140px;">
                                    <input type="number" 
                                           name="items[{{ $idx }}][received]" 
                                           value="{{ $rec }}" 
                                           min="0" 
                                           step="any"
                                           max="{{ $poQty > 0 ? $poQty : 999999 }}"
                                           class="js-qty-rec"
                                           {{ $isReadOnly ? 'disabled' : '' }}
                                           style="width: 100%; padding: 8px 12px; background: #FFFFFF; border: 1px solid #CBD5E1; border-radius: 8px; font-size: 13px; font-weight: 700; color: #2563EB; text-align: center; outline: none; box-sizing: border-box;">
                                </td>

                                <!-- Qty Cancelled Input -->
                                <td style="padding: 14px 16px; text-align: center; vertical-align: middle; width: 140px;">
                                    <input type="number" 
                                           name="items[{{ $idx }}][cancelled]" 
                                           value="{{ $canc }}" 
                                           min="0" 
                                           step="any"
                                           max="{{ $poQty > 0 ? $poQty : 999999 }}"
                                           class="js-qty-canc"
                                           placeholder="0"
                                           {{ $isReadOnly ? 'disabled' : '' }}
                                           style="width: 100%; padding: 8px 12px; background: #FFFFFF; border: 1px solid #CBD5E1; border-radius: 8px; font-size: 13px; font-weight: 700; color: #DC2626; text-align: center; outline: none; box-sizing: border-box;">
                                </td>

                                <!-- Balance -->
                                <td style="padding: 14px 16px; text-align: center; vertical-align: middle;">
                                    <span class="js-qty-bal" style="font-size: 14px; font-weight: 800; color: {{ $bal > 0 ? '#EA580C' : '#16A34A' }};">
                                        @if($bal > 0)
                                            {{ $bal }} remaining
                                        @else
                                            0 (Fully Accounted)
                                        @endif
                                    </span>
                                </td>

                                <!-- Status Badge -->
                                <td style="padding: 14px 16px; text-align: center; vertical-align: middle;">
                                    <span class="js-item-badge" style="display: inline-flex; align-items: center; gap: 4px; padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; 
                                        @if($itemSt === 'Completed') background: #DCFCE7; color: #15803D; border: 1px solid #BBF7D0;
                                        @elseif($itemSt === 'Partially Received') background: #EFF6FF; color: #1D4ED8; border: 1px solid #BFDBFE;
                                        @elseif($itemSt === 'Cancelled') background: #FEF2F2; color: #B91C1C; border: 1px solid #FECACA;
                                        @else background: #FFFBEB; color: #B45309; border: 1px solid #FDE68A;
                                        @endif">
                                        {{ $itemSt }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        <!-- Action Submit Buttons -->
        @if(!$isReadOnly)
        <div style="display: flex; justify-content: flex-end; align-items: center; gap: 12px; margin-top: 4px;">
            <a href="{{ route('po-register.index') }}" 
               style="padding: 12px 24px; border-radius: 10px; background: #F8FAFC; border: 1px solid #CBD5E1; color: #475569; font-weight: 700; font-size: 14px; text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
                Cancel
            </a>
            <button type="submit" 
                    style="padding: 12px 28px; border-radius: 10px; background: #2563EB; color: #FFFFFF; font-weight: 700; font-size: 14px; border: none; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 4px 12px rgba(37,99,235,0.3);">
                <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span>Save & File Invoice Receipt</span>
            </button>
        </div>
        @endif
    </form>

    <!-- PO Lifecycle Audit Trail Section -->
    <div style="background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.03); padding: 24px; margin-top: 24px;">
        <h3 style="font-size: 15px; font-weight: 800; color: #0F172A; margin: 0 0 16px 0; display: flex; align-items: center; gap: 8px;">
            <svg style="width: 18px; height: 18px; color: #64748B;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span>PO Lifecycle & Activity Audit Trail</span>
        </h3>

        @if(count($auditLogs) > 0)
            <div style="overflow-x: auto; border: 1px solid #E2E8F0; border-radius: 12px;">
                <table style="width: 100%; border-collapse: separate; border-spacing: 0; font-size: 12px;">
                    <thead>
                        <tr style="background: #F8FAFC; border-bottom: 1px solid #E2E8F0;">
                            <th style="padding: 12px 14px; text-align: left; font-size: 11px; font-weight: 800; color: #475569; text-transform: uppercase;">DATE & TIME</th>
                            <th style="padding: 12px 14px; text-align: left; font-size: 11px; font-weight: 800; color: #475569; text-transform: uppercase;">USER</th>
                            <th style="padding: 12px 14px; text-align: left; font-size: 11px; font-weight: 800; color: #475569; text-transform: uppercase;">ACTION</th>
                            <th style="padding: 12px 14px; text-align: left; font-size: 11px; font-weight: 800; color: #475569; text-transform: uppercase;">PREV STATUS</th>
                            <th style="padding: 12px 14px; text-align: left; font-size: 11px; font-weight: 800; color: #475569; text-transform: uppercase;">NEW STATUS</th>
                            <th style="padding: 12px 14px; text-align: left; font-size: 11px; font-weight: 800; color: #475569; text-transform: uppercase;">DETAILS / REASON</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($auditLogs as $log)
                            <tr style="border-bottom: 1px solid #F1F5F9; background: #FFFFFF;">
                                <td style="padding: 12px 14px; font-family: monospace; color: #64748B;">{{ $log->created_at }}</td>
                                <td style="padding: 12px 14px; font-weight: 700; color: #0F172A;">{{ $log->user_name ?? 'System' }}</td>
                                <td style="padding: 12px 14px; font-weight: 600; color: #2563EB; text-transform: capitalize;">{{ str_replace('_', ' ', $log->action) }}</td>
                                <td style="padding: 12px 14px; color: #64748B;">{{ $log->previous_status ?? '-' }}</td>
                                <td style="padding: 12px 14px; font-weight: 700; color: #0F172A;">{{ $log->new_status ?? '-' }}</td>
                                <td style="padding: 12px 14px; color: #475569;">{{ $log->reason ?? $log->details ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p style="font-size: 13px; color: #94A3B8; margin: 0; font-style: italic;">No audit log entries recorded yet for this Purchase Order.</p>
        @endif
    </div>

</div>

<!-- Close PO Modal -->
<div id="closePoModal_{{ $po->id }}" style="display: none; position: fixed; inset: 0; z-index: 9999; background: rgba(15, 23, 42, 0.6); align-items: center; justify-content: center; backdrop-filter: blur(2px);">
    <div style="background: #FFFFFF; border-radius: 16px; padding: 24px; width: 100%; max-width: 440px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1); border: 1px solid #E2E8F0; font-family: ui-sans-serif, system-ui, sans-serif;">
        <h3 style="font-size: 18px; font-weight: 800; color: #0F172A; margin: 0 0 8px 0;">Close Purchase Order #{{ $po->id }}</h3>
        <p style="font-size: 13px; color: #64748B; margin: 0 0 16px 0; line-height: 1.5;">Are you sure you want to close this Purchase Order? You can provide an optional closing reason below.</p>
        
        <form method="POST" action="{{ route('po-register.closePO', $po->id) }}">
            @csrf
            <div style="margin-bottom: 20px;">
                <label for="close_reason" style="display: block; font-size: 12px; font-weight: 800; color: #475569; margin-bottom: 6px;">Closing Reason (Optional)</label>
                <textarea name="close_reason" id="close_reason" rows="3" placeholder="Enter reason for closing this PO..."
                          style="width: 100%; padding: 10px 14px; background: #FFFFFF; border: 1px solid #CBD5E1; border-radius: 10px; font-size: 13px; font-weight: 500; color: #0F172A; outline: none; box-sizing: border-box;"></textarea>
            </div>
            <div style="display: flex; justify-content: flex-end; gap: 10px;">
                <button type="button" onclick="document.getElementById('closePoModal_{{ $po->id }}').style.display='none'"
                        style="padding: 10px 18px; border-radius: 10px; background: #F1F5F9; border: 1px solid #CBD5E1; color: #475569; font-weight: 700; font-size: 13px; cursor: pointer;">
                    Cancel
                </button>
                <button type="submit" 
                        style="padding: 10px 20px; border-radius: 10px; background: #DC2626; color: #FFFFFF; font-weight: 700; font-size: 13px; border: none; cursor: pointer; box-shadow: 0 4px 12px rgba(220,38,38,0.25);">
                    Confirm Close PO
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const expected = '{{ $po->expected_date ?? '' }}';
    const expectedDate = expected ? new Date(expected) : null;

    const receivingEl = document.getElementById('receiving_date');
    const delayEl     = document.getElementById('delay_in_days');

    function recalcDelay() {
        if (!expectedDate || !receivingEl?.value) return;
        const recv = new Date(receivingEl.value);
        if (isNaN(recv)) return;
        const diff = Math.round((recv - expectedDate) / (1000 * 60 * 60 * 24));
        if (delayEl) delayEl.value = Math.max(0, diff);
    }

    receivingEl?.addEventListener('change', recalcDelay);

    // Quantity receiving and remaining balance live updater
    document.querySelectorAll('.item-qty-row').forEach(row => {
        const reqEl   = row.querySelector('.js-qty-req');
        const recEl   = row.querySelector('.js-qty-rec');
        const cancEl  = row.querySelector('.js-qty-canc');
        const balEl   = row.querySelector('.js-qty-bal');
        const badgeEl = row.querySelector('.js-item-badge');

        function updateBalance() {
            const req = parseFloat(reqEl?.value || 0);
            let rec = parseFloat(recEl?.value || 0);
            let canc = parseFloat(cancEl?.value || 0);

            if (isNaN(rec) || rec < 0) rec = 0;
            if (isNaN(canc) || canc < 0) canc = 0;

            // Clamp received quantity so it cannot exceed required quantity
            if (req > 0 && rec > req) {
                rec = req;
                if (recEl) recEl.value = req;
            }

            // Clamp cancelled quantity so received + cancelled cannot exceed required quantity
            if (req > 0 && (rec + canc) > req) {
                canc = parseFloat((req - rec).toFixed(4));
                if (cancEl) cancEl.value = canc;
            }

            const bal = parseFloat(Math.max(0, req - (rec + canc)).toFixed(4));

            if (balEl) {
                if (bal > 0.0001) {
                    balEl.textContent = bal + ' remaining';
                    balEl.style.color = '#EA580C';
                } else {
                    balEl.textContent = '0 (' + (canc > 0.0001 ? canc + ' Cancelled' : 'Fully Accounted') + ')';
                    balEl.style.color = '#16A34A';
                }
            }

            if (badgeEl) {
                if (canc >= req && req > 0) {
                    badgeEl.textContent = 'Cancelled';
                    badgeEl.style.background = '#FEF2F2';
                    badgeEl.style.color = '#B91C1C';
                    badgeEl.style.border = '1px solid #FECACA';
                } else if (rec >= req && req > 0) {
                    badgeEl.textContent = 'Completed';
                    badgeEl.style.background = '#DCFCE7';
                    badgeEl.style.color = '#15803D';
                    badgeEl.style.border = '1px solid #BBF7D0';
                } else if (rec > 0) {
                    badgeEl.textContent = 'Partially Received';
                    badgeEl.style.background = '#EFF6FF';
                    badgeEl.style.color = '#1D4ED8';
                    badgeEl.style.border = '1px solid #BFDBFE';
                } else {
                    badgeEl.textContent = 'Pending';
                    badgeEl.style.background = '#FFFBEB';
                    badgeEl.style.color = '#B45309';
                    badgeEl.style.border = '1px solid #FDE68A';
                }
            }
        }

        recEl?.addEventListener('input', updateBalance);
        cancEl?.addEventListener('input', updateBalance);
        recEl?.addEventListener('change', updateBalance);
        cancEl?.addEventListener('change', updateBalance);
    });
});
</script>
