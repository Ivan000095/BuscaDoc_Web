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
        Schema::disableForeignKeyConstraints();

        Schema::create('farmacias', function (Blueprint $table) {
            $table->id();
            $table->string('tableName');
            $table->foreignId('user_id')->constrained();
            $table->string('nom_farmacia');
            $table->string('rfc')->nullable();
            $table->string('telefono')->nullable();
            $table->text('descripcion')->nullable();
            $table->string('horario')->nullable();
            $table->string('dias_op')->nullable();
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('farmacias');
    }
};
