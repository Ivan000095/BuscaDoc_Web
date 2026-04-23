<?php

namespace App\Http\Controllers;

use App\Models\Alerta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AlertaController extends Controller
{
    // Ver todas las alertas (leídas y no leídas)
    public function index()
    {
        $alertas = Alerta::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('alertas.index', compact('alertas'));
    }

    // Marcar como leída vía AJAX
    public function marcarLeida($id)
    {
        $alerta = Alerta::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $alerta->update(['leido' => true]);

        return response()->json(['success' => true]);
    }
}