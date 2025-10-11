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
        Schema::create('employee_benefits', function (Blueprint $table) {
            $table->id('EmployeeBenefitID');
            $table->unsignedBigInteger('EmployeeID');
            $table->unsignedBigInteger('BenefitID');
            $table->date('EffectiveDate')->default(now());
            $table->date('ExpiryDate')->nullable();
            $table->decimal('Amount', 10, 2)->nullable(); // Override amount if needed
            $table->decimal('Percentage', 5, 2)->nullable(); // Override percentage if needed
            $table->boolean('IsActive')->default(true);
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('EmployeeID')->references('id')->on('employees')->onDelete('cascade');
            $table->foreign('BenefitID')->references('BenefitID')->on('benefits')->onDelete('cascade');

            // Unique constraint to prevent duplicate benefit assignments
            $table->unique(['EmployeeID', 'BenefitID', 'EffectiveDate'], 'unique_employee_benefit_effective_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_benefits');
    }
};
