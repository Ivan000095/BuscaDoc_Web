<?php

namespace App\Http\Controllers;

use App\Models\Mensaje;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MensajeController extends Controller
{
    public function index()
    {
        // 1. Definir a quién mostrar en la lista de contactos
        $rolBuscado = Auth::user()->role == 'paciente' ? 'doctor' : 'paciente';

        // Traemos usuarios del rol contrario (Ej: Paciente ve doctores)
        // Opcional: Podrías filtrar solo aquellos con los que ya tiene mensajes si son muchos usuarios.
        $contactos = User::where('role', $rolBuscado)->get();

        return view('mensajes.index', compact('contactos'));
    }

    public function show($id)
    {
        // Misma lógica de contactos para mantener la barra lateral
        $rolBuscado = Auth::user()->role == 'paciente' ? 'doctor' : 'paciente';
        $contactos = User::where('role', $rolBuscado)->get();

        // Usuario con el que chateamos
        $usuarioActivo = User::findOrFail($id);

        // 2. Cargar la conversación (Tus columnas personalizadas)
        $mensajes = Mensaje::where(function ($q) use ($id) {
            $q->where('id_remitente', Auth::id())
                ->where('id_destinatario', $id);
        })
            ->orWhere(function ($q) use ($id) {
                $q->where('id_remitente', $id)
                    ->where('id_destinatario', Auth::id());
            })
            ->orderBy('created_at', 'asc')
            ->get();

        // Marcar como leídos los mensajes recibidos
        Mensaje::where('id_remitente', $id)
            ->where('id_destinatario', Auth::id())
            ->where('leido', false)
            ->update(['leido' => true]);

        return view('mensajes.index', compact('contactos', 'usuarioActivo', 'mensajes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_destinatario' => 'required|exists:users,id',
            'contenido' => 'required|string|max:1000',
        ]);

        Mensaje::create([
            'id_remitente' => Auth::id(),
            'id_destinatario' => $request->id_destinatario,
            'contenido' => $request->contenido,
            'leido' => false,
        ]);

        return back();
    }
}
