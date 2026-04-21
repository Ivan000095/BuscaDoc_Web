<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Cita;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class RecordarCitas extends Command
{
    protected $signature = 'citas:recordar';
    protected $description = 'Envía notificación push 1 hora antes de la cita confirmada';

    public function handle()
    {
        // Calculamos la hora exacta que será dentro de 60 minutos
        $dentroDeUnaHora = Carbon::now()->addHour()->format('H:i');
        $hoy = Carbon::now()->format('Y-m-d');

        // Buscamos las citas confirmadas para hoy, a esa hora exacta
        $citas = Cita::where('estado', 'confirmada')
            ->where('fecha', $hoy)
            ->where('hora_inicio', 'like', $dentroDeUnaHora . '%')
            ->with(['expediente.user', 'doctor.user'])
            ->get();

        foreach ($citas as $cita) {
            $paciente = $cita->expediente->user;
            
            // Si el paciente tiene la app instalada (fcm_token)
            if ($paciente && $paciente->fcm_token) {
                Http::withHeaders([
                    'Authorization' => 'key=' . env('FCM_SERVER_KEY'),
                    'Content-Type'  => 'application/json',
                ])->post('https://fcm.googleapis.com/fcm/send', [
                    'to' => $paciente->fcm_token,
                    'notification' => [
                        'title' => '¡Tu cita es en 1 hora!',
                        'body'  => 'Recuerda tu consulta con el Dr. ' . $cita->doctor->user->name . ' a las ' . Carbon::parse($cita->hora_inicio)->format('h:i A'),
                        'sound' => 'default'
                    ]
                ]);
            }
        }
    }
}