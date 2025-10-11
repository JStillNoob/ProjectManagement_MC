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
            // Remove old salary fields
            $table->dropColumn(['daily_salary', 'hourly_rate', 'monthly_salary']);
            
            // Add new salary structure
            $table->decimal('base_salary', 12, 2)->nullable()->after('position');
            $table->enum('salary_type', ['Monthly', 'Daily', 'Hourly'])->default('Monthly')->after('base_salary');
            
            // Remove old address field
            $table->dropColumn('address');
            
            // Add proper address structure
            $table->string('house_number')->nullable()->after('age');
            $table->string('street')->nullable()->after('house_number');
            $table->string('barangay')->nullable()->after('street');
            $table->string('city')->nullable()->after('barangay');
            $table->string('province')->nullable()->after('city');
            $table->string('postal_code', 10)->nullable()->after('province');
            $table->string('country')->default('Philippines')->after('postal_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            // Remove new salary fields
            $table->dropColumn(['base_salary', 'salary_type']);
            
            // Add back old salary fields
            $table->decimal('daily_salary', 10, 2)->nullable()->after('position');
            $table->decimal('hourly_rate', 10, 2)->nullable()->after('daily_salary');
            $table->decimal('monthly_salary', 10, 2)->nullable()->after('hourly_rate');
            
            // Remove new address fields
            $table->dropColumn([
                'house_number',
                'street', 
                'barangay',
                'city',
                'province',
                'postal_code',
                'country'
            ]);
            
            // Add back old address field
            $table->text('address')->after('age');
        });
    }
};
