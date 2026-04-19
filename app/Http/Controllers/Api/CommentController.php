<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comentario;
use App\Models\User;
use App\Models\Doctor;
use App\Models\Cita;
use App\Models\Paciente;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class CommentController extends Controller
{
    /**
     * Listar comentarios y preguntas de un usuario (doctor/farmacia)
     * GET /api/users/{id}/comments?tipo=resena
     */
    public function index($userId, Request $request): JsonResponse
    {
        try {
            $user = User::find($userId);
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuario no encontrado'
                ], 404);
            }

            // Query base
            $query = Comentario::where('id_destinatario', $userId)
                ->with(['autor:id,name,foto'])
                ->whereIn('tipo', ['resena', 'pregunta']);

            // 🔹 Filtro opcional por tipo (resena o pregunta)
            $tipo = $request->input('tipo');
            if ($tipo && in_array($tipo, ['resena', 'pregunta'])) {
                $query->where('tipo', $tipo);
            }

            $comments = $query->latest()->paginate(10);

            // Calcular promedio SOLO de reseñas con calificación
            $promedio = null;
            if (!$tipo || $tipo === 'resena') {
                $promedio = round(
                    Comentario::where('id_destinatario', $userId)
                        ->where('tipo', 'resena')
                        ->whereNotNull('calificacion')
                        ->avg('calificacion') ?? 0, 
                    1
                );
            }

            // Contar por tipo
            $totalResenas = Comentario::where('id_destinatario', $userId)
                ->where('tipo', 'resena')
                ->count();
                
            $totalPreguntas = Comentario::where('id_destinatario', $userId)
                ->where('tipo', 'pregunta')
                ->count();

            return response()->json([
                'success' => true,
                'data' => $comments->items(),
                'meta' => [
                    'promedio' => $promedio,
                    'total_resenas' => $totalResenas,
                    'total_preguntas' => $totalPreguntas,
                    'tipo_filtro' => $tipo ?? 'todos',
                    'pagination' => [
                        'current_page' => $comments->currentPage(),
                        'last_page' => $comments->lastPage(),
                        'per_page' => $comments->perPage(),
                        'total' => $comments->total(),
                    ]
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener comentarios',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Crear nueva reseña o pregunta
     * POST /api/comments
     */
    public function store(Request $request): JsonResponse
    {
        try {
            // Validaciones base
            $validator = Validator::make($request->all(), [
                'destinatario_id' => 'required|exists:users,id',
                'tipo'            => 'required|in:resena,pregunta',
                'contenido'       => 'required|string|max:500',
            ]);

            // Validación condicional: rating solo obligatorio para reseñas
            if ($request->tipo === 'resena') {
                $validator->addRules([
                    'rating' => 'required|integer|min:1|max:5'
                ]);
            }

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error de validación',
                    'errors' => $validator->errors()
                ], 422);
            }

            // 🔹 Verificar autenticación
            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Debes iniciar sesión para comentar'
                ], 401);
            }

            // 🔹 VALIDACIÓN: Solo pacientes pueden dejar reseñas
            if ($request->tipo === 'resena') {
                if ($user->role !== 'paciente') {
                    return response()->json([
                        'success' => false,
                        'message' => 'Solo los pacientes pueden dejar reseñas'
                    ], 403);
                }

                // 🔹 VALIDACIÓN: Verificar cita previa
                $destinatarioId = $request->destinatario_id;
                $patient = $user->patient;

                if ($patient) {
                    $doctor = Doctor::where('user_id', $destinatarioId)->first();
                    
                    if ($doctor) {
                        if ($doctor->citas == false) {
                            // Permitir reseña sin verificar cita
                        } else {
                            $citaExistente = Cita::where('paciente_id', $patient->id)
                                ->where('doctor_id', $doctor->id)
                                ->where('estado', 'finalizada')
                                ->exists();

                            if (!$citaExistente) {
                                return response()->json([
                                    'success' => false,
                                    'message' => 'Solo puedes reseñar a doctores con los que hayas tenido una cita finalizada',
                                    'error_code' => 'CITA_REQUERIDA'
                                ], 403);
                            }
                        }
                    }
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Perfil de paciente incompleto'
                    ], 400);
                }
            }

            // ✅ Crear comentario/pregunta
            $comentario = Comentario::create([
                'id_autor'        => $user->id,
                'id_destinatario' => $request->destinatario_id,
                'tipo'            => $request->tipo,
                'calificacion'    => $request->tipo === 'resena' ? $request->rating : null,
                'contenido'       => $request->contenido,
            ]);

            $comentario->load(['autor:id,name,foto']);

            $promedio = null;
            if ($comentario->tipo === 'resena') {
                $promedio = round(
                    Comentario::where('id_destinatario', $comentario->id_destinatario)
                        ->where('tipo', 'resena')
                        ->whereNotNull('calificacion')
                        ->avg('calificacion') ?? 0, 
                    1
                );
            }

            return response()->json([
                'success' => true,
                'message' => $comentario->tipo === 'resena' 
                    ? '¡Reseña publicada correctamente!' 
                    : '¡Pregunta enviada correctamente!',
                'data' => [
                    'comentario' => $comentario,
                    'nuevo_promedio' => $promedio
                ]
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Error en CommentController@store: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al publicar',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Verificar si el usuario autenticado puede reseñar a este doctor
     * GET /api/users/{userId}/can-review
     */
    public function canReview(Request $request, $userId): JsonResponse
    {
        try {
            $user = Auth::user();
            
            if (!$user || $user->role !== 'paciente') {
                return response()->json([
                    'success' => false,
                    'can_review' => false,
                    'message' => 'Solo los pacientes pueden dejar reseñas'
                ], 200);
            }

            $patient = $user->patient;
            if (!$patient) {
                return response()->json([
                    'success' => true,
                    'can_review' => false,
                    'message' => 'Perfil de paciente incompleto'
                ], 200);
            }

            $doctor = Doctor::where('user_id', $userId)->first();
            if (!$doctor) {
                return response()->json([
                    'success' => false,
                    'can_review' => false,
                    'message' => 'Doctor no encontrado'
                ], 404);
            }

            if ($doctor->citas == false) {
                return response()->json([
                    'success' => true,
                    'can_review' => true,
                    'message' => 'Este doctor no requiere cita previa para reseñar'
                ], 200);
            }

            $citaExistente = Cita::where('paciente_id', $patient->id)
                ->where('doctor_id', $doctor->id)
                ->where('estado', 'finalizada')
                ->exists();

            return response()->json([
                'success' => true,
                'can_review' => $citaExistente,
                'message' => $citaExistente 
                    ? 'Puede reseñar' 
                    : 'Requiere cita finalizada para reseñar'
            ], 200);

        } catch (\Exception $e) {
            \Log::error('Error en CommentController@canReview: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'can_review' => false,
                'message' => 'Error al verificar permiso de reseña'
            ], 500);
        }
    }
}