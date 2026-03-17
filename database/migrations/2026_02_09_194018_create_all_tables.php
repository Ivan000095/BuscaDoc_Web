<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
// Verifica si el tipo ya existe antes de crearlo
    DB::statement("DO $$ BEGIN
        IF NOT EXISTS (SELECT 1 FROM pg_type WHERE typname = 'estado_cita') THEN
            CREATE TYPE estado_cita AS ENUM ('pendiente', 'confirmada', 'cancelada', 'completada');
        END IF;
    END $$;");

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->string('email', 50)->unique();
            $table->string('password');
            $table->string('role', 10);
            $table->string('foto')->nullable();
            $table->date('f_nacimiento')->nullable();
            $table->decimal('latitud', 10, 8)->nullable();
            $table->decimal('longitud', 11, 8)->nullable();
            $table->boolean('estado')->default(true);
            $table->string('google_id')->nullable();
            $table->timestamps();
        });

        Schema::create('especialidads', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->timestamps();
        });

        Schema::create('cache', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->text('value');
            $table->integer('expiration');
        });

        Schema::create('cache_locks', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->string('owner');
            $table->integer('expiration');
        });

        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->string('queue')->index();
            $table->longText('payload');
            $table->unsignedTinyInteger('attempts');
            $table->unsignedInteger('reserved_at')->nullable();
            $table->unsignedInteger('available_at');
            $table->unsignedInteger('created_at');
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });

        Schema::create('personal_access_tokens', function (Blueprint $table) {
            $table->id();
            $table->morphs('tokenable');
            $table->string('name');
            $table->string('token', 64)->unique();
            $table->text('abilities')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });

        Schema::create('doctors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete()
                ->restrictOnDelete();
            $table->string('cedula')->nullable();
            $table->string('idiomas')->nullable();
            $table->text('descripcion')->nullable();
            $table->decimal('costo', 6, 2);
            $table->time('horario_entrada')->nullable();
            $table->time('horario_salida')->nullable();
            $table->boolean('citas')->default(false);
            $table->timestamps();
        });

        Schema::create('pacientes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete()
                ->restrictOnDelete();
            $table->string('tipo_sangre')->nullable();
            $table->text('alergias')->nullable();
            $table->text('cirugias')->nullable();
            $table->text('padecimientos')->nullable();
            $table->text('habitos')->nullable();
            $table->string('contacto_emergencia', 15)->nullable();
            $table->timestamps();
        });

        Schema::create('farmacias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained('users')
                ->restrictOnDelete()
                ->restrictOnDelete();
            $table->string('nom_farmacia', 50);
            $table->string('rfc', 15)->nullable();
            $table->string('telefono', 14)->nullable();
            $table->text('descripcion')->nullable();
            $table->time('horario_entrada')->nullable();
            $table->time('horario_salida')->nullable();
            $table->timestamps();
        });

        // 4. Tablas Relacionales (Con CASCADE)
        Schema::create('doctor__especialidads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id')
                ->constrained('doctors')
                ->cascadeOnDelete()
                ->restrictOnDelete();
            $table->foreignId('especialidad_id')
                ->constrained('especialidads')
                ->cascadeOnDelete()
                ->restrictOnDelete();
            $table->timestamps();
        });

        Schema::create('citas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id')
                ->constrained('doctors')
                ->cascadeOnDelete()
                ->restrictOnDelete();
            $table->foreignId('paciente_id')
                ->constrained('pacientes')
                ->cascadeOnDelete()
                ->restrictOnDelete();
            $table->timestamp('fecha_hora');
            $table->text('detalles')->nullable();
            $table->string('estado')->default('pendiente');
            $table->timestamps();
        });

        Schema::create('comentarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_autor')
                ->constrained('users')
                ->cascadeOnDelete()
                ->restrictOnDelete();
            $table->foreignId('id_destinatario')
                ->constrained('users')
                ->cascadeOnDelete()
                ->restrictOnDelete();
            $table->string('tipo', 10);
            $table->integer('calificacion')->nullable();
            $table->text('contenido')->nullable();
            $table->timestamps();
        });

        Schema::create('respuestas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('comentario_id')
                ->constrained('comentarios')
                ->cascadeOnDelete()
                ->restrictOnDelete();
            $table->foreignId('id_respondedor')
                ->constrained('users')
                ->cascadeOnDelete()
                ->restrictOnDelete();
            $table->text('contenido');
            $table->timestamps();
        });

        Schema::create('mensajes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_remitente')
                ->constrained('users')
                ->cascadeOnDelete()
                ->restrictOnDelete();
            $table->foreignId('id_destinatario')
                ->constrained('users')
                ->cascadeOnDelete()
                ->restrictOnDelete();
            $table->text('contenido');
            $table->boolean('leido')->default(false);
            $table->timestamps();
        });

        Schema::create('reportes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reportador_id')
                ->constrained('users')
                ->cascadeOnDelete()
                ->restrictOnDelete();
            $table->foreignId('reportado_id')
                ->constrained('users')
                ->cascadeOnDelete()
                ->restrictOnDelete();
            $table->text('descripcion');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_messages');
        Schema::dropIfExists('reportes');
        Schema::dropIfExists('mensajes');
        Schema::dropIfExists('respuestas');
        Schema::dropIfExists('comentarios');
        Schema::dropIfExists('citas');
        Schema::dropIfExists('doctor__especialidads');
        Schema::dropIfExists('farmacias');
        Schema::dropIfExists('pacientes');
        Schema::dropIfExists('doctors');
        Schema::dropIfExists('personal_access_tokens');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('jobs');
        Schema::dropIfExists('cache_locks');
        Schema::dropIfExists('cache');
        Schema::dropIfExists('especialidads');
        Schema::dropIfExists('users');
        Schema::dropIfExists('chat_messages');

        DB::statement("DROP TYPE IF EXISTS estado_cita");
    }
};