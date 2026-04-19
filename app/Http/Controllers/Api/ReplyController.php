<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comentario;
use App\Models\Respuesta;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ReplyController extends Controller
{
public function store(Request $request, $commentId): JsonResponse
{
    try {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Debes iniciar sesión para responder'
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'contenido' => 'required|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        $comentario = Comentario::find($commentId);
        if (!$comentario) {
            return response()->json([
                'success' => false,
                'message' => 'Comentario no encontrado'
            ], 404);
        }

        if ($comentario->tipo === 'pregunta') {
            if ($user->id !== $comentario->id_destinatario) {
                return response()->json([
                    'success' => false,
                    'message' => 'Solo el doctor o farmacia propietario puede responder a sus preguntas.'
                ], 403);
            }
        } elseif ($comentario->tipo === 'resena') {
            if ($user->role !== 'paciente') {
                return response()->json([
                    'success' => false,
                    'message' => 'Solo los pacientes pueden responder a las reseñas.'
                ], 403);
            }
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Tipo de comentario no válido.'
            ], 400);
        }

        $respuesta = Respuesta::create([
            'comentario_id'    => $commentId,
            'id_respondedor'   => $user->id,
            'contenido'        => $request->contenido,
        ]);

        $respondedorData = User::find($user->id);

        return response()->json([
            'success' => true,
            'message' => 'Respuesta publicada correctamente',
            'data' => [
                'id' => $respuesta->id,
                'comentario_id' => $respuesta->comentario_id,
                'id_respondedor' => $respuesta->id_respondedor,
                'contenido' => $respuesta->contenido,
                'created_at' => $respuesta->created_at,
                'respondedor' => $respondedorData ? [
                    'id' => $respondedorData->id,
                    'name' => $respondedorData->name,
                    'foto' => $respondedorData->foto,
                    'role' => $respondedorData->role,
                ] : null
            ]
        ], 201);

    } catch (\Exception $e) {
        \Log::error('Error en ReplyController@store: ' . $e->getMessage());
        \Log::error('Stack trace: ' . $e->getTraceAsString());
        
        return response()->json([
            'success' => false,
            'message' => 'Error interno del servidor',
            'error' => config('app.debug') ? $e->getMessage() : null
        ], 500);
    }
}
public function index($commentId): JsonResponse
{
    try {
        $comentario = Comentario::find($commentId);
        if (!$comentario) {
            return response()->json([
                'success' => false,
                'message' => 'Comentario no encontrado'
            ], 404);
        }

        $replies = Respuesta::where('comentario_id', $commentId)
            ->orderBy('created_at', 'desc')
            ->get();

        $repliesData = [];
        foreach ($replies as $reply) {
            $respondedor = User::find($reply->id_respondedor);
            
            $repliesData[] = [
                'id' => $reply->id,
                'comentario_id' => $reply->comentario_id,
                'contenido' => $reply->contenido,
                'created_at' => $reply->created_at,
                'respondedor' => $respondedor ? [
                    'id' => $respondedor->id,
                    'name' => $respondedor->name,
                    'foto' => $respondedor->foto,
                    'role' => $respondedor->role,
                ] : null
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $repliesData,
            'meta' => [
                'total' => count($repliesData),
                'comentario' => [
                    'id' => $comentario->id,
                    'tipo' => $comentario->tipo,
                ]
            ]
        ], 200);

    } catch (\Exception $e) {
        \Log::error('Error: ' . $e->getMessage());
        
        return response()->json([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ], 500);
    }
}
}