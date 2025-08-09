<div class="w-full px-4 py-6 bg-white shadow rounded">
    <form method="POST" action="{{ route('vendors.store') }}" enctype="multipart/form-data"
          class="sm:grid space-y-6 sm:space-y-0 grid-cols-4 gap-4 font-mono text-defaulttextcolor text-sm text-center font-bold rounded-sm">
        @csrf

        <!-- Vendor Name -->
        <div>
            <label for="name" class="block text-left mb-1 text-gray-700 font-semibold">
                Vendor Name <span class="text-red-500">*</span>
            </label>
            <input type="text" name="name" id="name"
                   class="form-control w-full text-black font-normal rounded border border-gray-300 p-2"
                   placeholder="Enter Vendor Name" required>
            @error('name') <small class="text-red-600">{{ $message }}</small> @enderror
        </div>

        <!-- Email -->
        <div>
            <label for="email" class="block text-left mb-1 text-gray-700 font-semibold">Email</label>
            <input type="email" name="email" id="email"
                   class="form-control w-full text-black font-normal rounded border border-gray-300 p-2"
                   placeholder="Enter Email Address">
            @error('email') <small class="text-red-600">{{ $message }}</small> @enderror
        </div>

        <!-- Phone -->
        <div>
            <label for="phone" class="block text-left mb-1 text-gray-700 font-semibold">Phone</label>
            <input type="text" name="phone" id="phone"
                   class="form-control w-full text-black font-normal rounded border border-gray-300 p-2"
                   placeholder="Enter Phone Number">
            @error('phone') <small class="text-red-600">{{ $message }}</small> @enderror
        </div>

        <!-- GST Number -->
        <div>
            <label for="gst_number" class="block text-left mb-1 text-gray-700 font-semibold">GST Number</label>
            <input type="text" name="gst_number" id="gst_number"
                   class="form-control w-full text-black font-normal rounded border border-gray-300 p-2"
                   placeholder="Enter GST Number">
            @error('gst_number') <small class="text-red-600">{{ $message }}</small> @enderror
        </div>

        <!-- PAN Number -->
        <div>
            <label for="pan_number" class="block text-left mb-1 text-gray-700 font-semibold">PAN Number</label>
            <input type="text" name="pan_number" id="pan_number"
                   class="form-control w-full text-black font-normal rounded border border-gray-300 p-2"
                   placeholder="Enter PAN Number">
            @error('pan_number') <small class="text-red-600">{{ $message }}</small> @enderror
        </div>

        <!-- Account Name -->
        <div>
            <label for="account_name" class="block text-left mb-1 text-gray-700 font-semibold">Account Name</label>
            <input type="text" name="account_name" id="account_name"
                   class="form-control w-full text-black font-normal rounded border border-gray-300 p-2"
                   placeholder="Enter Account Holder Name">
            @error('account_name') <small class="text-red-600">{{ $message }}</small> @enderror
        </div>

        <!-- Account Number -->
        <div>
            <label for="account_number" class="block text-left mb-1 text-gray-700 font-semibold">Account Number</label>
            <input type="text" name="account_number" id="account_number"
                   class="form-control w-full text-black font-normal rounded border border-gray-300 p-2"
                   placeholder="Enter Account Number">
            @error('account_number') <small class="text-red-600">{{ $message }}</small> @enderror
        </div>

        <!-- Bank Name -->
        <div>
            <label for="bank_name" class="block text-left mb-1 text-gray-700 font-semibold">Bank Name</label>
            <input type="text" name="bank_name" id="bank_name"
                   class="form-control w-full text-black font-normal rounded border border-gray-300 p-2"
                   placeholder="Enter Bank Name">
            @error('bank_name') <small class="text-red-600">{{ $message }}</small> @enderror
        </div>

        <!-- Branch Name -->
        <div>
            <label for="branch_name" class="block text-left mb-1 text-gray-700 font-semibold">Branch Name</label>
            <input type="text" name="branch_name" id="branch_name"
                   class="form-control w-full text-black font-normal rounded border border-gray-300 p-2"
                   placeholder="Enter Branch Name">
            @error('branch_name') <small class="text-red-600">{{ $message }}</small> @enderror
        </div>

        <!-- IFSC Code -->
        <div>
            <label for="ifsc_code" class="block text-left mb-1 text-gray-700 font-semibold">IFSC Code</label>
            <input type="text" name="ifsc_code" id="ifsc_code"
                   class="form-control w-full text-black font-normal rounded border border-gray-300 p-2"
                   placeholder="Enter IFSC Code">
            @error('ifsc_code') <small class="text-red-600">{{ $message }}</small> @enderror
        </div>

        <!-- Address -->
        <div class="col-span-4">
            <label for="address" class="block text-left mb-1 text-gray-700 font-semibold">Address</label>
            <textarea name="address" id="address" rows="2"
                      class="form-control w-full rounded border border-gray-300 p-2"
                      placeholder="Enter Vendor Address"></textarea>
            @error('address') <small class="text-red-600">{{ $message }}</small> @enderror
        </div>

        <!-- Active -->
        <div class="col-span-4 sm:col-span-2">
            <label class="block text-left mb-1 text-gray-700 font-semibold">Active</label>
            <div class="form-control text-black font-normal rounded border border-gray-300 p-2 flex items-center gap-3">
                <input type="checkbox" name="is_active" id="is_active" class="hidden" checked>
                <div id="toggle-active" class="toggle toggle-success on cursor-pointer mb-1">
                    <span></span>
                </div>
                <label for="is_active" class="text-sm text-gray-700 font-normal">Active</label>
            </div>
        </div>

        <!-- Submit -->
        <div class="col-span-4 flex justify-end pt-4">
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded shadow">
                Create Vendor
            </button>
        </div>
    </form>
</div>

<!-- Toggle Script -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const toggleDiv = document.getElementById('toggle-active');
        const hiddenCheckbox = document.getElementById('is_active');
        const toggleLabel = document.getElementById('toggle-label');

        toggleDiv.addEventListener('click', () => {
            const isActive = toggleDiv.classList.toggle('on');
            hiddenCheckbox.checked = isActive;
            toggleLabel.textContent = isActive ? 'ON' : 'OFF';
        });
    });
</script>
