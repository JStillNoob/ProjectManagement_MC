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
        // Add "Delayed" status if it doesn't exist
        $delayedExists = DB::table('project_status')
            ->where('StatusName', 'Delayed')
            ->exists();

        if (!$delayedExists) {
            DB::table('project_status')->insert([
                'StatusName' => 'Delayed',
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
        // Remove "Delayed" status
        DB::table('project_status')
            ->where('StatusName', 'Delayed')
            ->delete();
    }
};
