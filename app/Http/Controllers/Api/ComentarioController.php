<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\ComentarioStoreRequest;
use App\Http\Requests\API\ComentarioUpdateRequest;
use App\Http\Resources\API\ComentarioResource;
use App\Models\Comentario;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ComentarioController extends Controller
{
    public function index(Request $request): Response
    {
        $comentarios = Comentario::all();

        return new ComentarioResource($Comentario);
    }

    public function store(ComentarioStoreRequest $request): Response
    {
        $comentario = Comentario::create($request->validated());

        return new ComentarioResource($Comentario);
    }

    public function show(Request $request, Comentario $comentario): Response
    {
        return new ComentarioResource($comentario);
    }

    public function update(ComentarioUpdateRequest $request, Comentario $comentario): Response
    {
        $comentario->update($request->validated());

        return new ComentarioResource($comentario);
    }

    public function destroy(Request $request, Comentario $comentario): Response
    {
        $comentario->delete();

        return response()->noContent();
    }
}
