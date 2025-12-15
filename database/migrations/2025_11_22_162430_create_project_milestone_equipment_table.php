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
        Schema::create('project_milestone_equipment', function (Blueprint $table) {
            $table->id('EquipmentAssignmentID');
            $table->unsignedBigInteger('milestone_id');
            $table->unsignedBigInteger('ItemID');
            $table->decimal('QuantityAssigned', 10, 2);
            $table->date('DateAssigned')->default(now());
            $table->date('DateReturned')->nullable();
            $table->enum('Status', ['Assigned', 'Returned', 'Damaged', 'Missing'])->default('Assigned');
            $table->text('Remarks')->nullable();
            $table->text('ReturnRemarks')->nullable();
            $table->timestamps();
            
            $table->foreign('milestone_id')->references('milestone_id')->on('project_milestones')->onDelete('cascade');
            $table->foreign('ItemID')->references('ItemID')->on('inventory_items')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_milestone_equipment');
    }
};
