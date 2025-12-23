<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('milestone_required_items', function (Blueprint $table) {
            if (!Schema::hasColumn('milestone_required_items', 'unit')) {
                $table->string('unit', 50)->nullable()->after('estimated_quantity');
            }
        });
    }

    public function down(): void
    {
        Schema::table('milestone_required_items', function (Blueprint $table) {
            if (Schema::hasColumn('milestone_required_items', 'unit')) {
                $table->dropColumn('unit');
            }
        });
    }
};









