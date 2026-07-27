@extends("layouts.layout")

@section("bodyContent")
<div style="width: 100%; max-width: 100%; box-sizing: border-box; font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">

    @include("containers.indent.generateIndent.generateIndent")

    <!-- Draft List Card (Full Width) -->
    <div style="background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.03); overflow: hidden; margin-bottom: 24px;">
        
        <!-- Header -->
        <div style="padding: 20px; border-bottom: 1px solid #F1F5F9; display: flex; align-items: center; gap: 14px; background: #FAFAFA;">
            <div style="width: 44px; height: 44px; border-radius: 12px; background: #2563EB; color: #FFFFFF; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 12px rgba(37,99,235,0.25);">
                <svg style="width: 22px; height: 22px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
            </div>
            <div>
                <h2 style="font-size: 20px; font-weight: 800; color: #0F172A; margin: 0 0 2px 0; letter-spacing: -0.3px;">Draft List</h2>
                <p style="font-size: 13px; color: #64748B; margin: 0; font-weight: 500;">View and generate indent forms for draft tickets.</p>
            </div>
        </div>

        <!-- Table Responsive Container -->
        <div style="width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch;">
            <table style="width: 100%; min-width: 800px; border-collapse: collapse; text-align: left; font-size: 13px; table-layout: fixed;">
                <thead>
                    <tr style="background: #F8FAFC; border-bottom: 1px solid #E2E8F0; color: #64748B; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">
                        <th style="padding: 14px 20px; width: 160px; vertical-align: middle;">INDENT ID <span style="color:#CBD5E1;">↕</span></th>
                        <th style="padding: 14px 20px; vertical-align: middle;">DEPARTMENT NAME <span style="color:#CBD5E1;">↕</span></th>
                        <th style="padding: 14px 20px; width: 220px; text-align: center; vertical-align: middle;">ACTION</th>
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
                                <a href="{{ $row['action'] }}" 
                                   style="background: #ECFDF5; border: 1px solid #A7F3D0; color: #047857; font-weight: 700; font-size: 12px; padding: 7px 16px; border-radius: 8px; text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
                                    <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    <span>Generate Form</span>
                                </a>
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
        <div style="padding: 16px 20px; border-top: 1px solid #F1F5F9; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; font-size: 13px; color: #64748B; font-weight: 500;">
            <div>
                Showing 1 to {{ count($rows) }} of {{ count($rows) }} entries
            </div>

            <!-- Page Number Control -->
            <div style="display: flex; align-items: center; gap: 4px;">
                <button type="button" style="width: 32px; height: 32px; border-radius: 8px; border: 1px solid #E2E8F0; background: #FFFFFF; color: #94A3B8; display: flex; align-items: center; justify-content: center;">&lt;</button>
                <button type="button" style="width: 32px; height: 32px; border-radius: 8px; background: #2563EB; color: #FFFFFF; font-weight: 700; display: flex; align-items: center; justify-content: center; border: none;">1</button>
                <button type="button" style="width: 32px; height: 32px; border-radius: 8px; border: 1px solid #E2E8F0; background: #FFFFFF; color: #94A3B8; display: flex; align-items: center; justify-content: center;">&gt;</button>
            </div>
        </div>
    </div>
</div>
@endsection