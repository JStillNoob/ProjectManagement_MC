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
        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        // Truncate tables in the correct order
        DB::table('inventory_request_items')->truncate();
        DB::table('inventory_requests')->truncate();
        DB::table('milestone_required_items')->truncate();
        DB::table('project_milestone_materials')->truncate();
        DB::table('project_milestone_equipment')->truncate();
        DB::table('milestone_proof_images')->truncate();
        DB::table('project_milestones')->truncate();
        DB::table('project_employees')->truncate();
        DB::table('projects')->truncate();

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // Remove PONumber column from purchase_orders (if it exists)
        if (Schema::hasColumn('purchase_orders', 'PONumber')) {
            Schema::table('purchase_orders', function (Blueprint $table) {
                $table->dropColumn('PONumber');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Re-add PONumber column
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->string('PONumber', 50)->unique()->after('POID');
        });
    }
};
