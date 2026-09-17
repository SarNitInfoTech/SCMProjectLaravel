<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\{
    IndentRegister,
    PORegister,
    Vendor,
    Item,
    Department,
    Project
};

class MegaSearchController extends Controller
{
    /**
     * Live AJAX Mega Search / Omnibox search endpoint.
     * Searches across Indents, Purchase Orders, Vendors, Items, Departments, and Projects.
     */
    public function search(Request $request)
    {
        $q = trim($request->get('q', ''));
        if (strlen($q) < 2) {
            return response()->json([
                'query' => $q,
                'total_results' => 0,
                'results' => []
            ]);
        }

        $results = [];

        // 1. Search Indents (by ID, indent_id, department, items_description)
        try {
            $indents = IndentRegister::query()
                ->where('id', 'like', "%{$q}%")
                ->orWhere('indent_id', 'like', "%{$q}%")
                ->orWhere('indent_department', 'like', "%{$q}%")
                ->orWhere('indent_project', 'like', "%{$q}%")
                ->orWhere('items_description', 'like', "%{$q}%")
                ->latest()
                ->take(5)
                ->get();

            foreach ($indents as $indent) {
                // Parse items from items_description
                $rawItems = $indent->items_description;
                $itemNames = '';
                if (is_string($rawItems)) {
                    $decoded = json_decode($rawItems, true);
                    if (is_array($decoded)) {
                        $itemNames = collect($decoded)->map(function($i) {
                            return $i['description'] ?? $i['item_name'] ?? '';
                        })->filter()->take(3)->implode(', ');
                    }
                } elseif (is_array($rawItems)) {
                    $itemNames = collect($rawItems)->map(function($i) {
                        return $i['description'] ?? $i['item_name'] ?? '';
                    })->filter()->take(3)->implode(', ');
                }

                $displayId = $indent->indent_id ?? $indent->id;
                $url = Route::has('indent.edit') ? route('indent.edit', $indent->id) : (Route::has('indent-register.edit') ? route('indent-register.edit', $indent->id) : route('indent.index'));

                $results[] = [
                    'category' => 'Indents',
                    'icon' => 'assignment',
                    'color' => '#2563EB',
                    'bg' => '#EFF6FF',
                    'title' => 'Indent #' . $displayId . ($indent->indent_department ? ' • ' . $indent->indent_department : ''),
                    'subtitle' => ($itemNames ? 'Items: ' . $itemNames : 'Date: ' . ($indent->indent_date ?? ($indent->created_at ? $indent->created_at->format('d M Y') : 'N/A'))),
                    'badge' => ucfirst($indent->status ?? 'Active'),
                    'badge_class' => ($indent->status === 'Cancel' || $indent->status === 'cancelled' ? 'bg-red-50 text-red-700' : 'bg-blue-50 text-blue-700'),
                    'url' => $url,
                ];
            }
        } catch (\Throwable $e) {}

        // 2. Search Purchase Orders (by po_wo_no, indent_id, party_name, item_description)
        try {
            $pos = PORegister::query()
                ->where('id', 'like', "%{$q}%")
                ->orWhere('po_wo_no', 'like', "%{$q}%")
                ->orWhere('indent_id', 'like', "%{$q}%")
                ->orWhere('party_name', 'like', "%{$q}%")
                ->orWhere('department_id', 'like', "%{$q}%")
                ->orWhere('item_description', 'like', "%{$q}%")
                ->latest()
                ->take(5)
                ->get();

            foreach ($pos as $po) {
                // Parse items from item_description
                $rawItems = $po->item_description;
                $itemNames = '';
                if (is_string($rawItems)) {
                    $decoded = json_decode($rawItems, true);
                    if (is_array($decoded)) {
                        $itemNames = collect($decoded)->map(function($i) {
                            return $i['description'] ?? $i['item_name'] ?? '';
                        })->filter()->take(3)->implode(', ');
                    }
                } elseif (is_array($rawItems)) {
                    $itemNames = collect($rawItems)->map(function($i) {
                        return $i['description'] ?? $i['item_name'] ?? '';
                    })->filter()->take(3)->implode(', ');
                }

                $poNum = $po->po_wo_no ?? ('PO-' . $po->id);
                $vendorName = $po->party_name ? $po->party_name : 'PO Record';
                $url = Route::has('po-register.edit') ? route('po-register.edit', $po->id) : route('po-register.index');

                $results[] = [
                    'category' => 'Purchase Orders',
                    'icon' => 'receipt_long',
                    'color' => '#166534',
                    'bg' => '#F0FDF4',
                    'title' => $poNum . ' • ' . $vendorName,
                    'subtitle' => 'Indent #' . ($po->indent_id ?? 'N/A') . ($itemNames ? ' • ' . $itemNames : '') . ($po->po_amount ? ' • ₹' . number_format((float)$po->po_amount, 2) : ''),
                    'badge' => ucfirst($po->status ?? 'Open'),
                    'badge_class' => ($po->status === 'cancelled' ? 'bg-red-50 text-red-700' : 'bg-green-50 text-green-700'),
                    'url' => $url,
                ];
            }
        } catch (\Throwable $e) {}

        // 3. Search Vendors (by name, email, phone, gst_number)
        try {
            $vendors = Vendor::query()
                ->where('name', 'like', "%{$q}%")
                ->orWhere('email', 'like', "%{$q}%")
                ->orWhere('phone', 'like', "%{$q}%")
                ->orWhere('gst_number', 'like', "%{$q}%")
                ->latest()
                ->take(4)
                ->get();

            foreach ($vendors as $vendor) {
                $url = Route::has('vendors.edit') ? route('vendors.edit', $vendor->id) : route('vendors.list');
                $results[] = [
                    'category' => 'Vendors',
                    'icon' => 'storefront',
                    'color' => '#9333EA',
                    'bg' => '#FAF5FF',
                    'title' => $vendor->name ?? 'Vendor',
                    'subtitle' => ($vendor->email ? $vendor->email . ' • ' : '') . ($vendor->phone ?? ($vendor->gst_number ? 'GST: ' . $vendor->gst_number : 'Supplier')),
                    'badge' => 'Supplier',
                    'badge_class' => 'bg-purple-50 text-purple-700',
                    'url' => $url,
                ];
            }
        } catch (\Throwable $e) {}

        // 4. Search Master Items (by item name)
        try {
            $items = Item::query()
                ->where('name', 'like', "%{$q}%")
                ->latest()
                ->take(4)
                ->get();

            foreach ($items as $item) {
                $url = Route::has('items.edit') ? route('items.edit', $item->id) : route('items.index');
                $results[] = [
                    'category' => 'Master Items',
                    'icon' => 'category',
                    'color' => '#EA580C',
                    'bg' => '#FFF7ED',
                    'title' => $item->name ?? 'Item',
                    'subtitle' => 'Master Catalog Item #' . $item->id,
                    'badge' => 'Item',
                    'badge_class' => 'bg-orange-50 text-orange-700',
                    'url' => $url,
                ];
            }
        } catch (\Throwable $e) {}

        // 5. Search Departments
        try {
            $depts = Department::query()
                ->where('name', 'like', "%{$q}%")
                ->latest()
                ->take(3)
                ->get();

            foreach ($depts as $dept) {
                $url = Route::has('departments.edit') ? route('departments.edit', $dept->id) : route('departments.index');
                $results[] = [
                    'category' => 'Departments',
                    'icon' => 'corporate_fare',
                    'color' => '#0D9488',
                    'bg' => '#CCFBF1',
                    'title' => $dept->name ?? 'Department',
                    'subtitle' => 'Department Master Record',
                    'badge' => 'Department',
                    'badge_class' => 'bg-teal-50 text-teal-700',
                    'url' => $url,
                ];
            }
        } catch (\Throwable $e) {}

        // 6. Search Projects
        try {
            $projects = Project::query()
                ->where('name', 'like', "%{$q}%")
                ->latest()
                ->take(3)
                ->get();

            foreach ($projects as $project) {
                $url = Route::has('projects.edit') ? route('projects.edit', $project->id) : route('projects.index');
                $results[] = [
                    'category' => 'Projects',
                    'icon' => 'folder_open',
                    'color' => '#2563EB',
                    'bg' => '#EFF6FF',
                    'title' => $project->name ?? 'Project',
                    'subtitle' => 'Project Master Record',
                    'badge' => 'Project',
                    'badge_class' => 'bg-blue-50 text-blue-700',
                    'url' => $url,
                ];
            }
        } catch (\Throwable $e) {}

        return response()->json([
            'query' => $q,
            'total_results' => count($results),
            'results' => $results
        ]);
    }
}
