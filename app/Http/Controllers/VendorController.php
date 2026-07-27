<?php

namespace App\Http\Controllers;

use App\Helpers\SearchHelper;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class VendorController extends Controller
{
    public function index(Request $request)
    {
        $title = 'Vendor List';
        $query = Vendor::query();
        if ($request->filled('search')) {
            SearchHelper::applyFuzzySearch($query, $request->search, ['name', 'email', 'phone', 'address']);
        }
        $vendors = $query->orderBy('name')->paginate(10);

        return view('pages.vendors.listVendors.listVendors', compact('title', 'vendors'));
    }

    public function create()
    {
        return view('pages.vendors.addVendors.addVendors');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:vendors,name',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'gst_number' => 'nullable|string|max:20',
            'pan_number' => 'nullable|string|max:20',
            'account_name' => 'nullable|string|max:255',
            'account_number' => 'nullable|string|max:50',
            'bank_name' => 'nullable|string|max:255',
            'branch_name' => 'nullable|string|max:255',
            'ifsc_code' => 'nullable|string|max:20',
        ]);

        $validated['name'] = trim($validated['name']);
        $validated['is_active'] = $request->has('is_active');

        Vendor::create($validated);

        return redirect()->route('vendors.list')->with('success', 'Vendor created successfully.');
    }

    public function edit(Vendor $vendor)
    {
        return view('pages.vendors.editVendors.editVendors', compact('vendor'));
    }

    public function update(Request $request, Vendor $vendor)
{
    $validated = $request->validate([
        'name' => ['required', 'string', 'max:255', Rule::unique('vendors', 'name')->ignore($vendor->id)],
        'email' => 'nullable|email',
        'phone' => 'nullable|string|max:20',
        'address' => 'nullable|string',
        'gst_number' => 'nullable|string|max:20',
        'pan_number' => 'nullable|string|max:20',
        'account_name' => 'nullable|string|max:255',
        'account_number' => 'nullable|string|max:50',
        'bank_name' => 'nullable|string|max:255',
        'branch_name' => 'nullable|string|max:255',
        'ifsc_code' => 'nullable|string|max:20',
    ]);

    $validated['name'] = trim($validated['name']);
    // Handle toggle (checkbox) manually
    $validated['is_active'] = $request->has('is_active');

    // Update the vendor
    $vendor->update($validated);

    return redirect()->route('vendors.list')->with('success', 'Vendor updated successfully.');
}


    public function destroy(Vendor $vendor)
    {
        $vendor->delete();

        return redirect()->route('vendors.list')->with('success', 'Vendor deleted successfully.');
    }

    public function show(Vendor $vendor)
    {
        return redirect()->route('vendors.list');
    }
}
