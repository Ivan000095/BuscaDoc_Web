<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Kreait\Laravel\Firebase\Facades\Firebase;

class MensajeriaController extends Controller
{
    public function getContactosApi(Request $request)
    {
        $authId = (int) $request->user()->id; 
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

        $contactos = User::with('doctor.especialidades')->whereIn('id', $idsContactos)->get();
        
        $data = $contactos->map(function($user) {
            $especialidad = '';
            if ($user->role == 'doctor' && $user->doctor && $user->doctor->especialidades) {
                $especialidad = $user->doctor->especialidades->pluck('nombre')->join(', ');
            }

            return [
                'id' => (string) $user->id,
                'nombre' => $user->name,
                'rol' => $user->role,
                'especialidad' => $especialidad ?: 'Médico General',
                'fotoUrl' => $user->foto ? asset('storage/' . $user->foto) : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=random',
                'mensajesSinLeer' => 0,
                'enLinea' => false 
            ];
        })->values()->all();

        return response()->json($data);
    }

    public function getMensajesApi($id, Request $request)
    {
        $authId = (int) $request->user()->id;
        $chatId = ($authId < $id) ? "{$authId}_{$id}" : "{$id}_{$authId}";
        
        $firebase = Firebase::database();
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

        return response()->json(array_values($mensajes));
    }

    public function storeApi(Request $request)
    {
        $request->validate([
            'id_destinatario' => 'required',
            'contenido' => 'required|string|max:1000',
        ]);

        $authId = $request->user()->id;
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

        Firebase::database()->getReference('mensajes')->push($nuevoMensaje);

        return response()->json(['success' => true]);
    }
}
