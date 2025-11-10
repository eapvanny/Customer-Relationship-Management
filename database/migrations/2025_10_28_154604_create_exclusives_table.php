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
        Schema::create('exclusives', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('area_id')->nullable();
            $table->unsignedBigInteger('outlet_id')->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->string('customer_type')->nullable();
            $table->date('date')->nullable();

            $table->text('other')->nullable();

            $table->integer('250_ml')->nullable();
            $table->integer('350_ml')->nullable();
            $table->integer('600_ml')->nullable();
            $table->integer('1500_ml')->nullable();

            $table->string('phone')->nullable();

            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();

            $table->string('city')->nullable();
            $table->string('country')->nullable();

            $table->unsignedBigInteger('user_id')->nullable();

            $table->string('photo')->nullable();
            $table->string('customer')->nullable();

            $table->string('photo_foc')->nullable();
            $table->integer('foc_250_qty')->nullable();
            $table->integer('foc_350_qty')->nullable();
            $table->integer('foc_600_qty')->nullable();
            $table->integer('foc_1500_qty')->nullable();
            $table->string('foc_other')->nullable();
            $table->integer('foc_other_qty')->nullable();

            $table->string('posm_1')->nullable();
            $table->integer('posm_1_qty')->nullable();
            $table->string('posm_2')->nullable();
            $table->integer('posm_2_qty')->nullable();
            $table->string('posm_3')->nullable();
            $table->integer('posm_3_qty')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exclusives');
    }
};
