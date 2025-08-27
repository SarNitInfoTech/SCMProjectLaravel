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
					          

			

			
		
            @include('common.menu')
<script>
// Open fullscreen for a specific element (defaults to the whole page)
function openFullscreen(el = document.documentElement) {
  const req = el.requestFullscreen
    || el.webkitRequestFullscreen
    || el.mozRequestFullScreen
    || el.msRequestFullscreen;
  if (req) req.call(el);
}

// Exit fullscreen
function closeFullscreen() {
  const exit = document.exitFullscreen
    || document.webkitExitFullscreen
    || document.mozCancelFullScreen
    || document.msExitFullscreen;
  if (exit) exit.call(document);
}

// Check if currently in fullscreen
function isFullscreen() {
  return document.fullscreenElement
      || document.webkitFullscreenElement
      || document.mozFullScreenElement
      || document.msFullscreenElement;
}

// Toggle fullscreen on an element or the whole page
function toggleFullscreen(el = document.documentElement) {
  if (isFullscreen()) closeFullscreen();
  else openFullscreen(el);
}

// Optional: react to fullscreen changes
document.addEventListener('fullscreenchange', () => {
  // e.g., toggle an icon class, lock scroll, etc.
});
</script>

			
