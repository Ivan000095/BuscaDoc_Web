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

        Schema::create('doctors', function (Blueprint $table) {
            $table->id();
            $table->string('tableName');
            $table->foreignId('user_id')->constrained();
            $table->string('cedula')->nullable();
            $table->string('idiomas')->nullable();
            $table->text('descripcion')->nullable();
            $table->decimal('costo', 8, 2);
            $table->time('horario_entrada')->nullable();
            $table->time('horario_salida')->nullable();
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctors');
    }
};
