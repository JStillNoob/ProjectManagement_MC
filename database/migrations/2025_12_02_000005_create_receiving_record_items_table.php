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
        Schema::create('receiving_record_items', function (Blueprint $table) {
            $table->id('ReceivingItemID');
            $table->unsignedBigInteger('ReceivingID');
            $table->unsignedBigInteger('POItemID');
            $table->integer('QuantityReceived');
            $table->enum('Condition', ['Good', 'Damaged'])->default('Good');
            $table->integer('QuantityDamaged')->default(0);
            $table->text('ItemRemarks')->nullable();
            $table->timestamps();

            $table->foreign('ReceivingID')->references('ReceivingID')->on('receiving_records')->onDelete('cascade');
            $table->foreign('POItemID')->references('POItemID')->on('purchase_order_items')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('receiving_record_items');
    }
};
