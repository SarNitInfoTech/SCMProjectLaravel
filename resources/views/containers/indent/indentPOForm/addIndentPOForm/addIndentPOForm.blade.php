<div style="width: 100%; max-width: 100%; box-sizing: border-box; font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
    <form method="POST" action="{{ route('po-register.store') }}" style="display: flex; flex-direction: column; gap: 24px;">
        @csrf

        <input type="hidden" name="indent_id" value="{{ $indent_id }}">
        <input type="hidden" name="department_id" value="{{ $department_id }}">

        <!-- Top Metadata Card Section (3 Rows x 4 Columns) -->
        <div style="background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.03); padding: 24px;">
            
            <!-- Row 1 -->
            <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 20px;">
                <!-- Indent ID -->
                <div>
                    <label style="display: block; font-size: 11px; font-weight: 800; color: #475569; text-transform: uppercase; margin-bottom: 8px;">
                        INDENT ID <span style="color: #EF4444;">*</span>
                    </label>
                    <input type="text" value="{{ $indent_id }}" readonly disabled
                           style="width: 100%; padding: 10px 14px; background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 10px; font-size: 14px; font-weight: 800; color: #0F172A; outline: none; box-sizing: border-box;">
                </div>

                <!-- Department -->
                <div>
                    <label style="display: block; font-size: 11px; font-weight: 800; color: #475569; text-transform: uppercase; margin-bottom: 8px;">
                        DEPARTMENT <span style="color: #EF4444;">*</span>
                    </label>
                    <input type="text" value="{{ $department_id }}" readonly disabled
                           style="width: 100%; padding: 10px 14px; background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 10px; font-size: 14px; font-weight: 800; color: #0F172A; outline: none; box-sizing: border-box;">
                </div>

                <!-- Requirement Type -->
                <div>
                    <label for="is_mandatory" style="display: block; font-size: 11px; font-weight: 800; color: #475569; text-transform: uppercase; margin-bottom: 8px;">
                        REQUIREMENT TYPE <span style="color: #EF4444;">*</span>
                    </label>
                    <select name="is_mandatory" id="is_mandatory" style="width: 100%; padding: 10px 14px; background: #FFFFFF; border: 1px solid #CBD5E1; border-radius: 10px; font-size: 13px; font-weight: 600; color: #0F172A; outline: none; box-sizing: border-box;">
                        <option value="Mandatory" {{ old('is_mandatory', 'Mandatory') === 'Mandatory' ? 'selected' : '' }}>Mandatory</option>
                        <option value="Non-Mandatory" {{ old('is_mandatory') === 'Non-Mandatory' ? 'selected' : '' }}>Non-Mandatory</option>
                    </select>
                </div>

                <!-- Status -->
                <div>
                    <label for="status" style="display: block; font-size: 11px; font-weight: 800; color: #475569; text-transform: uppercase; margin-bottom: 8px;">
                        STATUS <span style="color: #EF4444;">*</span>
                    </label>
                    <select name="status" id="status" disabled style="width: 100%; padding: 10px 14px; background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 10px; font-size: 13px; font-weight: 700; color: #475569; outline: none; box-sizing: border-box; cursor: not-allowed;">
                        <option value="Pending" selected>Pending</option>
                    </select>
                    <input type="hidden" name="status" value="Pending">
                </div>
            </div>

            <!-- Row 2 -->
            <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 20px;">
                <!-- PO Date -->
                <div>
                    <label for="po_date" style="display: block; font-size: 11px; font-weight: 800; color: #475569; text-transform: uppercase; margin-bottom: 8px;">
                        PO DATE <span style="color: #EF4444;">*</span>
                    </label>
                    <input type="date" name="po_date" id="po_date" required value="{{ date('Y-m-d') }}"
                           style="width: 100%; padding: 10px 14px; background: #FFFFFF; border: 1px solid #CBD5E1; border-radius: 10px; font-size: 13px; font-weight: 600; color: #0F172A; outline: none; box-sizing: border-box;">
                </div>

                <!-- Party Name -->
                <div>
                    <label for="party_name" style="display: block; font-size: 11px; font-weight: 800; color: #475569; text-transform: uppercase; margin-bottom: 8px;">
                        PARTY NAME <span style="color: #EF4444;">*</span>
                    </label>
                    <select name="party_name" id="party_name" required style="width: 100%; padding: 10px 14px; background: #FFFFFF; border: 1px solid #CBD5E1; border-radius: 10px; font-size: 13px; font-weight: 600; color: #0F172A; outline: none; box-sizing: border-box;">
                        <option value="">Select party</option>
                        @foreach ($projectList as $vendor)
                            <option value="{{ $vendor->name }}">{{ $vendor->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- PO/WO No. -->
                <div>
                    <label for="po_wo_no" style="display: block; font-size: 11px; font-weight: 800; color: #475569; text-transform: uppercase; margin-bottom: 8px;">
                        PO/WO NO. <span style="color: #EF4444;">*</span>
                    </label>
                    <input type="text" name="po_wo_no" id="po_wo_no" required placeholder="Enter PO/WO No."
                           style="width: 100%; padding: 10px 14px; background: #FFFFFF; border: 1px solid #CBD5E1; border-radius: 10px; font-size: 13px; font-weight: 600; color: #0F172A; outline: none; box-sizing: border-box;">
                </div>

                <!-- PO Amount -->
                <div>
                    <label for="po_amount" style="display: block; font-size: 11px; font-weight: 800; color: #475569; text-transform: uppercase; margin-bottom: 8px;">
                        PO AMOUNT <span style="color: #EF4444;">*</span>
                    </label>
                    <input type="number" name="po_amount" id="po_amount" step="1" min="0" required placeholder="Enter amount"
                           style="width: 100%; padding: 10px 14px; background: #FFFFFF; border: 1px solid #CBD5E1; border-radius: 10px; font-size: 14px; font-weight: 700; color: #0F172A; outline: none; box-sizing: border-box;">
                </div>
            </div>

            <!-- Row 3 -->
            <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px;">
                <!-- Item Description Multi Select -->
                <div>
                    <label for="item_description" style="display: block; font-size: 11px; font-weight: 800; color: #475569; text-transform: uppercase; margin-bottom: 8px;">
                        ITEM DESCRIPTION <span style="color: #EF4444;">*</span>
                    </label>
                    <select name="item_description[]" id="item_description" class="choices-multiple-remove" multiple required
                            style="width: 100%; border: 1px solid #CBD5E1; border-radius: 10px; font-size: 13px; font-weight: 600;">
                        @foreach ($items as $item)
                            @php
                                $req = (int)($item['quantity_required'] ?? 0);
                                $filed = (int)($item['already_filed'] ?? 0);
                                $rem = (int)($item['remaining_to_file'] ?? max(0, $req - $filed));
                                $label = $item['description'] . ($req > 0 ? " (Req: {$req}, Filed: {$filed}, Rem: {$rem})" : '');
                            @endphp
                            <option value="{{ $item['description'] }}" selected>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Expected Date -->
                <div>
                    <label for="expected_date" style="display: block; font-size: 11px; font-weight: 800; color: #475569; text-transform: uppercase; margin-bottom: 8px;">
                        EXPECTED DATE <span style="color: #EF4444;">*</span>
                    </label>
                    <input type="date" name="expected_date" id="expected_date" required
                           style="width: 100%; padding: 10px 14px; background: #FFFFFF; border: 1px solid #CBD5E1; border-radius: 10px; font-size: 13px; font-weight: 600; color: #0F172A; outline: none; box-sizing: border-box;">
                </div>

                <!-- Expected Days -->
                <div>
                    <label for="expected_days" style="display: block; font-size: 11px; font-weight: 800; color: #475569; text-transform: uppercase; margin-bottom: 8px;">
                        EXPECTED DAYS
                    </label>
                    <input type="text" id="expected_days" readonly disabled
                           style="width: 100%; padding: 10px 14px; background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 10px; font-size: 13px; font-weight: 700; color: #475569; outline: none; box-sizing: border-box;">
                    <input type="hidden" name="expected_days" id="expected_days_hidden">
                </div>

                <!-- Remarks -->
                <div>
                    <label for="remarks" style="display: block; font-size: 11px; font-weight: 800; color: #475569; text-transform: uppercase; margin-bottom: 8px;">
                        REMARKS
                    </label>
                    <textarea name="remarks" id="remarks" rows="1" placeholder="Enter PO remarks (optional)..."
                              style="width: 100%; padding: 10px 14px; background: #FAFAFA; border: 1px solid #CBD5E1; border-radius: 10px; font-size: 13px; outline: none; box-sizing: border-box;">{{ old('remarks') }}</textarea>
                </div>
            </div>
        </div>

        <!-- Bottom Breakdown Card Section -->
        @if(!empty($items) && count($items) > 0)
        <div id="po-items-table-container" style="background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.03); padding: 24px;">
            <h3 style="font-size: 16px; font-weight: 800; color: #0F172A; margin: 0 0 16px 0;">
                Item Description & Filing Quantity (Count) Breakdown <span style="color: #EF4444;">*</span>
            </h3>

            <div style="width: 100%; overflow-x: auto; background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 14px; overflow: hidden;">
                <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 13px;">
                    <thead>
                        <tr style="background: #F1F5F9; border-bottom: 1px solid #E2E8F0; color: #475569; font-size: 11px; font-weight: 700; text-transform: uppercase;">
                            <th style="padding: 14px 16px; width: 60px; text-align: center; vertical-align: middle;">SELECT</th>
                            <th style="padding: 14px 16px; vertical-align: middle;">ITEM DESCRIPTION</th>
                            <th style="padding: 14px 16px; width: 100px; text-align: center; vertical-align: middle;">UNIT</th>
                            <th style="padding: 14px 16px; width: 130px; text-align: center; vertical-align: middle;">QTY REQUIRED</th>
                            <th style="padding: 14px 16px; width: 140px; text-align: center; vertical-align: middle;">PREVIOUSLY FILED</th>
                            <th style="padding: 14px 16px; width: 180px; text-align: center; vertical-align: middle;">FILING PO QTY (COUNT)</th>
                            <th style="padding: 14px 16px; width: 170px; text-align: center; vertical-align: middle;">REMAINING TO FILE</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($items as $idx => $item)
                            @php
                                $req = (int)($item['quantity_required'] ?? 1);
                                $rec = (int)($item['quantity_received'] ?? 0);
                                $filed = (int)($item['already_filed'] ?? 0);
                                $rem = (int)($item['remaining_to_file'] ?? max(0, $req - $filed));
                                $defaultFiling = $rem > 0 ? $rem : $req;
                            @endphp
                            <tr class="po-item-row" data-item-desc="{{ $item['description'] }}" style="border-bottom: 1px solid #E2E8F0; background: #FFFFFF;">
                                <td style="padding: 14px 16px; text-align: center; vertical-align: middle;">
                                    <input type="checkbox" name="po_items[{{ $idx }}][selected]" value="1" checked class="po-item-check"
                                           style="width: 18px; height: 18px; border-radius: 4px; accent-color: #2563EB; cursor: pointer;">
                                </td>
                                <td style="padding: 14px 16px; font-weight: 700; color: #0F172A; vertical-align: middle;">
                                    {{ $item['description'] }}
                                    <input type="hidden" name="po_items[{{ $idx }}][description]" value="{{ $item['description'] }}">
                                    <input type="hidden" name="po_items[{{ $idx }}][unit]" value="{{ $item['unit'] ?? '' }}">
                                    <input type="hidden" name="po_items[{{ $idx }}][quantity_required]" value="{{ $req }}" class="js-po-req">
                                    <input type="hidden" name="po_items[{{ $idx }}][quantity_received]" value="{{ $rec }}" class="js-po-rec">
                                    <input type="hidden" name="po_items[{{ $idx }}][already_filed]" value="{{ $filed }}" class="js-po-filed">
                                </td>
                                <td style="padding: 14px 16px; text-align: center; color: #475569; font-weight: 600; vertical-align: middle;">{{ $item['unit'] ?? '-' }}</td>
                                <td style="padding: 14px 16px; text-align: center; font-weight: 800; color: #0F172A; vertical-align: middle;">{{ $req }}</td>
                                <td style="padding: 14px 16px; text-align: center; color: #2563EB; font-weight: 800; vertical-align: middle;">{{ $filed }}</td>
                                <td style="padding: 14px 16px; text-align: center; vertical-align: middle;">
                                    <input type="number" 
                                           name="po_items[{{ $idx }}][po_quantity]" 
                                           value="{{ $defaultFiling }}" 
                                           min="1" 
                                           max="{{ $rem }}"
                                           class="js-po-qty" required
                                           style="width: 110px; height: 38px; text-align: center; border: 1px solid #CBD5E1; border-radius: 8px; font-size: 14px; font-weight: 800; color: #0F172A; outline: none; box-sizing: border-box;">
                                </td>
                                <td style="padding: 14px 16px; text-align: center; vertical-align: middle;">
                                    <span class="js-po-rem" style="font-weight: 800; font-size: 12px; padding: 4px 12px; border-radius: 20px; display: inline-block; {{ max(0, $rem - $defaultFiling) > 0 ? 'background: #EFF6FF; color: #2563EB; border: 1px solid #BFDBFE;' : 'background: #ECFDF5; color: #047857; border: 1px solid #A7F3D0;' }}">
                                        {{ max(0, $rem - $defaultFiling) > 0 ? max(0, $rem - $defaultFiling) . ' remaining' : '0 (Fully Filed)' }}
                                    </span>
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
                    style="background: #2563EB; color: #FFFFFF; font-weight: 700; font-size: 14px; padding: 12px 28px; border-radius: 10px; border: none; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 4px 12px rgba(37,99,235,0.25);">
                <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span>Submit PO</span>
            </button>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const poDateInput = document.getElementById('po_date');
        const expectedDateInput = document.getElementById('expected_date');

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

        // Checkbox & Filing Qty Calculations
        document.querySelectorAll('.po-item-row').forEach(row => {
            const check = row.querySelector('.po-item-check');
            const qtyInput = row.querySelector('.js-po-qty');
            const remSpan = row.querySelector('.js-po-rem');
            const reqVal = parseFloat(row.querySelector('.js-po-req')?.value) || 0;
            const filedVal = parseFloat(row.querySelector('.js-po-filed')?.value) || 0;

            function updateRem() {
                const maxRem = Math.max(0, reqVal - filedVal);
                const filingQty = parseFloat(qtyInput?.value) || 0;
                const rem = Math.max(0, maxRem - filingQty);

                if (remSpan) {
                    if (rem === 0) {
                        remSpan.textContent = '0 (Fully Filed)';
                        remSpan.style.background = '#ECFDF5';
                        remSpan.style.color = '#047857';
                        remSpan.style.border = '1px solid #A7F3D0';
                    } else {
                        remSpan.textContent = rem + ' remaining';
                        remSpan.style.background = '#EFF6FF';
                        remSpan.style.color = '#2563EB';
                        remSpan.style.border = '1px solid #BFDBFE';
                    }
                }
            }

            qtyInput?.addEventListener('input', updateRem);
            check?.addEventListener('change', function () {
                if (qtyInput) qtyInput.disabled = !this.checked;
            });
            updateRem();
        });
    });
</script>