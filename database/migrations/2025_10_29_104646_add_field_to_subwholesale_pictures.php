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
        Schema::table('subwholesale_pictures', function (Blueprint $table) {
            $table->char('latitude', 255)->nullable();
            $table->char('longitude', 255)->nullable();
            $table->string('city')->nullable();
            $table->string('country')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subwholesale_pictures', function (Blueprint $table) {
            $table->dropColumn(['latitude', 'longitude', 'city', 'country']);
        });
    }
};
