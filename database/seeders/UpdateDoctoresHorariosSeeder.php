<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class UpdateDoctorSchedulesSeeder extends Seeder
{
    public function run(): void
    {
        Log::info("Iniciando Seeder de Horarios y Citas para doctores...");

        // 1. Filtrar usuarios con rol doctor y email que empiece con 'doctor'
        // Usamos ILIKE para PostgreSQL y LIKE para MySQL
        $doctoresUsers = User::where('role', 'doctor')
            ->where(function($q) {
                $q->where('email', 'ILIKE', 'doctor%')
                  ->orWhere('email', 'LIKE', 'doctor%');
            })
            ->with('doctor') // Cargar la relación con la tabla doctors
            ->get();

        if ($doctoresUsers->isEmpty()) {
            Log::warning("No se encontraron doctores de prueba.");
            return;
        }

        $now = Carbon::now();

        foreach ($doctoresUsers as $user) {
            $doctor = $user->doctor;

            if (!$doctor) {
                Log::warning("El usuario {$user->email} no tiene registro en la tabla doctors.");
                continue;
            }

            // 2. Activar las citas y asegurar duración por defecto
            $doctor->citas = true;
            if (!$doctor->duracion_cita) {
                $doctor->duracion_cita = 30; // 30 mins según tu migración
            }
            $doctor->save();

            // 3. Limpiar cualquier disponibilidad vieja para evitar duplicados
            DB::table('doctor_disponibilidad')->where('doctor_id', $doctor->id)->delete();

            // 4. Armar el nuevo horario estándar
            $horarios = [];
            
            // Lunes (1) a Viernes (5) -> Mañana: 09:00 a 14:00 | Tarde: 16:00 a 19:00
            for ($dia = 1; $dia <= 5; $dia++) {
                $horarios[] = [
                    'doctor_id'   => $doctor->id,
                    'dia_semana'  => $dia,
                    'hora_inicio' => '09:00:00',
                    'hora_fin'    => '14:00:00',
                    'created_at'  => $now,
                    'updated_at'  => $now,
                ];
                $horarios[] = [
                    'doctor_id'   => $doctor->id,
                    'dia_semana'  => $dia,
                    'hora_inicio' => '16:00:00',
                    'hora_fin'    => '19:00:00',
                    'created_at'  => $now,
                    'updated_at'  => $now,
                ];
            }

            // Sábado (6) -> Medio tiempo: 09:00 a 13:00
            $horarios[] = [
                'doctor_id'   => $doctor->id,
                'dia_semana'  => 6,
                'hora_inicio' => '09:00:00',
                'hora_fin'    => '13:00:00',
                'created_at'  => $now,
                'updated_at'  => $now,
            ];

            // 5. Inserción masiva del horario
            DB::table('doctor_disponibilidad')->insert($horarios);

            Log::info("✅ Actualizado: {$user->email} (Citas Activas + Horario M-S)");
        }

        Log::info("Seeder completado con éxito.");
    }
}