<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\MensajeStoreRequest;
use App\Http\Requests\API\MensajeUpdateRequest;
use App\Http\Resources\API\MensajeResource;
use App\Models\Mensaje;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class MensajeController extends Controller
{
    public function index(Request $request): Response
    {
        $mensajes = Mensaje::all();

        return new MensajeResource($Mensaje);
    }

    public function store(MensajeStoreRequest $request): Response
    {
        $mensaje = Mensaje::create($request->validated());

        return new MensajeResource($Mensaje);
    }

    public function show(Request $request, Mensaje $mensaje): Response
    {
        return new MensajeResource($mensaje);
    }

    public function update(MensajeUpdateRequest $request, Mensaje $mensaje): Response
    {
        $mensaje->update($request->validated());

        return new MensajeResource($Mensaje);
    }

    public function destroy(Request $request, Mensaje $mensaje): Response
    {
        $mensaje->delete();

        return response()->noContent();
    }
}
