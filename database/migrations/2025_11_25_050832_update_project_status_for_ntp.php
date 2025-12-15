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
        // Update "Upcoming" status to "Waiting for Approval"
        \DB::table('project_status')
            ->where('StatusName', 'Upcoming')
            ->update(['StatusName' => 'Waiting for Approval']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert "Waiting for Approval" back to "Upcoming"
        \DB::table('project_status')
            ->where('StatusName', 'Waiting for Approval')
            ->update(['StatusName' => 'Upcoming']);
    }
};
