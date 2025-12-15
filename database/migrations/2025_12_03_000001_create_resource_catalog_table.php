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
        Schema::create('resource_catalog', function (Blueprint $table) {
            $table->id('ResourceCatalogID');
            $table->string('ItemName')->unique();
            $table->text('Description')->nullable();
            $table->string('Unit')->default('units');
            $table->enum('Type', ['Equipment', 'Materials'])->default('Materials');
            $table->decimal('EstimatedUnitPrice', 10, 2)->nullable();
            $table->enum('Status', ['Active', 'Inactive'])->default('Active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resource_catalog');
    }
};
