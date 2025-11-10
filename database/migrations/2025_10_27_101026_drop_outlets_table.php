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
        Schema::dropIfExists('outlets');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('outlets', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('phone')->nullable();
            $table->unsignedBigInteger('area_id')->nullable();
            // $table->unsignedBigInteger('depo_id')->nullable();
            $table->longText('outlet_photo')->nullable();
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->nullable();
            $table->string('code')->nullable();
            $table->string('customer_type')->nullable();
            $table->string('user_type')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->integer('active_status')->default(1)->comment('1: active, 0: inactive');
            $table->timestamps();
        });
    }
};
