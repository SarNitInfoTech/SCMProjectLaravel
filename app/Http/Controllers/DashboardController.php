<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Project;
use App\Models\Unit;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DashboardController extends Controller
{
    public function index()
    {
        $title = 'Dashboard Overview';
        $start = Carbon::today()->toDateString();
        $end = Carbon::today()->toDateString();

        $base = DB::table('indent_registers')
            ->whereBetween('indent_date', [$start, $end]);

        // Always provide these keys
        $stats = [
            'indent_generated' => (clone $base)->count(),
            'indent_pending' => (clone $base)
                ->whereRaw('LOWER(TRIM(status)) = ?', ['pending'])
                ->count(),
            'indent_closed' => (clone $base)
                ->whereIn(DB::raw('LOWER(TRIM(status))'), ['close', 'closed'])
                ->count(),
            'indent_cancelled' => (clone $base)
                ->whereIn(DB::raw('LOWER(TRIM(status))'), ['cancel', 'cancelled', 'canceled'])
                ->count(),
        ];

        // Indent Recent List
        $title = 'Recent Indent List';

        $columns = [
            ['key' => 'indent_id', 'label' => 'Indent ID'],
            ['key' => 'department_name', 'label' => 'Department'],
            ['key' => 'project', 'label' => 'Project'],
            ['key' => 'item_description', 'label' => 'Description'],  // Will hold comma-separated items
            ['key' => 'status', 'label' => 'Status', 'type' => 'status'],
            ['key' => 'date', 'label' => 'Created Date'],
            ['key' => 'action', 'label' => 'Action', 'type' => 'action'],
        ];

        // Join with departments for department name
        $registers = DB::table('indent_registers')
            ->join('departments', 'departments.name', '=', 'indent_registers.indent_department')
            ->select(
                'indent_registers.id',
                'indent_registers.indent_id',
                'departments.name as department_name',
                'indent_registers.items_description',
                'indent_registers.indent_project as project',
                'indent_registers.indent_date as date',
                'indent_registers.indent_department as department_id',
                'indent_registers.status',
                'indent_registers.created_at',
                'indent_registers.updated_at'
            )
            ->orderByDesc('indent_registers.created_at')
            ->paginate(5);

        // Format rows
        $rows = $registers->map(function ($reg) {
            $items = json_decode($reg->items_description, true) ?? [];
            $itemDescriptions = collect($items)->pluck('description')->filter()->implode(', ');  // ✅

            return [
                'indent_id' => $reg->indent_id,
                'department_name' => $reg->department_name,
                'department_id' => $reg->department_id,
                'project' => $reg->project,
                'date' => $reg->date,
                'item_description' => $itemDescriptions ?: '-',
                'status' => ucfirst($reg->status ?? 'Pending'),
                'action' => (function () use ($reg) {
                    $status = strtolower($reg->status ?? 'pending');
                    $actions = [];

                    $baseParams = [
                        'indent_id' => $reg->indent_id,
                        'department_id' => $reg->department_id,
                    ];

                    if ($status === 'pending') {
                        // usual pending actions
                        $actions['edit'] = route('indent-register.edit', $reg->id);
                        $actions['file_po'] = route('po-register.create', $baseParams);

                        // show Cancel and Close
                        $actions['cancel'] = [
                            'route' => route('po-register.statusCancel'),
                            'params' => $baseParams,
                        ];
                        $actions['close'] = [
                            'route' => route('po-register.statusClose'),
                            'params' => $baseParams,
                        ];
                    } elseif ($status === 'close') {
                        // when closed, only allow reverting to Pending
                        $actions['pending'] = [
                            'route' => route('po-register.statusPending'),
                            'params' => $baseParams,
                        ];
                        $actions['cancel'] = [
                            'route' => route('po-register.statusCancel'),
                            'params' => $baseParams,
                        ];
                    } elseif ($status === 'cancel') {
                        // when closed, only allow reverting to Pending
                        $actions['close'] = [
                            'route' => route('po-register.statusClose'),
                            'params' => $baseParams,
                        ];
                        $actions['pending'] = [
                            'route' => route('po-register.statusPending'),
                            'params' => $baseParams,
                        ];
                    } else {
                        // status is "cancel" (or anything else) → no actions
                        // If you want to allow reopen from cancel, uncomment below:

                        /*
                         * $actions['pending'] = [
                         *     'route'  => route('po-register.statusPending'),
                         *     'params' => $baseParams,
                         * ];
                         */
                    }

                    return $actions;
                })(),
            ];
        });

        $searchPlaceholder = 'Search indent records...';
        $redirectUrl = route('indent.create');

        $customButton = <<<HTML
                <a href="{$redirectUrl}" class="ti-btn ti-btn-primary-full">
                    <i class="bi bi-plus-lg"></i>
                    Add New Indent
                </a>
            HTML;
        // Indent Recent List

        return view('pages.dashboard.dashboard', [
        'title' => $title,
        'columns' => $columns,
        'rows' => $rows,
        'searchPlaceholder' => $searchPlaceholder,
        'customButton' => $customButton,
        'pagination' => $registers,
        ], compact('title', 'stats'));
    }

    public function filter(Request $req)
    {
        // Validate dates: YYYY-MM-DD
        $req->validate([
            'start' => ['required', 'date'],
            'end' => ['required', 'date', 'after_or_equal:start'],
        ]);

        // Parse & normalize to dates (no time since indent_date is DATE)
        $start = Carbon::parse($req->input('start'))->startOfDay();
        $end = Carbon::parse($req->input('end'))->endOfDay();

        // Optional guard: prevent absurdly large ranges
        if ($end->diffInDays($start) > 366) {
            throw ValidationException::withMessages([
                'end' => 'Date range is too large (max 366 days).',
            ]);
        }

        // Base query on indent_date (DATE)
        $base = DB::table('indent_registers')
            ->whereBetween('indent_date', [$start->toDateString(), $end->toDateString()]);

        // Total generated in range = all rows in range
        $indent_generated = (clone $base)->count();

        // Count by status (case-insensitive)
        $statusCounts = (clone $base)
            ->selectRaw('LOWER(TRIM(status)) as s, COUNT(*) as c')
            ->groupBy('s')
            ->pluck('c', 's');  // ['pending' => 10, 'close' => 5, 'cancel' => 1, ...]

        // Map to your four cards (handle different spellings/cases)
        $indent_pending = (int) ($statusCounts['pending'] ?? 0);
        $indent_closed = (int) (
            ($statusCounts['close'] ?? 0)
            + ($statusCounts['closed'] ?? 0)  // if some rows store "Closed"
        );
        $indent_cancelled = (int) (
            ($statusCounts['cancel'] ?? 0)
            + ($statusCounts['cancelled'] ?? 0)
            + ($statusCounts['canceled'] ?? 0)  // safeguard spelling variants
        );

        return response()->json([
            'indent_generated' => $indent_generated,
            'indent_pending' => $indent_pending,
            'indent_closed' => $indent_closed,
            'indent_cancelled' => $indent_cancelled,
        ]);
    }
}
