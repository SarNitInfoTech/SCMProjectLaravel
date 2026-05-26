<div class="w-full px-4 py-6 bg-white shadow rounded">
    <div class="mb-4 pb-2 border-b">
        <h3 class="text-lg font-semibold text-gray-800">Edit Stock Entry</h3>
        <p class="text-sm text-gray-500">Modify threshold values, storage location, or status for this stock record.</p>
    </div>

    <form method="POST" action="{{ route('inventory.stocks.update', $stock->id) }}" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 bg-gray-50 p-4 rounded border">
            <!-- Item (Read-only) -->
            <div>
                <label class="block text-sm font-semibold text-gray-600 mb-1">Item Name</label>
                <input type="text" class="w-full bg-gray-200 text-gray-700 rounded border p-2.5 cursor-not-allowed font-medium" value="{{ $stock->item?->name }}" disabled>
            </div>

            <!-- Department (Read-only) -->
            <div>
                <label class="block text-sm font-semibold text-gray-600 mb-1">Department</label>
                <input type="text" class="w-full bg-gray-200 text-gray-700 rounded border p-2.5 cursor-not-allowed font-medium" value="{{ $stock->department?->name }}" disabled>
            </div>

            <!-- Unit (Read-only) -->
            <div>
                <label class="block text-sm font-semibold text-gray-600 mb-1">Unit</label>
                <input type="text" class="w-full bg-gray-200 text-gray-700 rounded border p-2.5 cursor-not-allowed font-medium" value="{{ $stock->unit?->name }}" disabled>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Min Qty -->
            <div>
                <label for="min_qty" class="form-label text-gray-700 block mb-1 font-semibold">
                    Min Alert Qty <span class="text-red-500">*</span>
                </label>
                <input
                    type="number"
                    step="0.0001"
                    name="min_qty"
                    id="min_qty"
                    value="{{ old('min_qty', $stock->min_qty) }}"
                    class="form-control w-full rounded border border-gray-300 p-2.5 text-black font-normal"
                    required
                >
                @error('min_qty')
                    <small class="text-red-600">{{ $message }}</small>
                @enderror
            </div>

            <!-- Max Qty -->
            <div>
                <label for="max_qty" class="form-label text-gray-700 block mb-1 font-semibold">
                    Max Qty Limit
                </label>
                <input
                    type="number"
                    step="0.0001"
                    name="max_qty"
                    id="max_qty"
                    value="{{ old('max_qty', $stock->max_qty) }}"
                    class="form-control w-full rounded border border-gray-300 p-2.5 text-black font-normal"
                >
                @error('max_qty')
                    <small class="text-red-600">{{ $message }}</small>
                @enderror
            </div>

            <!-- Location -->
            <div>
                <label for="location" class="form-label text-gray-700 block mb-1 font-semibold">
                    Storage Location
                </label>
                <input
                    type="text"
                    name="location"
                    id="location"
                    value="{{ old('location', $stock->location) }}"
                    class="form-control w-full rounded border border-gray-300 p-2.5 text-black font-normal"
                >
                @error('location')
                    <small class="text-red-600">{{ $message }}</small>
                @enderror
            </div>
        </div>

        <!-- Active Toggle -->
        <div>
            <label class="block text-gray-700 font-semibold mb-1">Active Status</label>
            <div class="form-control text-black font-normal rounded border border-gray-300 p-2 flex items-center gap-3 w-48 bg-white">
                <input type="checkbox" name="is_active" id="is_active" class="hidden" value="1" {{ $stock->is_active ? 'checked' : '' }}>
                <div id="toggle-active" class="toggle toggle-success cursor-pointer mb-1 {{ $stock->is_active ? 'on' : '' }}">
                    <span></span>
                </div>
                <label for="is_active" class="text-sm text-gray-700 font-normal">Active & Tracked</label>
            </div>
        </div>

        <div class="flex justify-end gap-3 pt-4 border-t">
            <a href="{{ route('inventory.stocks.list') }}" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded transition-all">
                Cancel
            </a>
            <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded shadow transition-all">
                Update Stock Entry
            </button>
        </div>
    </form>
</div>

<!-- Toggle Script -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const toggleDiv = document.getElementById('toggle-active');
        const hiddenCheckbox = document.getElementById('is_active');

        toggleDiv.addEventListener('click', () => {
            const isActive = toggleDiv.classList.toggle('on');
            hiddenCheckbox.checked = isActive;
        });
    });
</script>
