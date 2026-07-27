<div style="width: 100%; max-width: 100%; box-sizing: border-box; font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
    <form method="POST" action="{{ route('indent-register.indentRegisterUpdate', $indent->id) }}" id="editIndentTicketForm">
        @csrf
        @method('PUT')
        <input type="hidden" name="indent_id" value="{{ $indent->indent_id }}">
        <input type="hidden" name="indent_department" value="{{ $indent->indent_department }}">

        <!-- Top Page Header -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
            <div style="display: flex; align-items: center; gap: 14px;">
                <div style="width: 48px; height: 48px; border-radius: 14px; background: #2563EB; color: #FFFFFF; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 12px rgba(37,99,235,0.25);">
                    <svg style="width: 24px; height: 24px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                </div>
                <div>
                    <h1 style="font-size: 24px; font-weight: 800; color: #0F172A; margin: 0 0 2px 0; letter-spacing: -0.5px;">Indent Ticket Details</h1>
                    <p style="font-size: 13px; color: #64748B; margin: 0; font-weight: 500;">Update and manage indent ticket items</p>
                </div>
            </div>

            <!-- Header Action Buttons -->
            <div style="display: flex; align-items: center; gap: 10px;">
                <a href="{{ route('indent.index') }}" 
                   style="background: #FFFFFF; border: 1px solid #CBD5E1; color: #334155; font-weight: 700; font-size: 13px; padding: 10px 18px; border-radius: 10px; text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
                    <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    <span>Back to List</span>
                </a>
                <button type="submit" 
                        style="background: #2563EB; color: #FFFFFF; font-weight: 700; font-size: 13px; padding: 10px 20px; border-radius: 10px; border: none; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 2px 6px rgba(37,99,235,0.25);">
                    <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                    <span>Save Indent Ticket</span>
                </button>
            </div>
        </div>

        <!-- Metadata Section Card (4 Columns) -->
        <div style="background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.03); padding: 24px; margin-bottom: 24px;">
            <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px;">
                
                <!-- Indent Ticket ID -->
                <div>
                    <label style="display: block; font-size: 11px; font-weight: 800; color: #475569; text-transform: uppercase; margin-bottom: 8px;">
                        INDENT TICKET ID <span style="color: #EF4444;">*</span>
                    </label>
                    <div style="position: relative;">
                        <input type="text" value="{{ $indent->indent_id }}" readonly disabled
                               style="width: 100%; padding: 10px 38px 10px 14px; background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 10px; font-size: 14px; font-weight: 800; color: #0F172A; outline: none; box-sizing: border-box;">
                        <span style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); color: #94A3B8;">
                            <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </span>
                    </div>
                </div>

                <!-- Department -->
                <div>
                    <label style="display: block; font-size: 11px; font-weight: 800; color: #475569; text-transform: uppercase; margin-bottom: 8px;">
                        DEPARTMENT <span style="color: #EF4444;">*</span>
                    </label>
                    <div style="position: relative;">
                        <input type="text" value="{{ $department_id }}" readonly disabled
                               style="width: 100%; padding: 10px 38px 10px 14px; background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 10px; font-size: 14px; font-weight: 800; color: #0F172A; outline: none; box-sizing: border-box;">
                        <span style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); color: #94A3B8;">
                            <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m3 0h1m-1-4h.01M9 16h.01M9 12h.01M9 8h.01M15 16h.01M15 12h.01M15 8h.01"/></svg>
                        </span>
                    </div>
                </div>

                <!-- Indent Date -->
                <div>
                    <label for="indent_date" style="display: block; font-size: 11px; font-weight: 800; color: #475569; text-transform: uppercase; margin-bottom: 8px;">
                        INDENT DATE <span style="color: #EF4444;">*</span>
                    </label>
                    <input type="date" name="indent_date" id="indent_date" required value="{{ $indent->indent_date }}"
                           style="width: 100%; padding: 10px 14px; background: #F8FAFC; border: 1px solid #CBD5E1; border-radius: 10px; font-size: 14px; font-weight: 600; color: #0F172A; outline: none; box-sizing: border-box;">
                </div>

                <!-- Indent Project -->
                <div>
                    <label for="indent_project" style="display: block; font-size: 11px; font-weight: 800; color: #475569; text-transform: uppercase; margin-bottom: 8px;">
                        INDENT PROJECT
                    </label>
                    <select name="indent_project" id="indent_project" style="width: 100%; padding: 10px 14px; background: #FFFFFF; border: 1px solid #CBD5E1; border-radius: 10px; font-size: 14px; font-weight: 600; color: #0F172A; outline: none; box-sizing: border-box;">
                        <option value="">-- Select Project --</option>
                        @foreach($projects as $project)
                            @php $current = old('indent_project', $indent->indent_project ?? null); @endphp
                            <option value="{{ $project->name }}" {{ $current === $project->name ? 'selected' : '' }}>
                                {{ $project->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- Indent Items Card Container -->
        <div style="background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.03); padding: 24px; margin-bottom: 24px;">
            
            <!-- Items Header Toolbar -->
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="width: 38px; height: 38px; border-radius: 10px; background: #EFF6FF; color: #2563EB; display: flex; align-items: center; justify-content: center; border: 1px solid #BFDBFE;">
                        <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    </div>
                    <div>
                        <h2 style="font-size: 18px; font-weight: 800; color: #0F172A; margin: 0;">Indent Items</h2>
                        <p style="font-size: 12px; color: #64748B; margin: 0;">Add items to your indent request</p>
                    </div>
                </div>

                <!-- Add New Item Button -->
                <button type="button" id="add-item" 
                        style="background: #FFFFFF; border: 1px solid #C7D2FE; color: #2563EB; font-weight: 700; font-size: 13px; padding: 9px 18px; border-radius: 10px; cursor: pointer; display: inline-flex; align-items: center; gap: 6px;">
                    <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                    <span>Add New Item</span>
                </button>
            </div>

            <!-- Dynamic Item Rows -->
            <div id="items-container" style="display: flex; flex-direction: column; gap: 16px;">
                @php $itemsDecoded = json_decode($indent->items_description, true) ?? []; @endphp
                @foreach($itemsDecoded as $i => $item)
                    <div class="item-row" style="background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 14px; padding: 18px; display: grid; grid-template-columns: 44px 2.2fr 1.2fr 1fr 1fr 1fr 1fr; gap: 14px; align-items: end; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
                        <!-- Delete Button -->
                        <div>
                            <button type="button" class="remove-row" style="width: 40px; height: 42px; border-radius: 10px; border: 1px solid #FECDD3; background: #FEF2F2; color: #DC2626; display: flex; align-items: center; justify-content: center; cursor: pointer;">
                                <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </div>

                        <!-- Item Description -->
                        <div>
                            <label style="display: block; font-size: 10px; font-weight: 800; color: #475569; text-transform: uppercase; margin-bottom: 6px;">
                                ITEM DESCRIPTION <span style="color: #EF4444;">*</span>
                            </label>
                            <select name="items[{{ $i }}][description]" class="form-control choices-js" required style="width: 100%; height: 42px; border: 1px solid #CBD5E1; border-radius: 10px; font-size: 13px; font-weight: 600;">
                                <option value="">-- Select Item --</option>
                                @foreach($items as $it)
                                    <option value="{{ $it->name }}" {{ ($item['description'] ?? '') == $it->name ? 'selected' : '' }}>
                                        {{ $it->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Unit -->
                        <div>
                            <label style="display: block; font-size: 10px; font-weight: 800; color: #475569; text-transform: uppercase; margin-bottom: 6px;">
                                UNIT <span style="color: #EF4444;">*</span>
                            </label>
                            <select name="items[{{ $i }}][unit]" required style="width: 100%; height: 42px; border: 1px solid #CBD5E1; border-radius: 10px; font-size: 13px; font-weight: 600; padding: 0 10px; background: #FFFFFF; outline: none;">
                                <option value="">-- Select Unit --</option>
                                @foreach($units as $unit)
                                    <option value="{{ $unit->id }}" {{ ($item['unit'] ?? '') == $unit->id ? 'selected' : '' }}>
                                        {{ $unit->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Quantity Required -->
                        <div>
                            <label style="display: block; font-size: 10px; font-weight: 800; color: #475569; text-transform: uppercase; margin-bottom: 6px;">
                                QUANTITY REQUIRED <span style="color: #EF4444;">*</span>
                            </label>
                            <input type="number" name="items[{{ $i }}][required]" class="qty-required" value="{{ $item['quantity_required'] ?? 0 }}" min="0" required
                                   style="width: 100%; height: 42px; border: 1px solid #CBD5E1; border-radius: 10px; font-size: 14px; font-weight: 700; padding: 0 12px; color: #0F172A; outline: none; box-sizing: border-box;">
                        </div>

                        <!-- Quantity Received -->
                        <div>
                            <label style="display: block; font-size: 10px; font-weight: 800; color: #64748B; text-transform: uppercase; margin-bottom: 6px;">
                                QUANTITY RECEIVED
                            </label>
                            <input type="number" name="items[{{ $i }}][received]" class="qty-received" value="{{ $item['quantity_received'] ?? 0 }}" min="0" readonly
                                   style="width: 100%; height: 42px; background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 10px; font-size: 14px; font-weight: 700; padding: 0 12px; color: #475569; outline: none; box-sizing: border-box;">
                        </div>

                        <!-- Quantity Cancelled -->
                        <div>
                            <label style="display: block; font-size: 10px; font-weight: 800; color: #64748B; text-transform: uppercase; margin-bottom: 6px;">
                                QTY CANCELLED
                            </label>
                            <input type="number" name="items[{{ $i }}][cancelled]" class="qty-cancelled" value="{{ $item['quantity_cancelled'] ?? 0 }}" min="0" readonly
                                   style="width: 100%; height: 42px; background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 10px; font-size: 14px; font-weight: 700; padding: 0 12px; color: #475569; outline: none; box-sizing: border-box;">
                        </div>

                        <!-- Quantity Balance -->
                        <div>
                            <label style="display: block; font-size: 10px; font-weight: 800; color: #2563EB; text-transform: uppercase; margin-bottom: 6px;">
                                QUANTITY BALANCE
                            </label>
                            <input type="number" name="items[{{ $i }}][balance]" class="qty-balance" value="{{ $item['quantity_balance'] ?? 0 }}" readonly
                                   style="width: 100%; height: 42px; background: #EFF6FF; border: 1px solid #DBEAFE; border-radius: 10px; font-size: 15px; font-weight: 800; padding: 0 12px; color: #2563EB; outline: none; box-sizing: border-box;">
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Bottom Summary Metrics Bar (5 Stat Cards) -->
            <div style="background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 14px; padding: 18px; margin-top: 24px; display: grid; grid-template-columns: repeat(5, 1fr); gap: 16px;">
                <!-- Total Items -->
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="width: 36px; height: 36px; border-radius: 10px; background: #F3E8FF; color: #7C3AED; display: flex; align-items: center; justify-content: center;">
                        <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <div>
                        <span style="font-size: 11px; font-weight: 700; color: #64748B; display: block;">Total Items</span>
                        <span id="summary-total-items" style="font-size: 16px; font-weight: 800; color: #2563EB;">{{ count($itemsDecoded) }}</span>
                    </div>
                </div>

                <!-- Total Quantity Required -->
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="width: 36px; height: 36px; border-radius: 10px; background: #ECFDF5; color: #059669; display: flex; align-items: center; justify-content: center;">
                        <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    </div>
                    <div>
                        <span style="font-size: 11px; font-weight: 700; color: #64748B; display: block;">Total Qty Required</span>
                        <span id="summary-total-required" style="font-size: 16px; font-weight: 800; color: #16A34A;">0</span>
                    </div>
                </div>

                <!-- Total Quantity Received -->
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="width: 36px; height: 36px; border-radius: 10px; background: #FFF7ED; color: #EA580C; display: flex; align-items: center; justify-content: center;">
                        <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    </div>
                    <div>
                        <span style="font-size: 11px; font-weight: 700; color: #64748B; display: block;">Total Qty Received</span>
                        <span id="summary-total-received" style="font-size: 16px; font-weight: 800; color: #EA580C;">0</span>
                    </div>
                </div>

                <!-- Total Quantity Cancelled -->
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="width: 36px; height: 36px; border-radius: 10px; background: #FEF2F2; color: #DC2626; display: flex; align-items: center; justify-content: center;">
                        <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                    </div>
                    <div>
                        <span style="font-size: 11px; font-weight: 700; color: #64748B; display: block;">Total Qty Cancelled</span>
                        <span id="summary-total-cancelled" style="font-size: 16px; font-weight: 800; color: #DC2626;">0</span>
                    </div>
                </div>

                <!-- Total Quantity Balance -->
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="width: 36px; height: 36px; border-radius: 10px; background: #EFF6FF; color: #2563EB; display: flex; align-items: center; justify-content: center;">
                        <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5 5 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5 5 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/></svg>
                    </div>
                    <div>
                        <span style="font-size: 11px; font-weight: 700; color: #64748B; display: block;">Total Qty Balance</span>
                        <span id="summary-total-balance" style="font-size: 16px; font-weight: 800; color: #2563EB;">0</span>
                    </div>
                </div>
            </div>

            <!-- Remarks Textarea Field -->
            <div style="margin-top: 24px;">
                <label for="remarks" style="display: block; font-size: 11px; font-weight: 800; color: #475569; text-transform: uppercase; margin-bottom: 8px;">
                    REMARKS
                </label>
                <textarea name="remarks" id="remarks" rows="2" placeholder="Enter remarks (optional)..."
                          style="width: 100%; padding: 12px; background: #FAFAFA; border: 1px solid #CBD5E1; border-radius: 10px; font-size: 13px; outline: none; box-sizing: border-box;">{{ old('remarks', $indent->remarks) }}</textarea>
            </div>
        </div>
    </form>
</div>

<!-- Template for Cloning Rows -->
<template id="item-template">
    <div class="item-row" style="background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 14px; padding: 18px; display: grid; grid-template-columns: 44px 2.2fr 1.2fr 1fr 1fr 1fr 1fr; gap: 14px; align-items: end; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
        <div>
            <button type="button" class="remove-row" style="width: 40px; height: 42px; border-radius: 10px; border: 1px solid #FECDD3; background: #FEF2F2; color: #DC2626; display: flex; align-items: center; justify-content: center; cursor: pointer;">
                <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </button>
        </div>

        <div>
            <label style="display: block; font-size: 10px; font-weight: 800; color: #475569; text-transform: uppercase; margin-bottom: 6px;">
                ITEM DESCRIPTION <span style="color: #EF4444;">*</span>
            </label>
            <select name="items[__index__][description]" class="form-control choices-js" required style="width: 100%; height: 42px; border: 1px solid #CBD5E1; border-radius: 10px; font-size: 13px; font-weight: 600;">
                <option value="">-- Select Item --</option>
                @foreach($items as $item)
                    <option value="{{ $item->name }}">{{ $item->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label style="display: block; font-size: 10px; font-weight: 800; color: #475569; text-transform: uppercase; margin-bottom: 6px;">
                UNIT <span style="color: #EF4444;">*</span>
            </label>
            <select name="items[__index__][unit]" required style="width: 100%; height: 42px; border: 1px solid #CBD5E1; border-radius: 10px; font-size: 13px; font-weight: 600; padding: 0 10px; background: #FFFFFF; outline: none;">
                <option value="">-- Select Unit --</option>
                @foreach($units as $unit)
                    <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label style="display: block; font-size: 10px; font-weight: 800; color: #475569; text-transform: uppercase; margin-bottom: 6px;">
                QUANTITY REQUIRED <span style="color: #EF4444;">*</span>
            </label>
            <input type="number" name="items[__index__][required]" class="qty-required" value="0" min="0" required
                   style="width: 100%; height: 42px; border: 1px solid #CBD5E1; border-radius: 10px; font-size: 14px; font-weight: 700; padding: 0 12px; color: #0F172A; outline: none; box-sizing: border-box;">
        </div>

        <div>
            <label style="display: block; font-size: 10px; font-weight: 800; color: #64748B; text-transform: uppercase; margin-bottom: 6px;">
                QUANTITY RECEIVED
            </label>
            <input type="number" name="items[__index__][received]" class="qty-received" value="0" min="0" readonly
                   style="width: 100%; height: 42px; background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 10px; font-size: 14px; font-weight: 700; padding: 0 12px; color: #475569; outline: none; box-sizing: border-box;">
        </div>

        <div>
            <label style="display: block; font-size: 10px; font-weight: 800; color: #64748B; text-transform: uppercase; margin-bottom: 6px;">
                QTY CANCELLED
            </label>
            <input type="number" name="items[__index__][cancelled]" class="qty-cancelled" value="0" min="0" readonly
                   style="width: 100%; height: 42px; background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 10px; font-size: 14px; font-weight: 700; padding: 0 12px; color: #475569; outline: none; box-sizing: border-box;">
        </div>

        <div>
            <label style="display: block; font-size: 10px; font-weight: 800; color: #2563EB; text-transform: uppercase; margin-bottom: 6px;">
                QUANTITY BALANCE
            </label>
            <input type="number" name="items[__index__][balance]" class="qty-balance" value="0" readonly
                   style="width: 100%; height: 42px; background: #EFF6FF; border: 1px solid #DBEAFE; border-radius: 10px; font-size: 15px; font-weight: 800; padding: 0 12px; color: #2563EB; outline: none; box-sizing: border-box;">
        </div>
    </div>
</template>

<!-- Choices.js -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css">
<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        let index = {{ count($itemsDecoded) }};

        function initChoices(select) {
            if (!select) return;
            return new Choices(select, {
                searchEnabled: true,
                itemSelectText: '',
                placeholder: true,
                shouldSort: false,
            });
        }

        function calculateOverallSummary() {
            const rows = document.querySelectorAll('.item-row');
            let totalItems = rows.length;
            let totalReq = 0, totalRec = 0, totalCanc = 0, totalBal = 0;

            rows.forEach(row => {
                const req = parseFloat(row.querySelector('.qty-required')?.value) || 0;
                const rec = parseFloat(row.querySelector('.qty-received')?.value) || 0;
                const canc = parseFloat(row.querySelector('.qty-cancelled')?.value) || 0;
                const bal = parseFloat(row.querySelector('.qty-balance')?.value) || 0;

                totalReq += req;
                totalRec += rec;
                totalCanc += canc;
                totalBal += bal;
            });

            document.getElementById('summary-total-items').textContent = totalItems;
            document.getElementById('summary-total-required').textContent = totalReq;
            document.getElementById('summary-total-received').textContent = totalRec;
            document.getElementById('summary-total-cancelled').textContent = totalCanc;
            document.getElementById('summary-total-balance').textContent = totalBal;
        }

        function updateQtyListeners(row) {
            const qtyRequired = row.querySelector('.qty-required');
            const qtyReceived = row.querySelector('.qty-received');
            const qtyCancelled = row.querySelector('.qty-cancelled');
            const qtyBalance = row.querySelector('.qty-balance');

            function calc() {
                const req = parseFloat(qtyRequired?.value) || 0;
                let rec = parseFloat(qtyReceived?.value) || 0;
                let canc = parseFloat(qtyCancelled?.value) || 0;

                if (rec < 0) { rec = 0; if (qtyReceived) qtyReceived.value = 0; }
                if (canc < 0) { canc = 0; if (qtyCancelled) qtyCancelled.value = 0; }

                if (req > 0 && rec > req) {
                    rec = req;
                    if (qtyReceived) qtyReceived.value = req;
                }

                if (req > 0 && (rec + canc) > req) {
                    canc = req - rec;
                    if (qtyCancelled) qtyCancelled.value = canc;
                }

                qtyBalance.value = Math.max(req - (rec + canc), 0);
                calculateOverallSummary();
            }

            qtyRequired?.addEventListener('input', calc);
            qtyReceived?.addEventListener('input', calc);
            qtyCancelled?.addEventListener('input', calc);

            calc();
        }

        function addRemoveHandler(row) {
            row.querySelector('.remove-row')?.addEventListener('click', () => {
                if (document.querySelectorAll('.item-row').length > 1) {
                    row.remove();
                    calculateOverallSummary();
                }
            });
        }

        document.querySelectorAll('.choices-js').forEach(initChoices);
        document.querySelectorAll('.item-row').forEach(row => {
            updateQtyListeners(row);
            addRemoveHandler(row);
        });

        document.getElementById('add-item').addEventListener('click', () => {
            const container = document.getElementById('items-container');
            const template = document.getElementById('item-template').innerHTML.replace(/__index__/g, index);
            const wrapper = document.createElement('div');
            wrapper.innerHTML = template.trim();
            const newRow = wrapper.firstElementChild;

            container.appendChild(newRow);
            newRow.querySelectorAll('.choices-js').forEach(initChoices);
            updateQtyListeners(newRow);
            addRemoveHandler(newRow);

            index++;
            calculateOverallSummary();
        });

        calculateOverallSummary();
    });
</script>