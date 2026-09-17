<div style="width: 100%; max-width: 100%; box-sizing: border-box; font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
    <form method="POST" action="{{ route('po-register.store') }}" style="display: flex; flex-direction: column; gap: 24px;">
        @csrf

        <input type="hidden" name="indent_id" value="{{ $indent_id }}">
        <input type="hidden" name="department_id" value="{{ $department_id }}">

        <!-- Top Page Header -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px; flex-wrap: wrap; gap: 14px;">
            <div style="display: flex; align-items: center; gap: 14px;">
                <div style="width: 48px; height: 48px; border-radius: 14px; background: #2563EB; color: #FFFFFF; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 12px rgba(37,99,235,0.25);">
                    <svg style="width: 24px; height: 24px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <div>
                    <h1 style="font-size: 24px; font-weight: 800; color: #0F172A; margin: 0 0 2px 0; letter-spacing: -0.5px;">File Purchase Order (Indent #{{ $indent_id }})</h1>
                    <p style="font-size: 13px; color: #64748B; margin: 0; font-weight: 500;">Fill in the details below to file a new purchase order for this indent.</p>
                </div>
            </div>
            <div style="display: flex; align-items: center; gap: 10px;">
                <a href="{{ route('indent.edit', $indent_id) }}" 
                   style="background: #EFF6FF; border: 1px solid #BFDBFE; color: #2563EB; font-weight: 700; font-size: 12px; padding: 8px 16px; border-radius: 10px; text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
                    <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    <span>Edit Indent</span>
                </a>
                <a href="{{ route('po-register.viewByIndent', ['indent_id' => $indent_id, 'department_id' => $department_id]) }}" 
                   style="background: #F8FAFC; border: 1px solid #CBD5E1; color: #475569; font-weight: 700; font-size: 12px; padding: 8px 16px; border-radius: 10px; text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
                    <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    <span>Indent Summary</span>
                </a>
            </div>
        </div>

        <!-- Metadata Section Card (3 Rows x 4 Columns) -->
        <div style="background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.03); padding: 24px;">
            
            <!-- Row 1 -->
            <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 20px;">
                <!-- Indent ID -->
                <div>
                    <label style="display: block; font-size: 12px; font-weight: 800; color: #475569; margin-bottom: 8px;">
                        Indent ID <span style="color: #EF4444;">*</span>
                    </label>
                    <input type="text" value="{{ $indent_id }}" readonly disabled
                           style="width: 100%; padding: 10px 14px; background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 10px; font-size: 14px; font-weight: 800; color: #0F172A; outline: none; box-sizing: border-box;">
                </div>

                <!-- Department -->
                <div>
                    <label style="display: block; font-size: 12px; font-weight: 800; color: #475569; margin-bottom: 8px;">
                        Department <span style="color: #EF4444;">*</span>
                    </label>
                    <div style="position: relative;">
                        <span style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #94A3B8;">
                            <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m3 0h1m-1-4h.01M9 16h.01M9 12h.01M9 8h.01M15 16h.01M15 12h.01M15 8h.01"/></svg>
                        </span>
                        <input type="text" value="{{ $department_id }}" readonly disabled
                               style="width: 100%; padding: 10px 14px 10px 38px; background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 10px; font-size: 14px; font-weight: 800; color: #0F172A; outline: none; box-sizing: border-box;">
                    </div>
                </div>

                <!-- Requirement Type -->
                <div>
                    <label for="is_mandatory" style="display: block; font-size: 12px; font-weight: 800; color: #475569; margin-bottom: 8px;">
                        Requirement Type <span style="color: #EF4444;">*</span>
                    </label>
                    <div style="position: relative;">
                        <span style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #94A3B8; z-index: 2;">
                            <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        </span>
                        <select name="is_mandatory" id="is_mandatory" style="width: 100%; padding: 10px 14px 10px 38px; background: #FFFFFF; border: 1px solid #CBD5E1; border-radius: 10px; font-size: 13px; font-weight: 600; color: #0F172A; outline: none; box-sizing: border-box;">
                            <option value="Mandatory" {{ old('is_mandatory', 'Mandatory') === 'Mandatory' ? 'selected' : '' }}>Mandatory</option>
                            <option value="Non-Mandatory" {{ old('is_mandatory') === 'Non-Mandatory' ? 'selected' : '' }}>Non-Mandatory</option>
                        </select>
                    </div>
                </div>

                <!-- Status Badge Selector -->
                <div>
                    <label for="status" style="display: block; font-size: 12px; font-weight: 800; color: #475569; margin-bottom: 8px;">
                        Status <span style="color: #EF4444;">*</span>
                    </label>
                    <div style="position: relative;">
                        <div style="width: 100%; padding: 8px 14px; background: #FFFBEB; border: 1px solid #FDE68A; border-radius: 10px; display: flex; align-items: center; justify-content: space-between; box-sizing: border-box;">
                            <span style="display: flex; align-items: center; gap: 8px; font-weight: 700; font-size: 13px; color: #B45309;">
                                <span style="width: 8px; height: 8px; border-radius: 50%; background: #F59E0B; display: inline-block;"></span>
                                Pending
                            </span>
                            <svg style="width: 16px; height: 16px; color: #B45309;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </div>
                        <input type="hidden" name="status" value="Pending">
                    </div>
                </div>
            </div>

            <!-- Row 2 -->
            <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 20px;">
                <!-- PR Date -->
                <div>
                    <label for="po_date" style="display: block; font-size: 12px; font-weight: 800; color: #475569; margin-bottom: 8px;">
                        PR Date <span class="req-asterisk" style="color: #EF4444;">*</span>
                    </label>
                    <div style="position: relative;">
                        <input type="date" name="po_date" id="po_date" required value="{{ date('Y-m-d') }}"
                               style="width: 100%; padding: 10px 14px; background: #FFFFFF; border: 1px solid #CBD5E1; border-radius: 10px; font-size: 13px; font-weight: 600; color: #0F172A; outline: none; box-sizing: border-box;">
                    </div>
                </div>

                <!-- Party Name -->
                <div>
                    <label for="party_name" style="display: block; font-size: 12px; font-weight: 800; color: #475569; margin-bottom: 8px;">
                        Party Name <span class="req-asterisk" style="color: #EF4444;">*</span>
                    </label>
                    <div style="position: relative;">
                        <select name="party_name" id="party_name" required style="width: 100%; padding: 10px 14px; background: #FFFFFF; border: 1px solid #CBD5E1; border-radius: 10px; font-size: 13px; font-weight: 600; color: #0F172A; outline: none; box-sizing: border-box;">
                            <option value="">Select party</option>
                            @foreach ($projectList as $vendor)
                                <option value="{{ $vendor->name }}">{{ $vendor->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- PR/WO No. -->
                <div>
                    <label for="po_wo_no" style="display: block; font-size: 12px; font-weight: 800; color: #475569; margin-bottom: 8px;">
                        PR/WO No.
                    </label>
                    <div style="position: relative;">
                        <span style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #94A3B8;">
                            <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </span>
                        <input type="text" name="po_wo_no" id="po_wo_no" placeholder="Enter PR/WO No."
                               style="width: 100%; padding: 10px 14px 10px 38px; background: #FFFFFF; border: 1px solid #CBD5E1; border-radius: 10px; font-size: 13px; font-weight: 600; color: #0F172A; outline: none; box-sizing: border-box;">
                    </div>
                </div>

                <!-- PO Amount -->
                <div>
                    <label for="po_amount" style="display: block; font-size: 12px; font-weight: 800; color: #475569; margin-bottom: 8px;">
                        PO Amount <span class="req-asterisk" style="color: #EF4444;">*</span>
                    </label>
                    <div style="position: relative;">
                        <span style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #94A3B8; font-weight: 700; font-size: 14px;">₹</span>
                        <input type="number" name="po_amount" id="po_amount" step="1" min="0" required placeholder="Enter PO amount"
                               style="width: 100%; padding: 10px 14px 10px 34px; background: #FFFFFF; border: 1px solid #CBD5E1; border-radius: 10px; font-size: 14px; font-weight: 700; color: #0F172A; outline: none; box-sizing: border-box;">
                    </div>
                </div>
            </div>

            <!-- Row 3 -->
            <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 20px;">
                <!-- Item Description Multi-Select Tag Dropdown Component -->
                <div style="grid-column: span 2; position: relative;">
                    <label style="display: block; font-size: 12px; font-weight: 800; color: #475569; margin-bottom: 8px;">
                        Item Description <span class="req-asterisk" style="color: #EF4444;">*</span>
                    </label>

                    <!-- Hidden select for backend compatibility -->
                    <select name="item_description[]" id="item_description" multiple style="display: none;">
                        @foreach ($items as $item)
                            <option value="{{ $item['description'] }}" selected>{{ $item['description'] }}</option>
                        @endforeach
                    </select>

                    <!-- Tag Select Control Box -->
                    <div id="tagSelectBox" onclick="toggleTagDropdown(event)" 
                         style="width: 100%; min-height: 42px; background: #FFFFFF; border: 1px solid #CBD5E1; border-radius: 10px; padding: 6px 10px; box-sizing: border-box; display: flex; flex-wrap: wrap; align-items: center; gap: 6px; cursor: pointer; transition: all 0.15s ease;">
                        
                        <div id="selectedTagPills" style="display: flex; flex-wrap: wrap; gap: 6px; flex: 1;">
                            <!-- JS dynamically injects selected tag pills here -->
                        </div>

                        <span id="tagPlaceholder" style="color: #94A3B8; font-size: 13px; font-weight: 500; display: none;">Select item descriptions...</span>

                        <span style="color: #64748B; margin-left: auto; display: flex; align-items: center; justify-content: center; pointer-events: none;">
                            <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </span>
                    </div>

                    <!-- Dropdown Menu List -->
                    <div id="tagDropdownMenu" 
                         style="display: none; position: absolute; top: calc(100% + 4px); left: 0; right: 0; background: #FFFFFF; border: 1px solid #CBD5E1; border-radius: 10px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); max-height: 220px; overflow-y: auto; z-index: 9999;">
                        @foreach ($items as $item)
                            @php
                                $req = (int)($item['quantity_required'] ?? 0);
                                $unitVal = $item['unit'] ?? '-';
                            @endphp
                            <div class="tag-dropdown-option" 
                                 data-desc="{{ $item['description'] }}"
                                 data-req="{{ $req }}"
                                 data-unit="{{ $unitVal }}"
                                 onclick="toggleTagItem(event, '{{ addslashes($item['description']) }}')"
                                 style="padding: 10px 14px; font-size: 13px; font-weight: 600; color: #0F172A; cursor: pointer; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #F1F5F9; transition: background 0.15s ease;"
                                 onmouseover="this.style.background='#F8FAFC'" onmouseout="this.style.background='#FFFFFF'">
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <span class="tag-option-check" style="color: #2563EB; font-weight: 800;">✓</span>
                                    <span>{{ $item['description'] }}</span>
                                </div>
                                <span style="color: #64748B; font-weight: 500; font-size: 11px;">(Qty: {{ $req }}, Unit: {{ $unitVal }})</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Expected Date -->
                <div>
                    <label for="expected_date" style="display: block; font-size: 12px; font-weight: 800; color: #475569; margin-bottom: 8px;">
                        Expected Date <span class="req-asterisk" style="color: #EF4444;">*</span>
                    </label>
                    <div style="position: relative;">
                        <input type="date" name="expected_date" id="expected_date" required value="{{ date('Y-m-d') }}"
                               style="width: 100%; padding: 10px 14px; background: #FFFFFF; border: 1px solid #CBD5E1; border-radius: 10px; font-size: 13px; font-weight: 600; color: #0F172A; outline: none; box-sizing: border-box;">
                    </div>
                </div>

                <!-- Expected Days -->
                <div>
                    <label for="expected_days" style="display: block; font-size: 12px; font-weight: 800; color: #475569; margin-bottom: 8px;">
                        Expected Days
                    </label>
                    <div style="position: relative;">
                        <span style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #94A3B8;">
                            <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </span>
                        <input type="text" id="expected_days" placeholder="Enter expected days" readonly disabled
                               style="width: 100%; padding: 10px 14px 10px 38px; background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 10px; font-size: 13px; font-weight: 700; color: #475569; outline: none; box-sizing: border-box;">
                        <input type="hidden" name="expected_days" id="expected_days_hidden">
                    </div>
                </div>
            </div>

            <!-- Row 4 -->
            <div style="display: grid; grid-template-columns: 1fr;">
                <!-- Remarks -->
                <div>
                    <label for="remarks" style="display: block; font-size: 12px; font-weight: 800; color: #475569; margin-bottom: 8px;">
                        Remarks
                    </label>
                    <textarea name="remarks" id="remarks" rows="2" placeholder="Enter any remarks (optional)..."
                              style="width: 100%; padding: 10px 14px; background: #FAFAFA; border: 1px solid #CBD5E1; border-radius: 10px; font-size: 13px; outline: none; box-sizing: border-box;">{{ old('remarks') }}</textarea>
                </div>
            </div>
        </div>

        <!-- Breakdown Card Section -->
        @if(!empty($items) && count($items) > 0)
        <div id="po-items-table-container" style="background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.03); padding: 24px;">
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 16px;">
                <div style="width: 32px; height: 32px; border-radius: 8px; background: #2563EB; color: #FFFFFF; display: flex; align-items: center; justify-content: center;">
                    <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                </div>
                <h3 style="font-size: 16px; font-weight: 800; color: #0F172A; margin: 0;">
                    Item Description & Filing Quantity (Count) Breakdown <span class="req-asterisk" style="color: #EF4444;">*</span>
                </h3>
            </div>

            <div style="width: 100%; overflow-x: auto; background: #FAFAFA; border: 1px solid #E2E8F0; border-radius: 14px; overflow: hidden;">
                <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 13px;">
                    <thead>
                        <tr style="background: #F8FAFC; border-bottom: 1px solid #E2E8F0; color: #475569; font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px;">
                            <th style="padding: 14px 16px; width: 50px; text-align: center; vertical-align: middle;">
                                <input type="checkbox" id="selectAllPoItems" checked style="width: 16px; height: 16px; border-radius: 4px; accent-color: #2563EB; cursor: pointer;">
                            </th>
                            <th style="padding: 14px 16px; vertical-align: middle;">ITEM DESCRIPTION</th>
                            <th style="padding: 14px 16px; width: 90px; text-align: center; vertical-align: middle;">UNIT</th>
                            <th style="padding: 14px 16px; width: 110px; text-align: center; vertical-align: middle;">QTY REQUIRED</th>
                            <th style="padding: 14px 16px; width: 120px; text-align: center; vertical-align: middle;">PREVIOUSLY FILED</th>
                            <th style="padding: 14px 16px; width: 120px; text-align: center; vertical-align: middle;">ITEM STATUS</th>
                            <th style="padding: 14px 16px; width: 160px; text-align: center; vertical-align: middle;">FILLING PO QTY (COUNT)</th>
                            <th style="padding: 14px 16px; width: 150px; text-align: center; vertical-align: middle;">REMAINING TO FILE</th>
                            <th style="padding: 14px 16px; width: 140px; text-align: center; vertical-align: middle;">CANCEL OPTION</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($items as $idx => $item)
                            @php
                                $req = (float)($item['quantity_required'] ?? 1);
                                $rec = (float)($item['quantity_received'] ?? 0);
                                $canc = (float)($item['quantity_cancelled'] ?? 0);
                                $filed = (float)($item['already_filed'] ?? 0);
                                $rem = (float)($item['remaining_to_file'] ?? max(0, $req - ($filed + $canc)));
                                $defaultFiling = $rem > 0 ? $rem : $req;
                                $itemStatus = $item['status'] ?? 'Pending';
                            @endphp
                            <tr class="po-item-row" data-item-desc="{{ $item['description'] }}" style="border-bottom: 1px solid #F1F5F9; background: #FFFFFF; transition: background 0.15s ease;">
                                <td style="padding: 14px 16px; text-align: center; vertical-align: middle;">
                                    <input type="checkbox" name="po_items[{{ $idx }}][selected]" value="1" checked class="po-item-check"
                                           style="width: 18px; height: 18px; border-radius: 4px; accent-color: #2563EB; cursor: pointer;">
                                </td>
                                <td style="padding: 14px 16px; font-weight: 700; color: #0F172A; vertical-align: middle;">
                                    <div style="display: flex; align-items: center; gap: 10px;">
                                        <div style="width: 28px; height: 28px; border-radius: 8px; background: #EFF6FF; color: #2563EB; display: flex; align-items: center; justify-content: center;">
                                            <svg style="width: 15px; height: 15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                        </div>
                                        <span>{{ $item['description'] }}</span>
                                    </div>
                                    <input type="hidden" name="po_items[{{ $idx }}][description]" value="{{ $item['description'] }}">
                                    <input type="hidden" name="po_items[{{ $idx }}][unit]" value="{{ $item['unit'] ?? '' }}">
                                    <input type="hidden" name="po_items[{{ $idx }}][quantity_required]" value="{{ $req }}" class="js-po-req">
                                    <input type="hidden" name="po_items[{{ $idx }}][quantity_received]" value="{{ $rec }}" class="js-po-rec">
                                    <input type="hidden" name="po_items[{{ $idx }}][quantity_cancelled]" value="{{ $canc }}" class="js-po-canc">
                                    <input type="hidden" name="po_items[{{ $idx }}][already_filed]" value="{{ $filed }}" class="js-po-filed">
                                </td>
                                <td style="padding: 14px 16px; text-align: center; color: #475569; font-weight: 600; vertical-align: middle;">{{ $item['unit'] ?? '-' }}</td>
                                <td style="padding: 14px 16px; text-align: center; font-weight: 800; color: #0F172A; vertical-align: middle;">{{ $req }}</td>
                                <td style="padding: 14px 16px; text-align: center; color: #2563EB; font-weight: 800; vertical-align: middle;">{{ $filed }}</td>
                                <td style="padding: 14px 16px; text-align: center; vertical-align: middle;">
                                    <span style="background: #EFF6FF; border: 1px solid #BFDBFE; color: #2563EB; font-weight: 700; font-size: 11px; padding: 4px 10px; border-radius: 20px; white-space: nowrap;">
                                        {{ $itemStatus }}
                                    </span>
                                </td>
                                <td style="padding: 14px 16px; text-align: center; vertical-align: middle;">
                                    <input type="number" 
                                           name="po_items[{{ $idx }}][po_quantity]" 
                                           value="{{ $defaultFiling }}" 
                                           min="0.001" 
                                           step="any"
                                           max="{{ $rem }}"
                                           class="js-po-qty" required
                                           style="width: 110px; height: 38px; text-align: center; border: 1px solid #CBD5E1; border-radius: 8px; font-size: 14px; font-weight: 800; color: #0F172A; outline: none; box-sizing: border-box;">
                                </td>
                                <td style="padding: 14px 16px; text-align: center; vertical-align: middle;">
                                    <span class="js-po-rem" style="font-weight: 700; font-size: 12px; padding: 5px 14px; border-radius: 20px; display: inline-flex; align-items: center; gap: 6px; {{ max(0, $rem - $defaultFiling) > 0 ? 'background: #EFF6FF; color: #2563EB; border: 1px solid #BFDBFE;' : 'background: #ECFDF5; color: #047857; border: 1px solid #A7F3D0;' }}">
                                        <span style="width: 6px; height: 6px; border-radius: 50%; background: {{ max(0, $rem - $defaultFiling) > 0 ? '#2563EB' : '#10B981' }}; display: inline-block;"></span>
                                        {{ max(0, $rem - $defaultFiling) > 0 ? max(0, $rem - $defaultFiling) . ' remaining' : '0 (Fully Filed)' }}
                                    </span>
                                </td>
                                <td style="padding: 14px 16px; text-align: center; vertical-align: middle;">
                                    <div style="display: flex; flex-direction: column; gap: 4px; align-items: center;">
                                        <label style="display: inline-flex; align-items: center; gap: 5px; font-size: 11.5px; font-weight: 700; color: #DC2626; cursor: pointer; background: #FEF2F2; border: 1px solid #FECDD3; padding: 4px 8px; border-radius: 6px; user-select: none;">
                                            <input type="checkbox" name="po_items[{{ $idx }}][cancel_item]" value="1" class="js-cancel-item-check" style="accent-color: #DC2626; width: 14px; height: 14px; cursor: pointer;">
                                            <span>Cancel Item</span>
                                        </label>
                                        <button type="button" 
                                                onclick="openCancelModal('{{ addslashes($indent_id) }}', '{{ addslashes($item['description']) }}', {{ $rem }})" 
                                                title="Cancel item immediately from this indent"
                                                style="background: transparent; border: none; color: #64748B; font-size: 11px; font-weight: 600; text-decoration: underline; cursor: pointer; padding: 0;">
                                            Cancel Now
                                        </button>
                                        <input type="hidden" name="po_items[{{ $idx }}][cancel_qty]" value="{{ $rem }}" class="js-cancel-qty-val">
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        <!-- Submit Button -->
        <div style="display: flex; justify-content: flex-end; margin-top: 8px;">
            <button type="submit" 
                    style="background: #2563EB; color: #FFFFFF; font-weight: 700; font-size: 13.5px; padding: 11px 24px; border-radius: 10px; border: none; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 4px 12px rgba(37,99,235,0.25);">
                <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                <span>Submit ID</span>
            </button>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const poDateInput = document.getElementById('po_date');
        const expectedDateInput = document.getElementById('expected_date');
        const isMandatorySelect = document.getElementById('is_mandatory');

        const expectedDaysDisplay = document.getElementById('expected_days');
        const expectedDaysHidden = document.getElementById('expected_days_hidden');

        function calculateExpectedDays() {
            if (!poDateInput || !expectedDateInput) return;
            const poDate = new Date(poDateInput.value);
            const expectedDate = new Date(expectedDateInput.value);

            if (!isNaN(poDate) && !isNaN(expectedDate)) {
                const days = Math.round((expectedDate - poDate) / (1000 * 60 * 60 * 24));
                expectedDaysDisplay.value = days;
                expectedDaysHidden.value = days;
            } else {
                expectedDaysDisplay.value = '';
                expectedDaysHidden.value = '';
            }
        }

        poDateInput?.addEventListener('change', calculateExpectedDays);
        expectedDateInput?.addEventListener('change', calculateExpectedDays);

        // Requirement Type (Mandatory vs Non-Mandatory) Toggle Logic
        function updateRequirementType() {
            if (!isMandatorySelect) return;
            const isNonMandatory = isMandatorySelect.value === 'Non-Mandatory';

            const fieldsToToggle = [
                document.getElementById('po_date'),
                document.getElementById('party_name'),
                document.getElementById('po_amount'),
                document.getElementById('expected_date')
            ];

            fieldsToToggle.forEach(field => {
                if (field) {
                    if (isNonMandatory) {
                        field.removeAttribute('required');
                    } else {
                        field.setAttribute('required', 'required');
                    }
                }
            });

            document.querySelectorAll('.js-po-qty').forEach(input => {
                const row = input.closest('.po-item-row');
                const isRowVisible = row && row.style.display !== 'none';
                if (isNonMandatory) {
                    input.removeAttribute('required');
                } else if (isRowVisible && !input.disabled) {
                    input.setAttribute('required', 'required');
                }
            });

            document.querySelectorAll('.req-asterisk').forEach(ast => {
                ast.style.display = isNonMandatory ? 'none' : 'inline';
            });
        }

        isMandatorySelect?.addEventListener('change', updateRequirementType);
        updateRequirementType();

        // All Item Data for Tag Selection
        const allItemData = [
            @foreach ($items as $item)
                { desc: @json($item['description']), req: {{ (int)($item['quantity_required'] ?? 0) }}, unit: @json($item['unit'] ?? '-') },
            @endforeach
        ];

        let selectedItemDescs = allItemData.map(i => i.desc);

        const selectedTagPills = document.getElementById('selectedTagPills');
        const tagPlaceholder = document.getElementById('tagPlaceholder');
        const tagDropdownMenu = document.getElementById('tagDropdownMenu');
        const tagSelectBox = document.getElementById('tagSelectBox');
        const selectEl = document.getElementById('item_description');
        const tableRows = document.querySelectorAll('.po-item-row');
        const selectAllCheck = document.getElementById('selectAllPoItems');

        window.toggleTagDropdown = function (e) {
            e.stopPropagation();
            if (tagDropdownMenu) {
                const isVisible = tagDropdownMenu.style.display === 'block';
                tagDropdownMenu.style.display = isVisible ? 'none' : 'block';
                if (tagSelectBox) {
                    tagSelectBox.style.borderColor = isVisible ? '#CBD5E1' : '#2563EB';
                }
            }
        };

        document.addEventListener('click', function (e) {
            if (tagDropdownMenu && !e.target.closest('#tagSelectBox') && !e.target.closest('#tagDropdownMenu')) {
                tagDropdownMenu.style.display = 'none';
                if (tagSelectBox) tagSelectBox.style.borderColor = '#CBD5E1';
            }
        });

        window.toggleTagItem = function (e, desc) {
            e.stopPropagation();
            if (selectedItemDescs.includes(desc)) {
                selectedItemDescs = selectedItemDescs.filter(d => d !== desc);
            } else {
                selectedItemDescs.push(desc);
            }
            renderTagState();
        };

        window.removeTagItem = function (e, desc) {
            e.stopPropagation();
            selectedItemDescs = selectedItemDescs.filter(d => d !== desc);
            renderTagState();
        };

        function renderTagState() {
            // 1. Render pills in control box
            if (selectedTagPills) {
                selectedTagPills.innerHTML = '';
                if (selectedItemDescs.length === 0) {
                    if (tagPlaceholder) tagPlaceholder.style.display = 'inline';
                } else {
                    if (tagPlaceholder) tagPlaceholder.style.display = 'none';
                    selectedItemDescs.forEach(desc => {
                        const item = allItemData.find(i => i.desc === desc);
                        const req = item ? item.req : 0;
                        const unit = item ? item.unit : '-';

                        const pill = document.createElement('div');
                        pill.style.cssText = 'background: #EFF6FF; border: 1px solid #BFDBFE; color: #1E40AF; border-radius: 20px; padding: 3px 10px; font-size: 12px; font-weight: 700; display: inline-flex; align-items: center; gap: 6px;';
                        pill.innerHTML = `
                            <span>${desc}</span>
                            <span style="color: #64748B; font-weight: 600; font-size: 11px;">(Qty: ${req}, Unit: ${unit})</span>
                            <span onclick="removeTagItem(event, '${desc.replace(/'/g, "\\'")}')" 
                                  style="color: #2563EB; font-weight: 800; font-size: 14px; cursor: pointer; padding: 0 3px; border-radius: 50%;">&times;</span>
                        `;
                        selectedTagPills.appendChild(pill);
                    });
                }
            }

            // 2. Update Dropdown option checkmarks & background
            document.querySelectorAll('.tag-dropdown-option').forEach(opt => {
                const desc = opt.getAttribute('data-desc');
                const check = opt.querySelector('.tag-option-check');
                const isSel = selectedItemDescs.includes(desc);
                if (check) check.style.opacity = isSel ? '1' : '0';
                opt.style.background = isSel ? '#EFF6FF' : '#FFFFFF';
            });

            // 3. Update hidden select element
            if (selectEl) {
                Array.from(selectEl.options).forEach(opt => {
                    opt.selected = selectedItemDescs.includes(opt.value);
                });
            }

            // 4. Show/Hide Breakdown Table Rows
            tableRows.forEach(row => {
                const desc = row.getAttribute('data-item-desc');
                const isSel = selectedItemDescs.includes(desc);
                row.style.display = isSel ? '' : 'none';
                const rowCheck = row.querySelector('.po-item-check');
                if (rowCheck) {
                    rowCheck.checked = isSel;
                    const qtyInput = row.querySelector('.js-po-qty');
                    if (qtyInput) {
                        const isNonMandatory = isMandatorySelect && isMandatorySelect.value === 'Non-Mandatory';
                        qtyInput.disabled = !isSel;
                        if (isNonMandatory) {
                            qtyInput.removeAttribute('required');
                        } else {
                            qtyInput.required = isSel;
                        }
                    }
                }
            });

            // 5. Update header select-all check state
            if (selectAllCheck) {
                const total = allItemData.length;
                const count = selectedItemDescs.length;
                selectAllCheck.checked = total > 0 && total === count;
                selectAllCheck.indeterminate = count > 0 && count < total;
            }
        }

        // Table row checkbox listener
        tableRows.forEach(row => {
            const rowCheck = row.querySelector('.po-item-check');
            const desc = row.getAttribute('data-item-desc');
            rowCheck?.addEventListener('change', function () {
                if (this.checked) {
                    if (!selectedItemDescs.includes(desc)) selectedItemDescs.push(desc);
                } else {
                    selectedItemDescs = selectedItemDescs.filter(d => d !== desc);
                }
                renderTagState();
            });
        });

        // Table header select-all checkbox listener
        selectAllCheck?.addEventListener('change', function () {
            if (this.checked) {
                selectedItemDescs = allItemData.map(i => i.desc);
            } else {
                selectedItemDescs = [];
            }
            renderTagState();
        });

        // Initial Render
        renderTagState();

        // Checkbox & Filing Qty Calculations
        document.querySelectorAll('.po-item-row').forEach(row => {
            const qtyInput = row.querySelector('.js-po-qty');
            const remSpan = row.querySelector('.js-po-rem');
            const reqVal = parseFloat(row.querySelector('.js-po-req')?.value) || 0;
            const filedVal = parseFloat(row.querySelector('.js-po-filed')?.value) || 0;
            const cancVal = parseFloat(row.querySelector('.js-po-canc')?.value) || 0;

            function updateRem() {
                const maxRem = Math.max(0, reqVal - (filedVal + cancVal));
                const filingQty = parseFloat(qtyInput?.value) || 0;
                const rem = parseFloat(Math.max(0, maxRem - filingQty).toFixed(4));

                if (remSpan) {
                    const cancelCheck = row.querySelector('.js-cancel-item-check');
                    if (cancelCheck && cancelCheck.checked) {
                        remSpan.innerHTML = '<span style="width: 6px; height: 6px; border-radius: 50%; background: #DC2626; display: inline-block;"></span> Cancelling ' + maxRem;
                        remSpan.style.background = '#FEE2E2';
                        remSpan.style.color = '#DC2626';
                        remSpan.style.border = '1px solid #FECDD3';
                        return;
                    }

                    if (rem <= 0.0001) {
                        remSpan.innerHTML = '<span style="width: 6px; height: 6px; border-radius: 50%; background: #10B981; display: inline-block;"></span> 0 (Fully Filed)';
                        remSpan.style.background = '#ECFDF5';
                        remSpan.style.color = '#047857';
                        remSpan.style.border = '1px solid #A7F3D0';
                    } else {
                        remSpan.innerHTML = '<span style="width: 6px; height: 6px; border-radius: 50%; background: #2563EB; display: inline-block;"></span> ' + rem + ' remaining';
                        remSpan.style.background = '#EFF6FF';
                        remSpan.style.color = '#2563EB';
                        remSpan.style.border = '1px solid #BFDBFE';
                    }
                }
            }

            qtyInput?.addEventListener('input', updateRem);
            updateRem();
        });

        // Cancel Item Checkbox Toggle
        document.querySelectorAll('.js-cancel-item-check').forEach(cancelCheck => {
            cancelCheck.addEventListener('change', function() {
                const row = this.closest('.po-item-row');
                const poCheck = row.querySelector('.po-item-check');
                const qtyInput = row.querySelector('.js-po-qty');
                const remSpan = row.querySelector('.js-po-rem');
                const maxRem = parseFloat(qtyInput?.getAttribute('max')) || 0;

                if (this.checked) {
                    row.style.background = '#FEF2F2';
                    if (poCheck) {
                        poCheck.checked = false;
                        poCheck.disabled = true;
                    }
                    if (qtyInput) {
                        qtyInput.value = 0;
                        qtyInput.disabled = true;
                        qtyInput.removeAttribute('required');
                    }
                    if (remSpan) {
                        remSpan.innerHTML = '<span style="width: 6px; height: 6px; border-radius: 50%; background: #DC2626; display: inline-block;"></span> Cancelling ' + maxRem;
                        remSpan.style.background = '#FEE2E2';
                        remSpan.style.color = '#DC2626';
                        remSpan.style.border = '1px solid #FECDD3';
                    }
                } else {
                    row.style.background = '#FFFFFF';
                    if (poCheck) {
                        poCheck.disabled = false;
                        poCheck.checked = true;
                    }
                    if (qtyInput) {
                        qtyInput.disabled = false;
                        qtyInput.value = maxRem;
                        const isNonMandatory = isMandatorySelect && isMandatorySelect.value === 'Non-Mandatory';
                        if (!isNonMandatory) qtyInput.setAttribute('required', 'required');
                    }
                    qtyInput?.dispatchEvent(new Event('input'));
                }
            });
        });
    });

    // Immediate Cancellation Modal Helpers
    window.openCancelModal = function(indentId, itemDesc, remainingQty) {
        const modal = document.getElementById('immediateCancelModal');
        if (!modal) return;
        document.getElementById('modal_cancel_indent_id').value = indentId;
        document.getElementById('modal_cancel_item_desc').value = itemDesc;
        document.getElementById('modal_cancel_display_desc').value = itemDesc;
        const qtyInput = document.getElementById('modal_cancel_qty');
        qtyInput.value = remainingQty;
        qtyInput.max = remainingQty;
        document.getElementById('modal_cancel_rem_hint').textContent = 'Remaining available to cancel: ' + remainingQty;
        modal.style.display = 'flex';
    };

    window.closeCancelModal = function() {
        const modal = document.getElementById('immediateCancelModal');
        if (modal) modal.style.display = 'none';
    };
</script>

<!-- Immediate Cancel Item Modal -->
<div id="immediateCancelModal" style="display: none; position: fixed; inset: 0; z-index: 99999; background: rgba(15,23,42,0.5); align-items: center; justify-content: center;">
  <div style="background: #FFFFFF; border-radius: 16px; padding: 24px; width: 100%; max-width: 440px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1);">
    <div style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 12px; border-bottom: 1px solid #F1F5F9; margin-bottom: 16px;">
      <h4 style="font-size: 17px; font-weight: 800; color: #0F172A; margin: 0; display: flex; align-items: center; gap: 8px;">
        <svg style="width: 20px; height: 20px; color: #DC2626;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        Cancel Item from Indent
      </h4>
      <button type="button" onclick="closeCancelModal()" style="background: transparent; border: none; font-size: 20px; cursor: pointer; color: #94A3B8;">&times;</button>
    </div>
    
    <form id="immediateCancelForm" method="POST" action="{{ route('indent-register.cancelItem') }}">
      @csrf
      <input type="hidden" name="indent_id" id="modal_cancel_indent_id" value="{{ $indent_id }}">
      <input type="hidden" name="item_description" id="modal_cancel_item_desc">

      <div style="margin-bottom: 14px;">
        <label style="display: block; font-size: 11px; font-weight: 700; color: #475569; text-transform: uppercase; margin-bottom: 4px;">Item Description</label>
        <input type="text" id="modal_cancel_display_desc" readonly disabled
               style="width: 100%; padding: 8px 12px; background: #F8FAFC; border: 1px solid #CBD5E1; border-radius: 8px; font-size: 13px; font-weight: 700; color: #0F172A; outline: none; box-sizing: border-box;">
      </div>

      <div style="margin-bottom: 14px;">
        <label style="display: block; font-size: 11px; font-weight: 700; color: #475569; text-transform: uppercase; margin-bottom: 4px;">Cancel Quantity</label>
        <input type="number" name="cancel_qty" id="modal_cancel_qty" min="0.001" step="any" required
               style="width: 100%; padding: 8px 12px; background: #FFFFFF; border: 1px solid #CBD5E1; border-radius: 8px; font-size: 13px; font-weight: 700; color: #0F172A; outline: none; box-sizing: border-box;">
        <span id="modal_cancel_rem_hint" style="font-size: 11px; color: #64748B; margin-top: 4px; display: block;"></span>
      </div>

      <div style="margin-bottom: 18px;">
        <label style="display: block; font-size: 11px; font-weight: 700; color: #475569; text-transform: uppercase; margin-bottom: 4px;">Cancellation Reason / Remarks</label>
        <textarea name="reason" id="modal_cancel_reason" rows="2" placeholder="e.g., Not required by department, Out of budget..."
                  style="width: 100%; padding: 8px 12px; background: #FFFFFF; border: 1px solid #CBD5E1; border-radius: 8px; font-size: 12px; outline: none; box-sizing: border-box;"></textarea>
      </div>

      <div style="display: flex; justify-content: flex-end; gap: 10px;">
        <button type="button" onclick="closeCancelModal()" style="padding: 8px 16px; border-radius: 8px; font-size: 12px; font-weight: 600; background: #F1F5F9; color: #475569; border: 1px solid #CBD5E1; cursor: pointer;">
          Keep Item
        </button>
        <button type="submit" style="padding: 8px 18px; border-radius: 8px; font-size: 12px; font-weight: 700; color: #FFFFFF; background: #DC2626; border: none; cursor: pointer;">
          Confirm Cancellation
        </button>
      </div>
    </form>
  </div>
</div>