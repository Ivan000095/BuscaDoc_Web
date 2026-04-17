<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\RespuestumStoreRequest;
use App\Http\Requests\API\RespuestumUpdateRequest;
use App\Http\Resources\API\RespuestumCollection;
use App\Http\Resources\API\RespuestumResource;
use App\Models\Respuesta;
use App\Models\Comentario;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class RespuestaController extends Controller
{
    public function index(Request $request)
    {
        $respuestas = Respuesta::all();
        return new RespuestumCollection($respuestas);
    }

    public function store(RespuestumStoreRequest $request)
    {
        $validated = $request->validated();
        
        // Obtener el comentario original
        $comentario = Comentario::findOrFail($validated['comentario_id']);
        $user = Auth::user();

        // Validación de autorización: Solo farmacias o doctores pueden responder
        if (!in_array($user->role, ['farmacia', 'doctor'])) {
            return response()->json([
                'message' => 'No tienes permiso para responder.'
            ], Response::HTTP_FORBIDDEN);
        }

        // Solo el dueño del perfil puede responder
        if ($user->id !== $comentario->id_destinatario) {
            return response()->json([
                'message' => 'No estás autorizado para responder este comentario.'
            ], Response::HTTP_FORBIDDEN);
        }

        $respuesta = Respuesta::create([
            'id_respondedor' => $user->id,
            'comentario_id'  => $comentario->id,
            'contenido'      => $validated['contenido'],
        ]);

        return new RespuestumResource($respuesta);
    }

    public function show(Request $request, Respuesta $respuesta)
    {
        return new RespuestumResource($respuesta);
    }

    public function update(RespuestumUpdateRequest $request, Respuesta $respuesta)
    {
        // Solo el autor puede actualizar su respuesta
        if ($respuesta->id_respondedor !== Auth::id()) {
            return response()->json(['message' => 'No autorizado'], Response::HTTP_FORBIDDEN);
        }

        $respuesta->update($request->validated());
        return new RespuestumResource($respuesta);
    }

    public function destroy(Request $request, Respuesta $respuesta)
    {
        // Solo el autor puede eliminar su respuesta
        if ($respuesta->id_respondedor !== Auth::id()) {
            return response()->json(['message' => 'No autorizado'], Response::HTTP_FORBIDDEN);
        }

        $respuesta->delete();
        return response()->noContent();
    }
}