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
        Schema::create('subwholesale_imports', function (Blueprint $table) {
            $table->id();
            $table->string('area_id');
            $table->string('outlet_id');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('date');
            $table->string('250_ml')->nullable();
            $table->string('350_ml')->nullable();
            $table->string('600_ml')->nullable();
            $table->string('1500_ml')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('city')->nullable();
            $table->string('country')->nullable();
            $table->string('other')->nullable();
            $table->unsignedBigInteger('posm')->nullable();
            $table->unsignedBigInteger('qty')->nullable();
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
        Schema::dropIfExists('subwholesale_imports');
    }
};
