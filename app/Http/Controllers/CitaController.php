<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cita;
use App\Models\Doctor; // Importante importar el modelo Doctor
use Illuminate\Support\Facades\Auth;

class CitaController extends Controller
{
    public function store(Request $request, $id)
    {
        $request->validate([
            'fecha'  => 'required|date|after:today',
            'hora'   => 'required',
            'motivo' => 'required|string|max:255',
        ]);

        $doctor = Doctor::find($id);
        
        if (!$doctor) {
            return back()->with('error', 'El doctor no existe.');
        }

        $user = Auth::user();

        if (!$user->patient) {
            return back()->with('error', 'Necesitas un perfil de paciente para agendar.');
        }

        $fechaHoraFinal = $request->fecha . ' ' . $request->hora;

        $existe = Cita::where('doctor_id', $id)
                      ->where('fecha_hora', $fechaHoraFinal)
                      ->where('estado', '!=', 'cancelada')
                      ->exists();

        if ($existe) {
            return back()->withErrors(['hora' => 'Este horario ya está ocupado.']);
        }

        // 4. Crear la Cita
        Cita::create([
            'paciente_id' => $user->patient->id,
            'doctor_id'   => $id,
            'fecha_hora'  => $fechaHoraFinal,
            'detalles'    => $request->motivo,
            'estado'      => 'pendiente',
        ]);

        return redirect()->route('users.show', $doctor->user_id)
            ->with('success', '¡Cita agendada correctamente! Espera la confirmación.');
    }

    public function index()
    {
        $user = Auth::user();
        $citas = [];

        if ($user->role == 'paciente') {
            if ($user->patient) {
                $citas = Cita::where('paciente_id', $user->patient->id)
                    ->orderBy('fecha_hora', 'desc')
                    ->get();
            }
        } elseif ($user->role == 'doctor') {
            if ($user->doctor) {
                $citas = Cita::where('doctor_id', $user->doctor->id)
                    ->orderBy('fecha_hora', 'desc')
                    ->get();
            }
        }

        // Selección de vista
        $vista = ($user->role == 'paciente') ? 'pacientes.citas' : 'doctores.citas';
        
        return view($vista, compact('citas'));
    }

    public function updateStatus(Request $request, $id)
    {
        $cita = Cita::findOrFail($id);

        if (Auth::user()->doctor->id !== $cita->doctor_id) {
            return back()->with('error', 'No tienes permiso para gestionar esta cita.');
        }

        $request->validate([
            'estado' => 'required|in:confirmada,cancelada'
        ]);

        // Actualizamos
        $cita->update(['estado' => $request->estado]);

        $mensaje = $request->estado == 'confirmada' ? 'Cita aceptada correctamente.' : 'Cita rechazada.';
        
        return back()->with('success', $mensaje);
    }
}