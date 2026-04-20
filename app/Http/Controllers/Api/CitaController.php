<?php

namespace App\Http\Controllers\Api;
use App\Models\SolicitudCambio;
use App\Http\Controllers\Controller;
use App\Models\Cita;
use App\Models\Doctor;
use App\Models\Expediente;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class CitaController extends Controller
{

public function store(Request $request): JsonResponse
    {
        $user = $request->user();

                // 2. Validaciones (Ya no pedimos user_id en el JSON)
                $rules = [
                    'doctor_id' => 'required|exists:doctors,id',
                    'fecha' => 'required|date|after_or_equal:today',
                    'hora_inicio' => 'required|date_format:H:i',
                    'expediente_id' => 'required', 
                    'motivo_consulta' => 'required|string|max:500',
                ];

                if ($request->expediente_id === 'nuevo_familiar') {
                    $rules['nuevo_nombre'] = 'required|string|max:80';
                    $rules['nuevo_fecha_nacimiento'] = 'required|date';
                    $rules['nuevo_genero'] = 'required|in:masculino,femenino,otro';
                    $rules['nuevo_parentesco'] = 'required|string|max:30';
                    $rules['nuevo_tipo_sangre'] = 'nullable|string';
                    $rules['nuevo_alergias'] = 'nullable|string';
                    $rules['nuevo_padecimientos'] = 'nullable|string';
                    $rules['nuevo_habitos'] = 'nullable|string';
                }

                $validated = $request->validate($rules);

                try {
                    return DB::transaction(function () use ($request, $validated, $user) {
                        
                        // SEGURIDAD EXTRA: Si no es familiar nuevo, verificamos que el expediente
                        // que envía realmente le pertenezca a este usuario logueado.
                        if ($validated['expediente_id'] !== 'nuevo_familiar') {
                            $expedienteValido = Expediente::where('id', $validated['expediente_id'])
                                ->where('user_id', $user->id)
                                ->exists();

                            if (!$expedienteValido) {
                                throw new \Exception("No tienes permiso para agendar citas con este expediente.");
                            }
                        }

                        $doctor = Doctor::with('disponibilidades')->findOrFail($validated['doctor_id']);
                        $horaCita = Carbon::parse($validated['hora_inicio']);
                        $diaSemana = Carbon::parse($validated['fecha'])->dayOfWeek;

                        // Validar Disponibilidad (si el doctor trabaja a esa hora)
                        $trabajaEsaHora = $doctor->disponibilidades->contains(function ($disponibilidad) use ($diaSemana, $horaCita) {
                            $inicioTurno = Carbon::parse($disponibilidad->hora_inicio);
                            $finTurno = Carbon::parse($disponibilidad->hora_fin);
                            return $disponibilidad->dia_semana == $diaSemana && 
                                $horaCita->between($inicioTurno, $finTurno->subMinutes(15));
                        });

                        if (!$trabajaEsaHora) {
                            throw new \Exception("El horario seleccionado está fuera de la disponibilidad del doctor.");
                        }

                        // Validar Empalmes
                        $citaExistente = Cita::where('doctor_id', $validated['doctor_id'])
                            ->where('fecha', $validated['fecha'])
                            ->where('hora_inicio', $validated['hora_inicio'])
                            ->whereIn('estado', ['pendiente', 'confirmada'])
                            ->exists();

                        if ($citaExistente) {
                            throw new \Exception("El doctor ya tiene una cita agendada en ese horario exacto.");
                        }

                        // CREAR FAMILIAR USANDO EL ID DEL USUARIO AUTENTICADO ($user->id)
                        $expedienteIdFinal = $validated['expediente_id'];
                        
                        if ($expedienteIdFinal === 'nuevo_familiar') {
                            $nuevoExpediente = Expediente::create([
                                'user_id' => $user->id, // ¡AQUÍ ESTÁ LA CORRECCIÓN!
                                'nombre_completo' => $request->nuevo_nombre,
                                'parentesco' => $request->nuevo_parentesco,
                                'genero' => $request->nuevo_genero,
                                'fecha_nacimiento' => $request->nuevo_fecha_nacimiento,
                                'tipo_sangre' => $request->nuevo_tipo_sangre,
                                'alergias' => $request->nuevo_alergias,
                                'padecimientos_cronicos' => $request->nuevo_padecimientos,
                                'habitos_salud' => $request->nuevo_habitos,
                            ]);
                            $expedienteIdFinal = $nuevoExpediente->id;
                        }

                        // CREAR LA CITA
                        $cita = Cita::create([
                            'expediente_id' => $expedienteIdFinal,
                            'doctor_id' => $validated['doctor_id'],
                            'fecha' => $validated['fecha'],
                            'hora_inicio' => $validated['hora_inicio'],
                            'estado' => 'pendiente',
                            'motivo_consulta' => $validated['motivo_consulta'],
                        ]);

                        return response()->json([
                            'success' => true,
                            'message' => 'Cita agendada correctamente',
                            'data' => $cita->load(['doctor.user', 'expediente'])
                        ], 201);
                    });

                } catch (\Exception $e) {
                    return response()->json([
                        'success' => false, 
                        'message' => $e->getMessage()
                    ], 400); 
                }
            }





    



    public function solicitarCambio(Request $request): JsonResponse
        {
            $validated = $request->validate([
                'cita_id' => 'required|exists:citas,id',
                'solicitante_id' => 'required|exists:users,id', // El usuario que presiona el botón
                'nueva_fecha' => 'required|date|after_or_equal:today',
                'nueva_hora' => 'required|date_format:H:i',
                'motivo' => 'nullable|string|max:500',
            ]);

            try {
                return DB::transaction(function () use ($validated) {
                    // Obtenemos la cita con sus relaciones para deducir quién es el "solicitado"
                    $cita = Cita::with(['doctor', 'expediente'])->findOrFail($validated['cita_id']);

                    $doctorUserId = $cita->doctor->user_id;
                    $pacienteUserId = $cita->expediente->user_id;

                    // Identificamos automáticamente al solicitado_id (la contraparte)
                    if ($validated['solicitante_id'] == $doctorUserId) {
                        $solicitadoId = $pacienteUserId;
                    } elseif ($validated['solicitante_id'] == $pacienteUserId) {
                        $solicitadoId = $doctorUserId;
                    } else {
                        throw new \Exception("El usuario solicitante no forma parte de esta cita.");
                    }

                    // Creamos o actualizamos la solicitud pendiente
                    $solicitud = SolicitudCambio::updateOrCreate(
                        ['cita_id' => $cita->id, 'estado' => 'pendiente'],
                        [
                            'solicitante_id' => $validated['solicitante_id'],
                            'solicitado_id' => $solicitadoId, // Se asigna al usuario que DEBE responder
                            'nueva_fecha' => $validated['nueva_fecha'],
                            'nueva_hora' => $validated['nueva_hora'],
                            'motivo' => $validated['motivo'],
                            'estado' => 'pendiente'
                        ]
                    );

                    return response()->json([
                        'success' => true,
                        'message' => 'Solicitud enviada al otro usuario.',
                        'data' => $solicitud
                    ], 201);
                });
            } catch (\Exception $e) {
                return response()->json(['success' => false, 'error' => $e->getMessage()], 400);
            }
        }

        /**
         * Paso 2: Consultar las solicitudes que un usuario DEBE responder
         * (Ideal para el ícono de la campanita o notificaciones en la App)
         */
        public function solicitudesPendientes(Request $request): JsonResponse
        {
            $request->validate(['user_id' => 'required|exists:users,id']);

            try {
                // GRACIAS A TU DISEÑO: Solo buscamos donde él es el "solicitado_id"
                $solicitudes = SolicitudCambio::with(['cita.doctor.user', 'cita.expediente'])
                    ->where('solicitado_id', $request->user_id)
                    ->where('estado', 'pendiente')
                    ->orderBy('created_at', 'desc')
                    ->get();

                return response()->json([
                    'success' => true,
                    'data' => $solicitudes
                ]);
            } catch (\Exception $e) {
                return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
            }
        }

        /**
         * Paso 3: Responder a la solicitud (El solicitado_id la acepta o rechaza)
         */
        public function responderSolicitud(Request $request, $id): JsonResponse
        {
            $validated = $request->validate([
                'accion' => 'required|in:aceptar,rechazar',
                'motivo_respuesta' => 'nullable|string|max:500', 
                'user_id' => 'required|exists:users,id', // Para seguridad, verificar quién responde
            ]);

            try {
                $solicitud = SolicitudCambio::with('cita')->findOrFail($id);

                // Validar que quien responde sea realmente el solicitado_id
                if ($solicitud->solicitado_id != $validated['user_id']) {
                    return response()->json(['success' => false, 'message' => 'No tienes permiso para responder a esta solicitud.'], 403);
                }

                return DB::transaction(function () use ($validated, $solicitud) {
                    if ($validated['accion'] === 'aceptar') {
                        // Actualizamos la cita principal
                        $solicitud->cita->update([
                            'fecha' => $solicitud->nueva_fecha,
                            'hora_inicio' => $solicitud->nueva_hora,
                            'estado' => 'confirmada'
                        ]);
                        $solicitud->update(['estado' => 'aceptada']);
                        $mensaje = "Cita reprogramada con éxito.";
                    } else {
                        // Solo actualizamos la solicitud
                        $solicitud->update([
                            'estado' => 'rechazada',
                            'motivo' => $validated['motivo_respuesta'] ?? $solicitud->motivo
                        ]);
                        $mensaje = "Solicitud rechazada.";
                    }

                    return response()->json([
                        'success' => true,
                        'message' => $mensaje,
                        'data' => $solicitud->load('cita')
                    ]);
                });
            } catch (\Exception $e) {
                return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
            }
        }




    public function show($id): JsonResponse
        {
            try {
                $cita = Cita::with(['doctor.user', 'expediente.user'])->findOrFail($id);
                
                return response()->json([
                    "success" => true,
                    "data" => $cita
                ], 200);
            } catch (\Exception $e) {
                return response()->json(["success" => false, "message" => "Cita no encontrada"], 404);
            }
        }

        /**
        * Actualizar el estado de la cita (Confirmar, Cancelar, Finalizar)
        */
        public function update(Request $request, $id): JsonResponse
        {
            try {
                $cita = Cita::findOrFail($id);

                // Solo permitimos actualizar el estado o el motivo desde este método
                $validated = $request->validate([
                    'estado' => 'sometimes|in:pendiente,confirmada,cancelada,finalizada',
                    'motivo_consulta' => 'sometimes|string|max:500',
                ]);

                $cita->update($validated);

                return response()->json([
                    'success' => true,
                    'message' => 'Cita actualizada',
                    'data' => $cita
                ], 200);

            } catch (\Exception $e) {
                return response()->json([
                    'success' => false, 
                    'error' => $e->getMessage()
                ], 500);
            }
        }

        /**
        * Eliminar la cita (Solo si es estrictamente necesario, 
        * usualmente es mejor cambiar el estado a 'cancelada')
        */
        public function destroy($id): JsonResponse
        {
            try {
                $cita = Cita::findOrFail($id);
                $cita->delete();

                return response()->json([
                    'success' => true,
                    'message' => 'Cita eliminada del sistema'
                ], 200);

            } catch (\Exception $e) {
                return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
            }
        }


}