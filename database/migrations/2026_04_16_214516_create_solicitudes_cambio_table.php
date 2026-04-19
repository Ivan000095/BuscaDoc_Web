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
        Schema::create('solicitudes_cambio', function (Blueprint $table) {
                $table->id();
                $table->foreignId('cita_id')->constrained('citas')->onDelete('cascade');
                $table->foreignId('solicitante_id')->constrained('users'); // Quién pide el cambio
                $table->foreignId('solicitado_id')->constrained('users');  // Quién debe responder
                $table->date('nueva_fecha');
                $table->time('nueva_hora');
                $table->text('motivo')->nullable(); // Por qué se pide o por qué se rechaza
                $table->enum('estado', ['pendiente', 'aceptada', 'rechazada'])->default('pendiente');
                $table->timestamps();
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('solicitudes_cambio');
    }
};
