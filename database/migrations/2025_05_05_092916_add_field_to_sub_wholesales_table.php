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
        Schema::table('sub_wholesales', function (Blueprint $table) {
            $table->string('customer')->nullable()->after('user_id');
            $table->string('customer_type')->nullable()->after('customer');
            $table->string('phone')->nullable()->after('customer_type');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sub_wholesales', function (Blueprint $table) {
            $table->string('customer')->nullable()->after('user_id');
            $table->string('customer_type')->nullable()->after('customer');
            $table->string('phone')->nullable()->after('customer_type');

        });
    }
};
