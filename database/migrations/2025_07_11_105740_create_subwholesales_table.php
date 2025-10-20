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
        Schema::create('subwholesales', function (Blueprint $table) {
            $table->id();
            $table->string('region')->nullable();
            $table->string('asm_name')->nullable();
            $table->string('sup_name')->nullable();
            $table->string('se_name')->nullable();
            $table->string('customer_name')->nullable();
            $table->string('contact_number')->nullable();
            $table->string('business_type')->nullable();
            $table->string('ams')->nullable();
            $table->string('display_parasol')->nullable();
            $table->string('foc')->nullable();
            $table->string('installation')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subwholesales');
    }
};
