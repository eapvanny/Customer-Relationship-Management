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
        Schema::table('asm_programs', function (Blueprint $table) {
            $table->dropColumn('outlet');

            $table->unsignedBigInteger('outlet_id')->after('customer_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('asm_programs', function (Blueprint $table) {
            $table->dropColumn('outlet_id');

            $table->unsignedBigInteger('outlet')->after('customer_id');

        });
    }
};
