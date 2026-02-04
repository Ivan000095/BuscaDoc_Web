<?php

namespace App\Http\Controllers;

use App\Http\Requests\ComentarioStoreRequest;
use App\Models\Comentario;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ComentarioController extends Controller
{
    public function store(ComentarioStoreRequest $request): Response
    {
        $comentario = Comentario::create($request->validated());

        return redirect()->route('back');
    }

    public function destroy(Request $request, Comentario $comentario): Response
    {
        $comentario->delete();

        return redirect()->route('back');
    }
}
