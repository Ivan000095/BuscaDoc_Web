<?php

namespace App\Http\Controllers;

use App\Models\Comentario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ComentarioController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'doctor_id' => 'required|exists:users,id',
            'tipo'      => 'required|in:resena,pregunta',
            'contenido' => 'required|string|max:500',
        ]);

        if ($request->tipo === 'resena') { 
            $request->validate(['rating' => 'required|integer|min:1|max:5']);
        }

        Comentario::create([
            'id_autor'        => Auth::id(),
            'id_destinatario' => $request->doctor_id,
            'tipo'            => $request->tipo,
            'calificacion'    => $request->rating ?? null,
            'contenido'       => $request->contenido,
        ]);

        return redirect()->to(url()->previous() . '#seccion-comentarios')
                     ->with('success', '¡Tu comentario se publicó correctamente!');
    }
}