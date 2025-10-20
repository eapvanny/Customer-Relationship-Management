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
        Schema::dropIfExists('display_subwholesales');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('display_subwholesales', function (Blueprint $table) {
            $table->id();
            $table->string('region')->nullable();
            $table->string('sm_name')->nullable();
            $table->string('rsm_name')->nullable();
            $table->string('asm_name')->nullable();
            $table->string('sup_name')->nullable();
            $table->string('se_name')->nullable();
            $table->string('se_code')->nullable();
            $table->string('customer_code')->nullable();
            $table->string('depo_contact')->nullable();
            $table->string('creater')->nullable();
            $table->string('depo_name')->nullable();
            $table->string('subwholesale_name')->nullable();
            $table->string('subwholesale_contact')->nullable();
            $table->string('business_type')->nullable();
            $table->integer('sale_kpi')->nullable();
            $table->integer('display_qty')->nullable();
            $table->integer('foc_qty')->nullable();
            $table->text('remark')->nullable();
            $table->unsignedBigInteger('apply_user')->nullable();
            $table->string('location')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
};
