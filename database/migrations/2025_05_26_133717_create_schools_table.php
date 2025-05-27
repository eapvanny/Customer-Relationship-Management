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
        Schema::create('schools', function (Blueprint $table) {
            $table->id();
            $table->string('area_id');
            $table->string('outlet_id');
            $table->string('customer_id')->nullable();
            $table->string('customer_type')->nullable();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->date('date')->nullable();
            $table->string('250_ml')->nullable();
            $table->string('350_ml')->nullable();
            $table->string('600_ml')->nullable();
            $table->string('1500_ml')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('city')->nullable();
            $table->string('country')->nullable();
            $table->string('phone')->nullable();
            $table->string('other')->nullable();
            $table->unsignedBigInteger('posm')->nullable();
            $table->unsignedBigInteger('qty')->nullable();
            $table->longText('photo_foc')->nullable();
            $table->string('foc_qty')->nullable();
            $table->longText('photo')->nullable();
            $table->boolean('is_seen')->default(false);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schools');
    }
};
