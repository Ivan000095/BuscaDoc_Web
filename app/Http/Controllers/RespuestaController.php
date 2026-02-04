<?php

namespace App\Http\Controllers;

use App\Http\Requests\RespuestumStoreRequest;
use App\Models\Respuesta;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class RespuestaController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'comentario_id' => 'required|exists:comentarios,id',
            'contenido' => 'required|string|max:100',
        ]);
        Respuesta::create([
            'id_respondedor'  => Auth::id(),
            'comentario_id'   => $request->comentario_id,
            'contenido'       => $request->contenido,
        ]);

        return redirect()->to(url()->previous() . '#seccion-comentarios')
                     ->with('success', '¡Tu respuesta se publicó correctamente!');
    }
}
