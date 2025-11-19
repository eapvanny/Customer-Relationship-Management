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
            $table->unsignedBigInteger('qty2')->nullable()->after('posm');
            $table->unsignedBigInteger('posm2')->nullable()->after('qty2');
            $table->unsignedBigInteger('qty3')->nullable()->after('posm2');
            $table->unsignedBigInteger('posm3')->nullable()->after('qty3');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->dropColumn(['qty2', 'posm2', 'qty3', 'posm3']);
        });
    }
};
