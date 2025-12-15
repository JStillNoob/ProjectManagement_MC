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
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropColumn(['ExpectedDeliveryDate', 'TotalAmount', 'Terms', 'Notes']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->date('ExpectedDeliveryDate')->nullable()->after('OrderDate');
            $table->decimal('TotalAmount', 15, 2)->default(0)->after('Status');
            $table->text('Terms')->nullable()->after('DateSent');
            $table->text('Notes')->nullable()->after('Terms');
        });
    }
};
