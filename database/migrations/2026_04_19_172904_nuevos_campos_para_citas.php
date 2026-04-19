<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('doctors', function (Blueprint $table) {
            $table->dropColumn(['horario_entrada', 'horario_salida']);
            $table->integer('duracion_cita')->default(30)->after('costo');
        });

        Schema::create('doctor_disponibilidad', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id')->constrained('doctors')->onDelete('cascade');
            $table->unsignedTinyInteger('dia_semana');
            $table->time('hora_inicio');
            $table->time('hora_fin');
            $table->timestamps();
        });

        Schema::create('doctor_excepciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id')->constrained('doctors')->onDelete('cascade');
            $table->date('fecha');
            $table->boolean('trabaja')->default(false);
            $table->time('hora_inicio')->nullable();
            $table->time('hora_fin')->nullable();
            $table->timestamps();
        });

        Schema::create('expedientes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('nombre_completo', 80);
            $table->date('fecha_nacimiento');
            $table->enum('genero', ['masculino', 'femenino', 'otro']);
            $table->string('parentesco', 30);
            $table->string('tipo_sangre', 5)->nullable();
            $table->text('alergias')->nullable();
            $table->text('padecimientos_cronicos')->nullable();
            $table->text('habitos_salud')->nullable();
            $table->timestamps();
        });

        Schema::table('pacientes', function (Blueprint $table) {
            $table->dropColumn([
                'tipo_sangre', 'alergias', 'cirugias', 'padecimientos', 'habitos', 'contacto_emergencia'
            ]);
        });

        DB::statement('ALTER TABLE citas DROP COLUMN IF EXISTS estado');

        Schema::table('citas', function (Blueprint $table) {
            // Eliminar foránea y columnas viejas
            $table->dropForeign(['paciente_id']);
            $table->dropColumn('paciente_id');
            $table->dropColumn('fecha_hora');
            $table->dropColumn('detalles');

            $table->foreignId('expediente_id')->nullable()->constrained('expedientes')->onDelete('cascade');
            $table->date('fecha')->nullable();
            $table->time('hora_inicio')->nullable();
            $table->timestamp('fecha_registro')->useCurrent();
            $table->text('motivo_consulta')->nullable();
            
            $table->enum('estado', ['pendiente', 'confirmada', 'cancelada', 'finalizada'])->default('pendiente');
        });

        Schema::create('notas_medicas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('expediente_id')->constrained('expedientes')->onDelete('cascade');
            $table->foreignId('doctor_id')->constrained('doctors');
            $table->foreignId('cita_id')->constrained('citas');
            $table->text('diagnostico');
            $table->text('tratamiento');
            $table->text('nota_seguimiento')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notas_medicas');
        Schema::dropIfExists('doctor_excepciones');
        Schema::dropIfExists('doctor_disponibilidad');
        Schema::dropIfExists('expedientes');
    }
};