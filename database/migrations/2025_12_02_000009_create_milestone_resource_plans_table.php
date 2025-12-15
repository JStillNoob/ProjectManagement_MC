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
        Schema::create('milestone_resource_plans', function (Blueprint $table) {
            $table->id('PlanID');
            $table->unsignedBigInteger('milestone_id');
            $table->unsignedBigInteger('ItemID');
            $table->integer('PlannedQuantity');
            $table->string('Unit', 20);
            $table->date('NeededDate');
            $table->enum('ResourceType', ['Material', 'Equipment']);
            $table->text('WorkDescription')->nullable();
            $table->enum('Status', ['Planned', 'Requested', 'Approved', 'Issued'])->default('Planned');
            $table->text('Notes')->nullable();
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
        Schema::dropIfExists('milestone_resource_plans');
    }
};
