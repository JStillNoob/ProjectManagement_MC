<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::getConnection()->getDriverName() === 'mysql') {
            // Add 'Mixed' option to RequestType enum
            DB::statement("ALTER TABLE inventory_requests MODIFY RequestType ENUM('Material', 'Equipment', 'Mixed') NOT NULL DEFAULT 'Material'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() === 'mysql') {
            // Revert to original enum without 'Mixed'
            DB::statement("ALTER TABLE inventory_requests MODIFY RequestType ENUM('Material', 'Equipment') NOT NULL DEFAULT 'Material'");
        }
    }
};


