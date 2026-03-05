<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

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
        $proximaCitaDoctor = null;
        $ultimaReview = null;
        $ultimaQuestion = null;
        $rutas = [];


        if ($user->role == 'paciente' && $user->patient) {
            $proximaCita = \App\Models\Cita::where('paciente_id', $user->patient->id)
                ->where('fecha_hora', '>=', now())
                ->where('estado', '!=', 'cancelada')
                ->orderBy('fecha_hora', 'asc')
                ->first();
            $rutas = User::whereNotNull('latitud')
                ->whereNotNull('longitud')
                ->whereIn('role', ['doctor', 'farmacia']) 
                ->select('id', 'name', 'role', 'latitud', 'longitud', 'foto') 
                ->get();
        }

        if ($user->role == 'doctor' && $user->doctor) {
            $proximaCitaDoctor = $user->doctor->citas()
                ->where('fecha_hora', '>=', now())
                ->whereIn('estado', ['pendiente', 'confirmada'])
                ->orderBy('fecha_hora', 'asc')
                ->first();
            $ultimaReview = $user->doctor->reviews()
                ->with('autor')
                ->latest()
                ->first();
            $ultimaQuestion = $user->doctor->questions()
                ->with('autor')
                ->latest()
                ->first();
        }

        return view('home', compact('proximaCita', 'proximaCitaDoctor', 'ultimaReview', 'ultimaQuestion', 'rutas'));
    }

    public function mostrarMapa()
        {
            $rutas = User::whereNotNull('latitud')
                        ->whereNotNull('longitud')
                        ->whereIn('role', ['doctor', 'farmacia']) 
                        ->select('id', 'name', 'role', 'latitud', 'longitud') 
                        ->get();
            return view('mapa.index', compact('rutas'));
        }
}
