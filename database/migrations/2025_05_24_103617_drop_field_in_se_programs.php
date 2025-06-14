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
            $table->dropColumn('area');
            $table->dropColumn('outlet');
            $table->dropColumn('customer');




        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('se_programs', function (Blueprint $table) {
            $table->string('area');
            $table->string('outlet');
            $table->string('customer');
        });
    }
};
