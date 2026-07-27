<div style="background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.03); padding: 24px; margin-bottom: 24px;">
    
    <!-- Top Header -->
    <div style="display: flex; align-items: center; gap: 14px; margin-bottom: 20px;">
        <div style="width: 44px; height: 44px; border-radius: 12px; background: #2563EB; color: #FFFFFF; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 12px rgba(37,99,235,0.25);">
            <svg style="width: 22px; height: 22px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        </div>
        <div>
            <h1 style="font-size: 20px; font-weight: 800; color: #0F172A; margin: 0 0 2px 0; letter-spacing: -0.3px;">Create / File New Indent</h1>
            <p style="font-size: 13px; color: #64748B; margin: 0; font-weight: 500;">Select department and enter indent ticket ID to file a new indent request.</p>
        </div>
    </div>

    <!-- Form -->
    <form method="POST" action="{{ route('indent.store') }}" style="display: flex; flex-direction: column; gap: 20px;">
        @csrf

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <!-- Select Department -->
            <div>
                <label for="department_id" style="display: block; font-size: 12px; font-weight: 800; color: #475569; margin-bottom: 8px;">
                    Select Department <span style="color: #EF4444;">*</span>
                </label>
                <div style="position: relative;">
                    <select name="department_id" id="department_id" class="choices-js" required style="width: 100%; height: 42px; border: 1px solid #CBD5E1; border-radius: 10px; font-size: 13px; font-weight: 600;">
                        <option value="">-- Select Department --</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                        @endforeach
                    </select>
                </div>
                @error('department_id')
                    <small style="color: #EF4444; font-size: 12px; margin-top: 4px; display: block;">{{ $message }}</small>
                @enderror
            </div>

            <!-- Indent Ticket ID -->
            <div>
                <label for="indent_id" style="display: block; font-size: 12px; font-weight: 800; color: #475569; margin-bottom: 8px;">
                    Indent Ticket ID <span style="color: #EF4444;">*</span>
                </label>
                <div style="position: relative;">
                    <span style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #94A3B8;">
                        <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                    </span>
                    <input type="number" name="indent_id" id="indent_id" required min="0" placeholder="Enter Indent Ticket ID"
                           style="width: 100%; padding: 10px 14px 10px 38px; background: #FFFFFF; border: 1px solid #CBD5E1; border-radius: 10px; font-size: 13px; font-weight: 600; color: #0F172A; outline: none; box-sizing: border-box;">
                </div>
                @error('indent_id')
                    <small style="color: #EF4444; font-size: 12px; margin-top: 4px; display: block;">{{ $message }}</small>
                @enderror
            </div>
        </div>

        <!-- Submit Button -->
        <div style="display: flex; justify-content: flex-end; margin-top: 4px;">
            <button type="submit" 
                    style="background: #2563EB; color: #FFFFFF; font-weight: 700; font-size: 13px; padding: 10px 22px; border-radius: 10px; border: none; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 2px 6px rgba(37,99,235,0.25);">
                <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span>File New Indent</span>
            </button>
        </div>
    </form>
</div>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css">
<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.choices-js').forEach(el => {
    new Choices(el, { searchEnabled: true, itemSelectText: '' });
  });
});
</script>
