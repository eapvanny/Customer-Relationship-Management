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
        Schema::table('display_subwholesales', function (Blueprint $table) {
            $table->string('sup_name')->nullable()->after('asm_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('display_subwholesales', function (Blueprint $table) {
            $table->dropColumn('sup_name');
        });
    }
};
