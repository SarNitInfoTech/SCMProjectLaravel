<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Item;
use App\Models\Project;
use App\Models\Unit;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MasterCrudTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'role' => 'super_admin',
        ]);
    }

    // ==========================================
    // DEPARTMENT MASTERS
    // ==========================================

    public function test_can_view_departments_list()
    {
        $response = $this->actingAs($this->user)->get(route('departments.index'));
        $response->assertStatus(200);
    }

    public function test_can_create_department()
    {
        $response = $this->actingAs($this->user)->post(route('departments.store'), [
            'name' => 'Testing Dept',
        ]);

        $response->assertRedirect(route('departments.index'));
        $this->assertDatabaseHas('departments', ['name' => 'Testing Dept']);
    }

    public function test_can_update_department()
    {
        $dept = Department::create(['name' => 'Old Dept']);

        $response = $this->actingAs($this->user)->patch(route('departments.update', $dept->id), [
            'name' => 'Updated Dept',
        ]);

        $response->assertRedirect(route('departments.index'));
        $this->assertDatabaseHas('departments', ['id' => $dept->id, 'name' => 'Updated Dept']);
    }

    public function test_can_delete_department()
    {
        $dept = Department::create(['name' => 'Dept To Delete']);

        $response = $this->actingAs($this->user)->delete(route('departments.destroy', $dept->id));

        $response->assertRedirect(route('departments.index'));
        $this->assertDatabaseMissing('departments', ['id' => $dept->id]);
    }

    // ==========================================
    // UNIT MASTERS
    // ==========================================

    public function test_can_view_units_list()
    {
        $response = $this->actingAs($this->user)->get(route('units.index'));
        $response->assertStatus(200);
    }

    public function test_can_create_unit()
    {
        $response = $this->actingAs($this->user)->post(route('units.store'), [
            'name' => 'Testing Unit',
        ]);

        $response->assertRedirect(route('units.index'));
        $this->assertDatabaseHas('units', ['name' => 'Testing Unit']);
    }

    public function test_can_update_unit()
    {
        $unit = Unit::create(['name' => 'Old Unit']);

        $response = $this->actingAs($this->user)->put(route('units.update', $unit->id), [
            'name' => 'Updated Unit',
        ]);

        $response->assertRedirect(route('units.index'));
        $this->assertDatabaseHas('units', ['id' => $unit->id, 'name' => 'Updated Unit']);
    }

    public function test_can_delete_unit()
    {
        $unit = Unit::create(['name' => 'Unit To Delete']);

        $response = $this->actingAs($this->user)->delete(route('units.destroy', $unit->id));

        $response->assertRedirect(route('units.index'));
        $this->assertDatabaseMissing('units', ['id' => $unit->id]);
    }

    // ==========================================
    // PROJECT MASTERS
    // ==========================================

    public function test_can_view_projects_list()
    {
        $response = $this->actingAs($this->user)->get(route('projects.index'));
        $response->assertStatus(200);
    }

    public function test_can_create_project()
    {
        $response = $this->actingAs($this->user)->post(route('projects.store'), [
            'name' => 'Testing Project',
        ]);

        $response->assertRedirect(route('projects.index'));
        $this->assertDatabaseHas('projects', ['name' => 'Testing Project']);
    }

    public function test_can_update_project()
    {
        $project = Project::create(['name' => 'Old Project']);

        $response = $this->actingAs($this->user)->put(route('projects.update', $project->id), [
            'name' => 'Updated Project',
        ]);

        $response->assertRedirect(route('projects.index'));
        $this->assertDatabaseHas('projects', ['id' => $project->id, 'name' => 'Updated Project']);
    }

    public function test_can_delete_project()
    {
        $project = Project::create(['name' => 'Project To Delete']);

        $response = $this->actingAs($this->user)->delete(route('projects.destroy', $project->id));

        $response->assertRedirect(route('projects.index'));
        $this->assertDatabaseMissing('projects', ['id' => $project->id]);
    }

    // ==========================================
    // ITEM MASTERS
    // ==========================================

    public function test_can_view_items_list()
    {
        $response = $this->actingAs($this->user)->get(route('items.index'));
        $response->assertStatus(200);
    }

    public function test_can_create_item()
    {
        $response = $this->actingAs($this->user)->post(route('items.store'), [
            'name' => 'Testing Item',
        ]);

        $response->assertRedirect(route('items.index'));
        $this->assertDatabaseHas('items', ['name' => 'Testing Item']);
    }

    public function test_can_update_item()
    {
        $item = Item::create(['name' => 'Old Item']);

        $response = $this->actingAs($this->user)->put(route('items.update', $item->id), [
            'name' => 'Updated Item',
        ]);

        $response->assertRedirect(route('items.index'));
        $this->assertDatabaseHas('items', ['id' => $item->id, 'name' => 'Updated Item']);
    }

    public function test_can_delete_item()
    {
        $item = Item::create(['name' => 'Item To Delete']);

        $response = $this->actingAs($this->user)->delete(route('items.destroy', $item->id));

        $response->assertRedirect(route('items.index'));
        $this->assertDatabaseMissing('items', ['id' => $item->id]);
    }

    // ==========================================
    // VENDOR MASTERS
    // ==========================================

    public function test_can_view_vendors_list()
    {
        $response = $this->actingAs($this->user)->get(route('vendors.list'));
        $response->assertStatus(200);
    }

    public function test_can_create_vendor()
    {
        $response = $this->actingAs($this->user)->post(route('vendors.store'), [
            'name' => 'Testing Vendor',
            'email' => 'vendor@test.com',
            'phone' => '1234567890',
        ]);

        $response->assertRedirect(route('vendors.list'));
        $this->assertDatabaseHas('vendors', ['name' => 'Testing Vendor']);
    }

    public function test_can_update_vendor()
    {
        $vendor = Vendor::create(['name' => 'Old Vendor']);

        $response = $this->actingAs($this->user)->put(route('vendors.update', $vendor->id), [
            'name' => 'Updated Vendor',
        ]);

        $response->assertRedirect(route('vendors.list'));
        $this->assertDatabaseHas('vendors', ['id' => $vendor->id, 'name' => 'Updated Vendor']);
    }

    public function test_can_delete_vendor()
    {
        $vendor = Vendor::create(['name' => 'Vendor To Delete']);

        $response = $this->actingAs($this->user)->delete(route('vendors.destroy', $vendor->id));

        $response->assertRedirect(route('vendors.list'));
        $this->assertDatabaseMissing('vendors', ['id' => $vendor->id]);
    }
}
