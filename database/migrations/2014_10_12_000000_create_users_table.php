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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('staff_id_card');
            $table->string('family_name');
            $table->string('name');
            $table->string('family_name_latin');
            $table->string('name_latin');
            $table->string('position');
            $table->string('area');
            $table->string('username')->unique();
            $table->string('email')->unique()->nullable();
            $table->string('phone_no')->nullable();
            $table->string('password');
            $table->string('photo')->nullable();
            $table->boolean('status')->default(1);
            $table->unsignedBigInteger('role_id');
            $table->tinyInteger('gender')->nullable();
            $table->string('user_lang')->default('kh');
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
