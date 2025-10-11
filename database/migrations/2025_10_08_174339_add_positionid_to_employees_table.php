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
        // Add PositionID column to employees table if it doesn't exist
        if (!Schema::hasColumn('employees', 'PositionID')) {
            Schema::table('employees', function (Blueprint $table) {
                $table->unsignedBigInteger('PositionID')->nullable()->after('position');
            });
        }

        // Add foreign key constraint to roles table (which will be renamed to positions later)
        if (Schema::hasTable('roles')) {
            Schema::table('employees', function (Blueprint $table) {
                $table->foreign('PositionID')->references('RoleID')->on('roles')->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropForeign(['PositionID']);
            $table->dropColumn('PositionID');
        });
    }
};
