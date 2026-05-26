<aside class="app-sidebar" id="sidebar">
	<div class="main-sidebar-header">
		<a href="{{ url('/') }}" class="header-logo flex items-center space-x-2">
			<img src="{{ asset('images/logo.png') }}" alt="logo" class="desktop-logo h-[40px]">
			<img src="{{ asset('images/logo.png') }}" alt="logo" class="toggle-logo h-[40px]">
		</a>
	</div>
	<div class="main-sidebar" id="sidebar-scroll">
		<nav class="main-menu-container nav nav-pills flex-column sub-open">
			<div class="slide-left" id="slide-left"><svg xmlns="http://www.w3.org/2000/svg" fill="#7b8191" width="24"
					height="24" viewBox="0 0 24 24">
					<path d="M13.293 6.293 7.586 12l5.707 5.707 1.414-1.414L10.414 12l4.293-4.293z"></path>
				</svg></div>
			<ul class="main-menu">
				<li class="slide__category"><span class="category-name">Main</span></li>
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
				@can('indents.create')
				<li class="slide__category">
					<span class="category-name">Indent</span>
				</li>
				<li class="slide">
					<a href="{{ route('indent.create') }}" class="side-menu__item">
						<svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" fill="none" viewBox="0 0 24 24"
							stroke="currentColor">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4" />
						</svg>
						<span class="side-menu__label">Generate Token</span>
					</a>
				</li>
				@endcan

				@if(auth()->user()->hasPermission('indents.view') || auth()->user()->hasPermission('pos.view'))
				<li class="slide__category">
					<span class="category-name">All Lists</span>
				</li>
				@endif

				<!-- Indent Register List -->
				@can('indents.view')
				<li class="slide">
					<a href="{{ route('indent.index') }}" class="side-menu__item">
						<svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" fill="none" viewBox="0 0 24 24"
							stroke="currentColor">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
						</svg>
						<span class="side-menu__label">Indent Register List</span>
					</a>
				</li>
				@endcan

				<!-- Purchase Order List -->
				@can('pos.view')
				<li class="slide">
					<a href="{{ route('indentroview.index') }}" class="side-menu__item">
						<svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" fill="none" viewBox="0 0 24 24"
							stroke="currentColor">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
								d="M3 10h18M3 6h18M3 14h9m-6 4h6" />
						</svg>
						<span class="side-menu__label">Purchase Order List</span>
					</a>
				</li>
				@endcan

				@can('reports.view')
				<li class="slide__category">
					<span class="category-name">All Reports</span>
				</li>
				<!-- Indent Register List -->
				<li class="slide">
					<a href="{{ route('report.viewAllIndent') }}" class="side-menu__item">
						<svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" fill="none" viewBox="0 0 24 24"
							stroke="currentColor">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
						</svg>
						<span class="side-menu__label">Indent Report</span>
					</a>
				</li>

				<!-- Purchase Order List -->
				<li class="slide">
					<a href="{{ route('reports.indentspos.index') }}" class="side-menu__item">
						<svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" fill="none" viewBox="0 0 24 24"
							stroke="currentColor">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
								d="M3 10h18M3 6h18M3 14h9m-6 4h6" />
						</svg>
						<span class="side-menu__label">Purchase Order Report</span>
					</a>
				</li>
				@endcan

				<!-- Inventory Management -->
				@can('inventory.view')
				<li class="slide__category">
					<span class="category-name">Inventory</span>
				</li>
				<li class="slide">
					<a href="{{ route('inventory.dashboard') }}" class="side-menu__item">
						<svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
						</svg>
						<span class="side-menu__label">Inventory Dashboard</span>
					</a>
				</li>
				<li class="slide">
					<a href="{{ route('inventory.stocks.list') }}" class="side-menu__item">
						<svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
						</svg>
						<span class="side-menu__label">Stock Management</span>
					</a>
				</li>
				<li class="slide">
					<a href="{{ route('inventory.reports.low-stock') }}" class="side-menu__item">
						<svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
						</svg>
						<span class="side-menu__label">Low Stock Alerts</span>
					</a>
				</li>
				@endcan

@if(auth()->check() && (auth()->user()->role === 'super_admin' || auth()->user()->role === 'admin' || auth()->user()->hasPermission('units.view') || auth()->user()->hasPermission('projects.view') || auth()->user()->hasPermission('departments.view') || auth()->user()->hasPermission('department_heads.view') || auth()->user()->hasPermission('users.view') || auth()->user()->hasPermission('vendors.view') || auth()->user()->hasPermission('items.view') || auth()->user()->hasPermission('roles.manage')))
	<!-- Start::Category Label -->
	<li class="slide__category">
		<span class="category-name">Master</span>
	</li>
	<!-- End::Category Label -->

	<!-- Master - Unit -->
	@can('units.view')
	<li class="slide">
		<a href="{{ route('unit.index') }}" class="side-menu__item">
			<svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
				<path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M3 7h18M3 12h18M3 17h18" />
			</svg>
			<span class="side-menu__label">Unit</span>
		</a>
	</li>
	@endcan

	<!-- Master - Project -->
	@can('projects.view')
	<li class="slide">
		<a href="{{ route('project.index') }}" class="side-menu__item">
			<svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
				<path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4" />
			</svg>
			<span class="side-menu__label">Project</span>
		</a>
	</li>
	@endcan

	<!-- Master - Department -->
	@can('departments.view')
	<li class="slide">
		<a href="{{ route('department.index') }}" class="side-menu__item">
			<svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
				<path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M3 10h18M4 6h16a1 1 0 011 1v10a1 1 0 01-1 1H4a1 1 0 01-1-1V7a1 1 0 011-1z" />
			</svg>
			<span class="side-menu__label">Department</span>
		</a>
	</li>
	@endcan

	<!-- Department Heads -->
	@can('department_heads.view')
	<li class="slide">
		<a href="{{ route('departmentHead.list') }}" class="side-menu__item">
			<svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
				<path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5.121 17.804A1 1 0 016 17h12a1 1 0 01.879.515l2 4A1 1 0 0120 23H4a1 1 0 01-.879-1.485l2-4zM15 11a3 3 0 11-6 0 3 3 0 016 0z" />
			</svg>
			<span class="side-menu__label">Department Heads</span>
		</a>
	</li>
	@endcan

	<!-- Master - Users -->
	@can('users.view')
	<li class="slide">
		<a href="{{ route('users.list') }}" class="side-menu__item">
			<svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
				<path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20h6m-3-16a4 4 0 110 8 4 4 0 010-8z" />
			</svg>
			<span class="side-menu__label">Users</span>
		</a>
	</li>
	@endcan

	<!-- Roles & Permissions -->
	@can('roles.manage')
	<li class="slide">
		<a href="{{ route('roles.index') }}" class="side-menu__item">
			<svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
				<path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
			</svg>
			<span class="side-menu__label">Roles & Access</span>
		</a>
	</li>
	@endcan

	<!-- Master - Vendors -->
	@can('vendors.view')
	<li class="slide">
		<a href="{{ route('vendors.list') }}" class="side-menu__item">
			<svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
				<path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M3 10h18M3 6h18M3 14h18M3 18h18" />
			</svg>
			<span class="side-menu__label">Vendors</span>
		</a>
	</li>
	@endcan

	<!-- Master - Items -->
	@can('items.view')
	<li class="slide">
		<a href="{{ route('items.index') }}" class="side-menu__item">
			<svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
				<path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M4 6h16M4 10h16M4 14h10M4 18h10" />
			</svg>
			<span class="side-menu__label">Items</span>
		</a>
	</li>
	@endcan
@endif

<li class="slide__category"><span class="category-name">Other</span></li>

<li class="slide">
    <a href="{{ route('notifications.index') }}" class="side-menu__item">
        <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" fill="none" viewBox="0 0 24 24"
             stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
        </svg>
        <span class="side-menu__label">Notifications</span>
    </a>
</li>
<li class="slide">
    <a href="{{ route('profile.show') }}" class="side-menu__item">
        <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" fill="none" viewBox="0 0 24 24"
             stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                  d="M5.121 17.804A1 1 0 016 17h12a1 1 0 01.879.515l2 4A1 1 0 0120 23H4a1 1 0 01-.879-1.485l2-4zM12 11a4 4 0 100-8 4 4 0 000 8z" />
        </svg>
        <span class="side-menu__label">My Profile</span>
    </a>
</li>
<li class="slide">
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="side-menu__item w-full text-left">
            <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" fill="none" viewBox="0 0 24 24"
                 stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                      d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
            <span class="side-menu__label">Logout</span>
        </button>
    </form>
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