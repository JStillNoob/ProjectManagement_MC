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
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn(['Address', 'Barangay', 'City', 'State', 'ZipCode']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->string('Address')->nullable();
            $table->string('Barangay')->nullable();
            $table->string('City')->nullable();
            $table->string('State')->nullable();
            $table->string('ZipCode')->nullable();
        });
    }
};