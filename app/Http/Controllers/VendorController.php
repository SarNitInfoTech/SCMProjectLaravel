<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use Illuminate\Http\Request;

class VendorController extends Controller
{
     public function index()
{
    $title = 'Vendor List';

    $columns = [
        ['key' => 'name', 'label' => 'Vendor Name'],
        ['key' => 'email', 'label' => 'Email'],
        ['key' => 'phone', 'label' => 'Phone'],
        ['key' => 'gst_number', 'label' => 'GST Number'],
        ['key' => 'bank_name', 'label' => 'Bank Name'],
        ['key' => 'is_active', 'label' => 'Status'],
        ['key' => 'created_at', 'label' => 'Created At'],
        ['key' => 'updated_at', 'label' => 'Updated At'],
        ['key' => 'action', 'label' => 'Action', 'type' => 'action'],
    ];

    $vendors = Vendor::select(
        'id',
        'name',
        'email',
        'phone',
        'gst_number',
        'bank_name',
        'is_active',
        'created_at',
        'updated_at'
    )->paginate(10);

    $rows = $vendors->map(function ($vendor) {
        return [
            'name' => $vendor->name,
            'email' => $vendor->email ?? '—',
            'phone' => $vendor->phone ?? '—',
            'gst_number' => $vendor->gst_number ?? '—',
            'bank_name' => $vendor->bank_name ?? '—',
            'is_active' => $vendor->is_active ? 'Active' : 'Inactive',
            'created_at' => $vendor->created_at->format('d-m-Y'),
            'updated_at' => $vendor->updated_at->format('d-m-Y'),
            'action' => route('vendors.edit', $vendor->id),
        ];
    });

    $searchPlaceholder = 'Search Vendors...';
    $redirectUrl = route('vendors.create');

    $customButton = <<<HTML
<a href="{$redirectUrl}" class="ti-btn ti-btn-primary-full">
    <i class="bi bi-plus-lg"></i>
    Add New Vendor
</a>
HTML;

    return view('pages.vendors.listVendors.listVendors', [
        'title' => $title,
        'columns' => $columns,
        'rows' => $rows,
        'searchPlaceholder' => $searchPlaceholder,
        'customButton' => $customButton,
        'pagination' => $vendors,
    ]);
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
