<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First convert empty strings to NULL (optional but recommended)
        $columns = ['family_name', 'name', 'family_name_latin', 'name_latin', 'position', 'area'];
        
        foreach ($columns as $column) {
            DB::table('users')
                ->where($column, '')
                ->update([$column => null]);
        }

        // Make columns nullable
        Schema::table('users', function (Blueprint $table) {
            $table->string('family_name')->nullable()->change();
            $table->string('name')->nullable()->change();
            $table->string('family_name_latin')->nullable()->change();
            $table->string('name_latin')->nullable()->change();
            $table->string('position')->nullable()->change();
            $table->string('area')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('family_name')->nullable(false)->change();
            $table->string('name')->nullable(false)->change();
            $table->string('family_name_latin')->nullable(false)->change();
            $table->string('name_latin')->nullable(false)->change();
            $table->string('position')->nullable(false)->change();
            $table->string('area')->nullable(false)->change();
        });
    }
};