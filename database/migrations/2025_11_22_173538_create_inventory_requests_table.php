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
        Schema::create('inventory_requests', function (Blueprint $table) {
            $table->id('RequestID');
            $table->unsignedBigInteger('ProjectID');
            $table->unsignedBigInteger('EmployeeID');
            $table->unsignedBigInteger('ItemID');
            $table->enum('RequestType', ['Material', 'Equipment']);
            $table->decimal('QuantityRequested', 10, 2);
            $table->text('Reason')->nullable();
            $table->enum('Status', ['Pending', 'Approved', 'Rejected', 'Fulfilled'])->default('Pending');
            $table->unsignedBigInteger('ApprovedBy')->nullable();
            $table->dateTime('ApprovedAt')->nullable();
            $table->text('RejectionReason')->nullable();
            $table->unsignedBigInteger('MilestoneID')->nullable();
            $table->timestamps();
            
            $table->foreign('ProjectID')->references('ProjectID')->on('projects')->onDelete('cascade');
            $table->foreign('EmployeeID')->references('id')->on('employees')->onDelete('cascade');
            $table->foreign('ItemID')->references('ItemID')->on('inventory_items')->onDelete('restrict');
            $table->foreign('ApprovedBy')->references('id')->on('users')->onDelete('set null');
            $table->foreign('MilestoneID')->references('milestone_id')->on('project_milestones')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_requests');
    }
};
