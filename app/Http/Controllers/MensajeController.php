<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Kreait\Laravel\Firebase\Facades\Firebase;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class MensajeController extends Controller
{
    public function index()
    {
        // CASTEO ESTRICTO: Obligamos a que siempre sea un número entero
        $authId = (int) Auth::id(); 
        $firebase = Firebase::database();

        $mensajesEnviados = $firebase->getReference('mensajes')
            ->orderByChild('id_remitente')
            ->equalTo($authId) // Ahora Firebase buscará un Entero sí o sí
            ->getValue();

        $mensajesRecibidos = $firebase->getReference('mensajes')
            ->orderByChild('id_destinatario')
            ->equalTo($authId)
            ->getValue();

        $idContactos = [];

        if ($mensajesEnviados) {
            foreach ($mensajesEnviados as $msg) {
                $idContactos[] = (int) $msg['id_destinatario']; // Aseguramos enteros
            }
        }

        if ($mensajesRecibidos) {
            foreach ($mensajesRecibidos as $msg) {
                $idContactos[] = (int) $msg['id_remitente']; // Aseguramos enteros
            }
        }

        $idsContactos = array_unique($idContactos);
        $idsContactos = array_diff($idsContactos, [$authId]);

        // Si la lista de IDs está vacía, whereIn simplemente devuelve una colección vacía sin fallar
        $contactos = User::whereIn('id', $idsContactos)->get();
        
        return view('mensajes.index', compact('contactos'));
    }

    public function show($id)
    {
        $authId = (int) Auth::id();
        $destId = (int) $id;
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
                $idContactos[] = (int) $msg['id_destinatario'];
            }
        }

        if ($mensajesRecibidos) {
            foreach ($mensajesRecibidos as $msg) {
                $idContactos[] = (int) $msg['id_remitente'];
            }
        }

        $idsContactos = array_unique($idContactos);
        // CORRECCIÓN DEL BUG DE LA 'S': Usamos $idsContactos en ambos lados
        $idsContactos = array_diff($idsContactos, [$authId]); 

        $contactos = User::whereIn('id', $idsContactos)->get();
        $usuarioActivo = User::findOrFail($destId);

        $chatId = ($authId < $destId) ? "{$authId}_{$destId}" : "{$destId}_{$authId}";
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

    public function store(Request $request) {
        $request->validate([
            'id_destinatario' => 'required|exists:users,id',
            'contenido' => 'required|string|max:1000',
        ]);

        $authId = (int) Auth::id(); // Aseguramos Entero
        $destId = (int) $request->id_destinatario; // Aseguramos Entero
        
        // Al ser ambos enteros matemáticos, la lógica de '<' nunca fallará 
        $chatId = ($authId < $destId) ? "{$authId}_{$destId}" : "{$destId}_{$authId}";

        $nuevoMensaje = [
            'id_remitente' => $authId,
            'id_destinatario' => $destId,
            'chat_id' => $chatId,
            'contenido' => $request->contenido,
            'leido' => false,
            'created_at' => now()->toDateTimeString()
        ];

        Firebase::database()->getReference('mensajes')->push($nuevoMensaje);

        $destinatario = User::find($destId);
        
        if ($destinatario && $destinatario->fcm_token) {
            $messaging = Firebase::messaging();
            
            $notificacion = CloudMessage::new()
                ->withNotification(Notification::create(
                    'Nuevo mensaje de ' . Auth::user()->name, 
                    $request->contenido
                ))
                ->withToken($destinatario->fcm_token);

            try {
                $messaging->send($notificacion);
            } catch (\Exception $e) {
                \Log::error('Error enviando push desde Web: ' . $e->getMessage());
            }
        }

        return response()->json(['success' => true]);
    }
}