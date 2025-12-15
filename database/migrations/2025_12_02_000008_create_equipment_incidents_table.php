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
        Schema::create('equipment_incidents', function (Blueprint $table) {
            $table->id('IncidentID');
            $table->unsignedBigInteger('ItemID'); // Equipment item
            $table->unsignedBigInteger('ProjectID')->nullable();
            $table->unsignedBigInteger('EquipmentAssignmentID')->nullable(); // From project_milestone_equipment
            $table->enum('IncidentType', ['Damage', 'Loss', 'Theft', 'Malfunction'])->default('Damage');
            $table->date('IncidentDate');
            $table->unsignedBigInteger('ResponsibleEmployeeID')->nullable();
            $table->text('Description');
            $table->decimal('EstimatedCost', 12, 2)->nullable();
            $table->enum('Status', ['Reported', 'Under Investigation', 'Resolved', 'Closed'])->default('Reported');
            $table->string('PhotoPath')->nullable();
            $table->text('ActionTaken')->nullable();
            $table->timestamps();

            $table->foreign('ItemID')->references('ItemID')->on('inventory_items')->onDelete('restrict');
            $table->foreign('ProjectID')->references('ProjectID')->on('projects')->onDelete('set null');
            $table->foreign('EquipmentAssignmentID')->references('EquipmentAssignmentID')->on('project_milestone_equipment')->onDelete('set null');
            $table->foreign('ResponsibleEmployeeID')->references('id')->on('employees')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipment_incidents');
    }
};
