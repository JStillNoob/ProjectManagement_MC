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
        // First remove the foreign key constraint from employees table
        Schema::table('employees', function (Blueprint $table) {
            if (Schema::hasColumn('employees', 'EmployeeTypeID')) {
                $table->dropForeign(['EmployeeTypeID']);
                $table->dropColumn('EmployeeTypeID');
            }
        });

        // Then drop the employee_types table
        Schema::dropIfExists('employee_types');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate employee_types table
        Schema::create('employee_types', function (Blueprint $table) {
            $table->id('EmployeeTypeID');
            $table->string('EmployeeTypeName');
            $table->boolean('hasBenefits')->default(false);
            $table->timestamps();
        });

        // Add back the column and foreign key to employees
        Schema::table('employees', function (Blueprint $table) {
            $table->unsignedBigInteger('EmployeeTypeID')->nullable()->after('PositionID');
            $table->foreign('EmployeeTypeID')->references('EmployeeTypeID')->on('employee_types')->onDelete('set null');
        });
    }
};











