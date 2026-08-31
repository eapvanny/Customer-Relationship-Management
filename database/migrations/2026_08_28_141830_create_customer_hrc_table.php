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
        Schema::create('customer_hrc', function (Blueprint $table) {
            $table->id();
            $table->string('area')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('code')->nullable();
            $table->string('name')->nullable();
            $table->string('phone')->nullable();
            $table->string('customer_type')->nullable();
            $table->text('outlet_photo')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('city')->nullable();
            $table->string('country')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_hrc');
    }
};
