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
        Schema::table('attendance', function (Blueprint $table) {
            // Modify the status enum to include 'Overtime'
            $table->enum('status', ['Present', 'Absent', 'Late', 'Half Day', 'Overtime'])->default('Present')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendance', function (Blueprint $table) {
            // Revert the status enum to original values
            $table->enum('status', ['Present', 'Absent', 'Late', 'Half Day'])->default('Present')->change();
        });
    }
};
