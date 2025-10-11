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
        Schema::table('employees', function (Blueprint $table) {
            // Remove salary_type field
            $table->dropColumn('salary_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            // Add back salary_type field if needed to rollback
            $table->enum('salary_type', ['Monthly', 'Daily', 'Hourly'])->default('Monthly')->after('base_salary');
        });
    }
};
