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
                // Convertimos el modelo a Array para que acepte nuevos campos sin problemas
                $citaArray = $cita->toArray();

                $citaArray['solicitud_recibida'] = \App\Models\SolicitudCambio::where('cita_id', $cita->id)
                    ->where('solicitado_id', $user->id)
                    ->where('estado', 'pendiente')->first();
                    
                $citaArray['solicitud_enviada'] = \App\Models\SolicitudCambio::where('cita_id', $cita->id)
                    ->where('solicitante_id', $user->id)
                    ->where('estado', 'pendiente')->first();
                    
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

            $existePendiente = SolicitudCambio::where('cita_id', $id)
                ->where('solicitante_id', $user->id)
                ->where('estado', 'pendiente')
                ->exists();

            if ($existePendiente) {
                return response()->json(['success' => false, 'message' => 'Ya hay una solicitud pendiente.'], 400);
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
}