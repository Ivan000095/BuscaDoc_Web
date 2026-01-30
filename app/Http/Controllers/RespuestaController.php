<?php

namespace App\Http\Controllers;

use App\Http\Requests\RespuestumStoreRequest;
use App\Models\Respuesta;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class RespuestaController extends Controller
{
    public function store(RespuestumStoreRequest $request): Response
    {
        $respuesta = Respuesta::create($request->validated());

        return redirect()->route('back');
    }
}
