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
        Schema::create('inventory_items', function (Blueprint $table) {
            $table->id('ItemID');
            $table->unsignedBigInteger('ItemTypeID');
            $table->string('ItemName');
            $table->string('ItemCode')->unique()->nullable();
            $table->text('Description')->nullable();
            $table->string('Unit')->default('units');
            $table->decimal('TotalQuantity', 10, 2)->default(0);
            $table->decimal('AvailableQuantity', 10, 2)->default(0);
            $table->decimal('MinimumStockLevel', 10, 2)->nullable();
            $table->decimal('UnitPrice', 10, 2)->nullable();
            $table->enum('Status', ['Active', 'Inactive'])->default('Active');
            $table->timestamps();
            
            $table->foreign('ItemTypeID')->references('ItemTypeID')->on('inventory_item_types')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_items');
    }
};
