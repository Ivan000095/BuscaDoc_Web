<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
        public function up()
        {
            Schema::table('citas', function (Blueprint $table) {
                // Por defecto es false (0), cuando cambie la fecha lo pasamos a true (1)
                $table->boolean('reprogramada')->default(false);
            });
        }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('citas', function (Blueprint $table) {
            //
        });
    }
};
