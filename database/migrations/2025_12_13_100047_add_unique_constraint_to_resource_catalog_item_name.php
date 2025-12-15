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
        // Check if unique constraint already exists
        $indexName = 'resource_catalog_itemname_unique';
        $indexes = \DB::select("SHOW INDEXES FROM resource_catalog WHERE Key_name = ?", [$indexName]);
        
        if (empty($indexes)) {
            Schema::table('resource_catalog', function (Blueprint $table) {
                $table->unique('ItemName');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('resource_catalog', function (Blueprint $table) {
            $table->dropUnique(['ItemName']);
        });
    }
};
