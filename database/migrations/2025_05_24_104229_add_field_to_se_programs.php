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
        Schema::table('se_programs', function (Blueprint $table) {
            $table->unsignedBigInteger('area_id')->after('id');
            $table->unsignedBigInteger('outlet_id')->after('area_id');
            $table->unsignedBigInteger('customer_id')->after('outlet_id');

            $table->longText('photo_foc')->nullable()->after('photo');
            $table->string('foc_qty')->nullable()->after('photo_foc');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('se_programs', function (Blueprint $table) {
            $table->dropColumn('area_id');
            $table->dropColumn('outlet_id');
            $table->dropColumn('customer_id');

            $table->dropColumn('photo_foc');
            $table->dropColumn('foc_qty');
        });
    }
};
