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
            // Drop the name fields
            $table->dropColumn(['FirstName', 'LastName', 'MiddleName', 'Name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Add back the name fields
            $table->string('FirstName');
            $table->string('LastName');
            $table->string('MiddleName')->nullable();
            $table->string('Name')->nullable();
        });
    }
};
