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
        Schema::table('users', function (Blueprint $table) {
            // Add Name field to store employee full name
            $table->string('Name')->nullable()->after('Email');
            
            // Remove Username field
            $table->dropColumn('Username');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Add back Username field
            $table->string('Username')->unique()->after('Email');
            
            // Remove Name field
            $table->dropColumn('Name');
        });
    }
};
