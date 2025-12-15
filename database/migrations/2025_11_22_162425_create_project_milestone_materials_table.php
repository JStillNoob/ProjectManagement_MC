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
        Schema::create('project_milestone_materials', function (Blueprint $table) {
            $table->id('MaterialUsageID');
            $table->unsignedBigInteger('milestone_id');
            $table->unsignedBigInteger('ItemID');
            $table->decimal('QuantityUsed', 10, 2);
            $table->date('DateUsed')->default(now());
            $table->text('Remarks')->nullable();
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
        Schema::dropIfExists('project_milestone_materials');
    }
};
