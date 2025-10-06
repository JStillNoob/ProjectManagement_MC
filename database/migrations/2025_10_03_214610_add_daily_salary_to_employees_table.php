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
            $table->decimal('daily_salary', 10, 2)->nullable()->after('position');
            $table->decimal('hourly_rate', 10, 2)->nullable()->after('daily_salary');
            $table->decimal('monthly_salary', 10, 2)->nullable()->after('hourly_rate');
            $table->string('availability')->nullable()->after('monthly_salary');
            $table->string('contact_number')->nullable()->after('availability');
            $table->string('emergency_contact')->nullable()->after('contact_number');
            $table->string('emergency_phone')->nullable()->after('emergency_contact');
            $table->text('benefits')->nullable()->after('emergency_phone');
            $table->string('health_insurance')->nullable()->after('benefits');
            $table->string('retirement_plan')->nullable()->after('health_insurance');
            $table->integer('vacation_days')->nullable()->after('retirement_plan');
            $table->integer('sick_days')->nullable()->after('vacation_days');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn([
                'daily_salary',
                'hourly_rate', 
                'monthly_salary',
                'availability',
                'contact_number',
                'emergency_contact',
                'emergency_phone',
                'benefits',
                'health_insurance',
                'retirement_plan',
                'vacation_days',
                'sick_days'
            ]);
        });
    }
};