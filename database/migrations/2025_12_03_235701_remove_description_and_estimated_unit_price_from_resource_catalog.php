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
        Schema::table('resource_catalog', function (Blueprint $table) {
            $table->dropColumn(['Description', 'EstimatedUnitPrice']);
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
