<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\InventoryItem;
use App\Models\InventoryItemType;

class InventoryItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get item type IDs
        $materialType = InventoryItemType::where('TypeName', 'Materials')->first();
        $equipmentType = InventoryItemType::where('TypeName', 'Equipment')->first();

        if (!$materialType || !$equipmentType) {
            $this->command->error('Please run InventoryItemTypeSeeder first!');
            return;
        }

        // Materials
        $materials = [
            [
                'ItemTypeID' => $materialType->ItemTypeID,
                'ItemName' => 'Portland Cement',
                'ItemCode' => 'MAT-001',
                'Description' => 'Type I Portland cement for general construction',
                'Unit' => 'bags',
                'TotalQuantity' => 500,
                'AvailableQuantity' => 500,
                'MinimumStockLevel' => 50,
                'UnitPrice' => 280.00,
                'Status' => 'Active',
            ],
            [
                'ItemTypeID' => $materialType->ItemTypeID,
                'ItemName' => 'Steel Bars (10mm)',
                'ItemCode' => 'MAT-002',
                'Description' => 'Deformed steel reinforcement bars 10mm diameter',
                'Unit' => 'pcs',
                'TotalQuantity' => 1000,
                'AvailableQuantity' => 1000,
                'MinimumStockLevel' => 100,
                'UnitPrice' => 185.00,
                'Status' => 'Active',
            ],
            [
                'ItemTypeID' => $materialType->ItemTypeID,
                'ItemName' => 'Steel Bars (12mm)',
                'ItemCode' => 'MAT-003',
                'Description' => 'Deformed steel reinforcement bars 12mm diameter',
                'Unit' => 'pcs',
                'TotalQuantity' => 800,
                'AvailableQuantity' => 800,
                'MinimumStockLevel' => 80,
                'UnitPrice' => 265.00,
                'Status' => 'Active',
            ],
            [
                'ItemTypeID' => $materialType->ItemTypeID,
                'ItemName' => 'Gravel (3/4)',
                'ItemCode' => 'MAT-004',
                'Description' => 'Crushed gravel 3/4 inch for concrete mix',
                'Unit' => 'cu.m',
                'TotalQuantity' => 100,
                'AvailableQuantity' => 100,
                'MinimumStockLevel' => 10,
                'UnitPrice' => 1200.00,
                'Status' => 'Active',
            ],
            [
                'ItemTypeID' => $materialType->ItemTypeID,
                'ItemName' => 'Sand',
                'ItemCode' => 'MAT-005',
                'Description' => 'Fine sand for concrete and masonry work',
                'Unit' => 'cu.m',
                'TotalQuantity' => 150,
                'AvailableQuantity' => 150,
                'MinimumStockLevel' => 15,
                'UnitPrice' => 800.00,
                'Status' => 'Active',
            ],
            [
                'ItemTypeID' => $materialType->ItemTypeID,
                'ItemName' => 'Hollow Blocks (4")',
                'ItemCode' => 'MAT-006',
                'Description' => 'Concrete hollow blocks 4 inches',
                'Unit' => 'pcs',
                'TotalQuantity' => 2000,
                'AvailableQuantity' => 2000,
                'MinimumStockLevel' => 200,
                'UnitPrice' => 15.00,
                'Status' => 'Active',
            ],
            [
                'ItemTypeID' => $materialType->ItemTypeID,
                'ItemName' => 'Hollow Blocks (6")',
                'ItemCode' => 'MAT-007',
                'Description' => 'Concrete hollow blocks 6 inches',
                'Unit' => 'pcs',
                'TotalQuantity' => 1500,
                'AvailableQuantity' => 1500,
                'MinimumStockLevel' => 150,
                'UnitPrice' => 22.00,
                'Status' => 'Active',
            ],
            [
                'ItemTypeID' => $materialType->ItemTypeID,
                'ItemName' => 'Common Nails (2")',
                'ItemCode' => 'MAT-008',
                'Description' => 'Common wire nails 2 inches',
                'Unit' => 'kg',
                'TotalQuantity' => 100,
                'AvailableQuantity' => 100,
                'MinimumStockLevel' => 10,
                'UnitPrice' => 85.00,
                'Status' => 'Active',
            ],
            [
                'ItemTypeID' => $materialType->ItemTypeID,
                'ItemName' => 'Plywood (1/2")',
                'ItemCode' => 'MAT-009',
                'Description' => 'Marine plywood 1/2 inch thick 4x8 ft',
                'Unit' => 'sheets',
                'TotalQuantity' => 200,
                'AvailableQuantity' => 200,
                'MinimumStockLevel' => 20,
                'UnitPrice' => 850.00,
                'Status' => 'Active',
            ],
            [
                'ItemTypeID' => $materialType->ItemTypeID,
                'ItemName' => 'Tie Wire (#16)',
                'ItemCode' => 'MAT-010',
                'Description' => 'GI tie wire gauge 16 for rebar tying',
                'Unit' => 'kg',
                'TotalQuantity' => 50,
                'AvailableQuantity' => 50,
                'MinimumStockLevel' => 5,
                'UnitPrice' => 95.00,
                'Status' => 'Active',
            ],
        ];

        // Equipment
        $equipment = [
            [
                'ItemTypeID' => $equipmentType->ItemTypeID,
                'ItemName' => 'Concrete Mixer',
                'ItemCode' => 'EQP-001',
                'Description' => 'One bagger concrete mixer machine',
                'Unit' => 'unit',
                'TotalQuantity' => 5,
                'AvailableQuantity' => 5,
                'MinimumStockLevel' => 1,
                'UnitPrice' => 45000.00,
                'Status' => 'Active',
            ],
            [
                'ItemTypeID' => $equipmentType->ItemTypeID,
                'ItemName' => 'Backhoe Loader',
                'ItemCode' => 'EQP-002',
                'Description' => 'JCB backhoe loader for excavation',
                'Unit' => 'unit',
                'TotalQuantity' => 2,
                'AvailableQuantity' => 2,
                'MinimumStockLevel' => 1,
                'UnitPrice' => 2500000.00,
                'Status' => 'Active',
            ],
            [
                'ItemTypeID' => $equipmentType->ItemTypeID,
                'ItemName' => 'Boom Truck (10 tons)',
                'ItemCode' => 'EQP-003',
                'Description' => '10-ton boom truck for lifting and hauling',
                'Unit' => 'unit',
                'TotalQuantity' => 2,
                'AvailableQuantity' => 2,
                'MinimumStockLevel' => 1,
                'UnitPrice' => 3500000.00,
                'Status' => 'Active',
            ],
            [
                'ItemTypeID' => $equipmentType->ItemTypeID,
                'ItemName' => 'Vibrator (Concrete)',
                'ItemCode' => 'EQP-004',
                'Description' => 'Concrete vibrator for compaction',
                'Unit' => 'unit',
                'TotalQuantity' => 8,
                'AvailableQuantity' => 8,
                'MinimumStockLevel' => 2,
                'UnitPrice' => 15000.00,
                'Status' => 'Active',
            ],
            [
                'ItemTypeID' => $equipmentType->ItemTypeID,
                'ItemName' => 'Welding Machine',
                'ItemCode' => 'EQP-005',
                'Description' => 'Arc welding machine 200A',
                'Unit' => 'unit',
                'TotalQuantity' => 6,
                'AvailableQuantity' => 6,
                'MinimumStockLevel' => 2,
                'UnitPrice' => 12000.00,
                'Status' => 'Active',
            ],
            [
                'ItemTypeID' => $equipmentType->ItemTypeID,
                'ItemName' => 'Angle Grinder',
                'ItemCode' => 'EQP-006',
                'Description' => '4-inch angle grinder for cutting and grinding',
                'Unit' => 'unit',
                'TotalQuantity' => 10,
                'AvailableQuantity' => 10,
                'MinimumStockLevel' => 3,
                'UnitPrice' => 3500.00,
                'Status' => 'Active',
            ],
            [
                'ItemTypeID' => $equipmentType->ItemTypeID,
                'ItemName' => 'Electric Drill',
                'ItemCode' => 'EQP-007',
                'Description' => 'Heavy duty electric drill with hammer function',
                'Unit' => 'unit',
                'TotalQuantity' => 8,
                'AvailableQuantity' => 8,
                'MinimumStockLevel' => 2,
                'UnitPrice' => 4500.00,
                'Status' => 'Active',
            ],
            [
                'ItemTypeID' => $equipmentType->ItemTypeID,
                'ItemName' => 'Scaffolding Set',
                'ItemCode' => 'EQP-008',
                'Description' => 'Steel scaffolding frame set (5 levels)',
                'Unit' => 'set',
                'TotalQuantity' => 20,
                'AvailableQuantity' => 20,
                'MinimumStockLevel' => 5,
                'UnitPrice' => 8000.00,
                'Status' => 'Active',
            ],
            [
                'ItemTypeID' => $equipmentType->ItemTypeID,
                'ItemName' => 'Wheelbarrow',
                'ItemCode' => 'EQP-009',
                'Description' => 'Heavy duty steel wheelbarrow',
                'Unit' => 'unit',
                'TotalQuantity' => 15,
                'AvailableQuantity' => 15,
                'MinimumStockLevel' => 5,
                'UnitPrice' => 2500.00,
                'Status' => 'Active',
            ],
            [
                'ItemTypeID' => $equipmentType->ItemTypeID,
                'ItemName' => 'Safety Harness',
                'ItemCode' => 'EQP-010',
                'Description' => 'Full body safety harness with lanyard',
                'Unit' => 'set',
                'TotalQuantity' => 25,
                'AvailableQuantity' => 25,
                'MinimumStockLevel' => 10,
                'UnitPrice' => 2800.00,
                'Status' => 'Active',
            ],
        ];

        // Create materials
        foreach ($materials as $material) {
            InventoryItem::create($material);
        }

        // Create equipment
        foreach ($equipment as $equip) {
            InventoryItem::create($equip);
        }

        $this->command->info('Inventory items seeded successfully: ' . count($materials) . ' materials and ' . count($equipment) . ' equipment.');
    }
}










