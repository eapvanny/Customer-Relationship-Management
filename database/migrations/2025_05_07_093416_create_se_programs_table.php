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
        Schema::create('se_programs', function (Blueprint $table) {
            $table->id();  
            $table->string('area')->nullable(); 
            $table->string('outlet')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->string('customer')->nullable();
            $table->string('customer_type')->nullable();
            $table->string('phone')->nullable();
            $table->datetime('text')->nullable(); 
            $table->string('250_ml')->nullable();  
            $table->string('350_ml')->nullable();  
            $table->string('600_ml')->nullable(); 
            $table->string('1500_ml')->nullable();
            $table->char('latitude', 255)->nullable();
            $table->char('longitude', 255)->nullable();
            $table->string('city')->nullable();
            $table->string('country')->nullable();
            $table->string('other')->nullable();
            $table->unsignedBigInteger('posm')->nullable();
            $table->unsignedBigInteger('qty')->nullable();
            $table->longText('photo')->nullable();
            $table->boolean('is_seen')->default(false);
            $table->date('date')->nullable();
            $table->timestamps();  
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('se_programs');
    }
};
