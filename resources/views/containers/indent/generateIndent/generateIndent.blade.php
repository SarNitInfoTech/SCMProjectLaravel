<div class="w-full px-4 py-6 bg-white shadow rounded">

    <form method="POST" action="{{ route('indent.store') }}" class="grid grid-rows-[auto_1fr] gap-6 h-full">
        @csrf

        <!-- Department Dropdown -->
        <div class="w-full">
            <label for="department_id" class="form-label block mb-1">
                Select Department <span class="text-red-500">*</span>
            </label>
            <select name="department_id" id="department_id" class="form-control choices-js w-full" required>
                <option value="">-- Select Department --</option>
                @foreach($departments as $dept)
                    <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                @endforeach
            </select>

            @error('department_id')
                <small class="text-red-600">{{ $message }}</small>
            @enderror
        </div>

        <!-- Indent ID (manually fillable) -->
        <div id="indent_id_wrapper" class="w-full">
            <label for="indent_id" class="form-label text-black block mb-1">Indent Ticket ID</label>
            <input type="number" name="indent_id" id="indent_id" class="form-control w-full bg-white"
                placeholder="Enter Indent Ticket ID" required min="0">
            @error('indent_id')
                <small class="text-red-600">{{ $message }}</small>
            @enderror
        </div>

        <div class="flex justify-end">
            <button type="submit" class="ti-btn ti-btn-primary-full">
                File New Indent
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
