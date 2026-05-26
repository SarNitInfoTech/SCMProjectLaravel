<?php

namespace App\Http\Controllers;

use App\Models\InventoryStock;
use App\Models\InventoryMovement;
use App\Models\Item;
use App\Models\Department;
use App\Models\Unit;
use App\Models\Vendor;
use App\Models\PORegister;
use App\Enums\MovementType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class InventoryController extends Controller
{
    /**
     * Display inventory dashboard with summary cards & stock lists.
     */
    public function dashboard(Request $request)
    {
        $title = 'Inventory Dashboard';

        // Statistics
        $totalItems = InventoryStock::where('is_active', true)->count();
        $outOfStock = InventoryStock::where('is_active', true)->where('current_qty', '<=', 0)->count();
        $lowStock = InventoryStock::lowStock()->count();
        $recentMovements = InventoryMovement::whereMonth('movement_date', now()->month)
            ->whereYear('movement_date', now()->year)
            ->count();

        $stats = [
            'total_items' => $totalItems,
            'out_of_stock' => $outOfStock,
            'low_stock' => $lowStock,
            'recent_movements' => $recentMovements,
        ];

        // Stocks list
        $query = InventoryStock::with(['item', 'department', 'unit']);

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('item', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        $stocks = $query->paginate(15);
        $departments = Department::all();

        return view('pages.inventory.dashboard.dashboard', compact('title', 'stats', 'stocks', 'departments'));
    }

    /**
     * List all inventory stocks.
     */
    public function stockList(Request $request)
    {
        $title = 'Inventory Stocks';

        $query = InventoryStock::with(['item', 'department', 'unit']);

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('item', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        $stocks = $query->paginate(15);
        $departments = Department::all();

        return view('pages.inventory.stocks.listStocks.listStocks', compact('title', 'stocks', 'departments'));
    }

    /**
     * Form to create a new stock item.
     */
    public function createStock()
    {
        $title = 'Add New Stock';
        $items = Item::where('is_active', true)->orderBy('name')->get();
        $departments = Department::orderBy('name')->get();
        $units = Unit::orderBy('name')->get();

        return view('pages.inventory.stocks.addStock.addStock', compact('title', 'items', 'departments', 'units'));
    }

    /**
     * Save a new stock item.
     */
    public function storeStock(Request $request)
    {
        $request->validate([
            'item_id' => 'required|exists:items,id',
            'department_id' => 'required|exists:departments,id',
            'unit_id' => 'required|exists:units,id',
            'current_qty' => 'required|numeric|min:0',
            'min_qty' => 'nullable|numeric|min:0',
            'max_qty' => 'nullable|numeric|min:0',
            'location' => 'nullable|string|max:255',
        ]);

        // Check if stock entry already exists for item + department + unit
        $exists = InventoryStock::where('item_id', $request->item_id)
            ->where('department_id', $request->department_id)
            ->where('unit_id', $request->unit_id)
            ->exists();

        if ($exists) {
            return redirect()->back()
                ->withInput()
                ->with('warning', 'Stock entry for this Item, Department, and Unit already exists. Add movement instead.');
        }

        DB::transaction(function () use ($request) {
            $stock = InventoryStock::create([
                'item_id' => $request->item_id,
                'department_id' => $request->department_id,
                'unit_id' => $request->unit_id,
                'current_qty' => 0, // initially zero, updated by the movement
                'min_qty' => $request->min_qty ?? 0,
                'max_qty' => $request->max_qty,
                'location' => $request->location,
                'is_active' => true,
            ]);

            // Create initial IN movement
            if ($request->current_qty > 0) {
                InventoryMovement::create([
                    'stock_id' => $stock->id,
                    'type' => 'IN',
                    'quantity' => $request->current_qty,
                    'movement_date' => now()->toDateString(),
                    'remarks' => 'Initial stock setup.',
                ]);
            }
        });

        return redirect()->route('inventory.stocks.list')->with('success', 'Stock entry created successfully.');
    }

    /**
     * Form to edit stock properties.
     */
    public function editStock($id)
    {
        $title = 'Edit Stock Entry';
        $stock = InventoryStock::with(['item', 'department', 'unit'])->findOrFail($id);

        return view('pages.inventory.stocks.editStock.editStock', compact('title', 'stock'));
    }

    /**
     * Update stock properties.
     */
    public function updateStock(Request $request, $id)
    {
        $stock = InventoryStock::findOrFail($id);

        $request->validate([
            'min_qty' => 'required|numeric|min:0',
            'max_qty' => 'nullable|numeric|min:0',
            'location' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        $stock->update([
            'min_qty' => $request->min_qty,
            'max_qty' => $request->max_qty,
            'location' => $request->location,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('inventory.stocks.list')->with('success', 'Stock properties updated successfully.');
    }

    /**
     * List movements for a specific stock item.
     */
    public function movements(Request $request, $stockId)
    {
        $stock = InventoryStock::with(['item', 'department', 'unit'])->findOrFail($stockId);
        $title = "Movements: " . ($stock->item?->name ?? 'Stock Item');

        $movements = InventoryMovement::with(['vendor', 'poRegister', 'user'])
            ->where('stock_id', $stockId)
            ->orderByDesc('movement_date')
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('pages.inventory.movements.listMovements.listMovements', compact('title', 'stock', 'movements'));
    }

    /**
     * Form to log a movement.
     */
    public function createMovement($stockId)
    {
        $stock = InventoryStock::with(['item', 'department', 'unit'])->findOrFail($stockId);
        $title = "Record Movement for: " . ($stock->item?->name ?? 'Stock');
        $types = MovementType::cases();
        $vendors = Vendor::where('is_active', true)->orderBy('name')->get();
        $pos = PORegister::orderByDesc('po_date')->get();
        $departments = Department::where('id', '!=', $stock->department_id)->orderBy('name')->get();

        return view('pages.inventory.movements.addMovement.addMovement', compact('title', 'stock', 'types', 'vendors', 'pos', 'departments'));
    }

    /**
     * Store movement.
     */
    public function storeMovement(Request $request, $stockId)
    {
        $stock = InventoryStock::findOrFail($stockId);

        $request->validate([
            'type' => 'required|in:' . implode(',', MovementType::values()),
            'quantity' => 'required|numeric|min:0.0001',
            'movement_date' => 'required|date',
            'remarks' => 'nullable|string',
            'reference_no' => 'nullable|string|max:100',
            'vendor_id' => 'nullable|exists:vendors,id',
            'po_register_id' => 'nullable|exists:po_registers,id',
            'destination_department_id' => 'required_if:type,TRANSFER|nullable|exists:departments,id',
        ]);

        // If transfer, ensure destination is different
        if ($request->type === 'TRANSFER' && $request->destination_department_id == $stock->department_id) {
            return redirect()->back()->withInput()->with('warning', 'Destination department must be different.');
        }

        // Validate stock quantity for OUT / TRANSFER / ADJUST (if reduction)
        $isReducing = in_array($request->type, ['OUT', 'TRANSFER']);
        if ($isReducing && $stock->current_qty < $request->quantity) {
            return redirect()->back()->withInput()
                ->with('warning', sprintf('Insufficient stock. Available: %s, Requested: %s', number_format($stock->current_qty, 2), number_format($request->quantity, 2)));
        }

        DB::transaction(function () use ($request, $stock) {
            if ($request->type === 'TRANSFER') {
                $destDept = Department::findOrFail($request->destination_department_id);

                // 1) Log OUT movement on current stock
                InventoryMovement::create([
                    'stock_id' => $stock->id,
                    'type' => 'TRANSFER',
                    'quantity' => $request->quantity,
                    'movement_date' => $request->movement_date,
                    'remarks' => sprintf('Transfer to department: %s. %s', $destDept->name, $request->remarks),
                    'reference_no' => $request->reference_no,
                ]);

                // 2) Find or create destination stock
                $destStock = InventoryStock::firstOrCreate([
                    'item_id' => $stock->item_id,
                    'department_id' => $request->destination_department_id,
                    'unit_id' => $stock->unit_id,
                ], [
                    'current_qty' => 0,
                    'min_qty' => 0,
                    'is_active' => true,
                ]);

                // 3) Log IN movement on destination stock
                InventoryMovement::create([
                    'stock_id' => $destStock->id,
                    'type' => 'IN',
                    'quantity' => $request->quantity,
                    'movement_date' => $request->movement_date,
                    'remarks' => sprintf('Transfer from department: %s. %s', $stock->department->name, $request->remarks),
                    'reference_no' => $request->reference_no,
                ]);
            } else {
                // Normal transaction (IN, OUT, ADJUST, RETURN)
                $qty = $request->quantity;
                
                // For ADJUST, we can support negative adjust via a checkbox or negative sign,
                // let's support a flag to decrease instead.
                if ($request->type === 'ADJUST' && $request->has('adjust_decrease')) {
                    $qty = -$qty;
                    if ($stock->current_qty + $qty < 0) {
                        throw new \Exception('Adjustment would result in negative stock.');
                    }
                }

                InventoryMovement::create([
                    'stock_id' => $stock->id,
                    'type' => $request->type,
                    'quantity' => $qty,
                    'movement_date' => $request->movement_date,
                    'remarks' => $request->remarks,
                    'reference_no' => $request->reference_no,
                    'vendor_id' => $request->vendor_id,
                    'po_register_id' => $request->po_register_id,
                ]);
            }
        });

        return redirect()->route('inventory.stocks.movements', $stock->id)->with('success', 'Movement logged successfully.');
    }

    /**
     * Report for items below min_qty.
     */
    public function lowStockReport()
    {
        $title = 'Low Stock Alert Report';
        $stocks = InventoryStock::lowStock()->with(['item', 'department', 'unit'])->get();

        return view('pages.inventory.reports.lowStock.lowStock', compact('title', 'stocks'));
    }

    /**
     * Report for all movements.
     */
    public function movementReport(Request $request)
    {
        $title = 'All Movements Ledger';
        $departments = Department::all();
        $types = MovementType::values();

        $query = InventoryMovement::with(['stock.item', 'stock.department', 'stock.unit', 'vendor', 'poRegister', 'user']);

        if ($request->filled('department_id')) {
            $query->whereHas('stock', function ($q) use ($request) {
                $q->where('department_id', $request->department_id);
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('movement_date', [$request->start_date, $request->end_date]);
        }

        $movements = $query->orderByDesc('movement_date')->orderByDesc('created_at')->paginate(20);

        return view('pages.inventory.reports.movementReport.movementReport', compact('title', 'movements', 'departments', 'types'));
    }
}
