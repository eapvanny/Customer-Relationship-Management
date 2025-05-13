<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Rename the 'outlet' column to 'outlet_id' using MariaDB-compatible syntax
        DB::statement('ALTER TABLE reports CHANGE COLUMN outlet outlet_id BIGINT UNSIGNED NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverse the change by renaming 'outlet_id' back to 'outlet'
        DB::statement('ALTER TABLE reports CHANGE COLUMN outlet_id outlet BIGINT UNSIGNED NULL');
    }
};