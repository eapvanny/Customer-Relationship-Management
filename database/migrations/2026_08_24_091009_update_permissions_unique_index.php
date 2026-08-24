<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Get all indexes on permissions table
        $indexes = DB::select('SHOW INDEX FROM permissions');

        foreach ($indexes as $index) {
            // Drop unique index on name + guard_name
            if (
                $index->Key_name !== 'PRIMARY' &&
                $index->Non_unique == 0 &&
                $index->Column_name === 'name'
            ) {
                Schema::table('permissions', function (Blueprint $table) use ($index) {
                    $table->dropUnique($index->Key_name);
                });

                break;
            }
        }

        // Add unique index using name + guard_name + type
        Schema::table('permissions', function (Blueprint $table) {
            $table->unique(
                ['name', 'guard_name', 'type'],
                'permissions_name_guard_name_type_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::table('permissions', function (Blueprint $table) {
            $table->dropUnique('permissions_name_guard_name_type_unique');

            $table->unique(
                ['name', 'guard_name'],
                'permissions_name_guard_name_unique'
            );
        });
    }
};
