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
            $table->longText('photo_foc')->nullable()->after('photo');
            $table->string('foc_qty')->nullable()->after('photo_foc');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('asm_programs', function (Blueprint $table) {
            $table->dropColumn('photo_foc');
            $table->dropColumn('foc_qty');
        });
    }
};
