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
        Schema::table('reports', function (Blueprint $table) {
            $table->string('area')->nullable()->after('area_id');
            $table->string('customer_name')->nullable()->after('customer_id');
            $table->string('outlet_name')->nullable()->after('outlet_id');
            $table->string('user_name')->nullable()->after('user_id');
            $table->string('cus_type')->nullable()->after('customer_type');
            $table->string('address')->nullable()->after('city');
            $table->string('posm_name1')->nullable()->after('posm');
            $table->string('posm_name2')->nullable()->after('posm2');
            $table->string('posm_name3')->nullable()->after('posm3');
            $table->string('sup_name')->nullable()->after('user_name');
            $table->string('rsm_name')->nullable()->after('sup_name');
            $table->string('status')->nullable()->after('is_seen');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->dropColumn('area');
            $table->dropColumn('customer_name');
            $table->dropColumn('outlet_name');
            $table->dropColumn('user_name');
            $table->dropColumn('cus_type');
            $table->dropColumn('address');
            $table->dropColumn('posm_name1');
            $table->dropColumn('posm_name2');
            $table->dropColumn('posm_name3');
            $table->dropColumn('sup_name');
            $table->dropColumn('rsm_name');
            $table->dropColumn('status');
        });
    }
};
