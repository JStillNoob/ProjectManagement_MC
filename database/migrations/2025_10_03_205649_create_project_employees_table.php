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
        Schema::create('project_employees', function (Blueprint $table) {
            $table->id('ProjectEmployeeID');
            $table->unsignedBigInteger('ProjectID');
            $table->unsignedBigInteger('EmployeeID');
            $table->timestamps();
            
            $table->foreign('ProjectID')->references('ProjectID')->on('projects')->onDelete('cascade');
            $table->foreign('EmployeeID')->references('id')->on('employees')->onDelete('cascade');
            $table->unique(['ProjectID', 'EmployeeID']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_employees');
    }
};
