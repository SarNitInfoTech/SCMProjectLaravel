  <!-- Main-Header -->
            <header class="app-header">
					<nav class="main-header" aria-label="Global">
						<div class="main-header-container !px-[0.85rem]">

						<div class="header-content-left">
							<!-- Start::header-element -->
							<div class="header-element">
							<div class="horizontal-logo">
								<a href="{{ url('/') }}" class="header-logo">
									<img src="{{ asset('images/logo.png') }}" alt="logo" class="h-[42px]">
								</a>
							</div>

							</div>
							<!-- End::header-element -->

							<!-- End::header-element -->
							<div class="header-element !items-center">
							<!-- Start::header-link -->
							<a aria-label="Hide Sidebar" class="sidemenu-toggle animated-arrow header-link  hor-toggle horizontal-navtoggle inline-flex items-center" href="javascript:void(0);"><i class="header-icon fe fe-align-left"></i></a>
							
							<!-- Header Mega Search Trigger Bar -->
							<div class="main-header-center hidden lg:block cursor-pointer" onclick="openMegaSearchModal()" role="button" tabindex="0" title="Click or press Ctrl+K to search">
								<div style="background: #FFFFFF; border: 1.5px solid #E2E8F0; border-radius: 10px; padding: 7px 14px; width: 290px; display: flex; align-items: center; justify-content: space-between; transition: all 0.2s; box-shadow: 0 1px 3px rgba(0,0,0,0.06);" onmouseover="this.style.borderColor='#3B82F6'; this.style.boxShadow='0 4px 12px rgba(59,130,246,0.15)'" onmouseout="this.style.borderColor='#E2E8F0'; this.style.boxShadow='0 1px 3px rgba(0,0,0,0.06)'">
									<div style="display: flex; align-items: center; gap: 8px;">
										<svg style="width: 16px; height: 16px; color: #2563EB;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
										<span style="font-size: 13px; color: #64748B; font-weight: 500;">Search Indents, POs, Items...</span>
									</div>
									<span style="background: #F1F5F9; color: #2563EB; border: 1px solid #CBD5E1; font-size: 10px; font-weight: 700; padding: 2px 7px; border-radius: 5px; font-family: monospace;">Ctrl K</span>
								</div>
							</div>
							<!-- End::header-link -->
							</div>
						</div>
						<div class="header-content-right">

							<!-- Mobile Search Trigger -->
							<div class="header-element lg:!hidden cursor-pointer" onclick="openMegaSearchModal()" role="button" tabindex="0" title="Search">
								<a href="javascript:void(0);" class="header-link">
									<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" class="header-link-icon">
										<path d="M784-120 532-372q-30 24-69 38t-83 14q-109 0-184.5-75.5T120-580q0-109 75.5-184.5T380-840q109 0 184.5 75.5T640-580q0 44-14 83t-38 69l252 252-56 56ZM380-400q75 0 127.5-52.5T560-580q0-75-52.5-127.5T380-760q-75 0-127.5 52.5T200-580q0 75 52.5 127.5T380-400Z"></path>
									</svg>
								</a>
							</div>

							

							<!-- light and dark theme -->
							<div class="header-element header-theme-mode hidden !items-center sm:block !py-[1rem] !px-[0.65rem]">
							<a aria-label="anchor" class="hs-dark-mode-active:hidden flex hs-dark-mode group flex-shrink-0 justify-center items-center gap-2  rounded-full font-medium transition-all text-xs dark:bg-bodybg dark:hover:bg-black/20 dark:text-white/70 dark:hover:text-white dark:focus:ring-white/10 dark:focus:ring-offset-white/10" href="javascript:void(0);" data-hs-theme-click-value="dark">
								<svg xmlns="http://www.w3.org/2000/svg" class="header-link-icon" height="24" viewBox="0 -960 960 960" width="24">
								<path d="M480-120q-150 0-255-105T120-480q0-150 105-255t255-105q14 0 27.5 1t26.5 3q-41 29-65.5 75.5T444-660q0 90 63 153t153 63q55 0 101-24.5t75-65.5q2 13 3 26.5t1 27.5q0 150-105 255T480-120Zm0-80q88 0 158-48.5T740-375q-20 5-40 8t-40 3q-123 0-209.5-86.5T364-660q0-20 3-40t8-40q-78 32-126.5 102T200-480q0 116 82 198t198 82Zm-10-270Z"></path>
								</svg>
							</a>
							<a aria-label="anchor" class="hs-dark-mode-active:flex hidden hs-dark-mode group flex-shrink-0 justify-center items-center gap-2  rounded-full font-medium text-defaulttextcolor  transition-all text-xs dark:bg-bodybg  dark:hover:bg-black/20 dark:text-white/70 dark:hover:text-white dark:focus:ring-white/10 dark:focus:ring-offset-white/10" href="javascript:void(0);" data-hs-theme-click-value="light">
								<svg xmlns="http://www.w3.org/2000/svg" class="header-link-icon" fill="currentColor" height="24" viewBox="0 -960 960 960" width="24">
								<path d="M480-360q50 0 85-35t35-85q0-50-35-85t-85-35q-50 0-85 35t-35 85q0 50 35 85t85 35Zm0 80q-83 0-141.5-58.5T280-480q0-83 58.5-141.5T480-680q83 0 141.5 58.5T680-480q0 83-58.5 141.5T480-280ZM200-440H40v-80h160v80Zm720 0H760v-80h160v80ZM440-760v-160h80v160h-80Zm0 720v-160h80v160h-80ZM256-650l-101-97 57-59 96 100-52 56Zm492 496-97-101 53-55 101 97-57 59Zm-98-550 97-101 59 57-100 96-56-52ZM154-212l101-97 55 53-97 101-59-57Zm326-268Z"></path>
								</svg>
							</a>
							</div>
							<!-- End light and dark theme -->

							

							<!--Header Notifictaion -->
							 @include('common.notification.notification')


							<!--End Header Notifictaion -->

							<!-- Fullscreen -->
							<div class="header-element header-fullscreen py-[1rem] md:px-[0.65rem] px-2">
								<!-- Start::header-link -->
								<a aria-label="anchor" onclick="openFullscreen();" href="javascript:void(0);" class="inline-flex flex-shrink-0 justify-center items-center gap-2  !rounded-full font-medium dark:hover:bg-black/20 dark:text-textmuted dark:hover:text-white dark:focus:ring-white/10 dark:focus:ring-offset-white/10">
									<i class="bx bx-fullscreen full-screen-open header-link-icon"></i>
									<i class="bx bx-exit-fullscreen full-screen-close header-link-icon hidden"></i>
								</a>
								<!-- End::header-link -->
							</div>
							<!-- End Full screen -->

							

							<!-- Header Profile -->
							@php
    use Illuminate\Support\Facades\Auth;
    $user = Auth::user();
@endphp

<div class="header-element md:!px-[0.5rem] px-2 hs-dropdown profile-dropdown !items-center ti-dropdown [--placement:bottom-right]">
   @php
    $profileImage = $user->profile_photo 
        ? asset('storage/' . $user->profile_photo) 
        : 'https://ui-avatars.com/api/?name=' . urlencode($user->name);
@endphp

<button id="dropdown-profile" type="button" class="hs-dropdown-toggle ti-dropdown-toggle !gap-2 !p-0 flex-shrink-0 me-0 !rounded-full !shadow-none text-xs align-middle !border-0 !shadow-transparent">
    <img class="inline-block rounded-full" src="{{ $profileImage }}" width="37" height="37" alt="{{ $user->name }}">
</button>


    <div class="main-header-dropdown !-mt-2 !p-0 hs-dropdown-menu ti-dropdown-menu bg-white !border-0 border-defaultborder hidden !m-0" aria-labelledby="dropdown-profile">
        <ul class="dropdown-menu pt-0 header-profile-dropdown dropdown-menu-end main-profile-menu">
            <li>
                <div class="main-header-profile bg-primary menu-header-content text-white">
                    <div class="my-auto">
                        <h6 class="mb-0 leading-none text-white">{{ $user->name }}</h6>
                        <span class="text-[.6875rem] opacity-[0.7] leading-none capitalize">{{ $user->role ?? 'Member' }}</span>
                    </div>
                </div>
            </li>

            <li><a class="dropdown-item text-defaulttextcolor flex" href="{{route("profile.show")}}"><i class="bx bx-user-circle text-[1.125rem] me-2 opacity-[0.7]"></i>Profile</a></li>

            {{-- Logout Form --}}
            <li>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="dropdown-item text-defaulttextcolor !rounded-bl-md !rounded-br-md flex w-full text-left">
                        <i class="bx bx-log-out text-[1.125rem] me-2 opacity-[0.7]"></i>Sign Out
                    </button>
                </form>
            </li>
        </ul>
    </div>
</div>

							<!-- End Header Profile -->

							
							<!-- End::header-element -->
						</div>
						</div>
					</nav>
				</header>
					          

			

			
		
            @include('common.menu')

<!-- ========================================== -->
<!-- 🔍 GLOBAL MEGA SEARCH MODAL & OMNIBOX      -->
<!-- ========================================== -->
<div id="mega-search-overlay" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(5px); -webkit-backdrop-filter: blur(5px); z-index: 99990;" onclick="closeMegaSearchModal()"></div>

<div id="mega-search-modal" style="display: none; position: fixed; top: 7%; left: 50%; transform: translateX(-50%); width: 720px; max-width: 95vw; background: #FFFFFF; border-radius: 18px; box-shadow: 0 25px 50px -12px rgba(15, 23, 42, 0.35), 0 0 0 1px rgba(226, 232, 240, 0.8); z-index: 99999; flex-direction: column; overflow: hidden; font-family: inherit;">
    <!-- Top Search Input Header -->
    <div style="padding: 16px 20px; border-bottom: 1px solid #F1F5F9; display: flex; align-items: center; gap: 12px; background: #FFFFFF;">
        <svg style="width: 20px; height: 20px; color: #2563EB; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
        <input id="mega-search-input" type="text" placeholder="Type to search Indents, POs, Vendors, Items, Reports, or Modules..." style="width: 100%; border: none; outline: none; font-size: 15px; font-weight: 600; color: #0F172A; background: transparent;" autocomplete="off" spellcheck="false" oninput="handleMegaSearchInput(this.value)">
        
        <!-- Live Loading Spinner -->
        <div id="mega-search-spinner" style="display: none; width: 18px; height: 18px; border: 2px solid #E2E8F0; border-top-color: #2563EB; border-radius: 50%; animation: megaSpin 0.7s linear infinite; flex-shrink: 0;"></div>
        
        <!-- Clear Button -->
        <button id="mega-search-clear" onclick="clearMegaSearch()" style="display: none; background: #F1F5F9; border: none; border-radius: 50%; width: 22px; height: 22px; cursor: pointer; color: #64748B; font-size: 13px; font-weight: 700; align-items: center; justify-content: center; line-height: 1;" title="Clear search">✕</button>
        
        <!-- Esc Key Badge -->
        <button onclick="closeMegaSearchModal()" style="font-size: 11px; font-weight: 700; color: #64748B; background: #F1F5F9; border: 1px solid #E2E8F0; padding: 3px 8px; border-radius: 6px; font-family: monospace; cursor: pointer;" title="Close (Esc)">ESC</button>
    </div>

    <!-- Quick Category Filter Pills -->
    <div style="display: flex; align-items: center; gap: 8px; padding: 10px 20px; background: #F8FAFC; border-bottom: 1px solid #F1F5F9; overflow-x: auto;">
        <button type="button" class="mega-filter-pill active" onclick="setMegaCategoryFilter('all', this)" style="padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 700; border: 1px solid #2563EB; background: #2563EB; color: #FFFFFF; cursor: pointer; transition: all 0.15s; white-space: nowrap;">All</button>
        <button type="button" class="mega-filter-pill" onclick="setMegaCategoryFilter('indents_pos', this)" style="padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 700; border: 1px solid #E2E8F0; background: #FFFFFF; color: #475569; cursor: pointer; transition: all 0.15s; white-space: nowrap;">Indents & POs</button>
        <button type="button" class="mega-filter-pill" onclick="setMegaCategoryFilter('reports', this)" style="padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 700; border: 1px solid #E2E8F0; background: #FFFFFF; color: #475569; cursor: pointer; transition: all 0.15s; white-space: nowrap;">Reports</button>
        <button type="button" class="mega-filter-pill" onclick="setMegaCategoryFilter('inventory', this)" style="padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 700; border: 1px solid #E2E8F0; background: #FFFFFF; color: #475569; cursor: pointer; transition: all 0.15s; white-space: nowrap;">Inventory</button>
        <button type="button" class="mega-filter-pill" onclick="setMegaCategoryFilter('vendors_items', this)" style="padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 700; border: 1px solid #E2E8F0; background: #FFFFFF; color: #475569; cursor: pointer; transition: all 0.15s; white-space: nowrap;">Vendors & Items</button>
        <button type="button" class="mega-filter-pill" onclick="setMegaCategoryFilter('master', this)" style="padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 700; border: 1px solid #E2E8F0; background: #FFFFFF; color: #475569; cursor: pointer; transition: all 0.15s; white-space: nowrap;">Administration</button>
    </div>

    <!-- Scrollable Results Container -->
    <div style="max-height: 480px; overflow-y: auto; padding: 16px 20px;" id="mega-search-results">
        
        <!-- Live Database Records Section (Dynamically Loaded via AJAX) -->
        <div id="mega-search-live-section" style="display: none; margin-bottom: 20px;">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 10px;">
                <span style="font-size: 11px; font-weight: 800; color: #2563EB; text-transform: uppercase; letter-spacing: 0.6px; display: flex; align-items: center; gap: 6px;">
                    <span style="width: 7px; height: 7px; border-radius: 50%; background: #2563EB; display: inline-block;"></span>
                    Live Database Matches
                </span>
                <span id="mega-live-count-badge" style="font-size: 10px; font-weight: 700; color: #2563EB; background: #EFF6FF; border: 1px solid #BFDBFE; padding: 2px 7px; border-radius: 6px;">0 found</span>
            </div>
            <div id="mega-search-live-results" style="display: flex; flex-direction: column; gap: 6px;">
                <!-- Live AJAX records injected here -->
            </div>
        </div>

        <!-- Static System Modules Section -->
        <div id="mega-search-modules-section">
            
            <!-- Group 1: Indents & Purchase Orders -->
            <div class="mega-search-section mb-4" data-group="indents_pos">
                <div style="font-size: 11px; font-weight: 800; color: #94A3B8; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">Indents & Purchase Orders</div>
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 8px;">
                    <a href="{{ route('indent.create') }}" class="mega-search-item flex items-center gap-3 p-2.5 rounded-xl hover:bg-blue-50/80 transition-all text-decoration-none group" data-title="Create Indent Ticket New Generate Token Raise Department">
                        <span style="width: 34px; height: 34px; border-radius: 9px; background: #EFF6FF; color: #2563EB; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 4v16m8-8H4"/></svg>
                        </span>
                        <div style="flex: 1; min-width: 0;">
                            <div style="font-size: 13px; font-weight: 700; color: #0F172A;">Create Indent Ticket</div>
                            <div style="font-size: 11px; color: #64748B;">Generate token & raise new indent</div>
                        </div>
                    </a>

                    <a href="{{ route('indent-register.index') }}" class="mega-search-item flex items-center gap-3 p-2.5 rounded-xl hover:bg-blue-50/80 transition-all text-decoration-none group" data-title="Indent Register List Indents View Search Edit Cancel Status Filter">
                        <span style="width: 34px; height: 34px; border-radius: 9px; background: #EEF2FF; color: #4338CA; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </span>
                        <div style="flex: 1; min-width: 0;">
                            <div style="font-size: 13px; font-weight: 700; color: #0F172A;">Indent Register List</div>
                            <div style="font-size: 11px; color: #64748B;">All registered indents, items & status</div>
                        </div>
                    </a>

                    <a href="{{ route('po-register.index') }}" class="mega-search-item flex items-center gap-3 p-2.5 rounded-xl hover:bg-green-50/80 transition-all text-decoration-none group" data-title="PO Register List Purchase Orders Invoices Vendors Manage Status">
                        <span style="width: 34px; height: 34px; border-radius: 9px; background: #F0FDF4; color: #166534; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                        </span>
                        <div style="flex: 1; min-width: 0;">
                            <div style="font-size: 13px; font-weight: 700; color: #0F172A;">PO Register List</div>
                            <div style="font-size: 11px; color: #64748B;">Manage and track purchase orders</div>
                        </div>
                    </a>

                    <a href="{{ route('indentroview.index') }}" class="mega-search-item flex items-center gap-3 p-2.5 rounded-xl hover:bg-emerald-50/80 transition-all text-decoration-none group" data-title="PO Entry Add Invoice Filing File Purchase Order Rate Vendor">
                        <span style="width: 34px; height: 34px; border-radius: 9px; background: #ECFDF5; color: #047857; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        </span>
                        <div style="flex: 1; min-width: 0;">
                            <div style="font-size: 13px; font-weight: 700; color: #0F172A;">File PO / Add Invoice</div>
                            <div style="font-size: 11px; color: #64748B;">Enter PO details & supplier bills</div>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Group 2: Reporting & Analytics Hub -->
            <div class="mega-search-section mb-4" data-group="reports">
                <div style="font-size: 11px; font-weight: 800; color: #94A3B8; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">Reporting & Analytics Hub</div>
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 8px;">
                    <a href="{{ route('report.viewAllIndent') }}" class="mega-search-item flex items-center gap-3 p-2.5 rounded-xl hover:bg-indigo-50/80 transition-all text-decoration-none group" data-title="All Indents Report Complete Lifecycle Requested Received Cancelled Balance Excel PDF CSV Export Drilldown">
                        <span style="width: 34px; height: 34px; border-radius: 9px; background: #EEF2FF; color: #4F46E5; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        </span>
                        <div style="flex: 1; min-width: 0;">
                            <div style="font-size: 13px; font-weight: 700; color: #0F172A;">All Indents Report (Master)</div>
                            <div style="font-size: 11px; color: #64748B;">Complete fulfillment lifecycle & Excel/PDF exports</div>
                        </div>
                    </a>

                    <a href="{{ route('reports.po') }}" class="mega-search-item flex items-center gap-3 p-2.5 rounded-xl hover:bg-indigo-50/80 transition-all text-decoration-none group" data-title="PO Register Report Purchase Orders Report View Analysis Status Department Vendor">
                        <span style="width: 34px; height: 34px; border-radius: 9px; background: #EFF6FF; color: #0284C7; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/></svg>
                        </span>
                        <div style="flex: 1; min-width: 0;">
                            <div style="font-size: 13px; font-weight: 700; color: #0F172A;">PO Status Report</div>
                            <div style="font-size: 11px; color: #64748B;">Purchase order status by party & department</div>
                        </div>
                    </a>

                    <a href="{{ route('reports.indentspo.index') }}" class="mega-search-item flex items-center gap-3 p-2.5 rounded-xl hover:bg-indigo-50/80 transition-all text-decoration-none group" data-title="Consolidated Indents & POs Combined Report Traceability Matching">
                        <span style="width: 34px; height: 34px; border-radius: 9px; background: #F0FDF4; color: #15803D; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"/></svg>
                        </span>
                        <div style="flex: 1; min-width: 0;">
                            <div style="font-size: 13px; font-weight: 700; color: #0F172A;">Indents & POs Combined List</div>
                            <div style="font-size: 11px; color: #64748B;">Cross-referencing indents with issued POs</div>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Group 3: Inventory & Stocks -->
            <div class="mega-search-section mb-4" data-group="inventory">
                <div style="font-size: 11px; font-weight: 800; color: #94A3B8; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">Inventory & Stock Management</div>
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 8px;">
                    <a href="{{ route('inventory.dashboard') }}" class="mega-search-item flex items-center gap-3 p-2.5 rounded-xl hover:bg-amber-50/80 transition-all text-decoration-none group" data-title="Inventory Dashboard Stocks Overview Analytics Warehouse Levels">
                        <span style="width: 34px; height: 34px; border-radius: 9px; background: #FEF3C7; color: #B45309; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        </span>
                        <div style="flex: 1; min-width: 0;">
                            <div style="font-size: 13px; font-weight: 700; color: #0F172A;">Inventory Dashboard</div>
                            <div style="font-size: 11px; color: #64748B;">Real-time stock valuation & storage alerts</div>
                        </div>
                    </a>

                    <a href="{{ route('inventory.stocks.list') }}" class="mega-search-item flex items-center gap-3 p-2.5 rounded-xl hover:bg-amber-50/80 transition-all text-decoration-none group" data-title="Stock Inventory List Items Quantity Balance Available Warehouse Store">
                        <span style="width: 34px; height: 34px; border-radius: 9px; background: #FFFBEB; color: #D97706; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                        </span>
                        <div style="flex: 1; min-width: 0;">
                            <div style="font-size: 13px; font-weight: 700; color: #0F172A;">Stock Inventory List</div>
                            <div style="font-size: 11px; color: #64748B;">Item-wise quantity on hand & batch tracking</div>
                        </div>
                    </a>

                    <a href="{{ route('inventory.reports.low-stock') }}" class="mega-search-item flex items-center gap-3 p-2.5 rounded-xl hover:bg-rose-50/80 transition-all text-decoration-none group" data-title="Low Stock Alert Threshold Reorder Inventory Critical Minimum">
                        <span style="width: 34px; height: 34px; border-radius: 9px; background: #FFE4E6; color: #BE123C; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        </span>
                        <div style="flex: 1; min-width: 0;">
                            <div style="font-size: 13px; font-weight: 700; color: #0F172A;">Low Stock Alerts</div>
                            <div style="font-size: 11px; color: #64748B;">Items nearing re-order thresholds</div>
                        </div>
                    </a>

                    <a href="{{ route('inventory.reports.movements') }}" class="mega-search-item flex items-center gap-3 p-2.5 rounded-xl hover:bg-sky-50/80 transition-all text-decoration-none group" data-title="Stock Movements Inward Outward Goods Receipt Issue Log History">
                        <span style="width: 34px; height: 34px; border-radius: 9px; background: #E0F2FE; color: #0369A1; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                        </span>
                        <div style="flex: 1; min-width: 0;">
                            <div style="font-size: 13px; font-weight: 700; color: #0F172A;">Stock Movements Audit</div>
                            <div style="font-size: 11px; color: #64748B;">Chronological log of receipts & dispatches</div>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Group 4: Vendors & Master Items -->
            <div class="mega-search-section mb-4" data-group="vendors_items">
                <div style="font-size: 11px; font-weight: 800; color: #94A3B8; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">Vendors & Item Catalog</div>
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 8px;">
                    <a href="{{ route('vendors.list') }}" class="mega-search-item flex items-center gap-3 p-2.5 rounded-xl hover:bg-purple-50/80 transition-all text-decoration-none group" data-title="Vendors Directory Suppliers Parties Contact GST Email Phone Bank">
                        <span style="width: 34px; height: 34px; border-radius: 9px; background: #FAF5FF; color: #9333EA; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        </span>
                        <div style="flex: 1; min-width: 0;">
                            <div style="font-size: 13px; font-weight: 700; color: #0F172A;">Vendors Directory</div>
                            <div style="font-size: 11px; color: #64748B;">Approved suppliers, contacts & GST details</div>
                        </div>
                    </a>

                    <a href="{{ route('items.index') }}" class="mega-search-item flex items-center gap-3 p-2.5 rounded-xl hover:bg-orange-50/80 transition-all text-decoration-none group" data-title="Items Master Catalog Product List Codes Specifications Unit Materials">
                        <span style="width: 34px; height: 34px; border-radius: 9px; background: #FFF7ED; color: #EA580C; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                        </span>
                        <div style="flex: 1; min-width: 0;">
                            <div style="font-size: 13px; font-weight: 700; color: #0F172A;">Item Master Catalog</div>
                            <div style="font-size: 11px; color: #64748B;">Standardized items, codes & descriptions</div>
                        </div>
                    </a>

                    <a href="{{ route('vendors.create') }}" class="mega-search-item flex items-center gap-3 p-2.5 rounded-xl hover:bg-purple-50/80 transition-all text-decoration-none group" data-title="Add New Vendor Register Party Supplier Onboarding">
                        <span style="width: 34px; height: 34px; border-radius: 9px; background: #F3E8FF; color: #7E22CE; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                        </span>
                        <div style="flex: 1; min-width: 0;">
                            <div style="font-size: 13px; font-weight: 700; color: #0F172A;">Register New Vendor</div>
                            <div style="font-size: 11px; color: #64748B;">Onboard new supplier with GST & bank info</div>
                        </div>
                    </a>

                    <a href="{{ route('items.create') }}" class="mega-search-item flex items-center gap-3 p-2.5 rounded-xl hover:bg-orange-50/80 transition-all text-decoration-none group" data-title="Create Item Add Master Material Code Specification">
                        <span style="width: 34px; height: 34px; border-radius: 9px; background: #FFEDD5; color: #C2410C; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                        </span>
                        <div style="flex: 1; min-width: 0;">
                            <div style="font-size: 13px; font-weight: 700; color: #0F172A;">Create New Item</div>
                            <div style="font-size: 11px; color: #64748B;">Define new inventory or purchase article</div>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Group 5: Master Data & System Administration -->
            <div class="mega-search-section mb-2" data-group="master">
                <div style="font-size: 11px; font-weight: 800; color: #94A3B8; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">Master Data & System Settings</div>
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 8px;">
                    <a href="{{ route('dashboard.index') }}" class="mega-search-item flex items-center gap-3 p-2.5 rounded-xl hover:bg-slate-100 transition-all text-decoration-none group" data-title="Dashboard Home KPI Statistics Executive Overview">
                        <span style="width: 34px; height: 34px; border-radius: 9px; background: #F1F5F9; color: #334155; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        </span>
                        <div style="flex: 1; min-width: 0;">
                            <div style="font-size: 13px; font-weight: 700; color: #0F172A;">Executive Dashboard</div>
                            <div style="font-size: 11px; color: #64748B;">System overview, statistics & draft tickets</div>
                        </div>
                    </a>

                    <a href="{{ route('departments.index') }}" class="mega-search-item flex items-center gap-3 p-2.5 rounded-xl hover:bg-teal-50/80 transition-all text-decoration-none group" data-title="Departments Master Lab Testing Accounts Maintenance Admin">
                        <span style="width: 34px; height: 34px; border-radius: 9px; background: #CCFBF1; color: #0F766E; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        </span>
                        <div style="flex: 1; min-width: 0;">
                            <div style="font-size: 13px; font-weight: 700; color: #0F172A;">Departments Directory</div>
                            <div style="font-size: 11px; color: #64748B;">Configure institute divisions & laboratories</div>
                        </div>
                    </a>

                    <a href="{{ route('units.index') }}" class="mega-search-item flex items-center gap-3 p-2.5 rounded-xl hover:bg-slate-100 transition-all text-decoration-none group" data-title="Units of Measurement Kg Meters Litres Numbers Box Pieces">
                        <span style="width: 34px; height: 34px; border-radius: 9px; background: #F1F5F9; color: #475569; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M3 6l3 18h12l3-18H3zm3 0V4a2 2 0 012-2h8a2 2 0 012 2v2"/></svg>
                        </span>
                        <div style="flex: 1; min-width: 0;">
                            <div style="font-size: 13px; font-weight: 700; color: #0F172A;">Units of Measurement</div>
                            <div style="font-size: 11px; color: #64748B;">Maintain measurement units (Kg, Mtr, Nos...)</div>
                        </div>
                    </a>

                    <a href="{{ route('projects.index') }}" class="mega-search-item flex items-center gap-3 p-2.5 rounded-xl hover:bg-blue-50/80 transition-all text-decoration-none group" data-title="Projects Master Schemes Funding Codes Industry Govt">
                        <span style="width: 34px; height: 34px; border-radius: 9px; background: #EFF6FF; color: #1D4ED8; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
                        </span>
                        <div style="flex: 1; min-width: 0;">
                            <div style="font-size: 13px; font-weight: 700; color: #0F172A;">Projects Master</div>
                            <div style="font-size: 11px; color: #64748B;">Institutional & sponsored research schemes</div>
                        </div>
                    </a>

                    <a href="{{ route('users.list') }}" class="mega-search-item flex items-center gap-3 p-2.5 rounded-xl hover:bg-blue-50/80 transition-all text-decoration-none group" data-title="Users Management Accounts Staff Officers Logins">
                        <span style="width: 34px; height: 34px; border-radius: 9px; background: #EFF6FF; color: #2563EB; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        </span>
                        <div style="flex: 1; min-width: 0;">
                            <div style="font-size: 13px; font-weight: 700; color: #0F172A;">User Accounts & Access</div>
                            <div style="font-size: 11px; color: #64748B;">Staff accounts, department heads & logins</div>
                        </div>
                    </a>

                    <a href="{{ route('roles.index') }}" class="mega-search-item flex items-center gap-3 p-2.5 rounded-xl hover:bg-slate-100 transition-all text-decoration-none group" data-title="Roles Permissions Security Access Control">
                        <span style="width: 34px; height: 34px; border-radius: 9px; background: #F1F5F9; color: #475569; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        </span>
                        <div style="flex: 1; min-width: 0;">
                            <div style="font-size: 13px; font-weight: 700; color: #0F172A;">Roles & Permissions</div>
                            <div style="font-size: 11px; color: #64748B;">Granular authorization & security policies</div>
                        </div>
                    </a>

                    <a href="{{ route('bulk-upload.index') }}" class="mega-search-item flex items-center gap-3 p-2.5 rounded-xl hover:bg-indigo-50/80 transition-all text-decoration-none group" data-title="Bulk Data Upload Import Excel CSV Indents Vendors PO">
                        <span style="width: 34px; height: 34px; border-radius: 9px; background: #EEF2FF; color: #4F46E5; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                        </span>
                        <div style="flex: 1; min-width: 0;">
                            <div style="font-size: 13px; font-weight: 700; color: #0F172A;">Bulk Data Upload</div>
                            <div style="font-size: 11px; color: #64748B;">Batch import spreadsheet datasets</div>
                        </div>
                    </a>

                    <a href="{{ route('profile.show') }}" class="mega-search-item flex items-center gap-3 p-2.5 rounded-xl hover:bg-slate-100 transition-all text-decoration-none group" data-title="My Profile Account Password Settings User Details">
                        <span style="width: 34px; height: 34px; border-radius: 9px; background: #F8FAFC; color: #64748B; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </span>
                        <div style="flex: 1; min-width: 0;">
                            <div style="font-size: 13px; font-weight: 700; color: #0F172A;">My Profile & Account</div>
                            <div style="font-size: 11px; color: #64748B;">Personal credentials & profile settings</div>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        <!-- Empty Results State -->
        <div id="mega-search-empty" style="display: none; padding: 40px 20px; text-align: center;">
            <div style="width: 52px; height: 52px; border-radius: 50%; background: #F1F5F9; color: #94A3B8; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 12px;">
                <svg style="width: 24px; height: 24px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div style="font-size: 15px; font-weight: 700; color: #1E293B; margin-bottom: 4px;">No results found</div>
            <div id="mega-search-empty-text" style="font-size: 12px; color: #64748B; max-width: 380px; margin: 0 auto;">No matching indents, purchase orders, vendors, or system modules could be found.</div>
            <div style="margin-top: 14px; font-size: 11px; color: #94A3B8;">Tip: Try searching by Indent #, PO Number, Party name, or Item code.</div>
        </div>
    </div>

    <!-- Modal Footer Controls -->
    <div style="padding: 10px 20px; background: #F8FAFC; border-top: 1px solid #F1F5F9; display: flex; align-items: center; justify-content: space-between; font-size: 11px; color: #64748B;">
        <div style="display: flex; align-items: center; gap: 6px;">
            <span style="font-weight: 600; color: #334155;">NITRA Omnisearch Hub</span>
            <span style="color: #CBD5E1;">•</span>
            <span>Live Records & Modules</span>
        </div>
        <div style="display: flex; align-items: center; gap: 12px; font-family: monospace;">
            <span><kbd style="background: #E2E8F0; padding: 2px 5px; border-radius: 4px; font-weight: 700; color: #475569;">↑</kbd> <kbd style="background: #E2E8F0; padding: 2px 5px; border-radius: 4px; font-weight: 700; color: #475569;">↓</kbd> navigate</span>
            <span><kbd style="background: #E2E8F0; padding: 2px 5px; border-radius: 4px; font-weight: 700; color: #475569;">↵</kbd> select</span>
            <span><kbd style="background: #E2E8F0; padding: 2px 5px; border-radius: 4px; font-weight: 700; color: #475569;">esc</kbd> close</span>
        </div>
    </div>
</div>

<style>
@keyframes megaSpin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}
.mega-search-item.is-focused, .mega-live-item.is-focused {
    background: #EFF6FF !important;
    outline: 2px solid #3B82F6 !important;
    outline-offset: -1px;
}
</style>

<script>
let megaActiveCategory = 'all';
let megaLiveAbortController = null;
let megaDebounceTimer = null;
let megaFocusedIndex = -1;

function openMegaSearchModal() {
    const overlay = document.getElementById('mega-search-overlay');
    const modal = document.getElementById('mega-search-modal');
    if (!overlay || !modal) return;
    
    overlay.style.display = 'block';
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';

    setTimeout(() => {
        const input = document.getElementById('mega-search-input');
        if (input) {
            input.value = '';
            input.focus();
            filterMegaSearch('');
        }
    }, 50);
}

function closeMegaSearchModal() {
    const overlay = document.getElementById('mega-search-overlay');
    const modal = document.getElementById('mega-search-modal');
    if (overlay) overlay.style.display = 'none';
    if (modal) modal.style.display = 'none';
    document.body.style.overflow = '';
    
    if (megaLiveAbortController) {
        megaLiveAbortController.abort();
    }
}

function clearMegaSearch() {
    const input = document.getElementById('mega-search-input');
    if (input) {
        input.value = '';
        input.focus();
        filterMegaSearch('');
    }
}

function setMegaCategoryFilter(category, btn) {
    megaActiveCategory = category;
    document.querySelectorAll('.mega-filter-pill').forEach(pill => {
        pill.style.background = '#FFFFFF';
        pill.style.color = '#475569';
        pill.style.borderColor = '#E2E8F0';
        pill.classList.remove('active');
    });
    if (btn) {
        btn.style.background = '#2563EB';
        btn.style.color = '#FFFFFF';
        btn.style.borderColor = '#2563EB';
        btn.classList.add('active');
    }
    const query = document.getElementById('mega-search-input')?.value || '';
    filterMegaSearch(query);
}

function handleMegaSearchInput(val) {
    clearTimeout(megaDebounceTimer);
    megaDebounceTimer = setTimeout(() => {
        filterMegaSearch(val);
    }, 150);
}

function filterMegaSearch(query) {
    const q = (query || '').toLowerCase().trim();
    const clearBtn = document.getElementById('mega-search-clear');
    if (clearBtn) {
        clearBtn.style.display = q ? 'inline-flex' : 'none';
    }

    megaFocusedIndex = -1;

    // 1. Filter Static Module Items & Sections
    let visibleModulesCount = 0;
    const sections = document.querySelectorAll('.mega-search-section');

    sections.forEach(section => {
        const group = section.getAttribute('data-group');
        const matchesCategory = (megaActiveCategory === 'all' || megaActiveCategory === group);

        let sectionHasVisible = false;
        const items = section.querySelectorAll('.mega-search-item');

        items.forEach(item => {
            const title = (item.getAttribute('data-title') || '').toLowerCase();
            const matchesQuery = (!q || title.includes(q));

            if (matchesCategory && matchesQuery) {
                item.style.display = 'flex';
                sectionHasVisible = true;
                visibleModulesCount++;
            } else {
                item.style.display = 'none';
            }
        });

        // Smart section auto-hide: if no items are visible, hide entire section header
        if (sectionHasVisible) {
            section.style.display = 'block';
        } else {
            section.style.display = 'none';
        }
    });

    // 2. Query Live Database Records (via AJAX) if query has >= 2 characters
    if (q.length >= 2) {
        fetchLiveMegaRecords(q);
    } else {
        const liveSec = document.getElementById('mega-search-live-section');
        if (liveSec) liveSec.style.display = 'none';
        const liveResults = document.getElementById('mega-search-live-results');
        if (liveResults) liveResults.innerHTML = '';
        checkMegaEmptyState(visibleModulesCount, 0, q);
    }
}

function fetchLiveMegaRecords(query) {
    const spinner = document.getElementById('mega-search-spinner');
    if (spinner) spinner.style.display = 'inline-block';

    if (megaLiveAbortController) {
        megaLiveAbortController.abort();
    }
    megaLiveAbortController = new AbortController();

    const url = "{{ route('mega-search.query') }}?q=" + encodeURIComponent(query);

    fetch(url, { signal: megaLiveAbortController.signal })
        .then(res => res.json())
        .then(data => {
            if (spinner) spinner.style.display = 'none';

            const liveSection = document.getElementById('mega-search-live-section');
            const liveContainer = document.getElementById('mega-search-live-results');
            const countBadge = document.getElementById('mega-live-count-badge');
            
            if (!liveSection || !liveContainer) return;

            const results = data.results || [];
            
            if (results.length > 0) {
                liveSection.style.display = 'block';
                if (countBadge) countBadge.textContent = results.length + ' found';

                liveContainer.innerHTML = results.map(item => `
                    <a href="${item.url}" class="mega-live-item flex items-center justify-between p-2.5 rounded-xl hover:bg-blue-50/90 transition-all text-decoration-none border border-slate-100 group" style="background: #FFFFFF;">
                        <div class="flex items-center gap-3 min-w-0">
                            <span style="width: 32px; height: 32px; border-radius: 8px; background: ${item.bg || '#EFF6FF'}; color: ${item.color || '#2563EB'}; display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: 14px; font-weight: 700;">
                                ${getMegaCategoryIcon(item.category)}
                            </span>
                            <div class="min-w-0">
                                <div style="font-size: 13px; font-weight: 700; color: #0F172A;" class="truncate">${escapeHtml(item.title)}</div>
                                <div style="font-size: 11px; color: #64748B;" class="truncate">${escapeHtml(item.subtitle)}</div>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 flex-shrink-0 ms-3">
                            <span class="text-xs px-2 py-0.5 rounded-md font-semibold ${item.badge_class || 'bg-blue-50 text-blue-700'}" style="font-size: 10px;">${escapeHtml(item.badge || item.category)}</span>
                            <span style="color: #94A3B8; font-size: 12px;" class="group-hover:translate-x-0.5 transition-transform">→</span>
                        </div>
                    </a>
                `).join('');
            } else {
                liveSection.style.display = 'none';
                liveContainer.innerHTML = '';
            }

            // Check if both static modules and live records yielded 0
            const visibleModulesCount = document.querySelectorAll('.mega-search-item:not([style*="display: none"])').length;
            checkMegaEmptyState(visibleModulesCount, results.length, query);
        })
        .catch(err => {
            if (err.name !== 'AbortError') {
                if (spinner) spinner.style.display = 'none';
            }
        });
}

function getMegaCategoryIcon(cat) {
    if (!cat) return '•';
    if (cat.includes('Indent')) return '📋';
    if (cat.includes('Purchase')) return '🧾';
    if (cat.includes('Vendor')) return '🏪';
    if (cat.includes('Item')) return '📦';
    if (cat.includes('Department')) return '🏢';
    if (cat.includes('Project')) return '📁';
    return '🔍';
}

function checkMegaEmptyState(modulesCount, liveCount, query) {
    const emptyState = document.getElementById('mega-search-empty');
    const emptyText = document.getElementById('mega-search-empty-text');
    if (!emptyState) return;

    if (modulesCount === 0 && liveCount === 0 && query.length > 0) {
        emptyState.style.display = 'block';
        if (emptyText) {
            emptyText.textContent = `No matching indents, POs, vendors, or modules found for "${query}".`;
        }
    } else {
        emptyState.style.display = 'none';
    }
}

function escapeHtml(text) {
    if (!text) return '';
    const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
    return String(text).replace(/[&<>"']/g, m => map[m]);
}

// Global Keyboard Shortcuts (Ctrl+K, Esc, ArrowDown, ArrowUp, Enter)
document.addEventListener('keydown', function(e) {
    // 1. Ctrl+K or Cmd+K to toggle modal
    if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') {
        e.preventDefault();
        const modal = document.getElementById('mega-search-modal');
        if (modal && modal.style.display === 'flex') {
            closeMegaSearchModal();
        } else {
            openMegaSearchModal();
        }
        return;
    }

    // Modal must be open for subsequent keys
    const modal = document.getElementById('mega-search-modal');
    if (!modal || modal.style.display !== 'flex') return;

    // 2. Escape to close
    if (e.key === 'Escape') {
        e.preventDefault();
        closeMegaSearchModal();
        return;
    }

    // 3. Arrow Down / Arrow Up navigation
    if (e.key === 'ArrowDown' || e.key === 'ArrowUp') {
        e.preventDefault();
        const items = Array.from(document.querySelectorAll(
            '#mega-search-live-results .mega-live-item, .mega-search-section:not([style*="display: none"]) .mega-search-item:not([style*="display: none"])'
        ));

        if (items.length === 0) return;

        items.forEach(el => el.classList.remove('is-focused'));

        if (e.key === 'ArrowDown') {
            megaFocusedIndex = (megaFocusedIndex + 1) % items.length;
        } else if (e.key === 'ArrowUp') {
            megaFocusedIndex = (megaFocusedIndex - 1 + items.length) % items.length;
        }

        const target = items[megaFocusedIndex];
        if (target) {
            target.classList.add('is-focused');
            target.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
        }
        return;
    }

    // 4. Enter to select focused or first item
    if (e.key === 'Enter') {
        const items = Array.from(document.querySelectorAll(
            '#mega-search-live-results .mega-live-item, .mega-search-section:not([style*="display: none"]) .mega-search-item:not([style*="display: none"])'
        ));

        if (items.length > 0) {
            e.preventDefault();
            const target = (megaFocusedIndex >= 0 && items[megaFocusedIndex]) ? items[megaFocusedIndex] : items[0];
            if (target && target.href) {
                window.location.href = target.href;
            }
        }
    }
});

// Fullscreen handlers
function openFullscreen(el = document.documentElement) {
  const req = el.requestFullscreen
    || el.webkitRequestFullscreen
    || el.mozRequestFullScreen
    || el.msRequestFullscreen;
  if (req) req.call(el);
}

function closeFullscreen() {
  const exit = document.exitFullscreen
    || document.webkitExitFullscreen
    || document.mozCancelFullScreen
    || document.msExitFullscreen;
  if (exit) exit.call(document);
}

function isFullscreen() {
  return document.fullscreenElement
      || document.webkitFullscreenElement
      || document.mozFullScreenElement
      || document.msFullscreenElement;
}

function toggleFullscreen(el = document.documentElement) {
  if (isFullscreen()) closeFullscreen();
  else openFullscreen(el);
}

document.addEventListener('fullscreenchange', () => {});
</script>


			
