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
        Schema::table('employees', function (Blueprint $table) {
            // Remove availability and emergency contact fields
            $table->dropColumn([
                'availability',
                'emergency_contact',
                'emergency_phone'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            // Add back the removed fields if needed to rollback
            $table->string('availability')->nullable()->after('contact_number');
            $table->string('emergency_contact')->nullable()->after('availability');
            $table->string('emergency_phone')->nullable()->after('emergency_contact');
        });
    }
};
