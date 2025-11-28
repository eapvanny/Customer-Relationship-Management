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
        Schema::table('retails', function (Blueprint $table) {
            // Drop the 'location' column
            if (Schema::hasColumn('retails', 'location')) {
                $table->dropColumn('location');
            }

            if (Schema::hasColumn('retails', 'foc_qty')) {
                $table->dropColumn('foc_qty');
            }


            // Rename 'business_type' to 'outlet_type'
            if (Schema::hasColumn('retails', 'business_type')) {
                $table->renameColumn('business_type', 'outlet_type');
                $table->renameColumn('depo_name', 'depot_name');
                $table->renameColumn('depo_contact', 'depot_contact');
                $table->renameColumn('retails_name', 'Retail_name');
                $table->renameColumn('retails_contact', 'retails_contact');
            }

            // Add new columns
            $table->string('province')->nullable()->after('region');
            $table->string('district')->nullable()->after('province');
            $table->string('commune')->nullable()->after('district');
            $table->string('sku')->nullable()->after('display_qty');
            $table->decimal('incentive', 10, 2)->nullable()->after('sku'); // adjust precision if needed
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('retails', function (Blueprint $table) {
              // Add back 'location' column
            $table->string('location')->nullable();

            // Rename 'outlet_type' back to 'business_type'
            if (Schema::hasColumn('retails', 'outlet_type')) {
                $table->renameColumn('outlet_type', 'business_type');
            }

            // Drop newly added columns
            $table->dropColumn(['province', 'district', 'commune', 'sku', 'incentive']);
        });
    }
};
