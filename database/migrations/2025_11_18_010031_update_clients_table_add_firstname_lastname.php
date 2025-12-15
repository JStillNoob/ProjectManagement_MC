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
            $table->string('FirstName')->nullable()->after('ClientName');
            $table->string('LastName')->nullable()->after('FirstName');
            $table->dropColumn('ContactPerson');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->string('ContactPerson')->nullable()->after('ClientName');
            $table->dropColumn(['FirstName', 'LastName']);
        });
    }
};
