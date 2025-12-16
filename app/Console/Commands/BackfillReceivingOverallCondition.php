<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ReceivingRecord;

class BackfillReceivingOverallCondition extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'receiving:backfill-overall-condition';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backfill OverallCondition for existing receiving records based on their items';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting backfill of OverallCondition for receiving records...');

        // Get all receiving records that don't have OverallCondition set or have null
        $receivingRecords = ReceivingRecord::whereNull('OverallCondition')
            ->orWhere('OverallCondition', '')
            ->with('items')
            ->get();

        $updatedCount = 0;
        $skippedCount = 0;

        foreach ($receivingRecords as $record) {
            if ($record->items->isEmpty()) {
                $this->line("Skipping ReceivingID {$record->ReceivingID}: No items found");
                $skippedCount++;
                continue;
            }

            // Track conditions
            $hasGoodItems = false;
            $hasDamagedItems = false;

            foreach ($record->items as $item) {
                if ($item->Condition == 'Good') {
                    $hasGoodItems = true;
                } elseif ($item->Condition == 'Damaged') {
                    $hasDamagedItems = true;
                }
            }

            // Determine overall condition
            $overallCondition = 'Good';
            if ($hasDamagedItems && $hasGoodItems) {
                $overallCondition = 'Mixed';
            } elseif ($hasDamagedItems) {
                $overallCondition = 'Damaged';
            }

            // Update the record
            $record->OverallCondition = $overallCondition;
            $record->save();

            $this->info("Updated ReceivingID {$record->ReceivingID}: OverallCondition = {$overallCondition}");
            $updatedCount++;
        }

        $this->info("Backfill complete!");
        $this->info("  Updated: {$updatedCount} receiving records");
        $this->info("  Skipped: {$skippedCount} records (no items)");

        return 0;
    }
}
