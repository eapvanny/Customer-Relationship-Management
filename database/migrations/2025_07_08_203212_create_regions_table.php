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
        Schema::create('regions', function (Blueprint $table) {
            $table->id();
            $table->string('region_name')->nullable();
            $table->string('rg_manager_kh')->nullable();
            $table->string('rg_manager_en')->nullable();
            $table->string('se_code')->nullable()->unique();
            $table->string('province')->nullable();
            $table->integer('active_status')->default(1)->comment('1:active, 0:inactive');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('regions');
    }
};
