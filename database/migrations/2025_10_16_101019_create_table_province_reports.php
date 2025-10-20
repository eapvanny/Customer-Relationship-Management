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
        Schema::create('province_reports', function (Blueprint $table) {
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
            $table->text('posm')->nullable();
            $table->integer('qty')->nullable();
            $table->string('photo')->nullable();
            $table->string('outlet_photo')->nullable();
            $table->unsignedBigInteger('manager_id')->nullable();
            $table->string('so_number')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('province_reports');
    }
};
