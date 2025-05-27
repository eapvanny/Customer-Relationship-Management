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
        Schema::table('subwholesale_imports', function (Blueprint $table) {
            $table->string('customer_id')->nullable()->after('outlet_id');
            $table->string('customer_type')->nullable()->after('customer_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subwholesale_imports', function (Blueprint $table) {
            $table->dropColumn('customer_id');
            $table->dropColumn('customer_type');

        });
    }
};
