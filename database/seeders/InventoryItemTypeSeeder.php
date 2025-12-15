<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\InventoryItemType;

class InventoryItemTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            [
                'TypeName' => 'Materials',
                'Description' => 'Consumable items that are permanently decreased when used (e.g., cement, steel bars, nails)',
            ],
            [
                'TypeName' => 'Equipment',
                'Description' => 'Tools and machinery that are not consumable and use check-in/check-out process (e.g., hammers, drills, shovels, backhoe, boom truck)',
            ],
        ];

        foreach ($types as $type) {
            InventoryItemType::create($type);
        }
    }
}
