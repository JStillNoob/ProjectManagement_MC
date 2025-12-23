<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add 'Ready for Approval' to the Status enum
        DB::statement("ALTER TABLE inventory_requests MODIFY COLUMN Status ENUM('Pending','Pending - To Order','Approved','Rejected','Fulfilled','Needs PO','Ordered','Ready for Approval') DEFAULT 'Pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert 'Ready for Approval' back to 'Ordered' before removing from enum
        DB::statement("UPDATE inventory_requests SET Status = 'Ordered' WHERE Status = 'Ready for Approval'");
        
        // Remove 'Ready for Approval' from enum
        DB::statement("ALTER TABLE inventory_requests MODIFY COLUMN Status ENUM('Pending','Pending - To Order','Approved','Rejected','Fulfilled','Needs PO','Ordered') DEFAULT 'Pending'");
    }
};



