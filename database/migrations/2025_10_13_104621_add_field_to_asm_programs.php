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
            $table->dropColumn(['foc_qty', 'posm', 'qty']);
            $table->string('foc_250_qty')->nullable()->after('photo_foc');
            $table->string('foc_350_qty')->nullable()->after('foc_250_qty');
            $table->string('foc_600_qty')->nullable()->after('foc_350_qty');
            $table->string('foc_1500_qty')->nullable()->after('foc_600_qty');
            $table->string('foc_other')->nullable()->after('foc_1500_qty');
            $table->string('foc_other_qty')->nullable()->after('foc_other');

            $table->string('posm_1')->nullable()->after('foc_other_qty');
            $table->string('posm_1_qty')->nullable()->after('posm_1');
            $table->string('posm_2')->nullable()->after('posm_1_qty');
            $table->string('posm_2_qty')->nullable()->after('posm_2');
            $table->string('posm_3')->nullable()->after('posm_2_qty');
            $table->string('posm_3_qty')->nullable()->after('posm_3');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('asm_programs', function (Blueprint $table) {
            $table->dropColumn(['foc_250_qty', 'foc_350_qty', 'foc_600_qty', 'foc_1500_qty', 'foc_other', 'foc_other_qty', 'posm_1', 'posm_1_qty', 'posm_2', 'posm_2_qty', 'posm_3', 'posm_3_qty']);
            $table->string('foc_qty')->nullable()->after('photo_foc');
            $table->string('posm')->nullable()->after('foc_qty');
            $table->string('qty')->nullable()->after('posm');
        });
    }
};
