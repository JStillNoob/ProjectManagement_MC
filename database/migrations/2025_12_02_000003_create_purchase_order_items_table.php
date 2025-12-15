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
        Schema::create('purchase_order_items', function (Blueprint $table) {
            $table->id('POItemID');
            $table->unsignedBigInteger('POID');
            $table->unsignedBigInteger('ItemID');
            $table->integer('QuantityOrdered');
            $table->integer('QuantityReceived')->default(0);
            $table->string('Unit', 20);
            $table->decimal('UnitPrice', 12, 2);
            $table->decimal('TotalPrice', 15, 2);
            $table->text('Specifications')->nullable();
            $table->text('Remarks')->nullable();
            $table->timestamps();

            $table->foreign('POID')->references('POID')->on('purchase_orders')->onDelete('cascade');
            $table->foreign('ItemID')->references('ItemID')->on('inventory_items')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_order_items');
    }
};
