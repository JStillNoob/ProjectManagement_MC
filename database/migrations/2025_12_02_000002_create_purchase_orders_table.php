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
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id('POID');
            $table->string('PONumber', 50)->unique();
            $table->unsignedBigInteger('SupplierID');
            $table->unsignedBigInteger('RequestID')->nullable(); // Link to InventoryRequest
            $table->date('OrderDate');
            $table->date('ExpectedDeliveryDate')->nullable();
            $table->enum('Status', ['Draft', 'Sent', 'Partially Received', 'Completed', 'Cancelled'])->default('Draft');
            $table->decimal('TotalAmount', 15, 2)->default(0);
            $table->unsignedBigInteger('CreatedBy'); // EmployeeID
            $table->unsignedBigInteger('ApprovedBy')->nullable(); // EmployeeID
            $table->timestamp('ApprovedAt')->nullable();
            $table->date('DateSent')->nullable();
            $table->text('Terms')->nullable(); // Payment terms, delivery terms, etc.
            $table->text('Notes')->nullable();
            $table->string('PDFPath')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('SupplierID')->references('SupplierID')->on('suppliers')->onDelete('restrict');
            $table->foreign('RequestID')->references('RequestID')->on('inventory_requests')->onDelete('set null');
            $table->foreign('CreatedBy')->references('id')->on('employees')->onDelete('restrict');
            $table->foreign('ApprovedBy')->references('id')->on('employees')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_orders');
    }
};
