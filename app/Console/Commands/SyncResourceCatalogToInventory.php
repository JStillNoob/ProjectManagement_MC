<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ResourceCatalog;
use App\Models\InventoryItem;

class SyncResourceCatalogToInventory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'inventory:sync-resource-catalog';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync all resource catalog items to inventory items (creates inventory items for resource catalog items that don\'t have one)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting sync of resource catalog to inventory items...');

        $resourceCatalogItems = ResourceCatalog::all();
        $createdCount = 0;
        $skippedCount = 0;

        foreach ($resourceCatalogItems as $resourceCatalog) {
            // Check if inventory item already exists
            $existingInventoryItem = InventoryItem::where('ResourceCatalogID', $resourceCatalog->ResourceCatalogID)->first();

            if ($existingInventoryItem) {
                $this->line("Skipping: {$resourceCatalog->ItemName} (inventory item already exists)");
                $skippedCount++;
                continue;
            }

            // Create inventory item with zero quantity
            InventoryItem::create([
                'ResourceCatalogID' => $resourceCatalog->ResourceCatalogID,
                'TotalQuantity' => 0,
                'AvailableQuantity' => 0,
                'CommittedQuantity' => 0,
                'MinimumStockLevel' => 0,
                'UnitPrice' => 0,
                'Status' => 'Active',
            ]);

            $this->info("Created inventory item for: {$resourceCatalog->ItemName}");
            $createdCount++;
        }

        $this->info("Sync complete!");
        $this->info("  Created: {$createdCount} inventory items");
        $this->info("  Skipped: {$skippedCount} items (already exist)");
        $this->info("  Total processed: " . ($createdCount + $skippedCount));

        return 0;
    }
}
