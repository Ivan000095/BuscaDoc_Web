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
        Schema::create('doctors', function (Blueprint $table) {
            $table->id();
            $table->string('especialidad')->nullable();
            $table->string('name');
            $table->text('descripcion')->nullable();
            $table->date('fecha')->nullable();
            $table->string('image')->nullable();
            $table->string('telefono')->nullable();
            $table->string('idioma')->nullable();
            $table->string('cedula')->nullable();
            $table->string('direccion')->nullable();
            $table->decimal('costos', 8, 2);
            $table->time('horarioentrada');
            $table->time('horariosalida');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctors');
    }
};
