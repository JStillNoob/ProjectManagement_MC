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
        Schema::create('receiving_records', function (Blueprint $table) {
            $table->id('ReceivingID');
            $table->unsignedBigInteger('POID');
            $table->date('ReceivedDate');
            $table->unsignedBigInteger('ReceivedBy'); // EmployeeID
            $table->string('DeliveryReceiptNumber', 50)->nullable();
            $table->enum('OverallCondition', ['Good', 'Damaged', 'Mixed'])->default('Good');
            $table->text('Remarks')->nullable();
            $table->string('AttachmentPath')->nullable(); // Photo of delivery receipt
            $table->timestamps();

            $table->foreign('POID')->references('POID')->on('purchase_orders')->onDelete('restrict');
            $table->foreign('ReceivedBy')->references('id')->on('employees')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('receiving_records');
    }
};
