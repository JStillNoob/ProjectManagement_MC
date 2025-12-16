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
        Schema::table('project_milestone_equipment', function (Blueprint $table) {
            $table->unsignedBigInteger('ReturnedBy')->nullable()->after('ReturnRemarks');
            $table->foreign('ReturnedBy')->references('id')->on('employees')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project_milestone_equipment', function (Blueprint $table) {
            $table->dropForeign(['ReturnedBy']);
            $table->dropColumn('ReturnedBy');
        });
    }
};
