<!-- Start::page-header -->
<style>
    .preset-active {
        background-color: #383853; /* Tailwind's gray-800 */
        color: #fff;

    }
    .preset-active:hover {
        background-color: #fff; /* Tailwind's gray-800 */
        color: #7987a1;

    }
   
</style>

<div class="md:flex block items-center justify-between my-6 page-header-breadcrumb">
    <div>
        <h4 class="mb-0 text-defaulttextcolor font-medium">
            Hi, {{ auth()->user()->name ?? 'User' }}!
        </h4>
        <p class="-mt-[0.2rem] mb-0 text-textmuted">
            Logged in as <strong>{{ auth()->user()->role ?? 'User' }}</strong>
            • {{ auth()->user()->email }}
        </p>
    </div>

    <div class="main-dashboard-header-right">
        <div>
            <div class="flex flex-wrap items-center justify-between mb-4">
                <div class="flex flex-wrap items-center gap-2">
                    <!-- Preset Buttons -->
                   <div class="flex gap-2">
    <button data-range="today"  class="px-3 py-1.5 text-sm rounded border bg-white hover:bg-gray-50 preset-active">Today</button>
    <button data-range="week"   class="px-3 py-1.5 text-sm rounded border bg-white hover:bg-gray-50">This Week</button>
    <button data-range="month"  class="px-3 py-1.5 text-sm rounded border bg-white hover:bg-gray-50">This Month</button>
    <button data-range="year"   class="px-3 py-1.5 text-sm rounded border bg-white hover:bg-gray-50">This Year</button>
</div>

                    <!-- Date Range Picker -->
                    <div class="form-group mb-0 bg-white">
                        <div class="input-group bg-white ">
                            <div class="input-group-text bg-white">
                                <i class="ri-calendar-line"></i>
                            </div>
                            <input type="text"
                                id="dateRangePicker"
                                class="form-control !border-s-0 flatpickr-input js-date-range"
                                placeholder="Choose date range"
                                readonly>
                        </div>
                    </div>

                    <!-- Apply -->
                    <button id="applyRange"
                        class="px-4 py-2 text-sm bg-gray-800 text-white rounded hover:bg-gray-900">
                        Apply
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End::page-header -->

<!-- row -->
<div class="grid grid-cols-12 gap-x-6">
    <div class="xl:col-span-3 lg:col-span-6 md:col-span-6 sm:col-span-12 col-span-12">
        <div class="box overflow-hidden sales-box bg-primary-gradient !rounded-sm">
            <div class="px-4 pt-4 pb-2">
                <div>
                    <h6 class="mb-3 text-[.75rem] font-medium text-fixed-white">INDENT GENERATED</h6>
                </div>
                <div class="pb-0 mt-0">
                    <div class="flex">
                        <div>
                            <h4 class="font-bold text-[1.25rem] text-fixed-white" data-stat="indent_generated">
                                {{ $stats['indent_generated'] }}
                            </h4>
                            <p class="mb-0 text-[.75rem] text-fixed-white opacity-[0.7]">Created within range</p>
                        </div>
                        <span class="float-end my-auto ms-auto">
                            <i class="fas fa-users text-fixed-white"></i>
                        </span>
                    </div>
                </div>
            </div>
            <div id="compositeline" class="!-mb-[2px]"></div>
        </div>
    </div>

    <div class="xl:col-span-3 lg:col-span-6 md:col-span-6 sm:col-span-12 col-span-12">
        <div class="box overflow-hidden sales-card bg-warning-gradient !rounded-sm">
            <div class="px-4 pt-4 pb-2">
                <div>
                    <h6 class="mb-3 text-[.75rem] font-medium text-fixed-white">INDENT PENDING</h6>
                </div>
                <div class="pb-0 mt-0">
                    <div class="flex">
                        <div>
                            <h4 class="text-[1.25rem] font-bold text-fixed-white" data-stat="indent_pending">
                                {{ $stats['indent_pending'] }}
                            </h4>
                            <p class="mb-0 text-[.75rem] text-fixed-white opacity-[0.7]">Awaiting action</p>
                        </div>
                        <span class="float-end my-auto ms-auto">
                            <i class="fas fa-building text-fixed-white"></i>
                        </span>
                    </div>
                </div>
            </div>
            <div id="compositeline2" class="!-mb-[2px]"></div>
        </div>
    </div>

    <div class="xl:col-span-3 lg:col-span-6 md:col-span-6 sm:col-span-12 col-span-12">
        <div class="box overflow-hidden sales-card bg-success-gradient !rounded-sm">
            <div class="px-4 pt-4 pb-2">
                <div>
                    <h6 class="mb-3 text-[.75rem] font-medium text-fixed-white">INDENT CLOSED</h6>
                </div>
                <div class="pb-0 mt-0">
                    <div class="flex">
                        <div>
                            <h4 class="text-[1.25rem] font-bold text-fixed-white" data-stat="indent_closed">
                                {{ $stats['indent_closed'] }}
                            </h4>
                            <p class="mb-0 text-[.75rem] text-fixed-white opacity-[0.7]">Completed indents</p>
                        </div>
                        <span class="float-end my-auto ms-auto">
                            <i class="fas fa-briefcase text-fixed-white"></i>
                        </span>
                    </div>
                </div>
            </div>
            <div id="compositeline3" class="!-mb-[2px]"></div>
        </div>
    </div>

    <div class="xl:col-span-3 lg:col-span-6 md:col-span-6 sm:col-span-12 col-span-12">
        <div class="box overflow-hidden sales-card bg-danger-gradient !rounded-sm">
            <div class="px-4 pt-4 pb-2">
                <div>
                    <h6 class="mb-3 text-[.75rem] font-medium text-fixed-white">INDENT CANCELLED</h6>
                </div>
                <div class="pb-0 mt-0">
                    <div class="flex">
                        <div>
                            <h4 class="text-[1.25rem] font-bold text-fixed-white" data-stat="indent_cancelled">
                                {{ $stats['indent_cancelled'] }}
                            </h4>
                            <p class="mb-0 text-[.75rem] text-fixed-white opacity-[0.7]">Cancelled within range</p>
                        </div>
                        <span class="float-end my-auto ms-auto">
                            <i class="fas fa-cubes text-fixed-white"></i>
                        </span>
                    </div>
                </div>
            </div>
            <div id="compositeline4" class="!-mb-[2px]"></div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const picker = flatpickr("#dateRangePicker", {
        mode: "range",
        dateFormat: "Y-m-d",
        allowInput: false
    });

    const presetButtons = document.querySelectorAll('[data-range]');

    function clearActivePresets() {
        presetButtons.forEach(btn => btn.classList.remove('preset-active'));
    }

    function fmt(d) { return d.toISOString().slice(0, 10); }
    function startOfWeek(d) { const n = new Date(d); const day = (n.getDay() + 6) % 7; n.setDate(n.getDate() - day); return n; }
    function endOfWeek(d) { const n = startOfWeek(d); n.setDate(n.getDate() + 6); return n; }
    function startOfMonth(d) { return new Date(d.getFullYear(), d.getMonth(), 1); }
    function endOfMonth(d) { return new Date(d.getFullYear(), d.getMonth() + 1, 0); }
    function startOfYear(d) { return new Date(d.getFullYear(), 0, 1); }
    function endOfYear(d) { return new Date(d.getFullYear(), 11, 31); }

    // Preset button click
    presetButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            clearActivePresets();
            btn.classList.add('preset-active');

            const now = new Date();
            let s, e;
            switch (btn.getAttribute('data-range')) {
                case 'today': s = e = now; break;
                case 'week':  s = startOfWeek(now); e = endOfWeek(now); break;
                case 'month': s = startOfMonth(now); e = endOfMonth(now); break;
                case 'year':  s = startOfYear(now); e = endOfYear(now); break;
            }
            picker.setDate([fmt(s), fmt(e)], true);
            requestStats(fmt(s), fmt(e));
        });
    });

    // Apply button click (custom range)
    document.getElementById('applyRange').addEventListener('click', () => {
        clearActivePresets();
        const dates = picker.selectedDates;
        if (dates.length === 2) {
            requestStats(fmt(dates[0]), fmt(dates[1]));
        } else {
            alert("Please select a valid date range.");
        }
    });

    // Auto-trigger "Today" on load
    document.querySelector('[data-range="today"]').click();

    async function requestStats(start, end) {
        try {
            const res = await fetch(`/stats/filter?start=${start}&end=${end}`, { headers: { 'Accept': 'application/json' } });
            const data = await res.json();

            document.querySelector('[data-stat="indent_generated"]').textContent = data.indent_generated ?? '-';
            document.querySelector('[data-stat="indent_pending"]').textContent   = data.indent_pending ?? '-';
            document.querySelector('[data-stat="indent_closed"]').textContent    = data.indent_closed ?? '-';
            document.querySelector('[data-stat="indent_cancelled"]').textContent = data.indent_cancelled ?? '-';
        } catch (err) {
            console.error(err);
            alert("Error fetching stats");
        }
    }
});
</script>
