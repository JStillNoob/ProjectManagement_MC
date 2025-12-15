<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Skip this migration since ItemID and QuantityRequested columns were already dropped
        // in the previous migration (2025_12_01_060000_create_inventory_request_items_table)
        if (!Schema::hasColumn('inventory_requests', 'ItemID')) {
            return;
        }

        Schema::table('inventory_requests', function (Blueprint $table) {
            // Drop foreign key constraint first (if it exists)
            try {
                $table->dropForeign(['ItemID']);
            } catch (\Exception $e) {
                // Foreign key may already be dropped
            }
            
            // Make ItemID and QuantityRequested nullable
            // These fields are legacy - the system now uses inventory_request_items table
            $table->unsignedBigInteger('ItemID')->nullable()->change();
            $table->decimal('QuantityRequested', 10, 2)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventory_requests', function (Blueprint $table) {
            // Make fields NOT nullable again
            $table->unsignedBigInteger('ItemID')->nullable(false)->change();
            $table->decimal('QuantityRequested', 10, 2)->nullable(false)->change();
            
            // Re-add foreign key constraint
            $table->foreign('ItemID')->references('ItemID')->on('inventory_items')->onDelete('restrict');
        });
    }
};
