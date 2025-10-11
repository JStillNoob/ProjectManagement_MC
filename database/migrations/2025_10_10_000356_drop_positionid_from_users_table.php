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
        Schema::table('users', function (Blueprint $table) {
            // Drop the foreign key constraint first
            $table->dropForeign(['PositionID']);
            // Then drop the PositionID field
            $table->dropColumn('PositionID');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Add back the PositionID field
            $table->unsignedBigInteger('PositionID')->nullable();
            // Add back the foreign key constraint
            $table->foreign('PositionID')->references('PositionID')->on('positions')->onDelete('set null');
        });
    }
};
