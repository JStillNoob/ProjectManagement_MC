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
            // Remove legacy benefit fields that are now handled by the benefits system
            $table->dropColumn([
                'benefits',           // Text field - now handled by employee_benefits table
                'health_insurance',   // String field - now handled by benefits table
                'retirement_plan',    // String field - now handled by benefits table
                'vacation_days',      // Integer field - now handled by benefits table
                'sick_days'          // Integer field - now handled by benefits table
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            // Add back the legacy fields if needed to rollback
            $table->text('benefits')->nullable()->after('emergency_phone');
            $table->string('health_insurance')->nullable()->after('benefits');
            $table->string('retirement_plan')->nullable()->after('health_insurance');
            $table->integer('vacation_days')->nullable()->after('retirement_plan');
            $table->integer('sick_days')->nullable()->after('vacation_days');
        });
    }
};
