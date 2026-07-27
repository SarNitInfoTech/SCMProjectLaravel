<?php

namespace App\Http\Controllers;

use App\Helpers\SearchHelper;
use App\Models\Item;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $title = 'Item List';
        $query = Item::query();
        if ($request->filled('search')) {
            SearchHelper::applyFuzzySearch($query, $request->search, ['name']);
        }
        $items = $query->orderBy('name')->paginate(10);

        return view('pages.items.listItems.listItems', compact('title', 'items'));
    }

    public function create()
    {
        return view('pages.items.addItems.addItems');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:items,name',
        ]);

        $item = Item::create([
            'name' => trim($request->name),
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
        $item = Item::findOrFail($id);

        $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('items', 'name')->ignore($item->id)],
        ]);

        $item->update([
            'name' => trim($request->name),
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
