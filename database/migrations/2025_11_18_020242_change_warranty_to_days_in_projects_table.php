<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            // Drop the old WarrantyEndDate column
            $table->dropColumn('WarrantyEndDate');
        });
        
        Schema::table('projects', function (Blueprint $table) {
            // Add the new WarrantyDays column
            $table->integer('WarrantyDays')->default(0)->after('EndDate');
        });
        
        // Set existing records to 0 (as per user requirement)
        DB::table('projects')->update(['WarrantyDays' => 0]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            // Drop WarrantyDays column
            $table->dropColumn('WarrantyDays');
        });
        
        Schema::table('projects', function (Blueprint $table) {
            // Restore WarrantyEndDate column
            $table->date('WarrantyEndDate')->nullable()->after('EndDate');
        });
    }
};
