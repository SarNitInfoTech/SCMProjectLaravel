<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use Illuminate\Http\Request;

class VendorController extends Controller
{
    public function index(Request $request)
    {
        $title = 'Vendor List';
        $query = Vendor::query();
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
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
            'name' => 'required|string|max:255',
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
        'name' => 'required|string|max:255',
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

    // Handle toggle (checkbox) manually
    $validated['is_active'] = $request->has('is_active');

    // Update the vendor
    $vendor->update($validated);

    return redirect()->route('vendors.list')->with('success', 'Vendor updated successfully.');
}


    public function destroy(Vendor $vendor)
    {
        $vendor->delete();

        return back()->with('success', 'Vendor deleted successfully.');
    }

    public function show(Vendor $vendor)
    {
        return redirect()->route('vendors.list');
    }
}
