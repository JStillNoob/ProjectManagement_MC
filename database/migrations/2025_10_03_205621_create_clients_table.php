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
        Schema::create('clients', function (Blueprint $table) {
            $table->id('ClientID');
            $table->string('ClientName');
            $table->string('ContactPerson')->nullable();
            $table->string('ContactNumber')->nullable();
            $table->string('Email')->nullable();
            $table->string('Address')->nullable();
            $table->string('Barangay')->nullable();
            $table->string('City')->nullable();
            $table->string('State')->nullable();
            $table->string('ZipCode')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
