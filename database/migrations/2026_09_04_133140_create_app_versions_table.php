<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('app_versions', function (Blueprint $table) {
            $table->id();

            $table->string('latest_version', 20);

            $table->boolean('can_update')->default(false);
            $table->boolean('force_update')->default(false);

            // Android
            $table->boolean('update_version_android')->default(false);
            $table->text('update_url_android')->nullable();

            // iOS
            $table->boolean('update_version_ios')->default(false);
            $table->text('update_url_ios')->nullable();

            $table->text('release_notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('app_versions');
    }
};