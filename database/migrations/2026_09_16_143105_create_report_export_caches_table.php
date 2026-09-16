<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('report_export_caches', function (Blueprint $table) {
            $table->id();

            // Original reports.id
            $table->unsignedBigInteger('report_id')->unique();

            // reports.updated_at when this cache was generated
            $table->timestamp('source_updated_at')->nullable();

            // Complete 31-column Excel row
            $table->json('data');

            $table->timestamps();

            $table->index('report_id');
            $table->index('source_updated_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_export_caches');
    }
};