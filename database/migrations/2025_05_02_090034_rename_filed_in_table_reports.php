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
        Schema::table('reports', function (Blueprint $table) {
            // First, drop the old 'area' column
            $table->dropColumn('area');
            $table->dropColumn('customer');

            // Then, add the new 'area_id' column
            $table->unsignedBigInteger('area_id')->after('id');
            // Set the default value to 0 (or any other default you prefer)
            $table->unsignedBigInteger('customer_id')->after('area_id')->nullable();

            // Optionally, if there's a related 'areas' table, add a foreign key:
            // $table->foreign('area_id')->references('id')->on('areas')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            // Drop the new 'area_id' column
            $table->dropColumn('area_id');

            // Restore the old 'area' column
            $table->string('area')->after('id');
        });
    }
};
