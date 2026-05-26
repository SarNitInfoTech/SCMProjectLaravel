<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;
use App\Models\User;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Seed Roles
        $roles = [
            'super_admin' => 'Super Admin',
            'admin'       => 'Admin',
            'user'        => 'User',
        ];

        $roleModels = [];
        foreach ($roles as $name => $label) {
            $roleModels[$name] = Role::firstOrCreate(
                ['name' => $name],
                ['label' => $label]
            );
        }

        // 2. Seed Permissions
        $permissions = [
            // Dashboard
            ['name' => 'dashboard.view', 'label' => 'View Dashboard', 'module' => 'Dashboard'],

            // Items
            ['name' => 'items.view', 'label' => 'View Items', 'module' => 'Items'],
            ['name' => 'items.create', 'label' => 'Create Items', 'module' => 'Items'],
            ['name' => 'items.edit', 'label' => 'Edit Items', 'module' => 'Items'],
            ['name' => 'items.delete', 'label' => 'Delete Items', 'module' => 'Items'],

            // Vendors
            ['name' => 'vendors.view', 'label' => 'View Vendors', 'module' => 'Vendors'],
            ['name' => 'vendors.create', 'label' => 'Create Vendors', 'module' => 'Vendors'],
            ['name' => 'vendors.edit', 'label' => 'Edit Vendors', 'module' => 'Vendors'],
            ['name' => 'vendors.delete', 'label' => 'Delete Vendors', 'module' => 'Vendors'],

            // Users
            ['name' => 'users.view', 'label' => 'View Users', 'module' => 'Users'],
            ['name' => 'users.create', 'label' => 'Create Users', 'module' => 'Users'],
            ['name' => 'users.edit', 'label' => 'Edit Users', 'module' => 'Users'],
            ['name' => 'users.delete', 'label' => 'Delete Users', 'module' => 'Users'],

            // Departments
            ['name' => 'departments.view', 'label' => 'View Departments', 'module' => 'Departments'],
            ['name' => 'departments.create', 'label' => 'Create Departments', 'module' => 'Departments'],
            ['name' => 'departments.edit', 'label' => 'Edit Departments', 'module' => 'Departments'],
            ['name' => 'departments.delete', 'label' => 'Delete Departments', 'module' => 'Departments'],

            // Department Heads
            ['name' => 'department_heads.view', 'label' => 'View Department Heads', 'module' => 'Department Heads'],
            ['name' => 'department_heads.create', 'label' => 'Create Department Heads', 'module' => 'Department Heads'],
            ['name' => 'department_heads.edit', 'label' => 'Edit Department Heads', 'module' => 'Department Heads'],
            ['name' => 'department_heads.delete', 'label' => 'Delete Department Heads', 'module' => 'Department Heads'],

            // Units
            ['name' => 'units.view', 'label' => 'View Units', 'module' => 'Units'],
            ['name' => 'units.create', 'label' => 'Create Units', 'module' => 'Units'],
            ['name' => 'units.edit', 'label' => 'Edit Units', 'module' => 'Units'],
            ['name' => 'units.delete', 'label' => 'Delete Units', 'module' => 'Units'],

            // Projects
            ['name' => 'projects.view', 'label' => 'View Projects', 'module' => 'Projects'],
            ['name' => 'projects.create', 'label' => 'Create Projects', 'module' => 'Projects'],
            ['name' => 'projects.edit', 'label' => 'Edit Projects', 'module' => 'Projects'],
            ['name' => 'projects.delete', 'label' => 'Delete Projects', 'module' => 'Projects'],

            // Indents
            ['name' => 'indents.view', 'label' => 'View Indents', 'module' => 'Indents'],
            ['name' => 'indents.create', 'label' => 'Create Indents', 'module' => 'Indents'],
            ['name' => 'indents.edit', 'label' => 'Edit Indents', 'module' => 'Indents'],
            ['name' => 'indents.delete', 'label' => 'Delete Indents', 'module' => 'Indents'],

            // PO Registers
            ['name' => 'pos.view', 'label' => 'View Purchase Orders', 'module' => 'Purchase Orders'],
            ['name' => 'pos.create', 'label' => 'Create Purchase Orders', 'module' => 'Purchase Orders'],
            ['name' => 'pos.edit', 'label' => 'Edit Purchase Orders', 'module' => 'Purchase Orders'],
            ['name' => 'pos.delete', 'label' => 'Delete Purchase Orders', 'module' => 'Purchase Orders'],

            // Reports
            ['name' => 'reports.view', 'label' => 'View Reports', 'module' => 'Reports'],

            // Inventory
            ['name' => 'inventory.view', 'label' => 'View Inventory', 'module' => 'Inventory'],
            ['name' => 'inventory.create', 'label' => 'Create/Add Inventory Stock/Movements', 'module' => 'Inventory'],
            ['name' => 'inventory.edit', 'label' => 'Edit Inventory Stock/Movements', 'module' => 'Inventory'],
            ['name' => 'inventory.delete', 'label' => 'Delete Inventory Stock/Movements', 'module' => 'Inventory'],

            // Roles Management
            ['name' => 'roles.manage', 'label' => 'Manage Roles and Access Control', 'module' => 'Roles & Permissions'],
        ];

        $permissionModels = [];
        foreach ($permissions as $perm) {
            $permissionModels[$perm['name']] = Permission::firstOrCreate(
                ['name' => $perm['name']],
                ['label' => $perm['label'], 'module' => $perm['module']]
            );
        }

        // 3. Assign Default Permissions to Admin Role
        // Admin gets all permissions by default except roles management (reserved for super admin)
        $adminPermissions = collect($permissionModels)
            ->reject(fn($model, $name) => $name === 'roles.manage')
            ->pluck('id')
            ->toArray();

        $roleModels['admin']->permissions()->sync($adminPermissions);

        // 4. Assign Default Permissions to User Role
        // User gets only view permissions and some create/edit permissions (e.g. indents creation, but not deletion, and no masters management)
        $userPermissions = [
            'dashboard.view',
            'items.view',
            'vendors.view',
            'departments.view',
            'units.view',
            'projects.view',
            'indents.view',
            'indents.create',
            'indents.edit',
            'pos.view',
            'reports.view',
            'inventory.view',
            'inventory.create',
        ];

        $userPermIds = collect($userPermissions)
            ->map(fn($name) => $permissionModels[$name]->id ?? null)
            ->filter()
            ->toArray();

        $roleModels['user']->permissions()->sync($userPermIds);

        // 5. Update main administrator user to super_admin role
        $adminUser = User::where('email', 'admin@nitratextile.org')->first();
        if ($adminUser) {
            $adminUser->update(['role' => 'super_admin']);
        }
    }
}
