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
        Schema::create('issuance_record_items', function (Blueprint $table) {
            $table->id('IssuanceItemID');
            $table->unsignedBigInteger('IssuanceID');
            $table->unsignedBigInteger('ItemID');
            $table->integer('QuantityIssued');
            $table->integer('QuantityReturned')->default(0);
            $table->string('Unit', 20);
            $table->enum('ItemType', ['Material', 'Equipment']);
            $table->string('BarcodeNumber')->nullable(); // For equipment tracking
            $table->text('ItemRemarks')->nullable();
            $table->timestamps();

            $table->foreign('IssuanceID')->references('IssuanceID')->on('issuance_records')->onDelete('cascade');
            $table->foreign('ItemID')->references('ItemID')->on('inventory_items')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('issuance_record_items');
    }
};
