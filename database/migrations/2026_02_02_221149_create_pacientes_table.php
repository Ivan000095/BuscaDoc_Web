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
        // Deshabilitar restricciones si es necesario para evitar errores de llaves foráneas en re-ejecuciones
        Schema::disableForeignKeyConstraints();

        Schema::create('pacientes', function (Blueprint $table) {
            $table->id();
            
            // Relación con la tabla users (Cascade asegura que si se borra el user, se borra el paciente)
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Campos existentes corregidos/mantenidos
            $table->string('tipo_sangre')->nullable();
            $table->text('alergias')->nullable();
            
            // --- NUEVOS CAMPOS SEGÚN LA IMAGEN ---
            
            // Usamos text para campos descriptivos largos
            $table->text('cirugias')->nullable();
            $table->text('padecimientos')->nullable();
            $table->text('habitos')->nullable();
            
            // Contacto de emergencia (limitado a 10 para números telefónicos estándar)
            $table->string('contacto_emergencia', 10)->nullable();
            
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pacientes');
    }
};
