<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\RespuestumStoreRequest;
use App\Http\Requests\API\RespuestumUpdateRequest;
use App\Http\Resources\API\RespuestumCollection;
use App\Http\Resources\API\RespuestumResource;
use App\Models\Respuesta;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class RespuestaController extends Controller
{
    public function index(Request $request): Response
    {
        $respuesta = Respuestum::all();

        return new RespuestumCollection($respuesta);
    }

    public function store(RespuestumStoreRequest $request): Response
    {
        $respuesta = Respuesta::create($request->validated());

        return new RespuestumResource($Respuesta);
    }

    public function show(Request $request, Respuestum $respuestum): Response
    {
        return new RespuestumResource($respuestum);
    }

    public function update(RespuestumUpdateRequest $request, Respuestum $respuestum): Response
    {
        $respuestum->update($request->validated());

        return new RespuestumResource($respuestum);
    }

    public function destroy(Request $request, Respuestum $respuestum): Response
    {
        $respuestum->delete();

        return response()->noContent();
    }
}
