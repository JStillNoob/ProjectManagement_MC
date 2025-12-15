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
        // Drop employee_benefits first (has foreign key to benefits)
        Schema::dropIfExists('employee_benefits');
        
        // Then drop benefits table
        Schema::dropIfExists('benefits');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate benefits table
        Schema::create('benefits', function (Blueprint $table) {
            $table->id('BenefitID');
            $table->string('BenefitName');
            $table->text('Description')->nullable();
            $table->decimal('Amount', 10, 2)->nullable();
            $table->timestamps();
        });

        // Recreate employee_benefits table
        Schema::create('employee_benefits', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('EmployeeID');
            $table->unsignedBigInteger('BenefitID');
            $table->date('StartDate')->nullable();
            $table->date('EndDate')->nullable();
            $table->boolean('IsActive')->default(true);
            $table->timestamps();

            $table->foreign('EmployeeID')->references('EmployeeID')->on('employees')->onDelete('cascade');
            $table->foreign('BenefitID')->references('BenefitID')->on('benefits')->onDelete('cascade');
        });
    }
};











