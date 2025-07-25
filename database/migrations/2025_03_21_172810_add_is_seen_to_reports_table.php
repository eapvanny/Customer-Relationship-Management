<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->boolean('is_seen')->default(false)->after('deleted_at'); // Add after 'deleted_at'
        });
    }

    public function down()
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->dropColumn('is_seen');
        });
    }
};
