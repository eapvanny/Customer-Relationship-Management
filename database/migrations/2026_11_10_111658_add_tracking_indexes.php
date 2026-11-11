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
        Schema::table('user_work_sessions', function (Blueprint $table) {
            $table->index(
                ['user_id', 'id'],
                'work_sessions_user_id_id_index'
            );
        });

        Schema::table('user_location_histories', function (Blueprint $table) {
            $table->index(
                ['user_work_session_id', 'created_at'],
                'location_session_created_index'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_work_sessions', function (Blueprint $table) {
            $table->dropIndex('work_sessions_user_id_id_index');
        });

        Schema::table('user_location_histories', function (Blueprint $table) {
            $table->dropIndex('location_session_created_index');
        });
    }
};
