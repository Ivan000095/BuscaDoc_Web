<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Expediente;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth')->except('top5');
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

        if ($user->role == 'paciente' && $user->paciente) {
            // Buscamos citas de cualquiera de sus expedientes
            $proximaCita = \App\Models\Cita::whereIn('expediente_id', $user->expedientes->pluck('id'))
                ->with(['doctor.user', 'expediente']) // Cargamos el expediente para saber de quién es la cita
                ->where(DB::raw("CONCAT(fecha, ' ', hora_inicio)"), '>=', now())
                ->where('estado', '!=', 'cancelada')
                ->orderBy('fecha', 'asc')
                ->orderBy('hora_inicio', 'asc')
                ->first();

            $rutas = User::whereNotNull('latitud')
                ->whereNotNull('longitud')
                ->whereIn('role', ['doctor', 'farmacia']) 
                ->select('id', 'name', 'role', 'latitud', 'longitud', 'foto') 
                ->get();
        }

        if ($user->role == 'doctor' && $user->doctor) {
            $proximaCitaDoctor = $user->doctor->citas()
                ->with('expediente') // Importante: Saber a quién va a atender
                ->where(DB::raw("CONCAT(fecha, ' ', hora_inicio)"), '>=', now())
                ->whereIn('estado', ['pendiente', 'confirmada'])
                ->orderBy('fecha', 'asc')
                ->orderBy('hora_inicio', 'asc')
                ->first();

            $ultimaReview = $user->doctor->reviews()->with('autor')->latest()->first();
            $ultimaQuestion = $user->doctor->questions()->with('autor')->latest()->first();
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
