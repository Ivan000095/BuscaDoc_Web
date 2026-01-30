<?php

namespace App\Http\Controllers;

use App\Http\Requests\MensajeStoreRequest;
use App\Models\Mensaje;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MensajeController extends Controller
{
    public function index(Request $request): Response
    {
        $mensajes = Mensaje::all();

        return view('mensajes.index', [
            'mensajes' => $mensajes,
        ]);
    }

    public function store(MensajeStoreRequest $request): Response
    {
        $mensaje = Mensaje::create($request->validated());

        return redirect()->route('mensajes.index');
    }
}
