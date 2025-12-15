<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('inventory_request_items', 'NeedsPurchase')) {
            Schema::table('inventory_request_items', function (Blueprint $table) {
                $table->boolean('NeedsPurchase')->default(false)->after('CommittedQuantity');
            });
        }

        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE inventory_requests MODIFY Status ENUM('Pending','Pending - To Order','Approved','Rejected','Fulfilled','Needs PO') DEFAULT 'Pending'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('inventory_request_items', 'NeedsPurchase')) {
            Schema::table('inventory_request_items', function (Blueprint $table) {
                $table->dropColumn('NeedsPurchase');
            });
        }

        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE inventory_requests MODIFY Status ENUM('Pending','Approved','Rejected','Fulfilled','Needs PO') DEFAULT 'Pending'");
        }
    }
};


