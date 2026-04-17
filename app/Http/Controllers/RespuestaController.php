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
            'contenido'     => 'required|string|max:250',
        ]);

        $comentario = Comentario::findOrFail($request->comentario_id);
        $user       = Auth::user();
        if (!in_array($user->role, ['farmacia', 'doctor'])) {
            abort(403, 'No tienes permiso para responder.');
        }
        if ($user->id !== $comentario->id_destinatario) {
            abort(403, 'No estás autorizado para responder este comentario.');
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