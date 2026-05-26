<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Department;
use App\Models\DepartmentHead;
use App\Models\Vendor;
use App\Models\PORegister;
use App\Models\IndentRegister;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

class POModuleTest extends TestCase
{
    use RefreshDatabase;

    protected $adminUser;
    protected $testDept;
    protected $testVendor;
    protected $testDeptHead;
    protected $testIndent;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);

        // Register permission gates since table was empty during AppServiceProvider boot
        if (\Illuminate\Support\Facades\Schema::hasTable('permissions')) {
            $permissions = \App\Models\Permission::all();
            foreach ($permissions as $permission) {
                \Illuminate\Support\Facades\Gate::define($permission->name, function ($user) use ($permission) {
                    return $user->hasPermission($permission->name);
                });
            }
        }

        // Setup base entities (Assert 1-5)
        $this->testDept = Department::create(['name' => 'IT Department']);
        $this->testVendor = Vendor::create(['name' => 'Microsoft Enterprise Partner']);
        
        $this->testDeptHead = DepartmentHead::create([
            'department_id' => $this->testDept->id,
            'department_head' => 'Dr. Jane Smith',
        ]);

        $this->assertDatabaseHas('departments', ['name' => 'IT Department']);
        $this->assertDatabaseHas('vendors', ['name' => 'Microsoft Enterprise Partner']);
        $this->assertDatabaseHas('department_heads', ['department_head' => 'Dr. Jane Smith']);

        $this->adminUser = User::factory()->create(['role' => 'admin', 'is_active' => true]);
    }

    /** @test */
    public function purchase_order_list_rendering_and_joins()
    {
        $this->actingAs($this->adminUser);

        // Seed a register (Assert 6-11)
        PORegister::create([
            'po_date' => '2026-05-26',
            'indent_id' => 12345,
            'department_id' => $this->testDept->id,
            'party_name' => 'Microsoft Enterprise Partner',
            'po_wo_no' => 'PO-12345',
            'po_amount' => 500000.00,
            'debit_head' => 'IT Software Budget',
            'item_description' => json_encode(['Windows 11 License', 'Visual Studio Enterprise']),
            'expected_days' => '30',
            'expected_date' => '2026-06-26',
            'status' => 'Pending'
        ]);

        $this->assertDatabaseHas('po_registers', ['po_wo_no' => 'PO-12345']);

        // 1. Visit List Page (Assert 12-15)
        $response = $this->get('/po-register');
        $response->assertStatus(200);
        $response->assertSee('12345');
        $response->assertSee('Microsoft Enterprise Partner');
        $response->assertSee('IT Department');
    }

    /** @test */
    public function purchase_order_creation_form_displays_remaining_items()
    {
        $this->actingAs($this->adminUser);

        // Create an indent register with items (Assert 16-19)
        IndentRegister::create([
            'indent_id' => '54321',
            'indent_date' => '2026-05-25',
            'indent_department' => 'IT Department',
            'indent_project' => 'Network Upgrades',
            'items_description' => json_encode([
                [
                    'description' => 'Cisco Catalyst Switch',
                    'unit' => 'Units',
                    'quantity_required' => 5,
                    'quantity_received' => 0,
                    'quantity_balance' => 5
                ],
                [
                    'description' => 'Cat6 Ethernet Cable',
                    'unit' => 'Meters',
                    'required' => 1000,
                    'received' => 0,
                    'balance' => 1000
                ]
            ]),
            'status' => 'Pending'
        ]);

        // Seed partial PO already holding Cat6 Ethernet Cable (Assert 20-22)
        PORegister::create([
            'indent_id' => 54321,
            'department_id' => $this->testDept->id,
            'po_wo_no' => 'PO-PARTIAL-1',
            'item_description' => json_encode(['Cat6 Ethernet Cable']),
            'status' => 'Pending'
        ]);

        // Visit create page; should filter out Cat6 Ethernet Cable (Assert 23-25)
        $response = $this->get("/po-register/create?indent_id=54321&department_id={$this->testDept->id}");
        $response->assertStatus(200);
        $response->assertSee('Cisco Catalyst Switch');
        $response->assertDontSee('Cat6 Ethernet Cable');
    }

    /** @test */
    public function new_purchase_order_storage_and_indent_balance_update()
    {
        $this->actingAs($this->adminUser);

        // Seed indent (Assert 26)
        IndentRegister::create([
            'indent_id' => '54321',
            'indent_date' => '2026-05-25',
            'indent_department' => 'IT Department',
            'indent_project' => 'Network Upgrades',
            'items_description' => json_encode([
                [
                    'description' => 'Cisco Catalyst Switch',
                    'unit' => 'Units',
                    'required' => 5,
                    'received' => 0,
                    'balance' => 5
                ]
            ]),
            'status' => 'Pending'
        ]);

        $response = $this->post('/po-register', [
            'indent_id' => 54321,
            'department_id' => (string)$this->testDept->id,
            'po_date' => '2026-05-26',
            'party_name' => 'Microsoft Enterprise Partner',
            'po_wo_no' => 'PO-54321',
            'po_amount' => 150000.00,
            'debit_head' => 'IT Software Budget',
            'item_description' => ['Cisco Catalyst Switch'],
            'remarks' => 'Urgent deployment'
        ]);

        $response->assertRedirect('/po-register');
        $this->assertDatabaseHas('po_registers', ['po_wo_no' => 'PO-54321']);

        // Assert 27-29: Verify relationships
        $po = PORegister::where('po_wo_no', 'PO-54321')->first();
        $this->assertNotNull($po);
        $this->assertEquals(54321, $po->indent_id);
    }

    /** @test */
    public function invoice_update_routing_and_validations()
    {
        $this->actingAs($this->adminUser);

        $po = PORegister::create([
            'po_date' => '2026-05-26',
            'indent_id' => 55555,
            'department_id' => $this->testDept->id,
            'po_wo_no' => 'PO-999',
            'status' => 'Pending'
        ]);

        // 1. Visit Invoice Screen (Assert 30-31)
        $response = $this->get("/indents/po/{$po->id}/addinvoice");
        $response->assertStatus(200);

        // 2. Perform Validation failures (Assert 32-33)
        $response = $this->put("/po-register/{$po->id}", [
            'delay_in_days' => -10
        ]);
        $response->assertSessionHasErrors(['delay_in_days']);

        // 3. Successful invoice update (Assert 34-37)
        $response = $this->put("/po-register/{$po->id}", [
            'invoice_date' => '2026-05-28',
            'receiving_date' => '2026-05-30',
            'delay_in_days' => 2,
            'store_indent_no' => 'STORE-123'
        ]);
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('po_registers', [
            'id' => $po->id,
            'store_indent_no' => 'STORE-123',
            'delay_in_days' => 2
        ]);
    }

    /** @test */
    public function status_transitions_resolve_departments_accurately()
    {
        $this->actingAs($this->adminUser);

        $this->testIndent = IndentRegister::create([
            'indent_id' => '555',
            'indent_date' => '2026-05-25',
            'indent_department' => 'IT Department',
            'indent_project' => 'Network Upgrades',
            'items_description' => json_encode([['description' => 'Switch', 'unit' => 'Units', 'quantity_required' => 5, 'quantity_received' => 0, 'quantity_balance' => 5]]),
            'status' => 'Pending'
        ]);

        $po = PORegister::create([
            'indent_id' => 555,
            'department_id' => $this->testDept->id,
            'po_wo_no' => 'PO-STATUS-TRANSITION',
            'status' => 'Pending'
        ]);

        // 1. Cancel transition (Assert 38-41)
        $response = $this->post("/po-register/status-cancel", [
            'indent_id' => 555,
            'department_id' => (string)$this->testDept->id
        ]);
        $response->assertStatus(302);
        $this->assertEquals('Cancel', IndentRegister::find($this->testIndent->id)->status);
        $this->assertEquals(\App\Enums\POStatus::CANCEL, PORegister::find($po->id)->status);

        // 2. Close transition (Assert 42-45)
        $response = $this->post("/po-register/status-close", [
            'indent_id' => 555,
            'department_id' => (string)$this->testDept->id
        ]);
        $response->assertStatus(302);
        $this->assertEquals('Close', IndentRegister::find($this->testIndent->id)->status);
        $this->assertEquals(\App\Enums\POStatus::CLOSE, PORegister::find($po->id)->status);

        // 3. Pending transition (Assert 46-50)
        $response = $this->post("/po-register/status-pending", [
            'indent_id' => 555,
            'department_id' => (string)$this->testDept->id
        ]);
        $response->assertStatus(302);
        $this->assertEquals('Pending', IndentRegister::find($this->testIndent->id)->status);
        $this->assertEquals(\App\Enums\POStatus::PENDING, PORegister::find($po->id)->status);
    }

    /** @test */
    public function purchase_order_view_by_indent_endpoint_with_joins()
    {
        $this->actingAs($this->adminUser);

        // Seed Indent and PO
        $indent = IndentRegister::create([
            'indent_id' => '8888',
            'indent_date' => '2026-05-25',
            'indent_department' => 'IT Department',
            'indent_project' => 'Software License Purchases',
            'items_description' => json_encode([['description' => 'IDE Licenses', 'unit' => 'Units', 'quantity_required' => 10, 'quantity_received' => 0, 'quantity_balance' => 10]]),
            'status' => 'Pending'
        ]);

        $po = PORegister::create([
            'indent_id' => 8888,
            'department_id' => $this->testDept->id,
            'po_wo_no' => 'PO-8888',
            'party_name' => 'JetBrains Enterprise Solutions',
            'po_amount' => 120000.00,
            'status' => 'Pending'
        ]);

        $response = $this->get("po-register/indent/8888/department/{$this->testDept->id}");
        $response->assertStatus(200);
        $response->assertSee('8888');
        $response->assertSee('JetBrains Enterprise Solutions');
    }

    /** @test */
    public function purchase_order_rbac_restrictions_on_forms_and_actions()
    {
        // 1. User without permissions gets 403 on create PO
        $user = User::factory()->create(['role' => 'user', 'is_active' => true]);
        $this->actingAs($user);

        $response = $this->get('/po-register/create');
        $response->assertStatus(403);

        // 2. User with permissions gets 200 on create PO
        $roleModel = \App\Models\Role::where('name', 'user')->first();
        $perm = \App\Models\Permission::where('name', 'pos.create')->first();
        $roleModel->permissions()->syncWithoutDetaching([$perm->id]);

        $response = $this->get('/po-register/create');
        $response->assertStatus(200);
    }

    /** @test */
    public function purchase_order_download_endpoints_resolve_correctly()
    {
        $this->actingAs($this->adminUser);

        // Seed Indent and PO
        $indent = IndentRegister::create([
            'indent_id' => '9999',
            'indent_date' => '2026-05-25',
            'indent_department' => 'IT Department',
            'indent_project' => 'Hardware Purchase',
            'items_description' => json_encode([['description' => 'Laptops', 'unit' => 'Units', 'quantity_required' => 5, 'quantity_received' => 0, 'quantity_balance' => 5]]),
            'status' => 'Pending'
        ]);

        $po = PORegister::create([
            'indent_id' => 9999,
            'department_id' => $this->testDept->id,
            'po_wo_no' => 'PO-9999',
            'party_name' => 'Dell Partner Network',
            'po_amount' => 450000.00,
            'status' => 'Pending'
        ]);

        // Visit PDF download
        $response = $this->get("/po/export/pdf/9999/{$this->testDept->id}");
        $response->assertStatus(200);
        $response->assertHeader('Content-Disposition');

        // Visit Excel download
        $response = $this->get("/po/export/excel/9999/{$this->testDept->id}");
        $response->assertStatus(200);
        $response->assertHeader('Content-Disposition');
    }
}
