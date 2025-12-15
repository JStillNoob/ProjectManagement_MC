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
        if (!Schema::hasTable('inventory_request_items')) {
            Schema::create('inventory_request_items', function (Blueprint $table) {
                $table->id('RequestItemID');
                $table->unsignedBigInteger('InventoryRequestID');
                $table->unsignedBigInteger('InventoryItemID');
                $table->decimal('QuantityRequested', 10, 2);
                $table->string('UnitOfMeasure', 50)->nullable();
                $table->decimal('CommittedQuantity', 10, 2)->default(0);
                $table->boolean('NeedsPurchase')->default(false);
                $table->timestamps();

                $table->foreign('InventoryRequestID', 'fk_request_items_request')
                    ->references('RequestID')
                    ->on('inventory_requests')
                    ->onDelete('cascade');

                $table->foreign('InventoryItemID', 'fk_request_items_item')
                    ->references('ItemID')
                    ->on('inventory_items')
                    ->onDelete('restrict');
            });
        }

        if (!Schema::hasColumn('inventory_items', 'CommittedQuantity')) {
            Schema::table('inventory_items', function (Blueprint $table) {
                $table->decimal('CommittedQuantity', 10, 2)
                    ->default(0)
                    ->after('AvailableQuantity');
            });
        }

        // Migrate existing single-item requests into the new items table.
        if (Schema::hasColumn('inventory_requests', 'ItemID') &&
            Schema::hasColumn('inventory_requests', 'QuantityRequested')) {
            $now = now();

            $requests = DB::table('inventory_requests')
                ->select('RequestID', 'ItemID', 'QuantityRequested')
                ->whereNotNull('ItemID')
                ->whereNotNull('QuantityRequested')
                ->get();

            if ($requests->isNotEmpty()) {
                $items = DB::table('inventory_items')
                    ->select('ItemID', 'Unit')
                    ->get()
                    ->keyBy('ItemID');

                $insertRows = [];
                foreach ($requests as $request) {
                    if (!$request->ItemID) {
                        continue;
                    }

                    $unit = $items->get($request->ItemID)->Unit ?? null;

                    $insertRows[] = [
                        'InventoryRequestID' => $request->RequestID,
                        'InventoryItemID' => $request->ItemID,
                        'QuantityRequested' => $request->QuantityRequested,
                        'UnitOfMeasure' => $unit,
                        'CommittedQuantity' => 0,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }

                if (!empty($insertRows)) {
                    DB::table('inventory_request_items')->insert($insertRows);
                }
            }

            Schema::table('inventory_requests', function (Blueprint $table) {
                // Drop foreign key before dropping column
                $table->dropForeign('inventory_requests_itemid_foreign');
                $table->dropColumn(['ItemID', 'QuantityRequested']);
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
        if (!Schema::hasColumn('inventory_requests', 'ItemID')) {
            Schema::table('inventory_requests', function (Blueprint $table) {
                $table->unsignedBigInteger('ItemID')->nullable()->after('EmployeeID');
            });
        }

        if (!Schema::hasColumn('inventory_requests', 'QuantityRequested')) {
            Schema::table('inventory_requests', function (Blueprint $table) {
                $table->decimal('QuantityRequested', 10, 2)->nullable()->after('RequestType');
            });
        }

        if (Schema::hasTable('inventory_request_items')) {
            // Restore the first child item back into header columns
            $items = DB::table('inventory_request_items')
                ->select('InventoryRequestID', 'InventoryItemID', 'QuantityRequested')
                ->orderBy('InventoryRequestID')
                ->get();

            foreach ($items as $item) {
                DB::table('inventory_requests')
                    ->where('RequestID', $item->InventoryRequestID)
                    ->update([
                        'ItemID' => $item->InventoryItemID,
                        'QuantityRequested' => $item->QuantityRequested,
                    ]);
            }

            Schema::dropIfExists('inventory_request_items');
        }

        if (Schema::hasColumn('inventory_items', 'CommittedQuantity')) {
            Schema::table('inventory_items', function (Blueprint $table) {
                $table->dropColumn('CommittedQuantity');
            });
        }

        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE inventory_requests MODIFY Status ENUM('Pending','Approved','Rejected','Fulfilled') DEFAULT 'Pending'");
        }
    }
};

