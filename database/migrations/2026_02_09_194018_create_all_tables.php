<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // 1. Tipos Nativos de PostgreSQL
        DB::transaction(function () {
            DB::statement("DO $$ BEGIN
                IF NOT EXISTS (SELECT 1 FROM pg_type WHERE typname = 'estado_reporte') THEN
                    CREATE TYPE estado_reporte AS ENUM ('pendiente', 'en_proceso', 'resuelto', 'descartado');
                END IF;
                IF NOT EXISTS (SELECT 1 FROM pg_type WHERE typname = 'estado_cita') THEN
                    CREATE TYPE estado_cita AS ENUM ('pendiente', 'confirmada', 'cancelada', 'completada');
                END IF;
            END $$;");
        });

        // 2. Tablas Base
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name', 80);
            $table->string('email', 80)->unique();
            $table->string('password');
            $table->string('role', 15);
            $table->string('foto', 255)->nullable();
            $table->date('f_nacimiento')->nullable();
            $table->decimal('latitud', 10, 8)->nullable();
            $table->decimal('longitud', 11, 8)->nullable();
            $table->boolean('estado')->default(true);
            $table->string('google_id', 100)->nullable();
            $table->timestamps();
        });

        Schema::create('especialidads', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 50);
            $table->timestamps();
        });

        Schema::create('doctors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('cedula', 15)->nullable();
            $table->string('idiomas', 100)->nullable();
            $table->text('descripcion')->nullable();
            $table->decimal('costo', 8, 2);
            $table->time('horario_entrada')->nullable();
            $table->time('horario_salida')->nullable();
            $table->timestamps();
        });

        Schema::create('pacientes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('tipo_sangre', 5)->nullable();
            $table->text('alergias')->nullable();
            $table->text('cirugias')->nullable();
            $table->text('padecimientos')->nullable();
            $table->text('habitos')->nullable();
            $table->string('contacto_emergencia', 14)->nullable(); 
            $table->timestamps();
        });

        Schema::create('farmacias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('nom_farmacia', 100);
            $table->string('rfc', 15)->nullable();
            $table->string('telefono', 14)->nullable();
            $table->text('descripcion')->nullable();
            $table->time('horario_entrada')->nullable();
            $table->time('horario_salida')->nullable();
            $table->timestamps();
        });

        Schema::create('doctor__especialidads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id')->constrained('doctors')->cascadeOnDelete();
            $table->foreignId('especialidad_id')->constrained('especialidads')->cascadeOnDelete();
            $table->timestamps();
        });

        // 3. Citas con corrección de tipo Postgres
        Schema::create('citas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id')->constrained('doctors')->cascadeOnDelete();
            $table->foreignId('paciente_id')->constrained('pacientes')->cascadeOnDelete();
            $table->timestamp('fecha_hora');
            $table->text('detalles')->nullable();
            $table->timestamps();
        });
        // Vinculamos el tipo ENUM manualmente para evitar el error de Laravel
        DB::statement('ALTER TABLE citas ADD estado estado_cita DEFAULT \'pendiente\'');

        Schema::create('comentarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_autor')->constrained('users')->cascadeOnDelete();
            $table->foreignId('id_destinatario')->constrained('users')->cascadeOnDelete();
            $table->string('tipo', 15);
            $table->unsignedTinyInteger('calificacion')->nullable();
            $table->text('contenido')->nullable();
            $table->timestamps();
        });

        Schema::create('respuestas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('comentario_id')->constrained('comentarios')->cascadeOnDelete();
            $table->foreignId('id_respondedor')->constrained('users')->cascadeOnDelete();
            $table->text('contenido');
            $table->timestamps();
        });

        Schema::create('mensajes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_remitente')->constrained('users')->cascadeOnDelete();
            $table->foreignId('id_destinatario')->constrained('users')->cascadeOnDelete();
            $table->text('contenido');
            $table->boolean('leido')->default(false);
            $table->timestamps();
        });

        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('receiver_id')->constrained('users')->cascadeOnDelete();
            $table->text('message');
            $table->boolean('is_read')->default(false);
            $table->timestamps();
        });

        // 4. Reportes con corrección de tipo Postgres
        Schema::create('reportes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reportador_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('reportado_id')->constrained('users')->cascadeOnDelete();
            $table->text('razon');
            $table->timestamps();
        });
        DB::statement('ALTER TABLE reportes ADD estado estado_reporte DEFAULT \'pendiente\'');

        // 5. Tablas de Soporte
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

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
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

        Schema::create('cache', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->mediumText('value');
            $table->integer('expiration');
        });

        Schema::create('cache_locks', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->string('owner');
            $table->integer('expiration');
        });
    }

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

        DB::statement("DROP TYPE IF EXISTS estado_reporte");
        DB::statement("DROP TYPE IF EXISTS estado_cita");
    }
};