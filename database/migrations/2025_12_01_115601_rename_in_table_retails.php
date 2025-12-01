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
        Schema::table('retails', function (Blueprint $table) {
            if (Schema::hasColumn('retails', 'retails_name')) {
                $table->renameColumn('retails_name', 'retail_name');
            }

            if (Schema::hasColumn('retails', 'retails_contact')) {
                $table->renameColumn('retails_contact', 'retail_contact');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('retails', function (Blueprint $table) {
            if (Schema::hasColumn('retails', 'retail_name')) {
                $table->renameColumn('retail_name', 'retails_name');
            }

            if (Schema::hasColumn('retails', 'retail_contact')) {
                $table->renameColumn('retail_contact', 'retails_contact');
            }
        });
    }
};
