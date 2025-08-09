<?php 
namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\Notification;
use Illuminate\Http\Request;

class UnitController extends Controller
{


public function index()
{
    $title = 'Unit List';

    $columns = [
        ['key' => 'name', 'label' => 'Unit Name'],
        ['key' => 'created_at', 'label' => 'Created At'],
        ['key' => 'updated_at', 'label' => 'Updated At'],
        ['key' => 'action', 'label' => 'Action', 'type' => 'action'],
    ];

    // Use paginate instead of get
    $Units = Unit::select('id', 'name', 'created_at', 'updated_at')->paginate(10);

    $rows = $Units->map(function ($dept) {
        return [
            'name' => $dept->name,
            'created_at' => $dept->created_at->format('d-m-Y'),
            'updated_at' => $dept->updated_at->format('d-m-Y'),
            'action' => route('units.edit', $dept->id),
        ];
    });

    $searchPlaceholder = 'Search Units...';
    $redirectUrl = route('units.create');

    $customButton = <<<HTML
<a href="{$redirectUrl}" class="ti-btn ti-btn-primary-full">
    <i class="bi bi-plus-lg"></i>
    Add New Unit
</a>
HTML;

    return view('pages.units.listUnits.listUnits', [
        'title' => $title,
        'columns' => $columns,
        'rows' => $rows,
        'searchPlaceholder' => $searchPlaceholder,
        'customButton' => $customButton,
        'pagination' => $Units, // Add this line
    ]);
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

