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
        // Add foreign keys to projects table
        Schema::table('projects', function (Blueprint $table) {
            $table->foreign('ClientID')->references('ClientID')->on('clients')->onDelete('set null');
        });

        // Add foreign keys to employees table
        Schema::table('employees', function (Blueprint $table) {
            $table->unsignedBigInteger('EmployeeTypeID')->nullable();
            $table->foreign('EmployeeTypeID')->references('EmployeeTypeID')->on('employee_types')->onDelete('set null');
        });

        // Add foreign keys to users table
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('EmployeeID')->nullable();
            $table->unsignedBigInteger('RoleID')->nullable();
            $table->foreign('EmployeeID')->references('id')->on('employees')->onDelete('set null');
            $table->foreign('RoleID')->references('RoleID')->on('roles')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove foreign keys from projects table
        Schema::table('projects', function (Blueprint $table) {
            $table->dropForeign(['ClientID']);
        });

        // Remove foreign keys from employees table
        Schema::table('employees', function (Blueprint $table) {
            $table->dropForeign(['EmployeeTypeID']);
            $table->dropColumn('EmployeeTypeID');
        });

        // Remove foreign keys from users table
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['EmployeeID']);
            $table->dropForeign(['RoleID']);
            $table->dropColumn(['EmployeeID', 'RoleID']);
        });
    }
};
