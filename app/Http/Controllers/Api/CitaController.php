<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cita;
use App\Models\SolicitudCambio;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CitaController extends Controller
{
    public function index(Request $request)
    {
        try {
            $user = $request->user();
            
            // 1. Inicializamos como Colección, no como arreglo normal '[]'
            $citas = collect();

            if ($user->role == 'paciente') {
                $expedientesIds = $user->expedientes->pluck('id');
                
                $citas = Cita::whereIn('expediente_id', $expedientesIds)
                    // Importante: Traer especialidades del doctor para que Flutter no marque error al buscar [0]['nombre']
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

            // 2. Usamos map() para forzar la inyección de los datos en el JSON
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

            return response()->json([
                'success' => true,
                'data' => $data
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar citas',
                'error' => $e->getMessage() . ' en linea ' . $e->getLine()
            ], 500);
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

            return response()->json([
                'success' => true,
                'message' => 'Estado actualizado a ' . $request->estado
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al actualizar.'], 500);
        }
    }

    public function solicitarCambio(Request $request, $id)
    {
        try {
            $cita = Cita::findOrFail($id);
            $user = $request->user();

            $yaPropuso = SolicitudCambio::where('cita_id', $id)
                ->where('solicitante_id', $user->id)
                ->exists();

            if ($yaPropuso) {
                return response()->json(['success' => false, 'message' => 'Ya utilizaste tu única oportunidad de proponer un cambio.'], 400);
            }

            $solicitadoId = ($user->id == $cita->expediente->user_id) 
                ? $cita->doctor->user_id 
                : $cita->expediente->user_id;

            SolicitudCambio::updateOrCreate(
                ['cita_id' => $cita->id, 'solicitante_id' => $user->id, 'estado' => 'rechazada'], // Si había rechazada la sobreescribe
                [
                    'solicitado_id' => $solicitadoId,
                    'nueva_fecha' => $request->nueva_fecha,
                    'nueva_hora' => $request->nueva_hora,
                    'motivo' => $request->motivo,
                    'estado' => 'pendiente'
                ]
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
            $solicitud = SolicitudCambio::where('cita_id', $id)
                ->where('solicitado_id', $user->id)
                ->where('estado', 'pendiente')
                ->firstOrFail();

            if ($request->accion == 'aceptar') {
                $solicitud->cita->update([
                    'fecha' => $solicitud->nueva_fecha,
                    'hora_inicio' => $solicitud->nueva_hora,
                    'estado' => 'confirmada'
                ]);
                $solicitud->update(['estado' => 'aceptada']);
                $msg = 'Fecha actualizada correctamente.';
            } else {
                $solicitud->update([
                    'estado' => 'rechazada',
                    'motivo' => $request->motivo_rechazo
                ]);
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

            // CANDADOS
            if ($cita->estado !== 'pendiente') return response()->json(['success' => false, 'message' => 'Solo puedes reagendar citas en espera.'], 400);
            if ($cita->reprogramada) return response()->json(['success' => false, 'message' => 'Ya has agotado tu oportunidad de reprogramar esta cita.'], 400);

            $cita->update([
                'fecha' => $request->nueva_fecha,
                'hora_inicio' => $request->nueva_hora,
                'reprogramada' => true // Bloqueamos futuros cambios
            ]);

            return response()->json(['success' => true, 'message' => 'Cita reprogramada con éxito.'], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al reprogramar.'], 500);
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

    public function store(Request $request, $id)
    {
        try {
            $user = $request->user();

            // 1. Configurar reglas de validación
            $rules = [
                'fecha' => 'required|date|after_or_equal:today',
                'hora_inicio' => 'required',
                'expediente_id' => 'required',
                'motivo_consulta' => 'required|string|max:500',
            ];

            // Reglas adicionales si se crea un nuevo familiar desde la app
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

            // 2. Ejecutar validación manualmente para devolver JSON estructurado
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Faltan datos obligatorios o son incorrectos.',
                    'errors' => $validator->errors()
                ], 422);
            }

            $doctor = \App\Models\Doctor::findOrFail($id);

            // 3. Ejecutar transacción de base de datos
            $resultado = DB::transaction(function () use ($request, $doctor, $user) {
                
                $finalExpedienteId = $request->expediente_id;

                // Creación de expediente para nuevo familiar si es necesario
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

                // Verificación real de traslapes (Comprueba si el horario exacto ya está ocupado)
                $existeCita = \App\Models\Cita::where('doctor_id', $doctor->id)
                    ->where('fecha', $request->fecha)
                    ->whereIn('estado', ['pendiente', 'confirmada']) // Solo buscamos citas activas
                    ->where('hora_inicio', $horaInicio->format('H:i:s'))
                    ->exists();

                if ($existeCita) {
                    return ['status' => 'overlap'];
                }

                // Determinamos el estado según el rol (Si el doc se agenda a sí mismo, ya entra confirmada)
                $estadoFinal = ($user->role == 'doctor') ? 'confirmada' : 'pendiente';

                // Crear la Cita
                $cita = \App\Models\Cita::create([
                    'expediente_id'   => $finalExpedienteId,
                    'doctor_id'       => $doctor->id,
                    'fecha'           => $request->fecha,
                    'hora_inicio'     => $horaInicio->format('H:i:s'),
                    'motivo_consulta' => $request->motivo_consulta,
                    'estado'          => $estadoFinal,
                ]);

                return ['status' => 'success', 'cita' => $cita];
            });

            // 4. Evaluar el resultado de la transacción
            if ($resultado['status'] === 'overlap') {
                return response()->json([
                    'success' => false, 
                    'message' => 'Lo sentimos, el horario acaba de ser ocupado por otro paciente.'
                ], 409); // 409 Conflict
            }

            return response()->json([
                'success' => true, 
                'message' => 'Cita programada correctamente!!',
                'data' => $resultado['cita']
            ], 201); // 201 Created

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error interno al intentar agendar la cita.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}