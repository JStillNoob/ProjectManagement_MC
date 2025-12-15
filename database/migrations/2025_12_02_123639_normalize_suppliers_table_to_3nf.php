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
        Schema::table('suppliers', function (Blueprint $table) {
            // Remove non-3NF columns
            $table->dropColumn(['TIN', 'AverageDeliveryDays', 'QualityRating', 'Notes']);
            
            // Split ContactPerson into FirstName and LastName
            $table->string('ContactFirstName', 50)->nullable()->after('SupplierName');
            $table->string('ContactLastName', 50)->nullable()->after('ContactFirstName');
            $table->dropColumn('ContactPerson');
            
            // Normalize Address into separate components
            $table->string('Street', 255)->nullable()->after('Email');
            $table->string('City', 100)->nullable()->after('Street');
            $table->string('Province', 100)->nullable()->after('City');
            $table->string('PostalCode', 20)->nullable()->after('Province');
            $table->dropColumn('Address');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('suppliers', function (Blueprint $table) {
            // Restore original columns
            $table->string('TIN', 50)->nullable();
            $table->decimal('AverageDeliveryDays', 5, 2)->nullable();
            $table->decimal('QualityRating', 3, 2)->nullable();
            $table->text('Notes')->nullable();
            
            // Restore ContactPerson
            $table->string('ContactPerson', 100)->nullable()->after('SupplierName');
            $table->dropColumn(['ContactFirstName', 'ContactLastName']);
            
            // Restore Address
            $table->text('Address')->nullable()->after('Email');
            $table->dropColumn(['Street', 'City', 'Province', 'PostalCode']);
        });
    }
};
