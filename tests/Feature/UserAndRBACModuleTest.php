<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;

class UserAndRBACModuleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Seed standard roles and permissions before every test
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
    }

    /** @test */
    public function authentication_flows_work_perfectly()
    {
        // 1. Visit Login screen (Assert 1)
        $response = $this->get('/login');
        $response->assertStatus(200);

        // 2. Assert standard login fields are visible in markup (Assert 2-4)
        $response->assertSee('email');
        $response->assertSee('password');
        $response->assertSee('Sign In');

        // 3. Attempt login with invalid credentials (Assert 5-6)
        $response = $this->post('/login', [
            'email' => 'nonexistent@nitratextile.org',
            'password' => 'wrongpassword',
        ]);
        $response->assertStatus(302);
        $response->assertSessionHas('error');

        // 4. Create a test user in DB (Assert 7-9)
        $user = User::factory()->create([
            'email' => 'testuser@nitratextile.org',
            'password' => bcrypt('correctpassword123'),
            'role' => 'user',
            'is_active' => true,
        ]);
        $this->assertDatabaseHas('users', ['email' => 'testuser@nitratextile.org']);
        $this->assertEquals('user', $user->role);
        $this->assertTrue($user->is_active);

        // 5. Attempt login with valid credentials (Assert 10-12)
        $response = $this->post('/login', [
            'email' => 'testuser@nitratextile.org',
            'password' => 'correctpassword123',
        ]);
        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);
        $this->get('/dashboard')->assertStatus(200);
    }

    /** @test */
    public function super_admin_bypasses_every_permission_gate()
    {
        // Create super admin user (Assert 13-14)
        $superAdmin = User::factory()->create(['role' => 'super_admin', 'is_active' => true]);
        $this->assertEquals('super_admin', $superAdmin->role);
        $this->actingAs($superAdmin);

        // Assert 15-20: Super Admin has permissions across all modules
        $this->assertTrue(Gate::allows('dashboard.view'));
        $this->assertTrue(Gate::allows('items.create'));
        $this->assertTrue(Gate::allows('users.delete'));
        $this->assertTrue(Gate::allows('roles.manage'));
        $this->assertTrue($superAdmin->hasPermission('dashboard.view'));
        $this->assertTrue($superAdmin->hasPermission('roles.manage'));
    }

    /** @test */
    public function user_role_has_restricted_permissions()
    {
        // Create standard user (Assert 21-22)
        $user = User::factory()->create(['role' => 'user', 'is_active' => true]);
        $this->assertEquals('user', $user->role);
        $this->actingAs($user);

        // Assert 23-28: User role permissions constraints
        $this->assertTrue(Gate::allows('dashboard.view'));
        $this->assertTrue(Gate::allows('indents.create'));
        $this->assertFalse(Gate::allows('roles.manage'));
        $this->assertFalse(Gate::allows('users.delete'));
        $this->assertFalse($user->hasPermission('roles.manage'));
        $this->assertTrue($user->hasPermission('indents.create'));
    }

    /** @test */
    public function roles_matrix_crud_validations_and_sync()
    {
        $superAdmin = User::factory()->create(['role' => 'super_admin', 'is_active' => true]);
        $this->actingAs($superAdmin);

        // 1. Visit Roles Management List (Assert 29-30)
        $response = $this->get('/roles');
        $response->assertStatus(200);
        $response->assertSee('Roles & Permissions');

        // 2. Create new Role validations (Assert 31-33)
        $response = $this->post('/roles', [
            'name' => '',
            'label' => ''
        ]);
        $response->assertSessionHasErrors(['name', 'label']);

        // 3. Create unique Role success (Assert 34-36)
        $response = $this->post('/roles', [
            'name' => 'inventory_manager',
            'label' => 'Inventory Manager'
        ]);
        $response->assertRedirect('/roles');
        $this->assertDatabaseHas('roles', ['name' => 'inventory_manager']);

        // 4. View Permissions Matrix Sync Screen (Assert 37-39)
        $role = Role::where('name', 'inventory_manager')->first();
        $response = $this->get("/roles/{$role->id}/edit");
        $response->assertStatus(200);
        $response->assertSee('Edit Access Permissions');

        // 5. Synchronize Permissions Matrix pivots (Assert 40-42)
        $perm1 = Permission::where('name', 'inventory.view')->first()->id;
        $perm2 = Permission::where('name', 'inventory.create')->first()->id;
        
        $response = $this->put("/roles/{$role->id}", [
            'label' => 'Inventory Manager Upgraded',
            'permissions' => [$perm1, $perm2]
        ]);
        $response->assertRedirect('/roles');
        $this->assertDatabaseHas('permission_role', [
            'role_id' => $role->id,
            'permission_id' => $perm1
        ]);
        $this->assertDatabaseHas('permission_role', [
            'role_id' => $role->id,
            'permission_id' => $perm2
        ]);
    }

    /** @test */
    public function dynamic_menu_sidebar_renders_based_on_gates()
    {
        // 1. Log in standard user (Assert 43-44)
        $user = User::factory()->create(['role' => 'user', 'is_active' => true]);
        $this->actingAs($user);

        // 2. Render sidebar (Assert 45-47)
        $response = $this->get('/dashboard');
        $response->assertStatus(200);
        $response->assertDontSee('Roles & Access'); // user has no roles.manage permission

        // 3. Log in Super Admin (Assert 48-50)
        $superAdmin = User::factory()->create(['role' => 'super_admin', 'is_active' => true]);
        $this->actingAs($superAdmin);

        $response = $this->get('/dashboard');
        $response->assertSee('Roles'); // Super admin sees it
        $response->assertSee('Users');
    }
}

