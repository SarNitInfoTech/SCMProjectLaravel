@extends("layouts.layout")

@section("bodyContent")
<div style="width: 100%; max-width: 100%; box-sizing: border-box; font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">

    @include("containers.indent.generateIndent.generateIndent")

    <!-- Draft / Recent Indent List Card (Full Width) -->
    <div style="background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.03); overflow: hidden; margin-bottom: 24px;">
        
        <!-- Header Toolbar -->
        <div style="padding: 20px; border-bottom: 1px solid #F1F5F9; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; background: #FAFAFA;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <div style="width: 34px; height: 34px; border-radius: 10px; background: #EFF6FF; color: #2563EB; display: flex; align-items: center; justify-content: center; border: 1px solid #BFDBFE;">
                    <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                </div>
                <div>
                    <h2 style="font-size: 18px; font-weight: 800; color: #0F172A; margin: 0;">Recent Draft Indent Tickets</h2>
                </div>
            </div>

            <!-- Search & Right Filter Drawer Controls -->
            <form method="GET" action="{{ route('indent.create') }}" style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                @if(request('department')) <input type="hidden" name="department" value="{{ request('department') }}"> @endif

                <!-- Search Input -->
                <div style="position: relative; width: 340px;">
                    <span style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #94A3B8;">
                        <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search ID, department..." 
                           style="width: 100%; padding: 9px 12px 9px 38px; background: #FFFFFF; border: 1px solid #CBD5E1; border-radius: 10px; font-size: 13px; outline: none; box-sizing: border-box;">
                </div>

                <!-- Right Drawer Filter Button -->
                <button type="button" onclick="openRightFilterDrawer()" 
                        style="background: #FFFFFF; border: 1px solid #DBEAFE; color: #2563EB; font-weight: 700; padding: 9px 16px; border-radius: 10px; font-size: 13px; cursor: pointer; display: inline-flex; align-items: center; gap: 6px;">
                    <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                    <span>Filter</span>
                    @if(request()->anyFilled(['department']))
                        <span style="width: 8px; height: 8px; border-radius: 50%; background: #2563EB; display: inline-block;"></span>
                    @endif
                </button>

                <!-- Search Button -->
                <button type="submit" style="background: #0F172A; color: #FFFFFF; font-weight: 700; padding: 9px 20px; border-radius: 10px; font-size: 13px; border: none; cursor: pointer;">
                    Search
                </button>

                @if(request()->anyFilled(['search', 'department']))
                    <a href="{{ route('indent.create') }}" style="padding: 9px 14px; font-size: 13px; font-weight: 600; background: #F1F5F9; color: #475569; border-radius: 10px; text-decoration: none;">Reset</a>
                @endif
            </form>
        </div>

        <!-- Table Responsive Container (Full Width) -->
        <div style="width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch;">
            <table style="width: 100%; min-width: 800px; border-collapse: collapse; text-align: left; font-size: 13px; table-layout: fixed;">
                <thead>
                    <tr style="background: #F8FAFC; border-bottom: 1px solid #E2E8F0; color: #64748B; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">
                        <th style="padding: 14px 20px; width: 180px; vertical-align: middle;">INDENT TICKET ID <span style="color:#CBD5E1;">↕</span></th>
                        <th style="padding: 14px 20px; vertical-align: middle;">DEPARTMENT NAME <span style="color:#CBD5E1;">↕</span></th>
                        <th style="padding: 14px 20px; width: 260px; text-align: center; vertical-align: middle;">ACTION</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rows as $idx => $row)
                        @php
                            $colors = [
                                ['bg' => '#EFF6FF', 'text' => '#2563EB', 'border' => '#BFDBFE'],
                                ['bg' => '#ECFDF5', 'text' => '#059669', 'border' => '#A7F3D0'],
                                ['bg' => '#F3E8FF', 'text' => '#7C3AED', 'border' => '#E9D5FF'],
                                ['bg' => '#FFF7ED', 'text' => '#EA580C', 'border' => '#FED7AA'],
                            ];
                            $theme = $colors[$idx % count($colors)];
                        @endphp
                        <tr style="border-bottom: 1px solid #F1F5F9; transition: background 0.15s ease;" onmouseover="this.style.background='#F8FAFC'" onmouseout="this.style.background='#FFFFFF'">
                            <td style="padding: 16px 20px; vertical-align: middle; white-space: nowrap;">
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <div style="width: 32px; height: 32px; border-radius: 8px; background: {{ $theme['bg'] }}; color: {{ $theme['text'] }}; border: 1px solid {{ $theme['border'] }}; display: flex; align-items: center; justify-content: center;">
                                        <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    </div>
                                    <span style="font-weight: 800; color: #0F172A; font-size: 14px;">{{ $row['indent_id'] }}</span>
                                </div>
                            </td>
                            <td style="padding: 16px 20px; vertical-align: middle; white-space: nowrap;">
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <div style="width: 28px; height: 28px; border-radius: 6px; background: {{ $theme['bg'] }}; color: {{ $theme['text'] }}; display: flex; align-items: center; justify-content: center;">
                                        <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m3 0h1m-1-4h.01M9 16h.01M9 12h.01M9 8h.01M15 16h.01M15 12h.01M15 8h.01"/></svg>
                                    </div>
                                    <span style="font-weight: 700; color: #1E293B;">{{ $row['department_name'] }}</span>
                                </div>
                            </td>
                            <td style="padding: 16px 20px; vertical-align: middle; text-align: center; white-space: nowrap;">
                                <div style="display: inline-flex; align-items: center; gap: 8px;">
                                    <a href="{{ $row['action'] }}" 
                                       style="background: #ECFDF5; border: 1px solid #A7F3D0; color: #047857; font-weight: 700; font-size: 12px; padding: 7px 16px; border-radius: 8px; text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
                                        <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        <span>Generate Form</span>
                                    </a>

                                    @if(!empty($row['delete_action']))
                                        <form action="{{ $row['delete_action'] }}" method="POST" onsubmit="return confirm('Are you sure you want to delete and release allocated Indent ID #{{ $row['indent_id'] }}?');" style="display: inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    style="background: #FEF2F2; border: 1px solid #FECDD3; color: #DC2626; font-weight: 700; font-size: 12px; padding: 7px 14px; border-radius: 8px; cursor: pointer; display: inline-flex; align-items: center; gap: 6px;"
                                                    title="Delete allocated Indent ID #{{ $row['indent_id'] }}">
                                                <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                <span>Delete</span>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" style="text-align: center; padding: 32px; color: #64748B; font-weight: 600;">No draft tickets found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Bottom Pagination Bar -->
        @if(isset($paginated))
        <div style="padding: 16px 20px; border-top: 1px solid #F1F5F9; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; font-size: 13px; color: #64748B; font-weight: 500;">
            <div>
                Showing {{ $paginated->firstItem() ?? 0 }} to {{ $paginated->lastItem() ?? 0 }} of {{ $paginated->total() }} entries
            </div>

            <!-- Page Number Controls -->
            <div>
                {{ $paginated->appends(request()->query())->links('pagination::tailwind') }}
            </div>

            <!-- Per Page Selector -->
            <form method="GET" action="{{ route('indent.create') }}" style="display: flex; align-items: center; gap: 8px;">
                @foreach(request()->except(['per_page', 'page']) as $k => $v)
                    <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                @endforeach
                <select name="per_page" onchange="this.form.submit()" style="background: #F8FAFC; border: 1px solid #CBD5E1; border-radius: 8px; padding: 4px 8px; font-size: 12px; font-weight: 700; outline: none;">
                    <option value="10" {{ request('per_page', 15) == 10 ? 'selected' : '' }}>10</option>
                    <option value="15" {{ request('per_page', 15) == 15 ? 'selected' : '' }}>15</option>
                    <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                    <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                    <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                </select>
                <span>per page</span>
            </form>
        </div>
        @endif
    </div>
</div>

<!-- Right Slide-over Filter Drawer for Recent Draft Tickets -->
<div id="rightFilterDrawer" style="display: none; position: fixed; inset: 0; z-index: 99999;">
    <!-- Backdrop Overlay -->
    <div onclick="closeRightFilterDrawer()" style="position: fixed; inset: 0; background: rgba(15,23,42,0.4); backdrop-filter: blur(2px);"></div>

    <!-- Drawer Panel sliding from Right -->
    <div style="position: fixed; top: 0; right: 0; bottom: 0; width: 380px; max-width: 90vw; background: #FFFFFF; box-shadow: -10px 0 25px rgba(0,0,0,0.15); display: flex; flex-direction: column; justify-content: space-between; z-index: 100000; animation: slideInRight 0.3s ease-out;">
        
        <!-- Header -->
        <div style="padding: 20px; border-bottom: 1px solid #E2E8F0; display: flex; align-items: center; justify-content: space-between; background: #F8FAFC;">
            <div style="display: flex; align-items: center; gap: 8px; font-weight: 800; font-size: 16px; color: #0F172A;">
                <svg style="width: 20px; height: 20px; color: #2563EB;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                <span>Filter Recent Tickets</span>
            </div>
            <button type="button" onclick="closeRightFilterDrawer()" style="background: transparent; border: none; font-size: 24px; font-weight: 700; cursor: pointer; color: #94A3B8;">&times;</button>
        </div>

        <!-- Body Form -->
        <form id="drawerFilterForm" method="GET" action="{{ route('indent.create') }}" style="padding: 20px; overflow-y: auto; flex: 1;">
            @if(request('search')) <input type="hidden" name="search" value="{{ request('search') }}"> @endif

            <!-- Department Filter -->
            <div style="margin-bottom: 18px;">
                <label style="display: block; font-size: 11px; font-weight: 700; color: #475569; text-transform: uppercase; margin-bottom: 6px;">Department</label>
                <select name="department" style="width: 100%; padding: 10px 12px; border: 1px solid #CBD5E1; border-radius: 10px; font-size: 13px; outline: none; background: #F8FAFC;">
                    <option value="">All Departments</option>
                    @if(!empty($departments))
                        @foreach($departments as $dept)
                            <option value="{{ $dept->name }}" {{ request('department') == $dept->name ? 'selected' : '' }}>{{ $dept->name }}</option>
                        @endforeach
                    @endif
                </select>
            </div>
        </form>

        <!-- Footer -->
        <div style="padding: 16px 20px; border-top: 1px solid #E2E8F0; background: #F8FAFC; display: flex; justify-content: flex-end; gap: 10px;">
            <a href="{{ route('indent.create') }}" style="padding: 10px 18px; border-radius: 10px; font-size: 13px; font-weight: 600; background: #F1F5F9; color: #475569; text-decoration: none;">Reset</a>
            <button type="button" onclick="document.getElementById('drawerFilterForm').submit()" style="padding: 10px 22px; border-radius: 10px; font-size: 13px; font-weight: 700; background: #2563EB; color: #FFFFFF; border: none; cursor: pointer;">Apply Filters</button>
        </div>
    </div>
</div>

<style>
@keyframes slideInRight {
    from { transform: translateX(100%); }
    to { transform: translateX(0); }
}
</style>

<script>
  function openRightFilterDrawer() {
      document.getElementById('rightFilterDrawer').style.display = 'block';
  }

  function closeRightFilterDrawer() {
      document.getElementById('rightFilterDrawer').style.display = 'none';
  }
</script>
@endsection