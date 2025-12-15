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
        // Create employee_status table
        Schema::create('employee_status', function (Blueprint $table) {
            $table->increments('EmployeeStatusID');
            $table->string('StatusName');
            $table->string('Description')->nullable();
            $table->timestamps();
        });

        // Modify employees table - add employee_status_id and remove status
        Schema::table('employees', function (Blueprint $table) {
            $table->unsignedInteger('employee_status_id')->nullable()->after('PositionID');
            $table->foreign('employee_status_id')->references('EmployeeStatusID')->on('employee_status')->onDelete('set null');
        });

        // Remove the old status column
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Add back the status column
        Schema::table('employees', function (Blueprint $table) {
            $table->enum('status', ['Active', 'Inactive'])->default('Active');
        });

        // Remove foreign key and column
        Schema::table('employees', function (Blueprint $table) {
            $table->dropForeign(['employee_status_id']);
            $table->dropColumn('employee_status_id');
        });

        // Drop employee_status table
        Schema::dropIfExists('employee_status');
    }
};
