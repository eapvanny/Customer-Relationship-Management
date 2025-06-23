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
        Schema::table('customers', function (Blueprint $table) {
            $table->renameColumn('outlet', 'depo_id');
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->unsignedBigInteger('depo_id')->change()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->renameColumn('depo_id', 'outlet');
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->string('outlet')->change()->nullable();
        });
    }
};
