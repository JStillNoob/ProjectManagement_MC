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
        // Check if columns exist before trying to drop them (might already be dropped)
        Schema::table('resource_catalog', function (Blueprint $table) {
            if (Schema::hasColumn('resource_catalog', 'Description')) {
                $table->dropColumn('Description');
            }
            if (Schema::hasColumn('resource_catalog', 'EstimatedUnitPrice')) {
                $table->dropColumn('EstimatedUnitPrice');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('resource_catalog', function (Blueprint $table) {
            $table->text('Description')->nullable()->after('ItemName');
            $table->decimal('EstimatedUnitPrice', 10, 2)->nullable()->after('Type');
        });
    }
};
