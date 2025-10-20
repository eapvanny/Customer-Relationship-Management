<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::dropIfExists('schools');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('schools', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('area_id')->nullable();
            $table->unsignedBigInteger('outlet_id')->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->string('customer_type')->nullable();
            $table->date('date')->nullable();
            $table->string('other')->nullable();

            $table->integer('250_ml')->default(0);
            $table->integer('350_ml')->default(0);
            $table->integer('600_ml')->default(0);
            $table->integer('1500_ml')->default(0);

            $table->string('phone')->nullable();
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->nullable();

            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('photo')->nullable();
            $table->string('customer')->nullable();

            // FOC fields
            $table->string('photo_foc')->nullable();
            $table->integer('foc_250_qty')->default(0);
            $table->integer('foc_350_qty')->default(0);
            $table->integer('foc_600_qty')->default(0);
            $table->integer('foc_1500_qty')->default(0);
            $table->string('foc_other')->nullable();
            $table->integer('foc_other_qty')->default(0);

            // POSM fields
            $table->unsignedBigInteger('posm_1')->nullable();
            $table->integer('posm_1_qty')->default(0);
            $table->unsignedBigInteger('posm_2')->nullable();
            $table->integer('posm_2_qty')->default(0);
            $table->unsignedBigInteger('posm_3')->nullable();
            $table->integer('posm_3_qty')->default(0);

            $table->timestamps();
        });
    }
};
