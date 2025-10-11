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
        Schema::table('project_employees', function (Blueprint $table) {
            $table->string('role_in_project')->nullable()->after('EmployeeID');
            $table->date('assigned_date')->nullable()->after('role_in_project');
            $table->enum('status', ['Active', 'Inactive'])->default('Active')->after('assigned_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project_employees', function (Blueprint $table) {
            $table->dropColumn(['role_in_project', 'assigned_date', 'status']);
        });
    }
};