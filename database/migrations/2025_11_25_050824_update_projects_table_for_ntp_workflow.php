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
        Schema::table('projects', function (Blueprint $table) {
            // Make StartDate and EndDate nullable (initially null, set when NTP approved)
            $table->date('StartDate')->nullable()->change();
            $table->date('EndDate')->nullable()->change();
            
            // Add EstimatedAccomplishDays (number of days from NTP start)
            $table->integer('EstimatedAccomplishDays')->nullable()->after('EndDate');
            
            // Add NTPStartDate (actual start date when NTP is approved)
            $table->date('NTPStartDate')->nullable()->after('EstimatedAccomplishDays');
            
            // Add NTPAttachment (file path for NTP document/image)
            $table->string('NTPAttachment', 500)->nullable()->after('NTPStartDate');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn(['EstimatedAccomplishDays', 'NTPStartDate', 'NTPAttachment']);
            // Note: We don't revert StartDate and EndDate to not nullable to avoid data loss
        });
    }
};
