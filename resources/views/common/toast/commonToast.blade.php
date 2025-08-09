{{-- Warning Alert --}}
@if(session('warning'))
<div class="fixed top-5 z-50" style="right: 10px">
    <div class="bg-yellow-500 text-white px-4 py-3 rounded shadow-lg flex items-start justify-between space-x-4 w-full max-w-sm">
        <div class="text-sm">
            {{ session('warning') }}
        </div>
        <button onclick="this.closest('div').remove()" class="text-white hover:text-white/80">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>
</div>
@endif

{{-- Error Alert --}}
@if(session('error'))
<div class="fixed top-5 z-50 mt-20" style="right: 10px">
    <div class="bg-red-600 text-white px-4 py-3 rounded shadow-lg flex items-start justify-between space-x-4 w-full max-w-sm">
        <div class="text-sm">
            {{ session('error') }}
        </div>
        <button onclick="this.closest('div').remove()" class="text-white hover:text-white/80">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>
</div>
@endif

{{-- Auto Remove Script --}}
<script>
    setTimeout(() => {
        document.querySelectorAll('.fixed.top-5').forEach(el => el.remove());
    }, 4000);
</script>
