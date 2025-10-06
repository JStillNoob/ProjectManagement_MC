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
        Schema::create('projects', function (Blueprint $table) {
            $table->id('ProjectID');
            $table->string('ProjectName', 150)->unique();
            $table->text('ProjectDescription')->nullable();
            $table->string('Client', 150)->nullable();
            $table->date('StartDate');
            $table->date('EndDate');
            $table->unsignedBigInteger('StatusID');
            $table->unsignedBigInteger('ClientID')->nullable();
            $table->date('WarrantyEndDate')->nullable();
            $table->string('StreetAddress', 200)->nullable();
            $table->string('Barangay', 100)->nullable();
            $table->string('City', 100)->nullable();
            $table->string('StateProvince', 100)->nullable();
            $table->string('ZipCode', 10)->nullable();
            $table->timestamps();
            
            // Foreign key constraints
            $table->foreign('StatusID')->references('StatusID')->on('project_status');
            // Note: ClientID foreign key would reference a clients table if it exists
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
