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
            $table->renameColumn('user_name', 'ssp_name');
            $table->string('driver_name')->nullable()->after('driver_id');
            $table->string('sup_id')->nullable()->after('user_name');
            $table->string('ssp_id')->nullable()->after('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->renameColumn('ssp_name', 'user_name');
            $table->dropColumn('driver_name');
            $table->dropColumn('sup_id');
            $table->dropColumn('ssp_id');
        });
    }
};
