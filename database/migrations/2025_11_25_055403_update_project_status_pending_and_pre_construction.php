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
        // Update "Waiting for Approval" to "Pending"
        DB::table('project_status')
            ->where('StatusName', 'Waiting for Approval')
            ->update(['StatusName' => 'Pending']);

        // Add "Pre-Construction" status if it doesn't exist
        $preConstructionExists = DB::table('project_status')
            ->where('StatusName', 'Pre-Construction')
            ->exists();

        if (!$preConstructionExists) {
            DB::table('project_status')->insert([
                'StatusName' => 'Pre-Construction',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert "Pending" back to "Waiting for Approval"
        DB::table('project_status')
            ->where('StatusName', 'Pending')
            ->update(['StatusName' => 'Waiting for Approval']);

        // Remove "Pre-Construction" status
        DB::table('project_status')
            ->where('StatusName', 'Pre-Construction')
            ->delete();
    }
};
