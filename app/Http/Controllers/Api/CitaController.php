<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cita;
use App\Models\SolicitudCambio;
use App\Models\Alerta; // Importamos el modelo de Alerta
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class CitaController extends Controller
{
    public function index(Request $request)
    {
        try {
            $user = $request->user();
            $citas = collect();

            if ($user->role == 'paciente') {
                $expedientesIds = $user->expedientes->pluck('id');
                $citas = Cita::whereIn('expediente_id', $expedientesIds)
                    ->with(['doctor.user', 'doctor.especialidades', 'expediente']) 
                    ->orderBy('fecha', 'desc')
                    ->orderBy('hora_inicio', 'desc')
                    ->get();
            } elseif ($user->role == 'doctor' && $user->doctor) {
                $citas = Cita::where('doctor_id', $user->doctor->id)
                    ->with(['expediente.user', 'doctor.user'])
                    ->orderBy('fecha', 'desc')
                    ->orderBy('hora_inicio', 'desc')
                    ->get();
            }

            $data = $citas->map(function ($cita) use ($user) {
                $citaArray = $cita->toArray();

                $citaArray['solicitud_recibida'] = SolicitudCambio::where('cita_id', $cita->id)
                    ->where('solicitado_id', $user->id)
                    ->where('estado', 'pendiente')->first();
                    
                $citaArray['solicitud_enviada'] = SolicitudCambio::where('cita_id', $cita->id)
                    ->where('solicitante_id', $user->id)
                    ->where('estado', 'pendiente')->first();
                
                $citaArray['ya_propuso_cambio'] = SolicitudCambio::where('cita_id', $cita->id)
                    ->where('solicitante_id', $user->id)
                    ->exists(); 
                    
                return $citaArray;
            });

            return response()->json(['success' => true, 'data' => $data], 200);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al cargar citas', 'error' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request, $id)
    {
        try {
            $user = $request->user();
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
            }

            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'message' => 'Faltan datos obligatorios.', 'errors' => $validator->errors()], 422);
            }

            $doctor = \App\Models\Doctor::findOrFail($id);

            $resultado = DB::transaction(function () use ($request, $doctor, $user) {
                $finalExpedienteId = $request->expediente_id;

                if ($finalExpedienteId === 'nuevo_familiar') {
                    $nuevoExpediente = \App\Models\Expediente::create([
                        'user_id'                => $user->id,
                        'nombre_completo'        => (string) $request->nuevo_nombre,
                        'fecha_nacimiento'       => $request->nuevo_fecha_nacimiento,
                        'genero'                 => (string) $request->nuevo_genero,
                        'parentesco'             => (string) $request->nuevo_parentesco,
                        'tipo_sangre'            => (string) $request->nuevo_tipo_sangre,
                        'alergias'               => is_array($request->nuevo_alergias) ? implode(', ', $request->nuevo_alergias) : $request->nuevo_alergias,
                        'padecimientos_cronicos' => is_array($request->nuevo_padecimientos) ? implode(', ', $request->nuevo_padecimientos) : $request->nuevo_padecimientos,
                        'habitos_salud'          => is_array($request->nuevo_habitos) ? implode(', ', $request->nuevo_habitos) : $request->nuevo_habitos,
                    ]);
                    $finalExpedienteId = $nuevoExpediente->id;
                }

                $horaInicio = Carbon::parse($request->fecha . ' ' . $request->hora_inicio);

                $existeCita = Cita::where('doctor_id', $doctor->id)
                    ->where('fecha', $request->fecha)
                    ->whereIn('estado', ['pendiente', 'confirmada'])
                    ->where('hora_inicio', $horaInicio->format('H:i:s'))
                    ->exists();

                if ($existeCita) {
                    return ['status' => 'overlap'];
                }

                $estadoFinal = ($user->role == 'doctor') ? 'confirmada' : 'pendiente';

                $cita = Cita::create([
                    'expediente_id'   => $finalExpedienteId,
                    'doctor_id'       => $doctor->id,
                    'fecha'           => $request->fecha,
                    'hora_inicio'     => $horaInicio->format('H:i:s'),
                    'motivo_consulta' => $request->motivo_consulta,
                    'estado'          => $estadoFinal,
                ]);

                if($user->role == 'paciente'){
                    $this->crearAlerta(
                        $doctor->user_id, 
                        "¡Nueva solicitud de cita!", 
                        "El paciente " . $user->name . " solicitó una cita para el " . $request->fecha,
                        'cita',
                        $cita->id
                    );
                }

                return ['status' => 'success', 'cita' => $cita];
            });

            if ($resultado['status'] === 'overlap') {
                return response()->json(['success' => false, 'message' => 'Lo sentimos, el horario acaba de ser ocupado.'], 409);
            }

            return response()->json(['success' => true, 'message' => 'Cita programada correctamente!!', 'data' => $resultado['cita']], 201);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Ocurrió un error interno.', 'error' => $e->getMessage()], 500);
        }
    }

    public function updateStatus(Request $request, $id)
    {
        try {
            $cita = Cita::findOrFail($id);
            $user = $request->user();

            $esDoctorOwner = $user->doctor && $user->doctor->id === $cita->doctor_id;
            $esPacienteOwner = $cita->expediente->user_id === $user->id;

            if (!$esDoctorOwner && (!$esPacienteOwner || $request->estado !== 'cancelada')) {
                return response()->json(['success' => false, 'message' => 'No tienes permisos.'], 403);
            }

            $cita->update(['estado' => $request->estado]);
            
            $this->notificarContraparte(
                $cita, 
                $user->id, 
                "Actualización de Cita", 
                "El estado de tu cita ha cambiado a: " . strtoupper($request->estado),
                'cita'
            );

            return response()->json(['success' => true, 'message' => 'Estado actualizado a ' . $request->estado], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al actualizar.'], 500);
        }
    }

    public function solicitarCambio(Request $request, $id)
    {
        try {
            $cita = Cita::findOrFail($id);
            $user = $request->user();

            $yaPropuso = SolicitudCambio::where('cita_id', $id)->where('solicitante_id', $user->id)->exists();

            if ($yaPropuso) {
                return response()->json(['success' => false, 'message' => 'Ya utilizaste tu única oportunidad de proponer un cambio.'], 400);
            }

            $solicitadoId = ($user->id == $cita->expediente->user_id) ? $cita->doctor->user_id : $cita->expediente->user_id;

            SolicitudCambio::updateOrCreate(
                ['cita_id' => $cita->id, 'solicitante_id' => $user->id, 'estado' => 'rechazada'],
                [
                    'solicitado_id' => $solicitadoId,
                    'nueva_fecha' => $request->nueva_fecha,
                    'nueva_hora' => $request->nueva_hora,
                    'motivo' => $request->motivo,
                    'estado' => 'pendiente'
                ]
            );

            $this->crearAlerta(
                $solicitadoId, 
                "Propuesta de Cambio", 
                $user->name . " ha propuesto un nuevo horario para tu cita.",
                'cita',
                $cita->id
            );

            return response()->json(['success' => true, 'message' => 'Propuesta enviada.'], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al solicitar cambio.'], 500);
        }
    }

    public function responderCambio(Request $request, $id)
    {
        try {
            $user = $request->user();
            $solicitud = SolicitudCambio::where('cita_id', $id)->where('solicitado_id', $user->id)->where('estado', 'pendiente')->firstOrFail();

            if ($request->accion == 'aceptar') {
                $solicitud->cita->update(['fecha' => $solicitud->nueva_fecha, 'hora_inicio' => $solicitud->nueva_hora, 'estado' => 'confirmada']);
                $solicitud->update(['estado' => 'aceptada']);
                
                $this->crearAlerta(
                    $solicitud->solicitante_id, 
                    "Propuesta Aceptada", 
                    "Tu propuesta de horario fue aceptada y la cita ha sido actualizada.",
                    'cita',
                    $solicitud->cita_id
                );
                
                $msg = 'Fecha actualizada correctamente.';
            } else {
                $solicitud->update(['estado' => 'rechazada', 'motivo' => $request->motivo_rechazo]);
                
                $this->crearAlerta(
                    $solicitud->solicitante_id, 
                    "Propuesta Rechazada", 
                    "Tu propuesta de horario fue rechazada. Motivo: " . $request->motivo_rechazo,
                    'cita',
                    $solicitud->cita_id
                );
                
                $msg = 'Propuesta rechazada.';
            }

            return response()->json(['success' => true, 'message' => $msg], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al procesar respuesta.'], 500);
        }
    }

    public function reprogramarLibre(Request $request, $id)
    {
        try {
            $cita = Cita::findOrFail($id);

            if ($cita->estado !== 'pendiente') return response()->json(['success' => false, 'message' => 'Solo puedes reagendar citas en espera.'], 400);
            if ($cita->reprogramada) return response()->json(['success' => false, 'message' => 'Ya has agotado tu oportunidad de reprogramar esta cita.'], 400);

            $cita->update([
                'fecha' => $request->nueva_fecha,
                'hora_inicio' => $request->nueva_hora,
                'reprogramada' => true
            ]);

            $this->crearAlerta(
                $cita->doctor->user_id, 
                "Cita Reagendada", 
                "El paciente ha movido su cita al " . $request->nueva_fecha,
                'cita',
                $cita->id
            );

            return response()->json(['success' => true, 'message' => 'Cita reprogramada con éxito.'], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al reprogramar.'], 500);
        }
    }

    public function finalizarConDiagnostico(Request $request, $id)
    {
        try {
            $cita = Cita::findOrFail($id);
            $request->validate(['diagnostico' => 'required|string|max:1000']);

            $cita->update(['estado' => 'finalizada']);

            \App\Models\NotaMedica::create([
                'cita_id' => $cita->id,
                'expediente_id' => $cita->expediente_id,
                'doctor_id' => $cita->doctor_id,
                'diagnostico' => $request->diagnostico,
            ]);

            $this->notificarContraparte(
                $cita, 
                $request->user()->id, 
                "Consulta Finalizada", 
                "El doctor ha finalizado la consulta y agregado una nota médica a tu expediente.",
                'cita'
            );

            return response()->json(['success' => true, 'message' => 'Cita finalizada y diagnóstico guardado.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al guardar el diagnóstico.'], 500);
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            $cita = Cita::findOrFail($id);
            if (in_array($cita->estado, ['cancelada', 'rechazada', 'finalizada', 'no asistida'])) {
                $cita->delete();
                return response()->json(['success' => true, 'message' => 'Cita eliminada de tu vista.'], 200);
            }
            return response()->json(['success' => false, 'message' => 'No puedes ocultar una cita activa.'], 400);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al eliminar.'], 500);
        }
    }

    // ==========================================
    // ALERTAS NATIVAS Y PUSH (HELPERS)
    // ==========================================
    
    private function notificarContraparte($cita, $actorId, $title, $body, $tipo)
    {
        $receptorId = ($actorId == $cita->expediente->user_id) ? $cita->doctor->user_id : $cita->expediente->user_id;
        $this->crearAlerta($receptorId, $title, $body, $tipo, $cita->id);
    }

    private function crearAlerta($userId, $title, $body, $tipo, $referenciaId)
    {
        // 1. Guardar Alerta en Base de Datos
        Alerta::create([
            'user_id'       => $userId,
            'titulo'        => $title,
            'mensaje'       => $body,
            'tipo'          => $tipo,
            'referencia_id' => $referenciaId,
            'leido'         => false
        ]);

        // 2. Intento de Notificación Push (FCM) como respaldo
        $receptor = User::find($userId);
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