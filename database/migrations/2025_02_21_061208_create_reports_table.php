<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();  
            $table->string('area'); 
            $table->string('outlet');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->datetime('date'); 
            $table->string('250_ml')->nullable();  
            $table->string('350_ml')->nullable();  
            $table->string('600_ml')->nullable(); 
            $table->string('1500_ml')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('city')->nullable();
            $table->string('country')->nullable();
            $table->string('other')->nullable();
            $table->timestamps();  
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
