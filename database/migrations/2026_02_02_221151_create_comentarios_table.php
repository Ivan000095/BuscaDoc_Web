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

        Schema::create('comentarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_autor')->constrained('users');
            $table->foreignId('id_destinatario')->constrained('users');
            $table->enum('tipo', ["resena","pregunta"]);
            $table->integer('calificacion')->nullable();
            $table->text('contenido')->nullable();
           
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comentarios');
    }
};
