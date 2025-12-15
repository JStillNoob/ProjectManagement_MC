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
        Schema::create('issuance_records', function (Blueprint $table) {
            $table->id('IssuanceID');
            $table->string('IssuanceNumber', 50)->unique();
            $table->unsignedBigInteger('RequestID')->nullable(); // Links to InventoryRequest
            $table->unsignedBigInteger('ProjectID');
            $table->unsignedBigInteger('MilestoneID')->nullable();
            $table->date('IssuanceDate');
            $table->unsignedBigInteger('IssuedBy'); // EmployeeID
            $table->unsignedBigInteger('ReceivedBy'); // EmployeeID (Foreman)
            $table->enum('Status', ['Issued', 'Returned', 'Partially Returned'])->default('Issued');
            $table->text('Purpose')->nullable();
            $table->text('Remarks')->nullable();
            $table->string('SignaturePath')->nullable(); // Digital signature
            $table->timestamps();

            $table->foreign('RequestID')->references('RequestID')->on('inventory_requests')->onDelete('set null');
            $table->foreign('ProjectID')->references('ProjectID')->on('projects')->onDelete('restrict');
            $table->foreign('MilestoneID')->references('milestone_id')->on('project_milestones')->onDelete('set null');
            $table->foreign('IssuedBy')->references('id')->on('employees')->onDelete('restrict');
            $table->foreign('ReceivedBy')->references('id')->on('employees')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('issuance_records');
    }
};
