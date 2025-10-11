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
        Schema::create('benefits', function (Blueprint $table) {
            $table->id('BenefitID');
            $table->string('BenefitName');
            $table->text('Description')->nullable();
            $table->decimal('Amount', 10, 2)->nullable(); // For fixed amount benefits
            $table->decimal('Percentage', 5, 2)->nullable(); // For percentage-based benefits
            $table->enum('BenefitType', ['Fixed', 'Percentage', 'Mandatory'])->default('Fixed');
            $table->boolean('IsActive')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('benefits');
    }
};
