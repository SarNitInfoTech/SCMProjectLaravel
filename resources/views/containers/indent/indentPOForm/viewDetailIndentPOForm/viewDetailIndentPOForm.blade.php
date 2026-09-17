<div style="max-w-7xl; margin: 0 auto; padding: 24px; font-family: inherit;">

    @php
        $indentId    = $indent->indent_id ?? $indent_id;
        $deptName    = $indent->department_name ?? $indent->indent_department ?? $department_id;
        $projectName = $indent->indent_project ?? '-';
        if (empty($projectName) || $projectName === '0') {
            $projectName = '-';
        }
        $indentDate  = !empty($indent->indent_date) ? date('d M Y', strtotime($indent->indent_date)) : (!empty($po->indent_date) ? date('d M Y', strtotime($po->indent_date)) : '-');

        // Extract indent items from indent_registers JSON
        $indentItems = [];
        if (!empty($indent->items_description)) {
            $indentItems = json_decode($indent->items_description, true) ?? [];
        } elseif (!empty($po->items_description)) {
            $indentItems = json_decode($po->items_description, true) ?? [];
        }

        // Helper function for custom item icon boxes matching mockup
        if (!function_exists('getMockupItemIcon')) {
            function getMockupItemIcon($desc, $idx) {
                $d = mb_strtolower(trim($desc));
                if (str_contains($d, 'zinc') || str_contains($d, 'zink')) {
                    return [
                        'bg' => '#F3E8FF', 'color' => '#9333EA', 'border' => '#E9D5FF',
                        'svg' => '<svg style="width:20px;height:20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>'
                    ];
                }
                if (str_contains($d, 'acetic') || str_contains($d, 'acid') || str_contains($d, 'glacial')) {
                    return [
                        'bg' => '#FEF3C7', 'color' => '#D97706', 'border' => '#FDE68A',
                        'svg' => '<svg style="width:20px;height:20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L5.6 15.12a2 2 0 00-1.022.547l-1.3 1.3A2 2 0 004.7 20h14.6a2 2 0 001.414-3.414l-1.286-1.158zM12 4v7"/></svg>'
                    ];
                }
                if (str_contains($d, 'ammonium') || str_contains($d, 'acetate')) {
                    return [
                        'bg' => '#DCFCE7', 'color' => '#16A34A', 'border' => '#BBF7D0',
                        'svg' => '<svg style="width:20px;height:20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L5.6 15.12a2 2 0 00-1.022.547l-1.3 1.3A2 2 0 004.7 20h14.6a2 2 0 001.414-3.414l-1.286-1.158zM12 4v7"/></svg>'
                    ];
                }
                if (str_contains($d, 'water') || str_contains($d, 'dis.')) {
                    return [
                        'bg' => '#E0F2FE', 'color' => '#0284C7', 'border' => '#BAE6FD',
                        'svg' => '<svg style="width:20px;height:20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>'
                    ];
                }
                if (str_contains($d, 'tissue') || str_contains($d, 'roll') || str_contains($d, 'rool')) {
                    return [
                        'bg' => '#FFE4E6', 'color' => '#E11D48', 'border' => '#FECDD3',
                        'svg' => '<svg style="width:20px;height:20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"/></svg>'
                    ];
                }
                if (str_contains($d, 'gas') || str_contains($d, 'nitrogen') || str_contains($d, 'cylinder')) {
                    return [
                        'bg' => '#CCFBF1', 'color' => '#0D9488', 'border' => '#99F6E4',
                        'svg' => '<svg style="width:20px;height:20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>'
                    ];
                }
                if (str_contains($d, 'funel') || str_contains($d, 'funnel')) {
                    return [
                        'bg' => '#FFEDD5', 'color' => '#EA580C', 'border' => '#FED7AA',
                        'svg' => '<svg style="width:20px;height:20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>'
                    ];
                }

                $schemes = [
                    ['bg' => '#F3E8FF', 'color' => '#9333EA', 'border' => '#E9D5FF'],
                    ['bg' => '#FEF3C7', 'color' => '#D97706', 'border' => '#FDE68A'],
                    ['bg' => '#DCFCE7', 'color' => '#16A34A', 'border' => '#BBF7D0'],
                    ['bg' => '#E0F2FE', 'color' => '#0284C7', 'border' => '#BAE6FD'],
                    ['bg' => '#FFE4E6', 'color' => '#E11D48', 'border' => '#FECDD3'],
                    ['bg' => '#CCFBF1', 'color' => '#0D9488', 'border' => '#99F6E4'],
                ];
                $s = $schemes[$idx % count($schemes)];
                $s['svg'] = '<svg style="width:20px;height:20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>';
                return $s;
            }
        }
    @endphp

    <!-- Page Title Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
        <div>
            <h1 style="font-size: 26px; font-weight: 800; color: #1E1B4B; margin: 0 0 4px 0; letter-spacing: -0.5px;">Indent Summary</h1>
            <p style="font-size: 14px; color: #6B7280; margin: 0;">Overview of requested and received items in this indent.</p>
        </div>
        <div style="display: flex; align-items: center; gap: 10px;">
            <a href="{{ route('indent.edit', $indent->id ?? $indentId) }}"
               style="background: #2563EB; color: #FFFFFF; font-weight: 700; font-size: 13px; padding: 10px 18px; border-radius: 10px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 2px 6px rgba(37,99,235,0.25);">
                <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                <span>Edit Indent</span>
            </a>
            <a href="{{ route('po-register.create', ['indent_id' => $indentId, 'department_id' => $department_id]) }}"
               style="background: #FF6B00; color: #FFFFFF; font-weight: 700; font-size: 13px; padding: 10px 18px; border-radius: 10px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 2px 6px rgba(255,107,0,0.25);">
                <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                <span>File PO / Remaining Items</span>
            </a>
        </div>
    </div>

    <!-- 1. Top Metadata Stat Cards Bar -->
    <div style="background: #FFFFFF; border: 1px solid #E5E7EB; border-radius: 16px; padding: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.04); margin-bottom: 24px;">
        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 24px;">
            <!-- Indent ID -->
            <div style="display: flex; align-items: center; gap: 14px;">
                <div style="width: 44px; height: 44px; border-radius: 12px; background: #F3E8FF; color: #9333EA; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <svg style="width: 22px; height: 22px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <div>
                    <span style="font-size: 10px; font-weight: 700; color: #9CA3AF; text-transform: uppercase; letter-spacing: 0.5px; display: block;">INDENT ID</span>
                    <span style="font-size: 20px; font-weight: 800; color: #1E1B4B;">{{ $indentId }}</span>
                </div>
            </div>

            <!-- Department -->
            <div style="display: flex; align-items: center; gap: 14px;">
                <div style="width: 44px; height: 44px; border-radius: 12px; background: #EFF6FF; color: #2563EB; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <svg style="width: 22px; height: 22px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </div>
                <div>
                    <span style="font-size: 10px; font-weight: 700; color: #9CA3AF; text-transform: uppercase; letter-spacing: 0.5px; display: block;">DEPARTMENT</span>
                    <span style="font-size: 18px; font-weight: 800; color: #1E1B4B;">{{ $deptName }}</span>
                </div>
            </div>

            <!-- Project -->
            <div style="display: flex; align-items: center; gap: 14px;">
                <div style="width: 44px; height: 44px; border-radius: 12px; background: #F3F4F6; color: #4B5563; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <svg style="width: 22px; height: 22px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
                </div>
                <div>
                    <span style="font-size: 10px; font-weight: 700; color: #9CA3AF; text-transform: uppercase; letter-spacing: 0.5px; display: block;">PROJECT</span>
                    <span style="font-size: 18px; font-weight: 800; color: #1E1B4B;">{{ $projectName }}</span>
                </div>
            </div>

            <!-- Indent Date -->
            <div style="display: flex; align-items: center; gap: 14px;">
                <div style="width: 44px; height: 44px; border-radius: 12px; background: #FFF7ED; color: #EA580C; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <svg style="width: 22px; height: 22px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3M3 11h18M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <div>
                    <span style="font-size: 10px; font-weight: 700; color: #9CA3AF; text-transform: uppercase; letter-spacing: 0.5px; display: block;">INDENT DATE</span>
                    <span style="font-size: 18px; font-weight: 800; color: #EA580C; white-space: nowrap;">{{ $indentDate }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. Requested vs Received (Summary) Grid Section -->
    <div style="background: #FFFFFF; border: 1px solid #E5E7EB; border-radius: 16px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.04); margin-bottom: 32px;">
        @php
            $totReq = 0;
            $totRec = 0;
            $totCanc = 0;
            $totRem = 0;
            if (!empty($indentItems) && is_array($indentItems)) {
                foreach ($indentItems as $iVal) {
                    $rq = (float)($iVal['quantity_required'] ?? 0);
                    $rc = (float)($iVal['quantity_received'] ?? 0);
                    $cn = (float)($iVal['quantity_cancelled'] ?? 0);
                    $totReq += $rq;
                    $totRec += $rc;
                    $totCanc += $cn;
                    $totRem += max(0, round($rq - ($rc + $cn), 4));
                }
            }
        @endphp
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding-bottom: 14px; border-bottom: 1px solid #F3F4F6; flex-wrap: wrap; gap: 12px;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <svg style="width: 20px; height: 20px; color: #4F46E5;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                <h3 style="font-size: 16px; font-weight: 800; color: #1E1B4B; margin: 0;">Requested vs Received (Summary)</h3>
            </div>
            <!-- Legend Indicators with Aggregate Totals -->
            <div style="display: flex; align-items: center; gap: 16px; font-size: 12px; font-weight: 600; color: #6B7280; flex-wrap: wrap;">
                <span style="display: flex; align-items: center; gap: 6px;">
                    <span style="width: 8px; height: 8px; border-radius: 50%; background: #9CA3AF; display: inline-block;"></span> Requested: <strong style="color: #374151;">{{ round($totReq, 4) }}</strong>
                </span>
                <span style="display: flex; align-items: center; gap: 6px;">
                    <span style="width: 8px; height: 8px; border-radius: 50%; background: #16A34A; display: inline-block;"></span> Received: <strong style="color: #16A34A;">{{ round($totRec, 4) }}</strong>
                </span>
                <span style="display: flex; align-items: center; gap: 6px;">
                    <span style="width: 8px; height: 8px; border-radius: 50%; background: #DC2626; display: inline-block;"></span> Cancelled: <strong style="color: {{ $totCanc > 0 ? '#DC2626' : '#6B7280' }};">{{ round($totCanc, 4) }}</strong>
                </span>
                <span style="display: flex; align-items: center; gap: 6px;">
                    <span style="width: 8px; height: 8px; border-radius: 50%; background: #2563EB; display: inline-block;"></span> Remaining: <strong style="color: #2563EB;">{{ round($totRem, 4) }}</strong>
                </span>
            </div>
        </div>

        @if(!empty($indentItems) && count($indentItems) > 0)
            <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px;">
                @foreach($indentItems as $idx => $it)
                    @php
                        $desc = $it['description'] ?? 'Item ' . ($idx + 1);
                        $req  = (float)($it['quantity_required'] ?? 0);
                        $rec  = (float)($it['quantity_received'] ?? 0);
                        $canc = (float)($it['quantity_cancelled'] ?? 0);
                        $rem  = max(0, round($req - ($rec + $canc), 4));
                        $icon = getMockupItemIcon($desc, $idx);
                    @endphp

                    <div style="background: #FAFAFA; border: 1px solid #F3F4F6; border-radius: 14px; padding: 14px 16px; transition: all 0.2s ease;">
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <div style="width: 38px; height: 38px; border-radius: 10px; background: {{ $icon['bg'] }}; color: {{ $icon['color'] }}; border: 1px solid {{ $icon['border'] }}; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                {!! $icon['svg'] !!}
                            </div>
                            <div style="min-width: 0; flex: 1;">
                                <div style="display: flex; align-items: center; justify-content: space-between; gap: 6px; margin-bottom: 4px;">
                                    <h4 style="font-size: 14px; font-weight: 700; color: #111827; margin: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $desc }}">{{ $desc }}</h4>
                                    @if($canc > 0 && $rem <= 0 && $rec <= 0)
                                        <span style="font-size: 10px; font-weight: 700; color: #DC2626; background: #FEF2F2; border: 1px solid #FECDD3; padding: 1px 6px; border-radius: 4px; white-space: nowrap; flex-shrink: 0;">Cancelled</span>
                                    @elseif($canc > 0)
                                        <span style="font-size: 10px; font-weight: 700; color: #DC2626; background: #FEF2F2; border: 1px solid #FECDD3; padding: 1px 6px; border-radius: 4px; white-space: nowrap; flex-shrink: 0;">Partial Canc</span>
                                    @endif
                                </div>
                                <div style="font-size: 11.5px; color: #6B7280; font-weight: 500; white-space: nowrap;">
                                    Req: <strong style="color: #4B5563; font-weight: 600;">{{ $req }}</strong>
                                    <span style="color: #E5E7EB; margin: 0 3px;">|</span>
                                    Rec: <strong style="color: #16A34A; font-weight: 700;">{{ $rec }}</strong>
                                    <span style="color: #E5E7EB; margin: 0 3px;">|</span>
                                    Canc: <strong style="color: {{ $canc > 0 ? '#DC2626' : '#9CA3AF' }}; font-weight: 700;">{{ $canc }}</strong>
                                    <span style="color: #E5E7EB; margin: 0 3px;">|</span>
                                    Rem: <strong style="color: #2563EB; font-weight: 700;">{{ $rem }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p style="font-size: 13px; color: #9CA3AF; font-style: italic; text-align: center; margin: 20px 0;">No items listed on this indent.</p>
        @endif
    </div>

    <!-- 3. All Purchase Orders Section -->
    <div style="margin-bottom: 32px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
            <h2 style="font-size: 22px; font-weight: 800; color: #1E1B4B; margin: 0; letter-spacing: -0.5px;">All Purchase Orders</h2>

            <!-- New PO File Orange Button -->
            <a href="{{ route('po-register.create', ['indent_id' => $indentId, 'department_id' => $department_id]) }}"
               style="background: #FF6B00; color: #FFFFFF; font-weight: 700; font-size: 13px; padding: 10px 18px; border-radius: 10px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 2px 6px rgba(255,107,0,0.25);">
                <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                <span>File PO / Remaining Items</span>
            </a>
        </div>

        <!-- Filter & Search Bar Toolbar -->
        <div style="background: #F8F7FF; border: 1px solid #E0E7FF; border-radius: 14px; padding: 12px 16px; display: flex; align-items: center; justify-content: space-between; gap: 16px; margin-bottom: 20px;">
            <div style="display: flex; align-items: center; gap: 12px; flex: 1;">
                <!-- Search Input -->
                <div style="position: relative; flex: 1; max-width: 280px;">
                    <span style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #9CA3AF; pointer-events: none;">
                        <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </span>
                    <input type="text" id="poSearchInput" placeholder="Search PO/WO No..." 
                           style="width: 100%; padding: 8px 12px 8px 36px; background: #FFFFFF; border: 1px solid #E5E7EB; border-radius: 10px; font-size: 13px; outline: none;">
                </div>

                <!-- PO Date Picker -->
                <div style="position: relative; width: 170px;">
                    <span style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #9CA3AF; pointer-events: none;">
                        <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3M3 11h18M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </span>
                    <input type="date" id="poDateFilter" 
                           style="width: 100%; padding: 8px 12px 8px 36px; background: #FFFFFF; border: 1px solid #E5E7EB; border-radius: 10px; font-size: 13px; outline: none;">
                </div>

                <!-- Status Filter Dropdown -->
                <div style="position: relative; width: 190px;">
                    <select id="poStatusFilter" 
                            style="width: 100%; padding: 8px 12px; background: #FFFFFF; border: 1px solid #E5E7EB; border-radius: 10px; font-size: 13px; outline: none;">
                        <option value="">Status: All</option>
                        <option value="open">Open</option>
                        <option value="partially received">Partially received</option>
                        <option value="completed">Completed</option>
                        <option value="closed">Closed</option>
                        <option value="reopened">Reopened</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- 4. PO Cards List -->
        <div id="poListContainer">
            @forelse($allPos as $row)
                @php
                    $rowStStr   = is_object($row->status) ? $row->status->value : (string)($row->status ?? 'Open');
                    $normSt     = mb_strtolower(trim($rowStStr));
                    $canEdit    = !in_array($normSt, ['closed', 'close', 'cancel', 'cancelled']);

                    // Status Badge Styling matching mockup
                    $badgeBg    = '#EFF6FF';
                    $badgeColor = '#2563EB';
                    if ($normSt === 'completed') { $badgeBg = '#ECFDF5'; $badgeColor = '#059669'; }
                    elseif ($normSt === 'reopened') { $badgeBg = '#F3E8FF'; $badgeColor = '#7C3AED'; }
                    elseif (in_array($normSt, ['closed', 'close'])) { $badgeBg = '#F3F4F6'; $badgeColor = '#4B5563'; }
                    elseif (in_array($normSt, ['cancel', 'cancelled'])) { $badgeBg = '#FEF2F2'; $badgeColor = '#DC2626'; }

                    $poItemsDecoded = !empty($row->item_description) ? (is_array($row->item_description) ? $row->item_description : json_decode($row->item_description, true)) : [];
                    $poItemsCount   = is_array($poItemsDecoded) ? count($poItemsDecoded) : 0;
                    $poWoDisplay    = !empty($row->po_wo_no) ? $row->po_wo_no : ('PO/' . $indentId . '/' . str_pad($row->id, 2, '0', STR_PAD_LEFT));
                    $poDateDisplay  = !empty($row->po_date) ? date('d M Y', strtotime($row->po_date)) : '-';
                @endphp

                <div class="po-card" 
                     style="background: #FFFFFF; border: 1px solid #E5E7EB; border-radius: 16px; padding: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.04); margin-bottom: 16px;"
                     data-po-no="{{ strtolower($poWoDisplay) }}"
                     data-po-date="{{ $row->po_date }}"
                     data-po-status="{{ $normSt }}">
                    
                    <!-- PO Card Header Row -->
                    <div style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 14px; border-bottom: 1px solid #F3F4F6; margin-bottom: 16px;">
                        <div style="display: flex; align-items: center; gap: 20px;">
                            <!-- PO No -->
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <div style="width: 36px; height: 36px; border-radius: 10px; background: #F3E8FF; color: #9333EA; display: flex; align-items: center; justify-content: center;">
                                    <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                </div>
                                <div>
                                    <span style="font-size: 10px; font-weight: 700; color: #9CA3AF; text-transform: uppercase; display: block;">PO/WO No.</span>
                                    <span style="font-size: 14px; font-weight: 800; color: #7C3AED;">{{ $poWoDisplay }}</span>
                                </div>
                            </div>

                            <span style="color: #E5E7EB;">|</span>

                            <!-- PO Date -->
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <svg style="width: 16px; height: 16px; color: #9CA3AF;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3M3 11h18M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                <div>
                                    <span style="font-size: 10px; font-weight: 700; color: #9CA3AF; text-transform: uppercase; display: block;">PO Date</span>
                                    <span style="font-size: 13px; font-weight: 700; color: #111827;">{{ $poDateDisplay }}</span>
                                </div>
                            </div>

                            <span style="color: #E5E7EB;">|</span>

                            <!-- Status Badge -->
                            <div>
                                <span style="font-size: 10px; font-weight: 700; color: #9CA3AF; text-transform: uppercase; display: block; margin-bottom: 2px;">Status</span>
                                <span style="background: {{ $badgeBg }}; color: {{ $badgeColor }}; font-weight: 700; font-size: 12px; padding: 3px 12px; border-radius: 20px; display: inline-block;">
                                    {{ ucfirst($rowStStr) }}
                                </span>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div style="display: flex; align-items: center; gap: 8px;">
                            @if($canEdit)
                                <a href="{{ route('po-register.edit', $row->id) }}" 
                                   style="background: #22C55E; color: #FFFFFF; font-weight: 700; font-size: 12px; padding: 8px 14px; border-radius: 8px; text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
                                    <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    <span>Update P.O.</span>
                                </a>

                                <a href="{{ route('indentroview.createInvoiceById', $row->id) }}" 
                                   style="background: #3B82F6; color: #FFFFFF; font-weight: 700; font-size: 12px; padding: 8px 14px; border-radius: 8px; text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
                                    <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    <span>{{ !empty($row->invoice_date) ? 'Update Invoice' : 'File Invoice / Goods Receipt' }}</span>
                                </a>

                                <button type="button" 
                                        onclick="document.getElementById('closePoModal_{{ $row->id }}').style.display='flex'" 
                                        style="background: #FFF; color: #EF4444; border: 1px solid #FCA5A5; font-weight: 700; font-size: 12px; padding: 8px 14px; border-radius: 8px; cursor: pointer; display: inline-flex; align-items: center; gap: 6px;">
                                    <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <span>Close PO</span>
                                </button>

                                <button type="button" 
                                        onclick="document.getElementById('cancelPoModal_{{ $row->id }}').style.display='flex'" 
                                        style="background: #FEF2F2; color: #DC2626; border: 1px solid #FECDD3; font-weight: 700; font-size: 12px; padding: 8px 14px; border-radius: 8px; cursor: pointer; display: inline-flex; align-items: center; gap: 6px;">
                                    <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    <span>Cancel PO</span>
                                </button>
                            @elseif(in_array($normSt, ['closed', 'close']))
                                <a href="{{ route('indentroview.createInvoiceById', $row->id) }}" 
                                   style="background: #F8FAFC; color: #475569; border: 1px solid #CBD5E1; font-weight: 700; font-size: 12px; padding: 8px 14px; border-radius: 8px; text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
                                    <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    <span>View Goods Receipt</span>
                                </a>

                                <form method="POST" action="{{ route('po-register.reopenPO', $row->id) }}" style="display: inline;" onsubmit="return confirm('Are you sure you want to reopen this PO?');">
                                    @csrf
                                    <button type="submit" style="background: #4F46E5; color: #FFFFFF; font-weight: 700; font-size: 12px; padding: 8px 14px; border-radius: 8px; cursor: pointer; border: none; display: inline-flex; align-items: center; gap: 6px;">
                                        <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>
                                        <span>Reopen PO</span>
                                    </button>
                                </form>
                            @else
                                <a href="{{ route('indentroview.createInvoiceById', $row->id) }}" 
                                   style="background: #F8FAFC; color: #475569; border: 1px solid #CBD5E1; font-weight: 700; font-size: 12px; padding: 8px 14px; border-radius: 8px; text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
                                    <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    <span>View Details</span>
                                </a>
                            @endif
                        </div>
                    </div>

                    <!-- PO Metadata Grid Row -->
                    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 14px;">
                        <div>
                            <span style="font-size: 10px; font-weight: 600; color: #9CA3AF; text-transform: uppercase; display: block;">Party</span>
                            <span style="font-size: 14px; font-weight: 800; color: #111827;">{{ $row->party_name ?? '-' }}</span>
                        </div>
                        <div>
                            <span style="font-size: 10px; font-weight: 600; color: #9CA3AF; text-transform: uppercase; display: block;">PO Amount</span>
                            <span style="font-size: 14px; font-weight: 800; color: #111827; font-family: monospace;">₹ {{ number_format($row->po_amount ?? 0, 2) }}</span>
                        </div>
                        <div>
                            <span style="font-size: 10px; font-weight: 600; color: #9CA3AF; text-transform: uppercase; display: block;">Items</span>
                            <span style="background: #EFF6FF; color: #2563EB; font-weight: 800; font-size: 12px; padding: 3px 10px; border-radius: 6px; display: inline-block;">
                                {{ $poItemsCount }} {{ Str::plural('Item', $poItemsCount) }}
                            </span>
                        </div>
                    </div>

                    <!-- PO Items List Breakdown -->
                    @if(is_array($poItemsDecoded) && count($poItemsDecoded) > 0)
                        <div style="background: #FAFAFA; border: 1px solid #F3F4F6; border-radius: 10px; padding: 12px; margin-bottom: 14px;">
                            @foreach($poItemsDecoded as $pIt)
                                @php
                                    $pDesc = is_array($pIt) ? ($pIt['description'] ?? '') : (string)$pIt;
                                    $pOrd  = is_array($pIt) ? (float)($pIt['quantity'] ?? $pIt['po_quantity'] ?? 1) : 1;
                                    $pRec  = is_array($pIt) ? (float)($pIt['quantity_received'] ?? 0) : 0;
                                    $pCanc = is_array($pIt) ? (float)($pIt['quantity_cancelled'] ?? 0) : 0;
                                    $pRem  = max(0, round($pOrd - ($pRec + $pCanc), 4));
                                @endphp

                                <div style="display: flex; align-items: center; justify-content: space-between; font-size: 12px; margin-bottom: 4px;">
                                    <div style="display: flex; align-items: center; gap: 8px; font-weight: 700; color: #1F2937;">
                                        <span style="width: 6px; height: 6px; border-radius: 50%; background: #9333EA; display: inline-block;"></span>
                                        <span>{{ $pDesc }}</span>
                                        @if($pCanc > 0 && $pRem <= 0 && $pRec <= 0)
                                            <span style="font-size: 10px; font-weight: 700; color: #DC2626; background: #FEF2F2; border: 1px solid #FECDD3; padding: 1px 6px; border-radius: 4px;">Cancelled</span>
                                        @elseif($pCanc > 0)
                                            <span style="font-size: 10px; font-weight: 700; color: #DC2626; background: #FEF2F2; border: 1px solid #FECDD3; padding: 1px 6px; border-radius: 4px;">Canc: {{ $pCanc }}</span>
                                        @endif
                                    </div>
                                    <div style="color: #6B7280; font-weight: 500;">
                                        Req: <strong style="color: #4B5563;">{{ $pOrd }}</strong>
                                        <span style="color: #E5E7EB; margin: 0 4px;">|</span>
                                        Rec: <strong style="color: #16A34A; font-weight: 700;">{{ $pRec }}</strong>
                                        <span style="color: #E5E7EB; margin: 0 4px;">|</span>
                                        Canc: <strong style="color: {{ $pCanc > 0 ? '#DC2626' : '#9CA3AF' }}; font-weight: 700;">{{ $pCanc }}</strong>
                                        <span style="color: #E5E7EB; margin: 0 4px;">|</span>
                                        Rem: <strong style="color: #2563EB; font-weight: 700;">{{ $pRem }}</strong>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <!-- Expandable Remarks Accordion -->
                    <details style="background: #F9FAFB; border: 1px solid #F3F4F6; border-radius: 10px;">
                        <summary style="display: flex; align-items: center; justify-content: space-between; padding: 10px 14px; font-size: 12px; font-weight: 600; color: #4B5563; cursor: pointer;">
                            <div style="display: flex; align-items: center; gap: 8px;">
                                💬 <span style="font-weight: 700;">Remarks</span>
                                <span style="color: #9CA3AF; font-style: italic; font-weight: 400;">
                                    {{ !empty($row->remarks) ? Str::limit($row->remarks, 60) : 'No remarks added' }}
                                </span>
                            </div>
                            <svg style="width: 14px; height: 14px; color: #9CA3AF;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </summary>
                        <div style="padding: 10px 14px; border-top: 1px solid #F3F4F6; font-size: 12px; color: #1F2937; background: #FFFFFF;">
                            {{ !empty($row->remarks) ? $row->remarks : 'No remarks recorded for this Purchase Order.' }}
                        </div>
                    </details>

                    <!-- Close PO Modal -->
                    <div id="closePoModal_{{ $row->id }}" style="display: none; position: fixed; inset: 0; z-index: 9999; background: rgba(0,0,0,0.5); align-items: center; justify-content: center;">
                        <div style="background: #FFFFFF; border-radius: 16px; padding: 24px; width: 100%; max-width: 440px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1);">
                            <h3 style="font-size: 18px; font-weight: 800; color: #111827; margin: 0 0 8px 0;">Close Purchase Order #{{ $row->id }}</h3>
                            <p style="font-size: 12px; color: #4B5563; margin: 0 0 16px 0;">Are you sure you want to close this PO? Once closed, no further goods receipts or invoice modifications will be allowed.</p>
                            <form method="POST" action="{{ route('po-register.closePO', $row->id) }}">
                                @csrf
                                <div style="margin-bottom: 16px;">
                                    <label style="display: block; font-size: 12px; font-weight: 700; color: #374151; margin-bottom: 4px;">Close Reason (Optional)</label>
                                    <textarea name="close_reason" rows="3" style="width: 100%; padding: 8px 12px; border: 1px solid #D1D5DB; border-radius: 8px; font-size: 12px; box-sizing: border-box;" placeholder="Enter reason for closing this PO..."></textarea>
                                </div>
                                <div style="display: flex; justify-content: flex-end; gap: 8px;">
                                    <button type="button" onclick="document.getElementById('closePoModal_{{ $row->id }}').style.display='none'" style="padding: 8px 16px; border-radius: 8px; font-size: 12px; font-weight: 600; background: #F3F4F6; color: #374151; border: none; cursor: pointer;">Cancel</button>
                                    <button type="submit" style="padding: 8px 16px; border-radius: 8px; font-size: 12px; font-weight: 600; background: #DC2626; color: #FFFFFF; border: none; cursor: pointer;">Confirm Close PO</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Cancel PO Modal -->
                    <div id="cancelPoModal_{{ $row->id }}" style="display: none; position: fixed; inset: 0; z-index: 9999; background: rgba(0,0,0,0.5); align-items: center; justify-content: center;">
                        <div style="background: #FFFFFF; border-radius: 16px; padding: 24px; width: 100%; max-width: 440px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1);">
                            <h3 style="font-size: 18px; font-weight: 800; color: #111827; margin: 0 0 8px 0;">Cancel Purchase Order #{{ $row->id }}</h3>
                            <p style="font-size: 12px; color: #4B5563; margin: 0 0 16px 0;">Are you sure you want to cancel this PO? This will mark the PO as cancelled and release remaining balances.</p>
                            <form method="POST" action="{{ route('po-register.cancelPO', $row->id) }}">
                                @csrf
                                <div style="margin-bottom: 16px;">
                                    <label style="display: block; font-size: 12px; font-weight: 700; color: #374151; margin-bottom: 4px;">Cancel Reason (Optional)</label>
                                    <textarea name="cancel_reason" rows="3" style="width: 100%; padding: 8px 12px; border: 1px solid #D1D5DB; border-radius: 8px; font-size: 12px; box-sizing: border-box;" placeholder="Enter reason for cancelling this PO..."></textarea>
                                </div>
                                <div style="display: flex; justify-content: flex-end; gap: 8px;">
                                    <button type="button" onclick="document.getElementById('cancelPoModal_{{ $row->id }}').style.display='none'" style="padding: 8px 16px; border-radius: 8px; font-size: 12px; font-weight: 600; background: #F3F4F6; color: #374151; border: none; cursor: pointer;">Back</button>
                                    <button type="submit" style="padding: 8px 16px; border-radius: 8px; font-size: 12px; font-weight: 600; background: #DC2626; color: #FFFFFF; border: none; cursor: pointer;">Confirm Cancel PO</button>
                                </div>
                            </form>
                        </div>
                    </div>

                </div>
            @empty
                <div style="background: #FFFFFF; border: 1px solid #E5E7EB; border-radius: 16px; padding: 40px; text-align: center; color: #6B7280;">
                    <p style="font-weight: 700; font-size: 14px; color: #374151; margin: 0;">No purchase orders found for this indent.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Search & Filter JavaScript -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('poSearchInput');
        const dateInput   = document.getElementById('poDateFilter');
        const statusInput = document.getElementById('poStatusFilter');
        const poCards     = document.querySelectorAll('.po-card');

        function filterPOCards() {
            const query  = searchInput ? searchInput.value.toLowerCase().trim() : '';
            const date   = dateInput   ? dateInput.value : '';
            const status = statusInput ? statusInput.value.toLowerCase().trim() : '';

            poCards.forEach(card => {
                const cardPoNo   = card.getAttribute('data-po-no') || '';
                const cardDate   = card.getAttribute('data-po-date') || '';
                const cardStatus = card.getAttribute('data-po-status') || '';

                const matchesQuery  = !query || cardPoNo.includes(query);
                const matchesDate   = !date  || cardDate === date;
                const matchesStatus = !status|| cardStatus === status;

                if (matchesQuery && matchesDate && matchesStatus) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        }

        searchInput?.addEventListener('input', filterPOCards);
        dateInput?.addEventListener('change', filterPOCards);
        statusInput?.addEventListener('change', filterPOCards);
    });
</script>