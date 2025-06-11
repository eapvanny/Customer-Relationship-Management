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
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('rsm_id')->nullable()->after('manager_id');
            $table->unsignedBigInteger('sup_id')->nullable()->after('rsm_id');
            $table->unsignedBigInteger('asm_id')->nullable()->after('sup_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['rsm_id','sup_id', 'asm_id']);
        });
    }
};
