<!DOCTYPE html>
<html lang="en" dir="ltr" data-nav-layout="vertical" class="light" data-header-styles="light" data-menu-styles="light">

<head>
    <meta charset="UTF-8">
    <title></title>

    {{-- Favicon --}}
    <link rel="shortcut icon" href="{{ asset('images/logo.png') }}">

    {{-- Main JS --}}
    <script src="{{ asset('js/main.js') }}"></script>

    {{-- CSS --}}
    <link href="{{ asset('css/icons.css') }}" rel="stylesheet">
    <link rel="preload" as="style" href="{{ asset('css/app-BEmGdVaN.css') }}">
    <link rel="stylesheet" href="{{ asset('css/app-BEmGdVaN.css') }}">
    <link rel="stylesheet" href="{{ asset('css/simplebar.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/nano.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/jsvectormap.min.css') }}">
</head>

<body>
    {{-- Header --}}
    @include("layouts.header")
    @include('common.toast.commonToast')

    {{-- Page Content --}}
    <div class="page">
        <div class="content">
            <div class="main-content" style="margin-top: 20px !important;">
                @yield('bodyContent')
            </div>
        </div>

        {{-- Footer --}}
        @include("layouts.footer")
    </div>

    {{-- Scroll to Top Button --}}
    <div class="scrollToTop">
        <span class="arrow"><i class="ri-arrow-up-s-fill text-xl"></i></span>
    </div>

    <div id="responsive-overlay"></div>

    {{-- JavaScript --}}
    <script src="{{ asset('js/popper.min.js') }}"></script>
    <script src="{{ asset('js/pickr.es5.min.js') }}"></script>
    <script src="{{ asset('js/switch.js') }}"></script>
    <script src="{{ asset('js/simplebar.min.js') }}"></script>
    <script src="{{ asset('js/preline.js') }}"></script>
    <script src="{{ asset('js/apexcharts.min.js') }}"></script>
    <script src="{{ asset('js/jsvectormap.min.js') }}"></script>
    <script src="{{ asset('js/world-merc.js') }}"></script>
    <script src="{{ asset('js/us-merc-en-V0CEs0pf.js') }}" type="module"></script>
    <script src="{{ asset('js/index-ChlSDD4z.js') }}" type="module"></script>
    <script src="{{ asset('js/sticky.js') }}"></script>
    <script src="{{ asset('js/custom-switcher-DhReuTTH.js') }}" type="module"></script>
    <script src="{{ asset('js/app-CLk324ZP.js') }}" type="module"></script>

    <style>
    .center-align {
        text-align: center !important;
    }
</style>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        console.log("✅ Script loaded");

        const table = document.querySelector("table");
        if (!table) {
            console.log("❌ Table not found");
            return;
        }

        const headers = table.querySelectorAll("thead th");
        let actionColIndex = -1;

        headers.forEach((th, index) => {
            const headerText = th.textContent.trim().toLowerCase();
            console.log(`🔍 Header ${index}: ${headerText}`);
            if (headerText === "action") {
                actionColIndex = index;
                th.classList.add("center-align");
                console.log(`✅ 'Action' column found at index ${index}`);
            }
        });

        if (actionColIndex === -1) {
            console.warn("⚠️ 'Action' column not found in header.");
            return;
        }

        const rows = table.querySelectorAll("tbody tr");
        rows.forEach((row, rowIndex) => {
            const cells = row.querySelectorAll("td");
            if (cells[actionColIndex]) {
                cells[actionColIndex].classList.add("center-align");
                console.log(`🔧 Aligned row ${rowIndex + 1}, cell ${actionColIndex + 1}`);
            }
        });

        console.log("✅ Alignment applied to all 'Action' cells.");
    });
</script>

</body>
</html>
