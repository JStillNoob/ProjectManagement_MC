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
        Schema::create('milestone_required_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('milestone_id');
            $table->unsignedBigInteger('item_id');
            $table->decimal('estimated_quantity', 10, 2)->default(0);
            $table->timestamps();

            $table->foreign('milestone_id')->references('milestone_id')->on('project_milestones')->onDelete('cascade');
            $table->foreign('item_id')->references('ItemID')->on('inventory_items')->onDelete('cascade');
            
            $table->unique(['milestone_id', 'item_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('milestone_required_items');
    }
};
