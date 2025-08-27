<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@nitratextile.org'],
            [
                'name' => 'Nitesh Kumar',
                'email_verified_at' => now(),
                'password' => Hash::make('Nitra@Admin'),
                'role' => 'admin',
                'is_active' => true,
                'bio' => 'System Administrator for NITRA platform.',
                'two_factor_enabled' => false,
                'avatar' => null,
                'designation' => 'Admin',
                'phone' => null,
                'department_id' => null, // update if you add dept
            ],
        );
         User::updateOrCreate(
            ['email' => 'user@nitratextile.org'],
            [
                'name' => 'First Last',
                'email_verified_at' => now(),
                'password' => Hash::make('Admin@1234'),
                'role' => 'admin',
                'is_active' => true,
                'bio' => 'This is a sample bio.',
                'two_factor_enabled' => false,
                'avatar' => null,
                'designation' => 'Data Entry',
                'phone' => null,
                'department_id' => null, // update if you add dept
            ],
        );
    }
}
