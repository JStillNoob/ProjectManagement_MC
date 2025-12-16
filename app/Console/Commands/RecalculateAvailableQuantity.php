<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\InventoryItem;
use App\Models\IssuanceRecordItem;
use Illuminate\Support\Facades\DB;

class RecalculateAvailableQuantity extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'inventory:recalculate-available-quantity';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recalculate AvailableQuantity for all inventory items based on existing issuance records';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting recalculation of AvailableQuantity...');
        
        DB::beginTransaction();
        try {
            $items = InventoryItem::with('resourceCatalog')->get();
            $updatedCount = 0;
            
            foreach ($items as $item) {
                // Get all issued quantities for this item (excluding returned quantities)
                // Only count items from issued records
                $totalIssued = IssuanceRecordItem::where('ItemID', $item->ItemID)
                    ->whereHas('issuanceRecord', function($query) {
                        $query->where('Status', 'Issued');
                    })
                    ->get()
                    ->sum(function($issuanceItem) {
                        // Net issued = QuantityIssued - QuantityReturned
                        $returned = $issuanceItem->QuantityReturned ?? 0;
                        return $issuanceItem->QuantityIssued - $returned;
                    });
                
                // For materials: AvailableQuantity = TotalQuantity - CommittedQuantity - Issued
                // For equipment: AvailableQuantity = TotalQuantity - CommittedQuantity - Issued (but TotalQuantity doesn't decrease)
                // Since we're recalculating, we'll use: AvailableQuantity = TotalQuantity - CommittedQuantity - totalIssued
                $calculatedAvailable = $item->TotalQuantity - ($item->CommittedQuantity ?? 0) - $totalIssued;
                
                // Ensure it doesn't go below 0
                $calculatedAvailable = max(0, $calculatedAvailable);
                
                // Only update if different (with small tolerance for floating point)
                if (abs($item->AvailableQuantity - $calculatedAvailable) > 0.01) {
                    $oldValue = $item->AvailableQuantity;
                    $item->AvailableQuantity = $calculatedAvailable;
                    $item->save();
                    
                    $itemName = $item->resourceCatalog->ItemName ?? 'Item #' . $item->ItemID;
                    $this->line("Updated {$itemName}: {$oldValue} → {$calculatedAvailable} (Issued: {$totalIssued})");
                    $updatedCount++;
                }
            }
            
            DB::commit();
            
            $this->info("Recalculation complete! Updated {$updatedCount} items.");
            return Command::SUCCESS;
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Error during recalculation: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
