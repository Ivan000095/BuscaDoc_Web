<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Especialidad;

class EspecialidadesController extends Controller
{
    public function index()
    {
        $especialidades = Especialidad::with(['doctors.user'])->get();

        return view('especialidades.index', compact('especialidades'));
    }

    public function show($id)
    {
        $especialidad = Especialidad::with(['doctors.user'])->findOrFail($id);

        return view('especialidades.index', compact('especialidad'));
    }
}
