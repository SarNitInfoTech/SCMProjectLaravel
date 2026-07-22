<?php 
namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

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

        return view('pages.Units.listUnits.listUnits', compact('title', 'units'));
    }

public function create()
{
    return view('pages.Units.addUnits.addUnits');
}

public function store(Request $request)
{
    // ✅ Validate input
    $request->validate([
        'name' => 'required|string|max:255|unique:units,name',
    ]);

    // ✅ Create the Unit
    $unit = Unit::create([
        'name' => trim($request->name),
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
    return view('pages.Units.editUnits.editUnits', compact('Unit'));
}

public function update(Request $request, $id)
{
    $Unit = Unit::findOrFail($id);

    $request->validate([
        'name' => ['required', 'string', 'max:255', Rule::unique('units', 'name')->ignore($Unit->id)],
    ]);

    $Unit->update([
        'name' => trim($request->name),
    ]);

    return redirect()->route('units.index')->with('success', 'Unit updated successfully.');
}

public function destroy($id)
{
    $unit = Unit::findOrFail($id);
    $unit->delete();

    return redirect()->route('units.index')->with('success', 'Unit deleted successfully.');
}


}
