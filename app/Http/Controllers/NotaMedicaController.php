<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cita;
use App\Models\NotaMedica;
use Illuminate\Support\Facades\Auth;

class NotaMedicaController extends Controller
{
    public function store(Request $request, Cita $cita)
    {
        $request->validate([
            'diagnostico' => 'required|string',
            'tratamiento' => 'required|string',
            'nota_seguimiento' => 'nullable|string',
        ]);

        // 1. Crear la Nota Médica
        NotaMedica::create([
            'expediente_id' => $cita->expediente_id,
            'doctor_id' => Auth::user()->doctor->id,
            'cita_id' => $cita->id,
            'diagnostico' => $request->diagnostico,
            'tratamiento' => $request->tratamiento,
            'nota_seguimiento' => $request->nota_seguimiento,
        ]);

        // 2. Cambiar el estado de la cita
        $cita->update(['estado' => 'finalizada']);

        return back()->with('success', 'Consulta finalizada y nota guardada correctamente.');
    }
}