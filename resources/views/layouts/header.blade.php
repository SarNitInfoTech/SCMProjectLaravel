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
							<div class="main-header-center hidden lg:block">
								<input class="form-control placeholder:!text-headerprimecolor placeholder:opacity-70 placeholder:font-thin placeholder:text-sm" placeholder="Search for anything..." type="search">
								<button class="btn"><i class="fa fa-search hidden md:block opacity-[0.5]"></i></button>
							</div>
							<!-- End::header-link -->
							</div>
						</div>
						<div class="header-content-right">

							<div class="header-element hs-dropdown search-dropdown ti-dropdown  lg:!hidden [--auto-close:inside]">
							<a href="javascript:void(0);" class="header-link dropdown-toggle" id="hs-dropdown-auto-close-inside" data-bs-toggle="dropdown" aria-expanded="false"> <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" class="header-link-icon">
								<path d="M784-120 532-372q-30 24-69 38t-83 14q-109 0-184.5-75.5T120-580q0-109 75.5-184.5T380-840q109 0 184.5 75.5T640-580q0 44-14 83t-38 69l252 252-56 56ZM380-400q75 0 127.5-52.5T560-580q0-75-52.5-127.5T380-760q-75 0-127.5 52.5T200-580q0 75 52.5 127.5T380-400Z">
								</path>
								</svg> </a> 

								<div class="hs-dropdown-menu ti-dropdown-menu hidden" aria-labelledby="hs-dropdown-auto-close-inside">
								<div>
									<div class="input-group w-full p-2"> <input type="text" class="form-control" placeholder="Search....">
									<div class="ti-btn ti-btn-primary-full !mb-0"> <i class="fa fa-search" aria-hidden="true"></i> </div>
									</div>
								</div>
							</div>
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
					            <!-- End Main-Header -->

            <!-- Country-selector modal -->
            <!-- Start::Off-canvas sidebar-->
			<div id="hs-overlay-chat" class="hs-overlay hidden ti-offcanvas ti-offcanvas-right overflow-auto" tabindex="-1">
				<div class="ti-offcanvas-header !py-2 rounded-none">
					<h5 class="text-[.875rem] uppercase mb-0 text-defaulttextcolor font-semibold" id="sidebarLabel">Notifications</h5>
					<button type="button" class="ti-btn flex-shrink-0 p-0  transition-none text-defaulttextcolor dark:text-defaulttextcolor/70 hover:text-gray-700 focus:ring-gray-400 focus:ring-offset-white  dark:hover:text-white/80 dark:focus:ring-white/10 dark:focus:ring-offset-white/10" data-hs-overlay="#hs-overlay-chat">
					<span class="sr-only">Close modal</span>
					<i class="ri-close-fill leading-none text-lg"></i>
					</button>
				</div>
				<div class="ti-offcanvas-body rounded-none p-0">
					<ul class="nav nav-tabs  p-4" role="tablist">
					<div class=" rtl:space-x-reverse" aria-label="Tabs" role="tablist">
						<button type="button" class="hs-tab-active:bg-primary w-full mb-2 rounded-[4px] !py-[10px] !px-[16px] text-start hs-tab-active:border-b-transparent text-defaultsize border-0 hs-tab-active:text-white dark:hs-tab-active:bg-primary dark:hs-tab-active:border-b-white/10 dark:hs-tab-active:text-white  bg-light  font-semibold  text-defaulttextcolor dark:text-defaulttextcolor/70  hover:text-gray-700 dark:bg-bodybg2 dark:border-white/10  active" id="chat-item" data-hs-tab="#chat" aria-controls="chat" role="tab">
						<i class="fe fe-message-circle text-[.9375rem] me-2 inline-flex"></i>Chat
						</button>
						<button type="button" class="hs-tab-active:bg-primary w-full mb-2  rounded-[4px] !py-[10px] !px-[16px] text-start hs-tab-active:border-b-transparent text-defaultsize border-0 hs-tab-active:text-white dark:hs-tab-active:bg-primary dark:hs-tab-active:border-b-white/10 dark:hs-tab-active:text-white   bg-light font-semibold  text-defaulttextcolor dark:text-defaulttextcolor/70  hover:text-gray-700 dark:bg-bodybg2 dark:border-white/10  dark:hover:text-gray-300" id="notification-item" data-hs-tab="#notification" aria-controls="notification" role="tab">
						<i class="fe fe-bell text-[.9375rem] me-2 inline-flex"></i> Notifications
						</button>
						<button type="button" class="hs-tab-active:bg-primary w-full mb-0 rounded-[4px] !py-[10px] !px-[16px] text-start hs-tab-active:border-b-transparent text-defaultsize border-0 hs-tab-active:text-white dark:hs-tab-active:bg-primary dark:hs-tab-active:border-b-white/10 dark:hs-tab-active:text-white   bg-light font-semibold  text-defaulttextcolor dark:text-defaulttextcolor/70  hover:text-gray-700 dark:bg-bodybg2 dark:border-white/10  dark:hover:text-gray-300" id="friends-item" data-hs-tab="#friends" aria-controls="friends" role="tab">
						<i class="fe fe-users text-[.9375rem] me-2 inline-flex"></i>Friends
						</button>
					</div>
					</ul>
					<div class="tab-content !border-0 ">
					<div class="tab-pane !text-defaulttextcolor dark:text-defaulttextcolor/70 !border-s-0 !border-e-0 !rounded-none !p-0 show border-defaultborder dark:border-defaultborder/10 " id="chat" role="tabpanel" aria-labelledby="chat-item">
						<div class="list flex items-center border-b border-defaultborder dark:border-defaultborder/10  p-3">
						<div class="">
							<span class="avatar bg-primary avatar-rounded avatar-md">CH</span>
						</div>
						<a class="w-full ms-3" href="javascript:void(0);">
							<p class="mb-0 flex ">
							<b>New Websites is Created</b>
							</p>
							<div class="flex justify-between items-center">
							<div class="flex items-center">
								<i class="fa-regular fa-clock text-textmuted me-1 text-[.6875rem]"></i>
								<small class="text-textmuted ms-auto">30 mins ago</small>
								<p class="mb-0"></p>
							</div>
							</div>
						</a>
						</div>
						<div class="list flex items-center border-b border-defaultborder dark:border-defaultborder/10  p-3">
						<div class="">
							<span class="avatar bg-danger avatar-rounded avatar-md">N</span>
						</div>
						<a class="w-full ms-3" href="javascript:void(0);">
							<p class="mb-0 flex ">
							<b>Prepare For the Next Project</b>
							</p>
							<div class="flex justify-between items-center">
							<div class="flex items-center">
								<i class="fa-regular fa-clock text-textmuted me-1 text-[.6875rem]"></i>
								<small class="text-textmuted ms-auto">2 hours ago</small>
								<p class="mb-0"></p>
							</div>
							</div>
						</a>
						</div>
						<div class="list flex items-center border-b border-defaultborder dark:border-defaultborder/10  p-3">
						<div class="">
							<span class="avatar bg-info avatar-rounded avatar-md">S</span>
						</div>
						<a class="w-full ms-3" href="javascript:void(0);">
							<p class="mb-0 flex ">
							<b>Decide the live Discussion</b>
							</p>
							<div class="flex justify-between items-center">
							<div class="flex items-center">
								<i class="fa-regular fa-clock text-textmuted me-1 text-[.6875rem]"></i>
								<small class="text-textmuted ms-auto">3 hours ago</small>
								<p class="mb-0"></p>
							</div>
							</div>
						</a>
						</div>
						<div class="list flex items-center border-b border-defaultborder dark:border-defaultborder/10  p-3">
						<div class="">
							<span class="avatar bg-warning avatar-rounded avatar-md">K</span>
						</div>
						<a class="w-full ms-3" href="javascript:void(0);">
							<p class="mb-0 flex ">
							<b>Meeting at 3:00 pm</b>
							</p>
							<div class="flex justify-between items-center">
							<div class="flex items-center">
								<i class="fa-regular fa-clock text-textmuted me-1 text-[.6875rem]"></i>
								<small class="text-textmuted ms-auto">4 hours ago</small>
								<p class="mb-0"></p>
							</div>
							</div>
						</a>
						</div>
						<div class="list flex items-center border-b border-defaultborder dark:border-defaultborder/10  p-3">
						<div class="">
							<span class="avatar bg-success avatar-rounded avatar-md">R</span>
						</div>
						<a class="w-full ms-3" href="javascript:void(0);">
							<p class="mb-0 flex ">
							<b>Prepare for Presentation</b>
							</p>
							<div class="flex justify-between items-center">
							<div class="flex items-center">
								<i class="fa-regular fa-clock text-textmuted me-1 text-[.6875rem]"></i>
								<small class="text-textmuted ms-auto">1 day ago</small>
								<p class="mb-0"></p>
							</div>
							</div>
						</a>
						</div>
						<div class="list flex items-center border-b border-defaultborder dark:border-defaultborder/10  p-3">
						<div class="">
							<span class="avatar bg-pinkmain avatar-rounded avatar-md">MS</span>
						</div>
						<a class="w-full ms-3" href="javascript:void(0);">
							<p class="mb-0 flex ">
							<b>Prepare for Presentation</b>
							</p>
							<div class="flex justify-between items-center">
							<div class="flex items-center">
								<i class="fa-regular fa-clock text-textmuted me-1 text-[.6875rem]"></i>
								<small class="text-textmuted ms-auto">1 day ago</small>
								<p class="mb-0"></p>
							</div>
							</div>
						</a>
						</div>
						<div class="list flex items-center border-b border-defaultborder dark:border-defaultborder/10  p-3">
						<div class="">
							<span class="avatar bg-purplemain avatar-rounded avatar-md">L</span>
						</div>
						<a class="w-full ms-3" href="javascript:void(0);">
							<p class="mb-0 flex ">
							<b>Prepare for Presentation</b>
							</p>
							<div class="flex justify-between items-center">
							<div class="flex items-center">
								<i class="fa-regular fa-clock text-textmuted me-1 text-[.6875rem]"></i>
								<small class="text-textmuted ms-auto">45 minutes ago</small>
								<p class="mb-0"></p>
							</div>
							</div>
						</a>
						</div>
						<div class="list flex border-b border-defaultborder dark:border-defaultborder/10 items-center p-3">
						<div class="">
							<span class="avatar bg-indigomain avatar-rounded avatar-md">U</span>
						</div>
						<a class="w-full ms-3" href="javascript:void(0);">
							<p class="mb-0 flex ">
							<b>Prepare for Presentation</b>
							</p>
							<div class="flex justify-between items-center">
							<div class="flex items-center">
								<i class="fa-regular fa-clock text-textmuted me-1 text-[.6875rem]"></i>
								<small class="text-textmuted ms-auto">2 days ago</small>
								<p class="mb-0"></p>
							</div>
							</div>
						</a>
						</div>
					</div>
					<div class="tab-pane !text-defaulttextcolor dark:text-defaulttextcolor/70 !border-s-0 !border-e-0 !rounded-none !p-0 border-defaultborder dark:border-defaultborder/10  hidden" id="notification" role="tabpanel" aria-labelledby="notification-item">
						<div class="ti-list-group ti-list-group-flush ">
						<div class="ti-list-group-item !border-s-0 !border-e-0 !border-t-0 flex  items-center">
							<span class="avatar avatar-lg online avatar-rounded flex-shrink-0">
							<img src="images/1.jpg" alt="img">
							</span>
							<div class="ms-3">
							<strong>Madeleine</strong> Hey! there I' am available....
							<div class="small text-textmuted">
								3 hours ago
							</div>
							</div>
						</div>
						<div class="ti-list-group-item !border-s-0 !border-e-0 !border-t-0 flex  items-center">
							<span class="avatar avatar-lg online avatar-rounded flex-shrink-0">
							<img src="images/2.jpg" alt="img">
							</span>
							<div class="ms-3">
							<strong>Anthony</strong> New product Launching...
							<div class="small text-textmuted">
								5 hour ago
							</div>
							</div>
						</div>
						<div class="ti-list-group-item !border-s-0 !border-e-0 !border-t-0 flex  items-center">
							<span class="avatar avatar-lg avatar-rounded flex-shrink-0">
							<img src="images/3.jpg" alt="img">
							</span>
							<div class="ms-3">
							<strong>Olivia</strong> New Schedule Realease......
							<div class="small text-textmuted">
								45 minutes ago
							</div>
							</div>
						</div>
						<div class="ti-list-group-item !border-s-0 !border-e-0 !border-t-0 flex  items-center">
							<span class="avatar avatar-lg avatar-rounded flex-shrink-0">
							<img src="images/4.jpg" alt="img">
							</span>
							<div class="ms-3">
							<strong>Madeleine</strong> Hey! there I' am available....
							<div class="small text-textmuted">
								3 hours ago
							</div>
							</div>
						</div>
						<div class="ti-list-group-item !border-s-0 !border-e-0 !border-t-0 flex  items-center">
							<span class="avatar avatar-lg avatar-rounded flex-shrink-0">
							<img src="images/5.jpg" alt="img">
							</span>
							<div class="ms-3">
							<strong>Anthony</strong> New product Launching...
							<div class="small text-textmuted">
								5 hour ago
							</div>
							</div>
						</div>
						<div class="ti-list-group-item !border-s-0 !border-e-0 !border-t-0 flex  items-center">
							<span class="avatar avatar-lg avatar-rounded flex-shrink-0">
							<img src="images/6.jpg" alt="img">
							</span>
							<div class="ms-3">
							<strong>Olivia</strong> New Schedule Realease......
							<div class="small text-textmuted">
								45 minutes ago
							</div>
							</div>
						</div>
						<div class="ti-list-group-item  !border-b border-defaultborder dark:border-defaultborder/10 !border-s-0 !border-e-0 !border-t-0 flex  items-center">
							<span class="avatar avatar-lg avatar-rounded flex-shrink-0">
							<img src="images/7.jpg" alt="img">
							</span>
							<div class="ms-3">
							<strong>Olivia</strong> Hey! there I' am available....
							<div class="small text-textmuted">
								12 minutes ago
							</div>
							</div>
						</div>
						</div>
					</div>
					<div class="tab-pane !text-defaulttextcolor dark:text-defaulttextcolor/70 !border-s-0 !border-e-0 !rounded-none !p-0 border-defaultborder dark:border-defaultborder/10  active hidden" id="friends" role="tabpanel" aria-labelledby="friends-item">
						<div class="ti-list-group ti-list-group-flush ">
						<div class="ti-list-group-item flex !border-t-0 items-center">
							<span class="avatar avatar-md online avatar-rounded flex-shrink-0">
							<img src="images/1.jpg" alt="img">
							</span>
							<div class="ms-2">
							<div class="font-semibold" data-hs-overlay="#chatmodel">Mozelle Belt</div>
							</div>
							<div class="ms-auto">
							<a href="javascript:void(0);" class="ti-btn ti-btn-sm ti-btn-light" data-hs-overlay="#chatmodel"><i class="fab fa-facebook-messenger"></i></a>
							</div>
						</div>
						<div class="ti-list-group-item flex  items-center">
							<span class="avatar avatar-md avatar-rounded flex-shrink-0">
							<img src="images/2.jpg" alt="img">
							</span>
							<div class="ms-2">
							<div class="font-semibold" data-hs-overlay="#chatmodel">Florinda Carasco</div>
							</div>
							<div class="ms-auto">
							<a href="javascript:void(0);" class="ti-btn ti-btn-sm ti-btn-light" data-hs-overlay="#chatmodel"><i class="fab fa-facebook-messenger"></i></a>
							</div>
						</div>
						<div class="ti-list-group-item flex  items-center">
							<span class="avatar avatar-md avatar-rounded flex-shrink-0">
							<img src="images/5.jpg" alt="img">
							</span>
							<div class="ms-2">
							<div class="font-semibold" data-hs-overlay="#chatmodel">Alina Bernier</div>
							</div>
							<div class="ms-auto">
							<a href="javascript:void(0);" class="ti-btn ti-btn-sm ti-btn-light" data-hs-overlay="#chatmodel"><i class="fab fa-facebook-messenger"></i></a>
							</div>
						</div>
						<div class="ti-list-group-item flex  items-center">
							<span class="avatar avatar-md online avatar-rounded flex-shrink-0">
							<img src="images/6.jpg" alt="img">
							</span>
							<div class="ms-2">
							<div class="font-semibold" data-hs-overlay="#chatmodel">Zula Mclaughin</div>
							</div>
							<div class="ms-auto">
							<a href="javascript:void(0);" class="ti-btn ti-btn-sm ti-btn-light" data-hs-overlay="#chatmodel"><i class="fab fa-facebook-messenger"></i></a>
							</div>
						</div>
						<div class="ti-list-group-item flex  items-center">
							<span class="avatar avatar-md online avatar-rounded flex-shrink-0">
							<img src="images/8.jpg" alt="img">
							</span>
							<div class="ms-2">
							<div class="font-semibold" data-hs-overlay="#chatmodel">Isidro Heide</div>
							</div>
							<div class="ms-auto">
							<a href="javascript:void(0);" class="ti-btn ti-btn-sm ti-btn-light" data-hs-overlay="#chatmodel"><i class="fab fa-facebook-messenger"></i></a>
							</div>
						</div>
						<div class="ti-list-group-item flex  items-center">
							<span class="avatar avatar-md online avatar-rounded flex-shrink-0">
							<img src="images/8.jpg" alt="img">
							</span>
							<div class="ms-2">
							<div class="font-semibold" data-hs-overlay="#chatmodel">Mozelle Belt</div>
							</div>
							<div class="ms-auto">
							<a href="javascript:void(0);" class="ti-btn ti-btn-sm ti-btn-light"><i class="fab fa-facebook-messenger"></i></a>
							</div>
						</div>
						<div class="ti-list-group-item flex  items-center">
							<span class="avatar avatar-md online avatar-rounded flex-shrink-0">
							<img src="images/9.jpg" alt="img">
							</span>
							<div class="ms-2">
							<div class="font-semibold" data-hs-overlay="#chatmodel">Florinda Carasco</div>
							</div>
							<div class="ms-auto">
							<a href="javascript:void(0);" class="ti-btn ti-btn-sm ti-btn-light" data-hs-overlay="#chatmodel"><i class="fab fa-facebook-messenger"></i></a>
							</div>
						</div>
						<div class="ti-list-group-item flex  items-center">
							<span class="avatar avatar-md online avatar-rounded flex-shrink-0">
							<img src="images/10.jpg" alt="img">
							</span>
							<div class="ms-2">
							<div class="font-semibold" data-hs-overlay="#chatmodel">Alina Bernier</div>
							</div>
							<div class="ms-auto">
							<a href="javascript:void(0);" class="ti-btn ti-btn-sm ti-btn-light"><i class="fab fa-facebook-messenger"></i></a>
							</div>
						</div>
						<div class="ti-list-group-item flex  items-center">
							<span class="avatar avatar-md avatar-rounded flex-shrink-0">
							<img src="images/11.jpg" alt="img">
							</span>
							<div class="ms-2">
							<div class="font-semibold" data-hs-overlay="#chatmodel">Zula Mclaughin</div>
							</div>
							<div class="ms-auto">
							<a href="javascript:void(0);" class="ti-btn ti-btn-sm ti-btn-light" data-hs-overlay="#chatmodel"><i class="fab fa-facebook-messenger"></i></a>
							</div>
						</div>
						<div class="ti-list-group-item flex  items-center">
							<span class="avatar avatar-md online avatar-rounded flex-shrink-0">
							<img src="images/12.jpg" alt="img">
							</span>
							<div class="ms-2">
							<div class="font-semibold" data-hs-overlay="#chatmodel">Isidro Heide</div>
							</div>
							<div class="ms-auto">
							<a href="javascript:void(0);" class="ti-btn ti-btn-sm ti-btn-light"><i class="fab fa-facebook-messenger"></i></a>
							</div>
						</div>
						<div class="ti-list-group-item flex  items-center">
							<span class="avatar avatar-md online avatar-rounded flex-shrink-0">
							<img src="images/2.jpg" alt="img">
							</span>
							<div class="ms-2">
							<div class="font-semibold" data-hs-overlay="#chatmodel">Florinda Carasco</div>
							</div>
							<div class="ms-auto">
							<a href="javascript:void(0);" class="ti-btn ti-btn-sm ti-btn-light" data-hs-overlay="#chatmodel"><i class="fab fa-facebook-messenger"></i></a>
							</div>
						</div>
						<div class="ti-list-group-item flex  items-center">
							<span class="avatar avatar-md online avatar-rounded flex-shrink-0">
							<img src="images/2.jpg" alt="img">
							</span>
							<div class="ms-2">
							<div class="font-semibold" data-hs-overlay="#chatmodel">Alina Bernier</div>
							</div>
							<div class="ms-auto">
							<a href="javascript:void(0);" class="ti-btn ti-btn-sm ti-btn-light" data-hs-overlay="#chatmodel"><i class="fab fa-facebook-messenger"></i></a>
							</div>
						</div>
						<div class="ti-list-group-item flex  items-center">
							<span class="avatar avatar-md online avatar-rounded flex-shrink-0">
							<img src="images/3.jpg" alt="img">
							</span>
							<div class="ms-2">
							<div class="font-semibold" data-hs-overlay="#chatmodel">Zula Mclaughin</div>
							</div>
							<div class="ms-auto">
							<a href="javascript:void(0);" class="ti-btn ti-btn-sm ti-btn-light" data-hs-overlay="#chatmodel"><i class="fab fa-facebook-messenger"></i></a>
							</div>
						</div>
						<div class="ti-list-group-item flex !border-b border-defaultborder dark:border-defaultborder/10 items-center">
							<span class="avatar avatar-md online avatar-rounded flex-shrink-0">
							<img src="images/4.jpg" alt="img">
							</span>
							<div class="ms-2">
							<div class="font-semibold" data-hs-overlay="#chatmodel">Isidro Heide</div>
							</div>
							<div class="ms-auto">
							<a href="javascript:void(0);" class="ti-btn ti-btn-sm ti-btn-light" data-hs-overlay="#chatmodel"><i class="fab fa-facebook-messenger"></i></a>
							</div>
						</div>
						</div>
					</div>
					</div>
				</div>
			</div>
			<!-- End::Off-canvas sidebar-->

			

			
			
            <!-- End Country-selector modal -->

            <!--Main-Sidebar-->
            @include('common.menu')

			
