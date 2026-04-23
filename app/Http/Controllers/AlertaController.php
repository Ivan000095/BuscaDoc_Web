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
        $user = Auth::user();

        $alertas = $user->alertas()
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $user->alertas()->where('leido', false)->update(['leido' => true]);

        return view('alertas.index', compact('alertas'));
    }

    public function marcarLeida($id)
    {
        $alerta = Alerta::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $alerta->update(['leido' => true]);

        return response()->json(['success' => true]);
    }

    public function marcarTodasLeidasWeb()
    {
        auth()->user()->alertas()->where('leido', false)->update(['leido' => true]);

        return response()->json(['success' => true]);
    }
}