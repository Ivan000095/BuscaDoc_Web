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
        $authId = Auth::id();

        $enviados = Mensaje::where('id_remitente', $authId)->select('id_destinatario as contacto_id');
        $recibidos = Mensaje::where('id_destinatario', $authId)->select('id_remitente as contacto_id')->union($enviados)->get();

        $idsContactos = $recibidos->pluck('contacto_id')->unique();
        $contactos = User::whereIn('id', $idsContactos)->get();
        return view('mensajes.index', compact('contactos'));
    }

    public function show($id)
    {
        $authId = Auth::id();

        $enviados = Mensaje::where('id_remitente', $authId)->select('id_destinatario as contacto_id');
        $recibidos = Mensaje::where('id_destinatario', $authId)->select('id_remitente as contacto_id')->union($enviados)->get();

        $idsContactos = $recibidos->pluck('contacto_id')->unique();
        $contactos = User::whereIn('id', $idsContactos)->get();

        $usuarioActivo = User::findOrFail($id);

        $mensajes = Mensaje::where(function ($q) use ($id, $authId) {
            $q->where('id_remitente', $authId)
              ->where('id_destinatario', $id);
        })
        ->orWhere(function ($q) use ($id, $authId) {
            $q->where('id_remitente', $id)
              ->where('id_destinatario', $authId);
        })
        ->orderBy('created_at', 'asc')
        ->get();

            Mensaje::where('id_remitente', $id)
                ->where('id_destinatario', $authId)
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
