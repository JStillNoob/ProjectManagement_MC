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
        // Add 'Ordered' to the Status enum
        DB::statement("ALTER TABLE inventory_requests MODIFY COLUMN Status ENUM('Pending','Pending - To Order','Approved','Rejected','Fulfilled','Needs PO','Ordered') DEFAULT 'Pending'");
        
        // Update any incorrectly set 'Approved' statuses that have a PO linked to 'Ordered'
        DB::statement("UPDATE inventory_requests SET Status = 'Pending - To Order' WHERE Status = 'Approved' AND RequestID IN (SELECT RequestID FROM purchase_orders)");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert 'Ordered' back to 'Pending - To Order' before removing from enum
        DB::statement("UPDATE inventory_requests SET Status = 'Pending - To Order' WHERE Status = 'Ordered'");
        
        // Remove 'Ordered' from enum
        DB::statement("ALTER TABLE inventory_requests MODIFY COLUMN Status ENUM('Pending','Pending - To Order','Approved','Rejected','Fulfilled','Needs PO') DEFAULT 'Pending'");
    }
};
