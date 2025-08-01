<aside class="app-sidebar" id="sidebar">

	<!-- Start::main-sidebar-header -->
	<div class="main-sidebar-header">
		<a href="{{ url('/') }}" class="header-logo flex items-center space-x-2">
			<!-- Show on full desktop view -->
			<img src="{{ asset('images/logo.png') }}" alt="logo" class="desktop-logo h-[40px]">

			<!-- Show only logo on toggle/sidebar collapsed -->
			<img src="{{ asset('images/logo.png') }}" alt="logo" class="toggle-logo h-[40px]">
		</a>
	</div>

	<!-- End::main-sidebar-header -->

	<!-- Start::main-sidebar -->
	<div class="main-sidebar" id="sidebar-scroll">

		<!-- Start::nav -->
		<nav class="main-menu-container nav nav-pills flex-column sub-open">
			<div class="slide-left" id="slide-left"><svg xmlns="http://www.w3.org/2000/svg" fill="#7b8191" width="24"
					height="24" viewBox="0 0 24 24">
					<path d="M13.293 6.293 7.586 12l5.707 5.707 1.414-1.414L10.414 12l4.293-4.293z"></path>
				</svg></div>
			<ul class="main-menu">
				<!-- Start::slide__category -->
				<li class="slide__category"><span class="category-name">Main</span></li>
				<!-- End::slide__category -->

				<!-- Dashboard Main Link -->
				<li class="slide">
					<a href="{{ route('dashboard.index') }}" class="side-menu__item">
						<svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24">
							<path d="M0 0h24v24H0V0z" fill="none"></path>
							<path
								d="M3 13h8V3H3v10zm2-8h4v6H5V5zm8 16h8V11h-8v10zm2-8h4v6h-4v-6zM13 3v6h8V3h-8zm6 4h-4V5h4v2zM3 21h8v-6H3v6zm2-4h4v2H5v-2z">
							</path>
						</svg>
						<span class="side-menu__label">Dashboard</span>
					</a>
				</li>

				<li class="slide__category">
					<span class="category-name">Indent</span>
				</li>
				<!-- End::Category Label -->


				<!-- Start::Master - Unit -->
				<!-- Indent Generate -->
				<li class="slide">
					<a href="{{ route('indent.create') }}" class="side-menu__item">
						<svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" fill="none" viewBox="0 0 24 24"
							stroke="currentColor">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
						</svg>
						<span class="side-menu__label">Indent Generate</span>
					</a>
				</li>

				<!-- Indent Register List -->
				<li class="slide">
					<a href="{{ route('indent.index') }}" class="side-menu__item">
						<svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" fill="none" viewBox="0 0 24 24"
							stroke="currentColor">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
						</svg>
						<span class="side-menu__label">Indent Register List</span>
					</a>
				</li>

				<!-- Purchase Order List -->
				<li class="slide">
					<a href="{{ route('indentroview.index') }}" class="side-menu__item">
						<svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" fill="none" viewBox="0 0 24 24"
							stroke="currentColor">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
								d="M3 10h18M3 6h18M3 14h9m-6 4h6" />
						</svg>
						<span class="side-menu__label">Purchase Order List</span>
					</a>
				</li>




				<!-- Start::Category Label -->
				<li class="slide__category">
					<span class="category-name">Master</span>
				</li>
				<!-- End::Category Label -->


				<!-- Start::Master - Unit -->
				<li class="slide">
					<a href="{{ route('unit.index') }}" class="side-menu__item">
						<svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" fill="none" viewBox="0 0 24 24"
							stroke="currentColor">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
								d="M3 7h18M3 12h18M3 17h18" />
						</svg>
						<span class="side-menu__label">Unit</span>
					</a>
				</li>

				<!-- Start::Master - Project -->
				<li class="slide">
					<a href="{{ route('project.index') }}" class="side-menu__item">
						<svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" fill="none" viewBox="0 0 24 24"
							stroke="currentColor">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
						</svg>
						<span class="side-menu__label">Project</span>
					</a>
				</li>

				<!-- Start::Master - Department -->
				<li class="slide">
					<a href="{{ route('department.index') }}" class="side-menu__item">
						<svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" fill="none" viewBox="0 0 24 24"
							stroke="currentColor">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
								d="M3 10h18M4 6h16a1 1 0 011 1v10a1 1 0 01-1 1H4a1 1 0 01-1-1V7a1 1 0 011-1z" />
						</svg>
						<span class="side-menu__label">Department</span>
					</a>
				</li>

				<li class="slide">
					<a href="{{ route('departmentHead.list') }}" class="side-menu__item">
						<svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" fill="none" viewBox="0 0 24 24"
							stroke="currentColor">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
								d="M5.121 17.804A1 1 0 016 17h12a1 1 0 01.879.515l2 4A1 1 0 0120 23H4a1 1 0 01-.879-1.485l2-4zM15 11a3 3 0 11-6 0 3 3 0 016 0z" />
						</svg>
						<span class="side-menu__label">Department Heads</span>
					</a>
				</li>


				<!-- Start::Category Label -->
				<li class="slide__category">
					<span class="category-name">Other</span>
				</li>
				<!-- End::Category Label -->
				<!-- Start::Master - Notification -->
<li class="slide">
    <a href="{{ route('notifications.index') }}" class="side-menu__item">
        <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" fill="none" viewBox="0 0 24 24"
             stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
        </svg>
        <span class="side-menu__label">Notifications</span>
    </a>
</li>
<!-- End::Master - Notification -->






			</ul>
			<div class="slide-right" id="slide-right"><svg xmlns="http://www.w3.org/2000/svg" fill="#7b8191" width="24"
					height="24" viewBox="0 0 24 24">
					<path d="M10.707 17.707 16.414 12l-5.707-5.707-1.414 1.414L13.586 12l-4.293 4.293z"></path>
				</svg></div>
		</nav>
		<!-- End::nav -->

	</div>
	<!-- End::main-sidebar -->

</aside>