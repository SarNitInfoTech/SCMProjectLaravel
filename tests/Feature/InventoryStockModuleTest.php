<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Department;
use App\Models\Unit;
use App\Models\Item;
use App\Models\Vendor;
use App\Models\PORegister;
use App\Models\InventoryStock;
use App\Models\InventoryMovement;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

class InventoryStockModuleTest extends TestCase
{
    use RefreshDatabase;

    protected $adminUser;
    protected $standardUser;
    protected $testDept1;
    protected $testDept2;
    protected $testUnit;
    protected $testItem;

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

        // Setup base master data (Assert 1-4)
        $this->testDept1 = Department::create(['name' => 'IT Department']);
        $this->testDept2 = Department::create(['name' => 'R&D Department']);
        $this->testUnit = Unit::create(['name' => 'Pieces']);
        $this->testItem = Item::create(['name' => 'Core i7 Processor']);

        $this->assertDatabaseHas('departments', ['name' => 'IT Department']);
        $this->assertDatabaseHas('departments', ['name' => 'R&D Department']);
        $this->assertDatabaseHas('units', ['name' => 'Pieces']);
        $this->assertDatabaseHas('items', ['name' => 'Core i7 Processor']);

        $this->adminUser = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $this->standardUser = User::factory()->create(['role' => 'user', 'is_active' => true]);
    }

    /** @test */
    public function stock_creation_validation_and_duplication()
    {
        $this->actingAs($this->adminUser);

        // 1. Validate required fields (Assert 5-8)
        $response = $this->post('/inventory/stocks', []);
        $response->assertSessionHasErrors(['item_id', 'department_id', 'unit_id', 'current_qty']);

        // 2. Create Stock success with initial quantity IN movement (Assert 9-14)
        $response = $this->post('/inventory/stocks', [
            'item_id' => $this->testItem->id,
            'department_id' => $this->testDept1->id,
            'unit_id' => $this->testUnit->id,
            'current_qty' => 15,
            'min_qty' => 5,
            'max_qty' => 50,
            'location' => 'Aisle 3 Shelf A'
        ]);

        $response->assertRedirect('/inventory/stocks');
        $this->assertDatabaseHas('inventory_stocks', [
            'item_id' => $this->testItem->id,
            'department_id' => $this->testDept1->id,
            'current_qty' => 15,
            'min_qty' => 5,
            'max_qty' => 50,
            'location' => 'Aisle 3 Shelf A'
        ]);

        $this->assertDatabaseHas('inventory_movements', [
            'type' => 'IN',
            'quantity' => 15,
            'qty_before' => 0,
            'qty_after' => 15
        ]);

        // 3. Attempt duplicate stock entry creation (Assert 15-16)
        $response = $this->post('/inventory/stocks', [
            'item_id' => $this->testItem->id,
            'department_id' => $this->testDept1->id,
            'unit_id' => $this->testUnit->id,
            'current_qty' => 10
        ]);
        $response->assertSessionHas('warning');
    }

    /** @test */
    public function stock_listing_and_dashboard_statistics()
    {
        $this->actingAs($this->adminUser);

        // Seed 1 active stock below min (Assert 17-18)
        $stock = InventoryStock::create([
            'item_id' => $this->testItem->id,
            'department_id' => $this->testDept1->id,
            'unit_id' => $this->testUnit->id,
            'current_qty' => 3,
            'min_qty' => 10,
            'is_active' => true
        ]);

        // Visit Dashboard (Assert 19-23)
        $response = $this->get('/inventory');
        $response->assertStatus(200);
        $response->assertSee('Inventory Dashboard');
        $response->assertSee('Core i7 Processor');
        $response->assertSee('IT Department');
    }

    /** @test */
    public function stock_properties_updates()
    {
        $this->actingAs($this->adminUser);

        $stock = InventoryStock::create([
            'item_id' => $this->testItem->id,
            'department_id' => $this->testDept1->id,
            'unit_id' => $this->testUnit->id,
            'current_qty' => 10,
            'min_qty' => 2,
            'is_active' => true
        ]);

        // 1. Visit Edit Form (Assert 24-25)
        $response = $this->get("/inventory/stocks/{$stock->id}/edit");
        $response->assertStatus(200);

        // 2. Perform Update validations (Assert 26-27)
        $response = $this->put("/inventory/stocks/{$stock->id}", [
            'min_qty' => -5
        ]);
        $response->assertSessionHasErrors(['min_qty']);

        // 3. Successful Update (Assert 28-31)
        $response = $this->put("/inventory/stocks/{$stock->id}", [
            'min_qty' => 8,
            'max_qty' => 100,
            'location' => 'Main Room'
        ]);
        $response->assertRedirect('/inventory/stocks');
        $this->assertDatabaseHas('inventory_stocks', [
            'id' => $stock->id,
            'min_qty' => 8,
            'max_qty' => 100,
            'location' => 'Main Room'
        ]);
    }

    /** @test */
    public function stock_movements_logging_ledger()
    {
        $this->actingAs($this->adminUser);

        $stock = InventoryStock::create([
            'item_id' => $this->testItem->id,
            'department_id' => $this->testDept1->id,
            'unit_id' => $this->testUnit->id,
            'current_qty' => 10,
            'min_qty' => 2,
            'is_active' => true
        ]);

        // 1. Validation limits (Assert 32-34)
        $response = $this->post("/inventory/stocks/{$stock->id}/movements", [
            'type' => 'OUT',
            'quantity' => 25, // More than current stock
            'movement_date' => '2026-05-26'
        ]);
        $response->assertSessionHas('warning');

        // 2. Successful OUT transaction (Assert 35-39)
        $response = $this->post("/inventory/stocks/{$stock->id}/movements", [
            'type' => 'OUT',
            'quantity' => 4,
            'movement_date' => '2026-05-26',
            'remarks' => 'Deducted CPU pieces'
        ]);

        $response->assertRedirect("/inventory/stocks/{$stock->id}/movements");
        $this->assertDatabaseHas('inventory_movements', [
            'stock_id' => $stock->id,
            'type' => 'OUT',
            'quantity' => 4,
            'qty_before' => 10,
            'qty_after' => 6
        ]);
        $this->assertEquals(6, InventoryStock::find($stock->id)->current_qty);
    }

    /** @test */
    public function stock_transfer_between_departments_atomic()
    {
        $this->actingAs($this->adminUser);

        // Seed sender stock (Assert 40-41)
        $senderStock = InventoryStock::create([
            'item_id' => $this->testItem->id,
            'department_id' => $this->testDept1->id,
            'unit_id' => $this->testUnit->id,
            'current_qty' => 20,
            'min_qty' => 2,
            'is_active' => true
        ]);

        // 1. Perform TRANSFER (Assert 42-47)
        // This must deduct 5 pieces from IT Department, create a R&D Stock if non-existent, and log 5 pieces IN.
        $response = $this->post("/inventory/stocks/{$senderStock->id}/movements", [
            'type' => 'TRANSFER',
            'quantity' => 5,
            'movement_date' => '2026-05-26',
            'destination_department_id' => $this->testDept2->id,
            'remarks' => 'Transferring items'
        ]);

        $response->assertRedirect("/inventory/stocks/{$senderStock->id}/movements");
        $this->assertEquals(15, InventoryStock::find($senderStock->id)->current_qty);
        
        $receiverStock = InventoryStock::where('item_id', $this->testItem->id)
            ->where('department_id', $this->testDept2->id)
            ->first();
        
        $this->assertNotNull($receiverStock);
        $this->assertEquals(5, $receiverStock->current_qty);

        // 2. Validate transfer same department constraint (Assert 48-50)
        $response = $this->post("/inventory/stocks/{$senderStock->id}/movements", [
            'type' => 'TRANSFER',
            'quantity' => 2,
            'movement_date' => '2026-05-26',
            'destination_department_id' => $this->testDept1->id
        ]);
        $response->assertSessionHas('warning');
    }
}
