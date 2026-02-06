<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        $proximaCita = null;

        if ($user->role == 'paciente' && $user->patient) {
            $proximaCita = \App\Models\Cita::where('paciente_id', $user->patient->id)
                ->where('fecha_hora', '>=', now())
                ->where('estado', '!=', 'cancelada')
                ->orderBy('fecha_hora', 'asc')
                ->first();

        }



        return view('home', compact('proximaCita'));
    }
}
