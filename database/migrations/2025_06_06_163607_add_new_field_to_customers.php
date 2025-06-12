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
        Schema::table('customers', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->after('id');
            $table->string('code')->nullable()->after('user_id');
            $table->string('customer_type')->nullable()->after('outlet');
            $table->text('outlet_photo')->nullable()->after('customer_type');
            $table->decimal('latitude', 10, 7)->nullable()->after('outlet_photo');
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            $table->string('city')->nullable()->after('longitude');
            $table->string('country')->nullable()->after('city');
        });
    }
    
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn(['outlet_photo','latitude', 'longitude', 'city', 'country']);
        });
    }
};
