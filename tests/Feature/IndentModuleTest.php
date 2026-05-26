<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Department;
use App\Models\Project;
use App\Models\Unit;
use App\Models\Item;
use App\Models\IndentTicket;
use App\Models\IndentRegister;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

class IndentModuleTest extends TestCase
{
    use RefreshDatabase;

    protected $adminUser;
    protected $standardUser;
    protected $testDept;
    protected $testProj;
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

        // Setup base data (Assert 1-5)
        $this->testDept = Department::create(['name' => 'Textile Engineering']);
        $this->testProj = Project::create(['name' => 'Weaving Loom Project']);
        $this->testUnit = Unit::create(['name' => 'Meters']);
        $this->testItem = Item::create(['name' => 'Cotton Yarn']);

        $this->assertDatabaseHas('departments', ['name' => 'Textile Engineering']);
        $this->assertDatabaseHas('projects', ['name' => 'Weaving Loom Project']);
        $this->assertDatabaseHas('units', ['name' => 'Meters']);
        $this->assertDatabaseHas('items', ['name' => 'Cotton Yarn']);

        $this->adminUser = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $this->standardUser = User::factory()->create(['role' => 'user', 'is_active' => true]);
    }

    /** @test */
    public function indent_ticket_creation_validation_and_uniqueness()
    {
        $this->actingAs($this->adminUser);

        // 1. Attempt ticket creation with missing department (Assert 6-7)
        $response = $this->post('/indents/store', [
            'indent_id' => 'IND-999'
        ]);
        $response->assertSessionHasErrors(['department_id']);
        
        // 2. Success Ticket creation (Assert 8-11)
        $response = $this->post('/indents/store', [
            'indent_id' => 'IND-999',
            'department_id' => $this->testDept->id
        ]);
        $response->assertRedirect('/indent/form');
        $this->assertDatabaseHas('indent_tickets', [
            'indent_id' => 'IND-999',
            'department_id' => $this->testDept->id
        ]);
        $this->assertDatabaseHas('notifications', [
            'title' => 'New Indent Created'
        ]);

        // 3. Attempt creation of exact duplicate ticket (Assert 12-13)
        $response = $this->post('/indents/store', [
            'indent_id' => 'IND-999',
            'department_id' => $this->testDept->id
        ]);
        $response->assertRedirect('/indents/create');
        $response->assertSessionHas('warning');
    }

    /** @test */
    public function dynamic_token_generator_endpoint()
    {
        $this->actingAs($this->adminUser);

        // 1. Missing department validation (Assert 14-15)
        $response = $this->postJson('/indents/generate-token', []);
        $response->assertStatus(422);

        // 2. Successful generation for fresh department (Assert 16-17)
        $response = $this->postJson('/indents/generate-token', [
            'department' => (string)$this->testDept->id
        ]);
        $response->assertStatus(200);
        $response->assertJson(['indent_id' => 1]);

        // 3. Increment verification based on database tickets (Assert 18-20)
        IndentTicket::create([
            'indent_id' => '1',
            'department_id' => $this->testDept->id
        ]);
        $response = $this->postJson('/indents/generate-token', [
            'department' => (string)$this->testDept->id
        ]);
        $response->assertStatus(200);
        $response->assertJson(['indent_id' => 2]);
    }

    /** @test */
    public function complete_indent_registration_workflow()
    {
        $this->actingAs($this->adminUser);

        // 1. Visit add indent form (Assert 21-23)
        $response = $this->get("/indent/form?indent_id=IND-999&department_id={$this->testDept->id}");
        $response->assertStatus(200);
        $response->assertSee('IND-999');
        $response->assertSee($this->testDept->name);

        // 2. Submit new complete register with JSON items (Assert 24-28)
        $items = [
            [
                'description' => 'Cotton Yarn Grade A',
                'unit' => 'Meters',
                'required' => 500,
                'received' => 0,
                'balance' => 500
            ],
            [
                'description' => 'Polyester Thread',
                'unit' => 'Meters',
                'required' => 200,
                'received' => 50,
                'balance' => 150
            ]
        ];

        $response = $this->post('/indent-register', [
            'indent_id' => 'IND-999',
            'indent_date' => '2026-05-26',
            'indent_department' => $this->testDept->name,
            'indent_project' => 'Weaving Loom Project',
            'items' => $items
        ]);

        $response->assertRedirect('/indent');
        $this->assertDatabaseHas('indent_registers', [
            'indent_id' => 'IND-999',
            'indent_department' => $this->testDept->name,
            'indent_project' => 'Weaving Loom Project',
            'status' => 'Pending'
        ]);

        // Assert 29-30: Verify json string matches
        $reg = IndentRegister::where('indent_id', 'IND-999')->first();
        $this->assertNotNull($reg);
        $decoded = json_decode($reg->items_description, true);
        $this->assertCount(2, $decoded);
        $this->assertEquals('Polyester Thread', $decoded[1]['description']);
    }

    /** @test */
    public function indent_list_rendering_and_search()
    {
        $this->actingAs($this->adminUser);

        // Seed two registers (Assert 31-33)
        IndentRegister::create([
            'indent_id' => 'IND-100',
            'indent_date' => '2026-05-20',
            'indent_department' => $this->testDept->name,
            'indent_project' => 'Weaving Loom Project', // Must match department join
            'items_description' => json_encode([['description' => 'Machine Oil', 'unit' => 'Liters', 'quantity_required' => 10, 'quantity_received' => 0, 'quantity_balance' => 10]]),
            'status' => 'Pending'
        ]);

        IndentRegister::create([
            'indent_id' => 'IND-200',
            'indent_date' => '2026-05-21',
            'indent_department' => $this->testDept->name,
            'indent_project' => 'Weaving Loom Project', // Must match department join
            'items_description' => json_encode([['description' => 'Gearbox', 'unit' => 'Units', 'quantity_required' => 2, 'quantity_received' => 0, 'quantity_balance' => 2]]),
            'status' => 'Close'
        ]);

        // 1. Retrieve listing without parameters (Assert 34-36)
        $response = $this->get('/indent-register');
        $response->assertStatus(200);
        $response->assertSee('IND-100');
        $response->assertSee('IND-200');

        // 2. Search filtering by ID (Assert 37-39)
        $response = $this->get('/indent-register?search=IND-100');
        $response->assertSee('IND-100');
        $response->assertDontSee('IND-200');
    }

    /** @test */
    public function edit_form_and_update_functionality()
    {
        $this->actingAs($this->adminUser);

        // Seed a register (Assert 43-44)
        $reg = IndentRegister::create([
            'indent_id' => 'IND-888',
            'indent_date' => '2026-05-25',
            'indent_department' => $this->testDept->name,
            'indent_project' => 'Weaving Loom Project',
            'items_description' => json_encode([['description' => 'Original Item', 'unit' => 'Meters', 'quantity_required' => 10, 'quantity_received' => 0, 'quantity_balance' => 10]]),
            'status' => 'Pending'
        ]);

        // 1. Retrieve Edit Form (Assert 45-46)
        $response = $this->get("/indent-register/{$reg->id}/edit");
        $response->assertStatus(200);
        $response->assertSee('Weaving Loom Project');

        // 2. Perform Update with new project name and items (Assert 47-50)
        $response = $this->put("/indent-register/{$reg->id}", [
            'indent_id' => 'IND-888',
            'indent_date' => '2026-05-26',
            'indent_department' => $this->testDept->name,
            'indent_project' => 'Upgraded Project',
            'items' => [
                [
                    'description' => 'Modified Item Description',
                    'unit' => 'Meters',
                    'required' => 20,
                    'received' => 2,
                    'balance' => 18
                ]
            ]
        ]);

        $response->assertRedirect('/indent');
        $this->assertDatabaseHas('indent_registers', [
            'id' => $reg->id,
            'indent_project' => 'Upgraded Project'
        ]);

        $updated = IndentRegister::find($reg->id);
        $items = json_decode($updated->items_description, true);
        $this->assertEquals('Modified Item Description', $items[0]['description']);
        $this->assertEquals(20, $items[0]['quantity_required']);
    }
}
