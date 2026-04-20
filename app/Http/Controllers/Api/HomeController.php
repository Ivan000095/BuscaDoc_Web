<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\User;
use App\Models\Cita;

class HomeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function getHomeData(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            $data = [
                'role' => $user->role,
                'proxima_cita' => null,
                'ultima_review' => null,
                'ultima_question' => null,
                'rutas' => []
            ];

            // 1. CARGAMOS LAS RUTAS PARA EL MAPA (Se cargan para todos los roles)
            // Esto asegura que el mapa de Flutter siempre tenga datos
            $data['rutas'] = User::whereNotNull('latitud')
                ->whereNotNull('longitud')
                ->where('estado', true) // Solo usuarios activos
                ->whereIn('role', ['doctor', 'farmacia'])
                ->select('id', 'name', 'role', 'latitud', 'longitud', 'foto')
                ->get();

            // 2. LÓGICA PARA PACIENTES
            if ($user->role === 'paciente') {
                // Buscamos la próxima cita usando la relación con expedientes
                // (Ya que eliminaste la tabla pacientes y ahora usas expedientes)
                $expedientesIds = $user->expedientes()->pluck('id');
                
                if ($expedientesIds->isNotEmpty()) {
                    $data['proxima_cita'] = Cita::with(['doctor.user'])
                        ->whereIn('expediente_id', $expedientesIds)
                        // Combinamos fecha y hora_inicio ya que los separaste en la migración
                        ->whereRaw("CONCAT(fecha, ' ', hora_inicio) >= ?", [now()->format('Y-m-d H:i:s')])
                        ->where('estado', '!=', 'cancelada')
                        ->orderBy('fecha', 'asc')
                        ->orderBy('hora_inicio', 'asc')
                        ->first();
                }
            }

            // 3. LÓGICA PARA DOCTORES
            if ($user->role === 'doctor' && $user->doctor) {
                // Próxima cita del doctor
                $data['proxima_cita'] = $user->doctor->citas()
                    ->with(['expediente']) // Cambiado de paciente.user a expediente
                    ->whereRaw("CONCAT(fecha, ' ', hora_inicio) >= ?", [now()->format('Y-m-d H:i:s')])
                    ->whereIn('estado', ['pendiente', 'confirmada'])
                    ->orderBy('fecha', 'asc')
                    ->orderBy('hora_inicio', 'asc')
                    ->first();

                // Últimos comentarios
                $data['ultima_review'] = \App\Models\Comentario::where('id_destinatario', $user->id)
                    ->where('tipo', 'review')
                    ->with('autor:id,name,foto')
                    ->latest()
                    ->first();

                $data['ultima_question'] = \App\Models\Comentario::where('id_destinatario', $user->id)
                    ->where('tipo', 'question')
                    ->with('autor:id,name,foto')
                    ->latest()
                    ->first();
            }

            return response()->json([
                'success' => true,
                'message' => 'Datos del dashboard cargados correctamente.',
                'data' => $data
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error interno al cargar el dashboard.',
                'error' => $e->getMessage() . ' en línea ' . $e->getLine()
            ], 500);
        }
    }
}