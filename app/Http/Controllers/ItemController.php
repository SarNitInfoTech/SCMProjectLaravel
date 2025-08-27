<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Notification;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function index()
    {
        $title = 'Item List';

        $columns = [
            ['key' => 'name', 'label' => 'Item Name'],
            ['key' => 'created_at', 'label' => 'Created At'],
            ['key' => 'updated_at', 'label' => 'Updated At'],
            ['key' => 'action', 'label' => 'Action', 'type' => 'action'],
        ];

        $items = Item::select('id', 'name', 'created_at', 'updated_at')->paginate(10);

        $rows = $items->map(function ($item) {
            return [
                'name' => $item->name,
                'created_at' => $item->created_at->format('d-m-Y'),
                'updated_at' => $item->updated_at->format('d-m-Y'),
                'action' => route('items.edit', $item->id),
            ];
        });

        $searchPlaceholder = 'Search Items...';
        $redirectUrl = route('items.create');

        $customButton = <<<HTML
<a href="{$redirectUrl}" class="ti-btn ti-btn-primary-full">
    <i class="bi bi-plus-lg"></i>
    Add New Item
</a>
HTML;

        return view('pages.items.listItems.listItems', [
            'title' => $title,
            'columns' => $columns,
            'rows' => $rows,
            'searchPlaceholder' => $searchPlaceholder,
            'customButton' => $customButton,
            'pagination' => $items,
        ]);
    }

    public function create()
    {
        return view('pages.items.addItems.addItems');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $item = Item::create([
            'name' => $request->name,
            'is_active' => $request->has('is_active'),
        ]);

        Notification::create([
            'title' => "New Item Created: {$item->name}",
            'link' => route('items.index'),
            'icon' => 'la la-box', // icon matching item
            'bg_color' => 'bg-success',
            'is_read' => false,
        ]);

        return redirect()->route('items.index')->with('success', 'Item created successfully!');
    }

    public function edit($id)
    {
        $item = Item::findOrFail($id);
        return view('pages.items.editItems.editItems', compact('item'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $item = Item::findOrFail($id);
        $item->update([
            'name' => $request->name,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('items.index')->with('success', 'Item updated successfully.');
    }

    public function destroy($id)
    {
        $item = Item::findOrFail($id);
        $item->delete();

        return redirect()->route('items.index')->with('success', 'Item deleted successfully.');
    }
}
