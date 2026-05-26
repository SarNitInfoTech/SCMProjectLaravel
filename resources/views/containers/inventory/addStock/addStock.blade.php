<div class="w-full px-4 py-6 bg-white shadow rounded">
    <div class="mb-4 pb-2 border-b">
        <h3 class="text-lg font-semibold text-gray-800">Add New Stock Entry</h3>
        <p class="text-sm text-gray-500">Track an existing item for a specific department with a default unit and initial balance.</p>
    </div>

    @if (session('warning'))
        <div class="mb-4 p-3 bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 text-sm">
            {{ session('warning') }}
        </div>
    @endif

    <form method="POST" action="{{ route('inventory.stocks.store') }}" class="space-y-6">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Item Select -->
            <div>
                <label for="item_id" class="form-label text-gray-700 block mb-1 font-semibold">
                    Select Item <span class="text-red-500">*</span>
                </label>
                <select name="item_id" id="item_id" class="form-control w-full rounded border border-gray-300 p-2.5 bg-white text-black font-normal" required>
                    <option value="">-- Choose Item --</option>
                    @foreach($items as $item)
                        <option value="{{ $item->id }}" {{ old('item_id') == $item->id ? 'selected' : '' }}>
                            {{ $item->name }}
                        </option>
                    @endforeach
                </select>
                @error('item_id')
                    <small class="text-red-600">{{ $message }}</small>
                @enderror
            </div>

            <!-- Department Select -->
            <div>
                <label for="department_id" class="form-label text-gray-700 block mb-1 font-semibold">
                    Department <span class="text-red-500">*</span>
                </label>
                <select name="department_id" id="department_id" class="form-control w-full rounded border border-gray-300 p-2.5 bg-white text-black font-normal" required>
                    <option value="">-- Choose Department --</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>
                            {{ $dept->name }}
                        </option>
                    @endforeach
                </select>
                @error('department_id')
                    <small class="text-red-600">{{ $message }}</small>
                @enderror
            </div>

            <!-- Unit Select -->
            <div>
                <label for="unit_id" class="form-label text-gray-700 block mb-1 font-semibold">
                    Unit <span class="text-red-500">*</span>
                </label>
                <select name="unit_id" id="unit_id" class="form-control w-full rounded border border-gray-300 p-2.5 bg-white text-black font-normal" required>
                    <option value="">-- Choose Unit --</option>
                    @foreach($units as $unit)
                        <option value="{{ $unit->id }}" {{ old('unit_id') == $unit->id ? 'selected' : '' }}>
                            {{ $unit->name }}
                        </option>
                    @endforeach
                </select>
                @error('unit_id')
                    <small class="text-red-600">{{ $message }}</small>
                @enderror
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <!-- Initial Qty -->
            <div>
                <label for="current_qty" class="form-label text-gray-700 block mb-1 font-semibold">
                    Initial Quantity <span class="text-red-500">*</span>
                </label>
                <input
                    type="number"
                    step="0.0001"
                    name="current_qty"
                    id="current_qty"
                    value="{{ old('current_qty', '0') }}"
                    class="form-control w-full rounded border border-gray-300 p-2.5 text-black font-normal"
                    placeholder="e.g. 100"
                    required
                >
                @error('current_qty')
                    <small class="text-red-600">{{ $message }}</small>
                @enderror
            </div>

            <!-- Min Qty -->
            <div>
                <label for="min_qty" class="form-label text-gray-700 block mb-1 font-semibold">
                    Min Alert Qty
                </label>
                <input
                    type="number"
                    step="0.0001"
                    name="min_qty"
                    id="min_qty"
                    value="{{ old('min_qty', '0') }}"
                    class="form-control w-full rounded border border-gray-300 p-2.5 text-black font-normal"
                    placeholder="e.g. 10"
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
                    value="{{ old('max_qty') }}"
                    class="form-control w-full rounded border border-gray-300 p-2.5 text-black font-normal"
                    placeholder="e.g. 500"
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
                    value="{{ old('location') }}"
                    class="form-control w-full rounded border border-gray-300 p-2.5 text-black font-normal"
                    placeholder="e.g. Rack B2"
                >
                @error('location')
                    <small class="text-red-600">{{ $message }}</small>
                @enderror
            </div>
        </div>

        <div class="flex justify-end gap-3 pt-4 border-t">
            <a href="{{ route('inventory.stocks.list') }}" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded transition-all">
                Cancel
            </a>
            <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded shadow transition-all">
                Create Stock Entry
            </button>
        </div>
    </form>
</div>
