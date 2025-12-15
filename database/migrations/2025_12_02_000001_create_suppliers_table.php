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
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id('SupplierID');
            $table->string('SupplierName', 100);
            $table->string('ContactPerson', 100)->nullable();
            $table->string('PhoneNumber', 20)->nullable();
            $table->string('Email', 100)->nullable();
            $table->text('Address')->nullable();
            $table->string('TIN', 50)->nullable(); // Tax Identification Number
            $table->enum('Status', ['Active', 'Inactive'])->default('Active');
            $table->decimal('AverageDeliveryDays', 5, 2)->nullable();
            $table->decimal('QualityRating', 3, 2)->nullable(); // 0.00 to 5.00
            $table->text('Notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
