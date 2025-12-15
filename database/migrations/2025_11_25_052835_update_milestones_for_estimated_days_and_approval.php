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
        Schema::table('project_milestones', function (Blueprint $table) {
            // Add EstimatedDays (number of days from project start)
            $table->integer('EstimatedDays')->nullable()->after('description');
            
            // Make target_date nullable (will be calculated)
            $table->date('target_date')->nullable()->change();
            
            // Add submission and approval fields
            $table->unsignedBigInteger('SubmittedBy')->nullable()->after('actual_date');
            $table->dateTime('SubmittedAt')->nullable()->after('SubmittedBy');
            $table->unsignedBigInteger('ApprovedBy')->nullable()->after('SubmittedAt');
            $table->dateTime('ApprovedAt')->nullable()->after('ApprovedBy');
            $table->enum('SubmissionStatus', ['Not Submitted', 'Pending Approval', 'Approved'])->default('Not Submitted')->after('ApprovedAt');
            
            // Add foreign keys
            $table->foreign('SubmittedBy')->references('id')->on('employees')->onDelete('set null');
            $table->foreign('ApprovedBy')->references('id')->on('employees')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project_milestones', function (Blueprint $table) {
            $table->dropForeign(['SubmittedBy']);
            $table->dropForeign(['ApprovedBy']);
            $table->dropColumn(['EstimatedDays', 'SubmittedBy', 'SubmittedAt', 'ApprovedBy', 'ApprovedAt', 'SubmissionStatus']);
            // Note: We don't revert target_date to not nullable to avoid data loss
        });
    }
};
