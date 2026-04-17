<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\ComentarioStoreRequest;
use App\Http\Requests\API\ComentarioUpdateRequest;
use App\Http\Resources\API\ComentarioResource;
use App\Models\Comentario;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class ComentarioController extends Controller
{
    public function index(Request $request)
    {
        $comentarios = Comentario::all();
        return new ComentarioResource($comentarios);
    }

    public function store(ComentarioStoreRequest $request)
    {
        $validated = $request->validated();

        if ($validated['tipo'] === 'resena' && isset($validated['calificacion'])) {
            $request->validate([
                'calificacion' => 'required|integer|min:1|max:5'
            ]);
        }

        $comentario = Comentario::create([
            'id_autor'        => Auth::id(),
            'id_destinatario' => $validated['destinatario_id'],
            'tipo'            => $validated['tipo'],
            'calificacion'    => $validated['calificacion'] ?? null,
            'contenido'       => $validated['contenido'],
        ]);

        return new ComentarioResource($comentario);
    }

    public function show(Request $request, Comentario $comentario)
    {
        return new ComentarioResource($comentario);
    }

    public function update(ComentarioUpdateRequest $request, Comentario $comentario)
    {
        $comentario->update($request->validated());
        return new ComentarioResource($comentario);
    }

    public function destroy(Request $request, Comentario $comentario)
    {
        // Solo el autor puede eliminar su comentario
        if ($comentario->id_autor !== Auth::id()) {
            return response()->json(['message' => 'No autorizado'], Response::HTTP_FORBIDDEN);
        }
        
        $comentario->delete();
        return response()->noContent();
    }
}