<?php 
namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\Notification;
use Illuminate\Http\Request;

class UnitController extends Controller
{


    public function index(Request $request)
    {
        $title = 'Unit List';
        $query = Unit::query();
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        $units = $query->orderBy('name')->paginate(10);

        return view('pages.units.listUnits.listUnits', compact('title', 'units'));
    }

public function create()
{
    return view('pages.units.addUnits.addUnits');
}

public function store(Request $request)
{
    // ✅ Validate input
    $request->validate([
        'name' => 'required|string|max:255',
    ]);

    // ✅ Create the Unit
    $unit = Unit::create([
        'name' => $request->name,
    ]);

    // ✅ Create Notification
    Notification::create([
        'title'    => "New Unit Created: {$unit->name}",
        'link'     => route('units.index'),
        'icon'     => 'la la-balance-scale', // Optional: use icon related to units
        'bg_color' => 'bg-primary',
        'is_read'  => false,
    ]);

    // ✅ Redirect back with success message
    return redirect()->route('units.index')->with('success', 'Unit created successfully!');
}

public function edit($id)
{
    $Unit = Unit::findOrFail($id);
    return view('pages.units.editUnits.editUnits', compact('Unit'));
}

public function update(Request $request, $id)
{
    $request->validate([
        'name' => 'required|string|max:255',
    ]);

    $Unit = Unit::findOrFail($id);
    $Unit->update([
        'name' => $request->name,
    ]);

    return redirect()->route('units.index')->with('success', 'Unit updated successfully.');
}


}

