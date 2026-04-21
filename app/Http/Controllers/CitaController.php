<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cita;
use App\Models\Doctor;
use App\Models\Expediente;
use App\Models\SolicitudCambio;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class CitaController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        if ($user->role == 'paciente') {
            $expedientesIds = $user->expedientes->pluck('id');
            $citas = Cita::whereIn('expediente_id', $expedientesIds)
                ->with(['doctor.user', 'expediente'])
                ->orderBy('fecha', 'desc')
                ->get();
            return view('pacientes.citas', compact('citas'));
        }

        if ($user->role == 'doctor') {
            $citas = Cita::where('doctor_id', $user->doctor->id)
                ->with('expediente')
                ->orderBy('fecha', 'desc')
                ->get();
            return view('doctores.citas', compact('citas'));
        }
    }

    public function store(Request $request, $id)
    {
        $user = Auth::user();
        $rules = [
            'fecha' => 'required|date|after_or_equal:today',
            'hora_inicio' => 'required',
            'expediente_id' => 'required',
            'motivo_consulta' => 'required|string|max:500',
        ];

        if ($request->expediente_id === 'nuevo_familiar') {
            $rules['nuevo_nombre'] = 'required|string|max:80';
            $rules['nuevo_fecha_nacimiento'] = 'required|date';
            $rules['nuevo_genero'] = 'required|in:masculino,femenino';
            $rules['nuevo_parentesco'] = 'required|string|max:30';
            $rules['nuevo_tipo_sangre'] = 'nullable|string|max:5';
            $rules['nuevo_alergias'] = 'nullable|string';
            $rules['nuevo_padecimientos'] = 'nullable|string';
            $rules['nuevo_habitos'] = 'nullable|string';
        }

        $request->validate($rules);
        $doctor = Doctor::findOrFail($id);

        return DB::transaction(function () use ($request, $doctor, $user) {
            $finalExpedienteId = $request->expediente_id;

            if ($finalExpedienteId === 'nuevo_familiar') {
                $nuevoExpediente = Expediente::create([
                    'user_id' => $user->id,
                    'nombre_completo' => (string) $request->nuevo_nombre,
                    'fecha_nacimiento' => $request->nuevo_fecha_nacimiento,
                    'genero' => (string) $request->nuevo_genero,
                    'parentesco' => (string) $request->nuevo_parentesco,
                    'tipo_sangre' => (string) $request->nuevo_tipo_sangre,
                    'alergias' => is_array($request->nuevo_alergias) ? implode(', ', $request->nuevo_alergias) : $request->nuevo_alergias,
                    'padecimientos_cronicos' => is_array($request->nuevo_padecimientos) ? implode(', ', $request->nuevo_padecimientos) : $request->nuevo_padecimientos,
                    'habitos_salud' => is_array($request->nuevo_habitos) ? implode(', ', $request->nuevo_habitos) : $request->nuevo_habitos,
                ]);
                $finalExpedienteId = $nuevoExpediente->id;
            }

            $horaInicio = Carbon::parse($request->fecha . ' ' . $request->hora_inicio);

            $existeCita = Cita::where('doctor_id', $doctor->id)
                ->where('fecha', $request->fecha)
                ->where('estado', '!=', 'cancelada')
                ->where(function ($query) use ($horaInicio) {
                    $query->where('hora_inicio', '<', $horaInicio->format('H:i:s'))
                        ->where('hora_inicio', '>', $horaInicio->format('H:i:s'));
                })->exists();

            if ($existeCita) {
                return redirect()->route('home')->with('error', 'El horario acaba de ser ocupado.');
            }

            if($user->role == 'paciente'){
                $cita = Cita::create([
                    'expediente_id' => $finalExpedienteId,
                    'doctor_id' => $doctor->id,
                    'fecha' => $request->fecha,
                    'hora_inicio' => $horaInicio->format('H:i:s'),
                    'motivo_consulta' => $request->motivo_consulta,
                    'estado' => 'pendiente',
                ]);
                
                $this->notificarUsuario($doctor->user_id, "¡Nueva solicitud de cita!", "Un paciente ha solicitado una cita para el " . $request->fecha);
                return redirect()->route('pacientes.citas')->with('success', 'Solicitud enviada correctamente!!');
            
            } elseif($user->role == 'doctor') {
                Cita::create([
                    'expediente_id' => $finalExpedienteId,
                    'doctor_id' => $doctor->id,
                    'fecha' => $request->fecha,
                    'hora_inicio' => $horaInicio->format('H:i:s'),
                    'motivo_consulta' => $request->motivo_consulta,
                    'estado' => 'confirmada',
                ]);
                return redirect()->route('doctores.citas')->with('success', 'Cita programada correctamente!!');
            }
        });
    }

    public function storeExterna(Request $request)
    {
        $request->validate([
            'tipo_paciente' => 'required|in:existente,nuevo',
            'nueva_fecha' => 'required|date',
            'nueva_hora' => 'required',
        ]);

        $user = Auth::user();
        $doctor = $user->doctor;

        if ($request->tipo_paciente == 'nuevo') {
            $request->validate([
                'nombre_completo' => 'required|string|max:80',
                'fecha_nacimiento' => 'required|date',
                'genero' => 'required|in:masculino,femenino,otro',
            ]);

            $expediente = Expediente::create([
                'user_id' => $user->id,
                'nombre_completo' => $request->nombre_completo,
                'fecha_nacimiento' => $request->fecha_nacimiento,
                'genero' => $request->genero,
                'parentesco' => 'Paciente Externo',
            ]);
        } else {
            $request->validate(['expediente_id' => 'required|exists:expedientes,id']);
            $expediente = Expediente::findOrFail($request->expediente_id);
        }

        Cita::create([
            'expediente_id' => $expediente->id,
            'doctor_id' => $doctor->id,
            'fecha' => $request->nueva_fecha,
            'hora_inicio' => $request->nueva_hora,
            'motivo_consulta' => $request->motivo_consulta ?? 'Cita registrada externamente por el doctor.',
            'estado' => 'confirmada'
        ]);

        return back()->with('success', 'Cita externa agendada correctamente.');
    }

    public function updateStatus(Request $request, $id)
    {
        $cita = Cita::findOrFail($id);
        $user = Auth::user();

        $esDoctorOwner = $user->doctor && $user->doctor->id === $cita->doctor_id;
        $esPacienteOwner = $cita->expediente->user_id === $user->id;

        if (!$esDoctorOwner && !$esPacienteOwner) {
            return back()->with('error', 'No tienes permiso para gestionar esta cita.');
        }

        if ($esPacienteOwner && $request->estado !== 'cancelada') {
            return back()->with('error', 'Acción no permitida. Solo puedes cancelar tu solicitud.');
        }

        if (in_array($request->estado, ['finalizada', 'no asistida'])) {
            $fechaCita = Carbon::parse($cita->fecha->format('Y-m-d') . ' ' . $cita->hora_inicio);
            if ($fechaCita->isFuture()) {
                return back()->with('error', 'No puedes finalizar una cita que aún no ha ocurrido.');
            }
        }

        $cita->update(['estado' => $request->estado]);

        $this->notificarContraparte($cita, $user->id, "Actualización de Cita", "El estado de tu cita ha cambiado a: " . strtoupper($request->estado));

        $mensajes = [
            'confirmada' => 'Cita confirmada.',
            'cancelada'  => 'Cita cancelada.',
            'finalizada' => 'Cita marcada como finalizada con éxito.',
            'no asistida' => 'Se registró que el paciente no asistió.'
        ];

        return back()->with('success', $mensajes[$request->estado] ?? 'Estado de la cita actualizado.');
    }

    public function reprogramarLibre(Request $request, $id)
    {
        $cita = Cita::findOrFail($id);

        if ($cita->estado !== 'pendiente') {
            return redirect()->route('pacientes.citas')->with('error', 'Solo puedes reagendar citas que aún están en espera.');
        }

        if ($cita->reprogramada) {
            return redirect()->route('pacientes.citas')->with('error', 'Ya has agotado tu oportunidad de reprogramar esta cita.');
        }

        $request->validate([
            'nueva_fecha' => 'required|date|after_or_equal:today',
            'nueva_hora' => 'required',
        ]);

        $cita->update([
            'fecha' => $request->nueva_fecha,
            'hora_inicio' => $request->nueva_hora,
            'reprogramada' => true
        ]);

        $this->notificarUsuario($cita->doctor->user_id, "Cita Reagendada", "El paciente ha movido su cita a un nuevo horario.");

        return redirect()->route('pacientes.citas')->with('success', 'Cita reprogramada con éxito. Recuerda que es el único cambio permitido.');
    }

    public function solicitarCambio(Request $request, $id)
    {
        $cita = Cita::findOrFail($id);
        $user = Auth::user();
        
        $yaPropuso = SolicitudCambio::where('cita_id', $id)->where('solicitante_id', $user->id)->exists();

        if ($yaPropuso) {
            return back()->with('error', 'Ya utilizaste tu única oportunidad de proponer un cambio para esta cita.');
        }

        $existePendiente = SolicitudCambio::where('cita_id', $id)->where('solicitante_id', $user->id)->where('estado', 'pendiente')->exists();
        $estadoDiferente = SolicitudCambio::where('cita_id', $id)->where('solicitante_id', $user->id)->where('estado','!=', 'pendiente')->first();

        if ($existePendiente) {
            $ruta = $user->role == 'doctor' ? 'doctores.citas' : 'pacientes.citas';
            return redirect()->route($ruta)->with('error', 'Ya hay una solicitud de cambio pendiente para esta cita.');
        }

        $solicitadoId = ($user->id == $cita->expediente->user_id) ? $cita->doctor->user_id : $cita->expediente->user_id;

        if(!$estadoDiferente){
            SolicitudCambio::create([
                'cita_id' => $cita->id,
                'solicitante_id' => $user->id,
                'solicitado_id' => $solicitadoId,
                'nueva_fecha' => $request->nueva_fecha,
                'nueva_hora' => $request->nueva_hora,
                'motivo' => $request->motivo,
                'estado' => 'pendiente'
            ]);
        }else{
            $estadoDiferente->update([
                'nueva_fecha' => $request->nueva_fecha,
                'nueva_hora' => $request->nueva_hora,
                'motivo' => $request->motivo,
                'estado' => 'pendiente'
            ]);
        }

        $this->notificarUsuario($solicitadoId, "Propuesta de Cambio", "Tienes una nueva propuesta de horario para tu cita.");

        $ruta = $user->role == 'doctor' ? 'doctores.citas' : 'pacientes.citas';
        return redirect()->route($ruta)->with('success', 'Solicitud enviada');
    }

    public function responderCambio(Request $request, $id)
    {
        $user = Auth::user();
        $solicitud = SolicitudCambio::where('cita_id', $id)->where('solicitado_id', $user->id)->where('estado', 'pendiente')->first();

        if (!$solicitud) {
            return back()->with('error', 'La solicitud ya no está disponible o ya fue respondida.');
        }

        if ($request->accion == 'aceptar') {
            if($solicitud->cita->estado == 'confirmada'){
                $solicitud->cita->update(['fecha' => $solicitud->nueva_fecha, 'hora_inicio' => $solicitud->nueva_hora]);
            }else{
                $solicitud->cita->update(['fecha' => $solicitud->nueva_fecha, 'hora_inicio' => $solicitud->nueva_hora, 'estado' => 'confirmada']);
            }
            $solicitud->update(['estado' => 'aceptada']);
            $this->notificarUsuario($solicitud->solicitante_id, "Propuesta Aceptada", "Tu propuesta de horario fue aceptada.");
            $msg = 'Se ha actualizado la fecha de la cita.';
        } else {
            $solicitud->update(['estado' => 'rechazada', 'motivo' => $request->motivo_rechazo]);
            $this->notificarUsuario($solicitud->solicitante_id, "Propuesta Rechazada", "Tu propuesta de horario fue rechazada.");
            $msg = 'Has rechazado la solicitud de cambio.';
        }

        $ruta = $user->role == 'paciente' ? 'pacientes.citas' : 'doctores.citas';
        return redirect()->route($ruta)->with('success', $msg);
    }

    public function getDisponibilidad(Request $request, $doctorId)
    {
        try {
            $fecha = $request->query('fecha');
            $date = Carbon::parse($fecha);
            $esHoy = $date->isToday(); 
            $ahora = Carbon::now(); 
            $diaNumero = $date->dayOfWeek; 

            $horariosLaborales = DB::table('doctor_disponibilidad')
                ->where('doctor_id', $doctorId)
                ->where('dia_semana', $diaNumero)
                ->get();

            if ($horariosLaborales->isEmpty()) {
                $nombres = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
                return response()->json(['slots' => [], 'mensaje' => "El doctor no tiene turnos registrados para el día " . $nombres[$diaNumero] . "."]);
            }

            $doctor = Doctor::findOrFail($doctorId);
            $intervalo = $doctor->duracion_cita ?? 30;

            $citasOcupadas = Cita::where('doctor_id', $doctorId)
                ->where('fecha', $fecha)
                ->where('estado', '!=', 'cancelada')
                ->pluck('hora_inicio')
                ->map(fn($h) => Carbon::parse($h)->format('H:i'))
                ->toArray();

            $slots = [];

            foreach ($horariosLaborales as $horario) {
                $inicio = Carbon::parse($fecha . ' ' . $horario->hora_inicio);
                $fin = Carbon::parse($fecha . ' ' . $horario->hora_fin);

                while ($inicio->lt($fin)) {
                    $horaActualStr = $inicio->format('H:i');
                    $estaOcupada = in_array($horaActualStr, $citasOcupadas);
                    $yaPaso = $esHoy && $inicio->lt($ahora);

                    if (!$estaOcupada && !$yaPaso) {
                        $slots[] = $horaActualStr;
                    }
                    $inicio->addMinutes($intervalo);
                }
            }
            return response()->json(['slots' => $slots]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        $cita = Cita::findOrFail($id);
        if (in_array($cita->estado, ['cancelada', 'rechazada', 'finalizada'])) {
            $cita->delete(); 
            return back()->with('success', 'La cita se ha quitado de tu vista.');
        }
        return back()->with('error', 'No puedes ocultar una cita que aún está pendiente.');
    }

    // ==========================================
    // NOTIFICACIONES PUSH (HELPER)
    // ==========================================
    private function notificarContraparte($cita, $actorId, $title, $body)
    {
        $receptorId = ($actorId == $cita->expediente->user_id) ? $cita->doctor->user_id : $cita->expediente->user_id;
        $this->notificarUsuario($receptorId, $title, $body);
    }

    private function notificarUsuario($userId, $title, $body)
    {
        $receptor = \App\Models\User::find($userId);
        if ($receptor && $receptor->fcm_token) {
            $serverKey = env('FCM_SERVER_KEY');
            if($serverKey){
                Http::withHeaders([
                    'Authorization' => 'key=' . $serverKey,
                    'Content-Type'  => 'application/json',
                ])->post('https://fcm.googleapis.com/fcm/send', [
                    'to' => $receptor->fcm_token,
                    'notification' => [
                        'title' => $title,
                        'body'  => $body,
                        'sound' => 'default'
                    ]
                ]);
            }
        }
    }
}