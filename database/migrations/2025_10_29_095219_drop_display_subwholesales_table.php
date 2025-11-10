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
            $table->string('province')->nullable();
            $table->string('district')->nullable();
            $table->string('commune')->nullable();
            $table->string('sm_name')->nullable();
            $table->string('rsm_name')->nullable();
            $table->string('asm_name')->nullable();
            $table->string('se_name')->nullable();
            $table->string('se_code')->nullable();
            $table->string('customer_code')->nullable();
            $table->string('depot_contact')->nullable();
            $table->string('depot_name')->nullable();
            $table->string('sub_ws_name')->nullable();
            $table->string('sub_ws_contact')->nullable();
            $table->string('outlet_type')->nullable();
            $table->integer('sale_kpi')->nullable();
            $table->integer('display_qty')->nullable();
            $table->string('sku')->nullable();
            $table->decimal('incentive', 10, 2)->nullable();
            $table->char('latitude', 255)->nullable();
            $table->char('longitude', 255)->nullable();
            $table->string('city')->nullable();
            $table->string('country')->nullable();
            $table->text('remark')->nullable();
            $table->string('apply_user')->nullable();
            $table->timestamps();
        });
    }
};
