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
        if (Schema::hasColumn('inventory_requests', 'DateNeeded')) {
            Schema::table('inventory_requests', function (Blueprint $table) {
                $table->dropColumn('DateNeeded');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasColumn('inventory_requests', 'DateNeeded')) {
            Schema::table('inventory_requests', function (Blueprint $table) {
                $table->date('DateNeeded')->nullable()->after('MilestoneID');
            });
        }
    }
};








