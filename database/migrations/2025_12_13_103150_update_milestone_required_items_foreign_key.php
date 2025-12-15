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
        Schema::table('milestone_required_items', function (Blueprint $table) {
            // Drop the old foreign key constraint
            $table->dropForeign(['item_id']);
            
            // Add new foreign key constraint to resource_catalog
            $table->foreign('item_id')
                  ->references('ResourceCatalogID')
                  ->on('resource_catalog')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('milestone_required_items', function (Blueprint $table) {
            // Drop the new foreign key constraint
            $table->dropForeign(['item_id']);
            
            // Restore the old foreign key constraint
            $table->foreign('item_id')
                  ->references('ItemID')
                  ->on('inventory_items')
                  ->onDelete('cascade');
        });
    }
};
