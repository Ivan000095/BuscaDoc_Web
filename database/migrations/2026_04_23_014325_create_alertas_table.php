<?php

// database/migrations/2024_04_23_000000_create_alertas_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('alertas', function (Blueprint $table) {
            $table->id();
            // El usuario que RECIBE la alerta
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); 
            $table->string('titulo');
            $table->text('mensaje');
            $table->string('tipo'); // Ejemplo: 'mensaje', 'cita_confirmada', 'cita_cancelada'
            $table->unsignedBigInteger('referencia_id')->nullable(); // ID de la cita o del chat
            $table->boolean('leido')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('alertas');
    }
};
