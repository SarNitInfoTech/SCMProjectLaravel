<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Item;
use App\Models\Project;
use App\Models\Unit;
use Illuminate\Database\Seeder;

class MasterDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Seed Departments (including NITRA and NTC as requested)
        $departments = [
            'NITRA',
            'NTC',
            'Procurement',
            'Quality Assurance',
            'Production',
            'Spinning',
            'Weaving',
            'IT Support'
        ];

        foreach ($departments as $dept) {
            Department::firstOrCreate(['name' => $dept]);
        }

        // 2. Seed Measurement Units
        $units = [
            'Kilograms',
            'Meters',
            'Grams',
            'Bales',
            'Boxes',
            'Pieces',
            'Rolls',
            'Litres',
            'Packs'
        ];

        foreach ($units as $unit) {
            Unit::firstOrCreate(['name' => $unit]);
        }

        // 3. Seed Projects
        $projects = [
            'NITRA Research Hub',
            'NTC Spinning Expansion',
            'Quality Testing Facility',
            'Solar Power Integration',
            'Weaving Shed Automations'
        ];

        foreach ($projects as $proj) {
            Project::firstOrCreate(['name' => $proj]);
        }

        // 4. Seed Catalog Items
        $items = [
            'Cotton Fiber Grade A',
            'Polyester Filament 150D',
            'Nylon Thread 210D',
            'Weaving Dye Blue',
            'Spindle Lubricant Oil',
            'Carded Cotton Yarn 30s',
            'Thermal Imaging Camera',
            'Industrial Cleaning Solvent'
        ];

        foreach ($items as $item) {
            Item::firstOrCreate(['name' => $item]);
        }
    }
}
