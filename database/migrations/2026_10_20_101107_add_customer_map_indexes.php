<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->index(
                ['latitude', 'longitude'],
                'customers_map_coordinates_index'
            );

            $table->index(
                ['area_id', 'user_id', 'depo_id'],
                'customers_map_filters_index'
            );
        });

        Schema::table('reports', function (Blueprint $table) {
            $table->index(
                ['customer_id', 'date'],
                'reports_customer_date_index'
            );
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropIndex('customers_map_coordinates_index');
            $table->dropIndex('customers_map_filters_index');
        });

        Schema::table('reports', function (Blueprint $table) {
            $table->dropIndex('reports_customer_date_index');
        });
    }
};
