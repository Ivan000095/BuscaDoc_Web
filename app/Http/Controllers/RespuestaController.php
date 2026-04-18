<?php

namespace App\Http\Controllers;

use App\Models\Respuesta;
use App\Models\Comentario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RespuestaController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'comentario_id' => 'required|exists:comentarios,id',
            'contenido'     => 'required|string|max:500',
        ]);

        $comentario = Comentario::findOrFail($request->comentario_id);
        $user = Auth::user();

        // 🔒 LÓGICA DE PERMISOS ACTUALIZADA
        if ($comentario->tipo === 'pregunta') {
            // ❓ Preguntas: SOLO el dueño (doctor/farmacia) puede responder
            if ($user->id !== $comentario->id_destinatario) {
                abort(403, 'Solo el doctor o farmacia propietario puede responder a sus preguntas.');
            }
        } 
        elseif ($comentario->tipo === 'resena') {
            // ⭐ Reseñas: SOLO pacientes pueden responder
            if ($user->role !== 'paciente') {
                abort(403, 'Solo los pacientes pueden responder a las reseñas.');
            }
        } 
        else {
            abort(400, 'Tipo de comentario no válido.');
        }

        Respuesta::create([
            'id_respondedor' => $user->id,
            'comentario_id'  => $comentario->id,
            'contenido'      => $request->contenido,
        ]);

        return redirect()->to(url()->previous() . '#seccion-comentarios')
            ->with('success', '¡Tu respuesta se publicó correctamente!');
    }
}