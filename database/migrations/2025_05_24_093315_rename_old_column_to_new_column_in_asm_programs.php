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
        Schema::table('asm_programs', function (Blueprint $table) {
            $table->dropColumn('area');
            $table->dropColumn('customer');

            // Then, add the new 'area_id' column
            $table->unsignedBigInteger('area_id')->after('id');
            // Set the default value to 0 (or any other default you prefer)
            $table->unsignedBigInteger('customer_id')->after('area_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('asm_programs', function (Blueprint $table) {
            $table->dropColumn('area_id');

            // Restore the old 'area' column
            $table->string('area')->after('id');
        });
    }
};
