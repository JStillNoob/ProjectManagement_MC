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
        Schema::table('inventory_items', function (Blueprint $table) {
            // Drop old ItemTypeID foreign key and column
            $table->dropForeign(['ItemTypeID']);
            $table->dropColumn('ItemTypeID');
            
            // Add ResourceCatalogID reference
            $table->unsignedBigInteger('ResourceCatalogID')->after('ItemID');
            $table->foreign('ResourceCatalogID')
                  ->references('ResourceCatalogID')
                  ->on('resource_catalog')
                  ->onDelete('restrict');
            
            // Remove ItemName, Unit, Description as they come from resource_catalog
            $table->dropColumn(['ItemName', 'Unit', 'Description']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventory_items', function (Blueprint $table) {
            // Drop ResourceCatalogID
            $table->dropForeign(['ResourceCatalogID']);
            $table->dropColumn('ResourceCatalogID');
            
            // Restore old structure
            $table->unsignedBigInteger('ItemTypeID')->after('ItemID');
            $table->foreign('ItemTypeID')
                  ->references('ItemTypeID')
                  ->on('inventory_item_types')
                  ->onDelete('restrict');
            
            $table->string('ItemName')->after('ItemTypeID');
            $table->string('Unit')->default('units')->after('Description');
            $table->text('Description')->nullable()->after('ItemName');
        });
    }
};
