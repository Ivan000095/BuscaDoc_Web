<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Alerta;
use Illuminate\Http\Request;

class AlertaController extends Controller
{
    public function index(Request $request)
    {
        try {
            $alertas = $request->user()->alertas()
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $alertas
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener alertas'
            ], 500);
        }
    }

    public function marcarLeida(Request $request, $id)
    {
        try {
            $alerta = Alerta::where('id', $id)
                ->where('user_id', $request->user()->id)
                ->firstOrFail();

            $alerta->update(['leido' => true]);

            return response()->json([
                'success' => true,
                'message' => 'Alerta marcada como leída'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'No se pudo actualizar la alerta'
            ], 404);
        }
    }

    // Opcional: Contador de no leídas (muy útil para el badge de Flutter)
    public function contadorNoLeidas(Request $request)
    {
        $count = $request->user()->alertas()->where('leido', false)->count();
        return response()->json(['unread_count' => $count], 200);
    }
}