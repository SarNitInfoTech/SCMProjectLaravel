<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\DepartmentHead;
use App\Models\IndentRegister;
use App\Models\Item;
use App\Models\PORegister;
use App\Models\Project;
use App\Models\Unit;
use App\Models\User;
use App\Models\Vendor;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class BulkUploadController extends Controller
{
    /**
     * Display the Bulk Upload dashboard.
     */
    public function index(Request $request)
    {
        $title = 'Bulk Data Import';
        $selectedModule = $request->get('module', 'departments');

        $modules = [
            'departments' => [
                'name' => 'Departments',
                'description' => 'Import organizational departments',
                'icon' => 'bi-building-gear',
                'columns' => [
                    ['name' => 'Name', 'required' => true, 'type' => 'String', 'desc' => 'Unique department name. E.g., Procurement']
                ]
            ],
            'units' => [
                'name' => 'Measurement Units',
                'description' => 'Import packaging/measurement units',
                'icon' => 'bi-rulers',
                'columns' => [
                    ['name' => 'Name', 'required' => true, 'type' => 'String', 'desc' => 'Unique unit name. E.g., Kilograms']
                ]
            ],
            'projects' => [
                'name' => 'Projects',
                'description' => 'Import project profiles',
                'icon' => 'bi-kanban',
                'columns' => [
                    ['name' => 'Name', 'required' => true, 'type' => 'String', 'desc' => 'Unique project name. E.g., Spinning Mill Expansion']
                ]
            ],
            'items' => [
                'name' => 'Inventory Items',
                'description' => 'Import items catalog',
                'icon' => 'bi-box-seam',
                'columns' => [
                    ['name' => 'Name', 'required' => true, 'type' => 'String', 'desc' => 'Unique item name. E.g., Polyester Yarn 40/1']
                ]
            ],
            'vendors' => [
                'name' => 'Vendors & Suppliers',
                'description' => 'Import external vendors database',
                'icon' => 'bi-truck-flatbed',
                'columns' => [
                    ['name' => 'Name', 'required' => true, 'type' => 'String', 'desc' => 'Vendor company name. E.g., Indo Cotton Ltd'],
                    ['name' => 'Email', 'required' => false, 'type' => 'Email', 'desc' => 'Valid unique email address'],
                    ['name' => 'Phone', 'required' => false, 'type' => 'String', 'desc' => 'Contact number'],
                    ['name' => 'Address', 'required' => false, 'type' => 'String', 'desc' => 'Complete physical location address'],
                    ['name' => 'GST Number', 'required' => false, 'type' => 'String', 'desc' => '15-digit GSTIN number'],
                    ['name' => 'PAN Number', 'required' => false, 'type' => 'String', 'desc' => '10-digit PAN number'],
                    ['name' => 'Account Name', 'required' => false, 'type' => 'String', 'desc' => 'Bank account holder name'],
                    ['name' => 'Account Number', 'required' => false, 'type' => 'String', 'desc' => 'Bank account number'],
                    ['name' => 'Bank Name', 'required' => false, 'type' => 'String', 'desc' => 'Name of the bank'],
                    ['name' => 'Branch Name', 'required' => false, 'type' => 'String', 'desc' => 'Bank branch name'],
                    ['name' => 'IFSC Code', 'required' => false, 'type' => 'String', 'desc' => '11-digit bank IFSC code']
                ]
            ],
            'users' => [
                'name' => 'Users Register',
                'description' => 'Import portal users and roles',
                'icon' => 'bi-people',
                'columns' => [
                    ['name' => 'Name', 'required' => true, 'type' => 'String', 'desc' => 'Full name of the user'],
                    ['name' => 'Email', 'required' => true, 'type' => 'Email', 'desc' => 'Unique portal login email address'],
                    ['name' => 'Role', 'required' => true, 'type' => 'String', 'desc' => 'User role. Options: super_admin, admin, user'],
                    ['name' => 'Department Name', 'required' => false, 'type' => 'String', 'desc' => 'Department name (will be created if missing)'],
                    ['name' => 'Designation', 'required' => false, 'type' => 'String', 'desc' => 'Job role/designation'],
                    ['name' => 'Phone', 'required' => false, 'type' => 'String', 'desc' => 'Contact number'],
                    ['name' => 'Password', 'required' => false, 'type' => 'String', 'desc' => 'Login password (defaults to password123 if blank)']
                ]
            ],
            'department-heads' => [
                'name' => 'Department Heads',
                'description' => 'Import department heads assignment',
                'icon' => 'bi-person-badge',
                'columns' => [
                    ['name' => 'Department Name', 'required' => true, 'type' => 'String', 'desc' => 'Existing department name'],
                    ['name' => 'Department Head Name', 'required' => true, 'type' => 'String', 'desc' => 'Full name of the department head']
                ]
            ],
            'indents' => [
                'name' => 'Indent Registers',
                'description' => 'Import indents with automatic items grouping',
                'icon' => 'bi-file-earmark-spreadsheet',
                'columns' => [
                    ['name' => 'Indent ID', 'required' => true, 'type' => 'String', 'desc' => 'Custom ticket number (e.g. CSE-101)'],
                    ['name' => 'Indent Date', 'required' => true, 'type' => 'Date', 'desc' => 'YYYY-MM-DD format'],
                    ['name' => 'Department Name', 'required' => true, 'type' => 'String', 'desc' => 'Target department name'],
                    ['name' => 'Project Name', 'required' => false, 'type' => 'String', 'desc' => 'Associated project name'],
                    ['name' => 'Item Description', 'required' => true, 'type' => 'String', 'desc' => 'Item description'],
                    ['name' => 'Unit Name', 'required' => true, 'type' => 'String', 'desc' => 'Measurement unit name'],
                    ['name' => 'Quantity Required', 'required' => true, 'type' => 'Integer', 'desc' => 'Required quantity'],
                    ['name' => 'Quantity Received', 'required' => false, 'type' => 'Integer', 'desc' => 'Received quantity (defaults to 0)'],
                    ['name' => 'Quantity Balance', 'required' => false, 'type' => 'Integer', 'desc' => 'Balance quantity (defaults to required - received)']
                ]
            ],
            'po-registers' => [
                'name' => 'Purchase Orders (PO)',
                'description' => 'Import completed purchase orders',
                'icon' => 'bi-receipt',
                'columns' => [
                    ['name' => 'Indent ID', 'required' => true, 'type' => 'String/Integer', 'desc' => 'Linked numeric or code Indent ID'],
                    ['name' => 'Department Name', 'required' => true, 'type' => 'String', 'desc' => 'Linked department name'],
                    ['name' => 'PO Date', 'required' => true, 'type' => 'Date', 'desc' => 'YYYY-MM-DD format'],
                    ['name' => 'Party Name', 'required' => true, 'type' => 'String', 'desc' => 'Vendor company name'],
                    ['name' => 'PO WO No', 'required' => true, 'type' => 'String', 'desc' => 'PO/WO reference number'],
                    ['name' => 'PO Amount', 'required' => true, 'type' => 'Decimal', 'desc' => 'Purchase amount. E.g. 50000.00'],
                    ['name' => 'Debit Head', 'required' => false, 'type' => 'String', 'desc' => 'Accounting debit head'],
                    ['name' => 'Item Descriptions', 'required' => true, 'type' => 'String', 'desc' => 'Comma-separated descriptions of items. E.g. Raw cotton, dye'],
                    ['name' => 'Expected Days', 'required' => false, 'type' => 'Integer', 'desc' => 'Delivery lead time in days'],
                    ['name' => 'Expected Date', 'required' => false, 'type' => 'Date', 'desc' => 'YYYY-MM-DD format'],
                    ['name' => 'Invoice Date', 'required' => false, 'type' => 'Date', 'desc' => 'YYYY-MM-DD format'],
                    ['name' => 'Receiving Date', 'required' => false, 'type' => 'Date', 'desc' => 'YYYY-MM-DD format'],
                    ['name' => 'Invoice', 'required' => false, 'type' => 'String', 'desc' => 'Invoice reference number'],
                    ['name' => 'Delay In Days', 'required' => false, 'type' => 'Integer', 'desc' => 'Number of days delayed'],
                    ['name' => 'Store Indent No', 'required' => false, 'type' => 'String', 'desc' => 'Store book entry indent number'],
                    ['name' => 'Remarks', 'required' => false, 'type' => 'String', 'desc' => 'Additional notes or remarks']
                ]
            ]
        ];

        return view('pages.bulk-upload.index', compact('title', 'modules', 'selectedModule'));
    }

    /**
     * Stream-generate a sample CSV template on the fly.
     */
    public function downloadTemplate($module)
    {
        $headers = [];
        $sampleRow = [];

        switch ($module) {
            case 'departments':
                $headers = ['Name'];
                $sampleRow = ['Procurement Department'];
                break;
            case 'units':
                $headers = ['Name'];
                $sampleRow = ['Kilograms'];
                break;
            case 'projects':
                $headers = ['Name'];
                $sampleRow = ['Spinning Mill Phase 2'];
                break;
            case 'items':
                $headers = ['Name'];
                $sampleRow = ['Carded Cotton Thread 30s'];
                break;
            case 'vendors':
                $headers = ['Name', 'Email', 'Phone', 'Address', 'GST Number', 'PAN Number', 'Account Name', 'Account Number', 'Bank Name', 'Branch Name', 'IFSC Code'];
                $sampleRow = ['Indo Cotton Textiles', 'orders@indocotton.com', '9876543210', 'Plot 45, MIDC, Nagpur, MH', '27AAAAA1111A1Z1', 'ABCDE1234F', 'Indo Cotton Accounts', '334455667788', 'State Bank of India', 'Main Branch', 'SBIN0001234'];
                break;
            case 'users':
                $headers = ['Name', 'Email', 'Role', 'Department Name', 'Designation', 'Phone', 'Password'];
                $sampleRow = ['John Carter', 'john.carter@nitra.com', 'admin', 'Procurement Department', 'Purchase Manager', '9890123456', 'Welcome@2026'];
                break;
            case 'department-heads':
                $headers = ['Department Name', 'Department Head Name'];
                $sampleRow = ['Procurement Department', 'Robert Downey'];
                break;
            case 'indents':
                $headers = ['Indent ID', 'Indent Date', 'Department Name', 'Project Name', 'Item Description', 'Unit Name', 'Quantity Required', 'Quantity Received', 'Quantity Balance'];
                $sampleRow = ['CSE-202', '2026-05-26', 'Procurement Department', 'Spinning Mill Phase 2', 'Carded Cotton Thread 30s', 'Kilograms', '5000', '1500', '3500'];
                break;
            case 'po-registers':
                $headers = ['Indent ID', 'Department Name', 'PO Date', 'Party Name', 'PO WO No', 'PO Amount', 'Debit Head', 'Item Descriptions', 'Expected Days', 'Expected Date', 'Invoice Date', 'Receiving Date', 'Invoice', 'Delay In Days', 'Store Indent No', 'Remarks'];
                $sampleRow = ['202', 'Procurement Department', '2026-05-28', 'Indo Cotton Textiles', 'PO-26-0034', '125000.00', 'Raw Material Head', 'Carded Cotton Thread 30s, Polyester Yarn', '15', '2026-06-12', '2026-06-10', '2026-06-11', 'INV-99881', '0', 'IND-10022', 'Material loaded successfully'];
                break;
            default:
                abort(404, 'Invalid template requested.');
        }

        $fileName = "sample_{$module}_template.csv";
        
        $callback = function() use ($headers, $sampleRow) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $headers);
            fputcsv($file, $sampleRow);
            fclose($file);
        };

        return response()->stream($callback, 200, [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename={$fileName}",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ]);
    }

    /**
     * Process bulk data import.
     */
    public function store(Request $request)
    {
        $request->validate([
            'module' => 'required|string',
            'file' => 'required|file|mimes:csv,txt,xlsx,xls|max:10240',
        ]);

        $module = $request->input('module');
        $file = $request->file('file');

        try {
            // Load spreadsheet using GenericImport helper
            $data = Excel::toArray(new \App\Imports\GenericImport, $file);
            
            if (empty($data) || empty($data[0])) {
                return back()->with('error', 'The uploaded file is empty.');
            }

            $rows = $data[0];
            
            // Clean/find header row
            $headerRowIndex = 0;
            while ($headerRowIndex < count($rows) && empty(array_filter($rows[$headerRowIndex]))) {
                $headerRowIndex++;
            }

            if ($headerRowIndex >= count($rows)) {
                return back()->with('error', 'No header row detected in the sheet.');
            }

            $rawHeaders = $rows[$headerRowIndex];
            $headers = array_map(function($header) {
                return strtolower(trim(preg_replace('/[^a-zA-Z0-9_]/', '_', (string)$header)));
            }, $rawHeaders);

            $importRows = array_slice($rows, $headerRowIndex + 1);

            $successCount = 0;
            $errorCount = 0;
            $logs = [];

            // Grouping indents data to process consolidated records later
            $indentGroups = [];

            foreach ($importRows as $index => $rawRow) {
                // Skip fully blank rows
                if (empty(array_filter($rawRow))) {
                    continue;
                }

                $rowNum = $index + $headerRowIndex + 2;

                // Map header to row values
                $row = [];
                foreach ($headers as $hIndex => $headerKey) {
                    if (empty($headerKey)) continue;
                    $row[$headerKey] = $rawRow[$hIndex] ?? null;
                }

                // -----------------------
                // Process Module Import
                // -----------------------
                DB::beginTransaction();
                try {
                    switch ($module) {
                        case 'departments':
                            $validator = Validator::make($row, [
                                'name' => 'required|string|max:255',
                            ]);
                            if ($validator->fails()) {
                                throw new \Exception(implode(', ', $validator->errors()->all()));
                            }

                            $deptName = trim($row['name']);
                            $exists = Department::where('name', $deptName)->exists();
                            if ($exists) {
                                throw new \Exception("Department '{$deptName}' already exists.");
                            }

                            Department::create(['name' => $deptName]);
                            break;

                        case 'units':
                            $validator = Validator::make($row, [
                                'name' => 'required|string|max:255',
                            ]);
                            if ($validator->fails()) {
                                throw new \Exception(implode(', ', $validator->errors()->all()));
                            }

                            $unitName = trim($row['name']);
                            $exists = Unit::where('name', $unitName)->exists();
                            if ($exists) {
                                throw new \Exception("Unit '{$unitName}' already exists.");
                            }

                            Unit::create(['name' => $unitName]);
                            break;

                        case 'projects':
                            $validator = Validator::make($row, [
                                'name' => 'required|string|max:255',
                            ]);
                            if ($validator->fails()) {
                                throw new \Exception(implode(', ', $validator->errors()->all()));
                            }

                            $projectName = trim($row['name']);
                            $exists = Project::where('name', $projectName)->exists();
                            if ($exists) {
                                throw new \Exception("Project '{$projectName}' already exists.");
                            }

                            Project::create(['name' => $projectName]);
                            break;

                        case 'items':
                            $validator = Validator::make($row, [
                                'name' => 'required|string|max:255',
                            ]);
                            if ($validator->fails()) {
                                throw new \Exception(implode(', ', $validator->errors()->all()));
                            }

                            $itemName = trim($row['name']);
                            $exists = Item::where('name', $itemName)->exists();
                            if ($exists) {
                                throw new \Exception("Item '{$itemName}' already exists.");
                            }

                            Item::create(['name' => $itemName]);
                            break;

                        case 'vendors':
                            $validator = Validator::make($row, [
                                'name' => 'required|string|max:255',
                                'email' => 'nullable|email|max:255',
                            ]);
                            if ($validator->fails()) {
                                throw new \Exception(implode(', ', $validator->errors()->all()));
                            }

                            $vendorName = trim($row['name']);
                            
                            Vendor::create([
                                'name' => $vendorName,
                                'email' => $row['email'] ?? null,
                                'phone' => $row['phone'] ?? null,
                                'address' => $row['address'] ?? null,
                                'gst_number' => $row['gst_number'] ?? null,
                                'pan_number' => $row['pan_number'] ?? null,
                                'account_name' => $row['account_name'] ?? null,
                                'account_number' => $row['account_number'] ?? null,
                                'bank_name' => $row['bank_name'] ?? null,
                                'branch_name' => $row['branch_name'] ?? null,
                                'ifsc_code' => $row['ifsc_code'] ?? null,
                                'is_active' => true
                            ]);
                            break;

                        case 'users':
                            $validator = Validator::make($row, [
                                'name' => 'required|string|max:255',
                                'email' => 'required|email|max:255',
                                'role' => 'required|string|in:super_admin,admin,user',
                            ]);
                            if ($validator->fails()) {
                                throw new \Exception(implode(', ', $validator->errors()->all()));
                            }

                            $email = trim($row['email']);
                            if (User::where('email', $email)->exists()) {
                                throw new \Exception("User with email '{$email}' already exists.");
                            }

                            // Resolve or create department
                            $deptId = null;
                            if (!empty($row['department_name'])) {
                                $dept = Department::firstOrCreate(['name' => trim($row['department_name'])]);
                                $deptId = $dept->id;
                            }

                            User::create([
                                'name' => trim($row['name']),
                                'email' => $email,
                                'role' => trim($row['role']),
                                'department_id' => $deptId,
                                'designation' => $row['designation'] ?? null,
                                'phone' => $row['phone'] ?? null,
                                'password' => Hash::make(!empty($row['password']) ? trim($row['password']) : 'password123'),
                                'is_active' => true
                            ]);
                            break;

                        case 'department_heads':
                        case 'department_heads_assignment':
                        case 'department-heads':
                            $validator = Validator::make($row, [
                                'department_name' => 'required|string|max:255',
                                'department_head_name' => 'required|string|max:255',
                            ]);
                            if ($validator->fails()) {
                                throw new \Exception(implode(', ', $validator->errors()->all()));
                            }

                            $deptName = trim($row['department_name']);
                            $dept = Department::where('name', $deptName)->first();
                            if (!$dept) {
                                throw new \Exception("Department '{$deptName}' does not exist. Please import the department first.");
                            }

                            DepartmentHead::create([
                                'department_id' => $dept->id,
                                'department_head' => trim($row['department_head_name'])
                            ]);
                            break;

                        case 'indents':
                            $validator = Validator::make($row, [
                                'indent_id' => 'required|string',
                                'indent_date' => 'required|date',
                                'department_name' => 'required|string',
                                'item_description' => 'required|string',
                                'unit_name' => 'required|string',
                                'quantity_required' => 'required|integer|min:0',
                            ]);
                            if ($validator->fails()) {
                                throw new \Exception(implode(', ', $validator->errors()->all()));
                            }

                            // Collate this item inside memory instead of direct save to merge later
                            $groupKey = trim($row['indent_id']) . ':::' . trim($row['department_name']);
                            if (!isset($indentGroups[$groupKey])) {
                                $indentGroups[$groupKey] = [
                                    'indent_id' => trim($row['indent_id']),
                                    'indent_date' => trim($row['indent_date']),
                                    'department_name' => trim($row['department_name']),
                                    'project_name' => !empty($row['project_name']) ? trim($row['project_name']) : null,
                                    'items' => [],
                                    'row_numbers' => []
                                ];
                            }

                            // Resolve unit name to numeric id if possible, otherwise create it dynamically
                            $unitName = trim($row['unit_name']);
                            $unit = Unit::firstOrCreate(['name' => $unitName]);

                            // Make sure item is created in master dynamically
                            Item::firstOrCreate(['name' => trim($row['item_description'])]);

                            $indentGroups[$groupKey]['items'][] = [
                                'description' => trim($row['item_description']),
                                'unit' => $unit->id,
                                'quantity_required' => (int)$row['quantity_required'],
                                'quantity_received' => (int)($row['quantity_received'] ?? 0),
                                'quantity_balance' => (int)($row['quantity_balance'] ?? max((int)$row['quantity_required'] - (int)($row['quantity_received'] ?? 0), 0))
                            ];
                            $indentGroups[$groupKey]['row_numbers'][] = $rowNum;
                            break;

                        case 'po-registers':
                            $validator = Validator::make($row, [
                                'indent_id' => 'required',
                                'department_name' => 'required|string',
                                'po_date' => 'required|date',
                                'party_name' => 'required|string',
                                'po_wo_no' => 'required|string',
                                'po_amount' => 'required|numeric',
                                'item_descriptions' => 'required|string',
                            ]);
                            if ($validator->fails()) {
                                throw new \Exception(implode(', ', $validator->errors()->all()));
                            }

                            // Resolve Department Name
                            $deptName = trim($row['department_name']);
                            $dept = Department::where('name', $deptName)->first();
                            if (!$dept) {
                                throw new \Exception("Department '{$deptName}' does not exist.");
                            }

                            // Split items by comma
                            $items = array_map('trim', explode(',', $row['item_descriptions']));

                            // Convert dates safely
                            $fmtDate = function($val) {
                                if (empty($val)) return null;
                                try {
                                    return Carbon::parse($val)->format('Y-m-d');
                                } catch (\Exception $e) {
                                    return null;
                                }
                            };

                            DB::table('po_registers')->insert([
                                'indent_id' => $row['indent_id'],
                                'department_id' => $dept->id,
                                'status' => 'Pending',
                                'po_date' => $fmtDate($row['po_date']),
                                'party_name' => trim($row['party_name']),
                                'po_wo_no' => trim($row['po_wo_no']),
                                'po_amount' => (float)$row['po_amount'],
                                'debit_head' => $row['debit_head'] ?? null,
                                'item_description' => json_encode($items),
                                'expected_days' => isset($row['expected_days']) ? (string)$row['expected_days'] : null,
                                'expected_date' => $fmtDate($row['expected_date'] ?? null),
                                'invoice_date' => $fmtDate($row['invoice_date'] ?? null),
                                'receiving_date' => $row['receiving_date'] ?? null,
                                'invoice' => $row['invoice'] ?? null,
                                'delay_in_days' => isset($row['delay_in_days']) ? (int)$row['delay_in_days'] : null,
                                'store_indent_no' => $row['store_indent_no'] ?? null,
                                'remarks' => $row['remarks'] ?? null,
                                'created_at' => now(),
                                'updated_at' => now()
                            ]);
                            break;

                        default:
                            throw new \Exception("Unsupported import module: {$module}");
                    }

                    if ($module !== 'indents') {
                        DB::commit();
                        $successCount++;
                        $logs[] = ['row' => $rowNum, 'status' => 'success', 'message' => 'Successfully imported row.'];
                    } else {
                        // For indents, we delay commit and handle group commits later
                        DB::rollBack();
                    }

                } catch (\Exception $e) {
                    DB::rollBack();
                    $errorCount++;
                    $logs[] = ['row' => $rowNum, 'status' => 'error', 'message' => $e->getMessage()];
                }
            }

            // ---------------------------------------------
            // Consolidated Processing for Indent Registers
            // ---------------------------------------------
            if ($module === 'indents' && !empty($indentGroups)) {
                foreach ($indentGroups as $key => $group) {
                    DB::beginTransaction();
                    try {
                        // Resolve department
                        $dept = Department::where('name', $group['department_name'])->first();
                        if (!$dept) {
                            throw new \Exception("Department '{$group['department_name']}' does not exist. Please import department first.");
                        }

                        // Create project dynamically if not exists
                        $projectName = $group['project_name'];
                        if (!empty($projectName)) {
                            Project::firstOrCreate(['name' => $projectName]);
                        }

                        // See if indent already registered
                        $existing = IndentRegister::where('indent_id', $group['indent_id'])
                            ->where('indent_department', $dept->name)
                            ->first();

                        if ($existing) {
                            // Decode and merge items
                            $existingItems = json_decode($existing->items_description, true) ?? [];
                            $mergedItems = array_merge($existingItems, $group['items']);
                            
                            $existing->items_description = json_encode($mergedItems);
                            $existing->save();
                        } else {
                            IndentRegister::create([
                                'indent_id' => $group['indent_id'],
                                'indent_date' => $group['indent_date'],
                                'indent_department' => $dept->name,
                                'indent_project' => $projectName,
                                'items_description' => json_encode($group['items']),
                                'status' => 'Pending'
                            ]);
                        }

                        DB::commit();
                        $successCount += count($group['items']);
                        foreach ($group['row_numbers'] as $rNum) {
                            $logs[] = ['row' => $rNum, 'status' => 'success', 'message' => 'Consolidated & imported into Indent Ticket ' . $group['indent_id']];
                        }
                    } catch (\Exception $e) {
                        DB::rollBack();
                        $errorCount += count($group['items']);
                        foreach ($group['row_numbers'] as $rNum) {
                            $logs[] = ['row' => $rNum, 'status' => 'error', 'message' => $e->getMessage()];
                        }
                    }
                }
            }

            // Sort logs by row number
            usort($logs, function($a, $b) {
                return $a['row'] <=> $b['row'];
            });

            return back()->with([
                'import_results' => [
                    'success_count' => $successCount,
                    'error_count' => $errorCount,
                    'logs' => $logs
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Bulk import general error: ' . $e->getMessage());
            return back()->with('error', 'Error reading uploaded file: ' . $e->getMessage());
        }
    }
}
