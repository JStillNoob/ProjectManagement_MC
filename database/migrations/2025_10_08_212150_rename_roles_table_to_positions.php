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
        // Check if positions table already exists
        if (Schema::hasTable('positions')) {
            // If positions table exists, drop it first
            Schema::dropIfExists('positions');
        }
        
        // Rename the roles table to positions
        Schema::rename('roles', 'positions');
        
        // Rename columns to be more consistent
        Schema::table('positions', function (Blueprint $table) {
            $table->renameColumn('RoleID', 'PositionID');
            $table->renameColumn('RoleName', 'PositionName');
        });
        
        // Update foreign key references in employees table
        Schema::table('employees', function (Blueprint $table) {
            $table->dropForeign(['PositionID']);
            $table->foreign('PositionID')->references('PositionID')->on('positions')->onDelete('set null');
        });
        
        // Update foreign key references in users table
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['RoleID']);
            $table->renameColumn('RoleID', 'PositionID');
            $table->foreign('PositionID')->references('PositionID')->on('positions')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverse the changes
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['PositionID']);
            $table->renameColumn('PositionID', 'RoleID');
            $table->foreign('RoleID')->references('RoleID')->on('roles')->onDelete('set null');
        });
        
        Schema::table('employees', function (Blueprint $table) {
            $table->dropForeign(['PositionID']);
            $table->foreign('PositionID')->references('RoleID')->on('roles')->onDelete('set null');
        });
        
        Schema::table('roles', function (Blueprint $table) {
            $table->renameColumn('PositionID', 'RoleID');
            $table->renameColumn('PositionName', 'RoleName');
        });
        
        Schema::rename('positions', 'roles');
    }
};
