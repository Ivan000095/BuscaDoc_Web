<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Kreait\Laravel\Firebase\Facades\Firebase;

class MensajeController extends Controller
{
    public function index()
    {
        $authId = Auth::id();
        $firebase = Firebase::database();

        $mensajesEnviados = $firebase->getReference('mensajes')
            ->orderByChild('id_remitente')
            ->equalTo($authId)
            ->getValue();

        $mensajesRecibidos = $firebase->getReference('mensajes')
            ->orderByChild('id_destinatario')
            ->equalTo($authId)
            ->getValue();

        $idContactos = [];

        if ($mensajesEnviados) {
            foreach ($mensajesEnviados as $msg) {
                $idContactos[] = $msg['id_destinatario'];
            }
        }

        if ($mensajesRecibidos) {
            foreach ($mensajesRecibidos as $msg) {
                $idContactos[] = $msg['id_remitente'];
            }
        }

        $idsContactos = array_unique($idContactos);
        $idsContactos = array_diff($idsContactos, [$authId]);

        $contactos = User::whereIn('id', $idsContactos)->get();
        
        return view('mensajes.index', compact('contactos'));
    }

    public function show($id)
    {
        $authId = Auth::id();
        $firebase = Firebase::database();

        $mensajesEnviados = $firebase->getReference('mensajes')
            ->orderByChild('id_remitente')
            ->equalTo($authId) // ¡AQUÍ ESTABA EL 3! Ya está corregido a $authId
            ->getValue();

        $mensajesRecibidos = $firebase->getReference('mensajes')
            ->orderByChild('id_destinatario')
            ->equalTo($authId)
            ->getValue();

        $idContactos = [];

        if ($mensajesEnviados) {
            foreach ($mensajesEnviados as $msg) {
                $idContactos[] = $msg['id_destinatario'];
            }
        }

        if ($mensajesRecibidos) {
            foreach ($mensajesRecibidos as $msg) {
                $idContactos[] = $msg['id_remitente'];
            }
        }

        $idsContactos = array_unique($idContactos);
        $idsContactos = array_diff($idContactos, [$authId]);

        $contactos = User::whereIn('id', $idsContactos)->get();
        $usuarioActivo = User::findOrFail($id);

        $chatId = ($authId < $id) ? "{$authId}_{$id}" : "{$id}_{$authId}";
        $datosCrudos = $firebase->getReference('mensajes')
            ->orderByChild('chat_id')
            ->equalTo($chatId)
            ->getValue();

        $mensajes = [];
        if ($datosCrudos) {
            foreach ($datosCrudos as $fid => $msg) {
                $msg['firebase_id'] = $fid;
                $mensajes[] = $msg;
            }
            usort($mensajes, fn($a, $b) => strtotime($a['created_at']) <=> strtotime($b['created_at']));
        }

        return view('mensajes.index', compact('contactos', 'usuarioActivo', 'mensajes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_destinatario' => 'required|exists:users,id',
            'contenido' => 'required|string|max:1000',
        ]);

        $authId = Auth::id();
        $destId = (int) $request->id_destinatario;
        $chatId = ($authId < $destId) ? "{$authId}_{$destId}" : "{$destId}_{$authId}";

        $nuevoMensaje = [
            'id_remitente' => $authId,
            'id_destinatario' => $destId,
            'chat_id' => $chatId,
            'contenido' => $request->contenido,
            'leido' => false,
            'created_at' => now()->toDateTimeString()
        ];

        Firebase::database()
            ->getReference('mensajes')
            ->push($nuevoMensaje);

        return response()->json(['success' => true]);
    }
}