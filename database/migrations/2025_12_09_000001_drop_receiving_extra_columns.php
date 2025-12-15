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
        Schema::table('receiving_records', function (Blueprint $table) {
            if (Schema::hasColumn('receiving_records', 'DeliveryReceiptNumber')) {
                $table->dropColumn('DeliveryReceiptNumber');
            }
            if (Schema::hasColumn('receiving_records', 'OverallCondition')) {
                $table->dropColumn('OverallCondition');
            }
            if (Schema::hasColumn('receiving_records', 'Remarks')) {
                $table->dropColumn('Remarks');
            }
            if (Schema::hasColumn('receiving_records', 'AttachmentPath')) {
                $table->dropColumn('AttachmentPath');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('receiving_records', function (Blueprint $table) {
            $table->string('DeliveryReceiptNumber', 50)->nullable();
            $table->enum('OverallCondition', ['Good', 'Damaged', 'Mixed'])->default('Good');
            $table->text('Remarks')->nullable();
            $table->string('AttachmentPath')->nullable();
        });
    }
};





