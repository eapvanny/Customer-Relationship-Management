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
        Schema::create('display_subwholesales', function (Blueprint $table) {
            $table->id();
            $table->string('region')->nullable();
            $table->string('location')->nullable();
            $table->string('asm_name')->nullable();
            $table->string('se_name')->nullable();
            $table->string('se_code')->nullable();
            $table->string('customer_code')->nullable();
            $table->string('depo_contact')->nullable();
            $table->string('depo_name')->nullable();
            $table->string('subwholesale_name')->nullable();
            $table->string('subwholesale_contact')->nullable();
            $table->string('business_type')->nullable();
            $table->string('sale_kpi')->nullable();
            $table->string('display_qty')->nullable();
            $table->string('foc_qty')->nullable();
            $table->string('remark')->nullable();
            $table->unsignedBigInteger('employee_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('display_subwholesales');
    }
};
