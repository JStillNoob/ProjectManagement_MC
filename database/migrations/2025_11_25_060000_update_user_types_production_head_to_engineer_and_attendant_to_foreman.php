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
        // Update "Production Head" to "Engineer" (case-insensitive)
        DB::table('tblusertype')
            ->whereRaw('LOWER(UserType) = ?', ['production head'])
            ->update(['UserType' => 'Engineer']);

        // Update "Attendant Officer" to "Foreman" (case-insensitive, handles "attenndant" typo)
        DB::table('tblusertype')
            ->where(function($query) {
                $query->whereRaw('LOWER(UserType) = ?', ['attendant officer'])
                      ->orWhereRaw('LOWER(UserType) = ?', ['attenndant officer']);
            })
            ->update(['UserType' => 'Foreman']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert "Engineer" back to "Production Head"
        DB::table('tblusertype')
            ->where('UserType', 'Engineer')
            ->update(['UserType' => 'Production Head']);

        // Revert "Foreman" back to "Attendant Officer"
        DB::table('tblusertype')
            ->where('UserType', 'Foreman')
            ->update(['UserType' => 'Attendant Officer']);
    }
};

