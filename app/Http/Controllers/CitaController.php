<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cita;
use App\Models\Doctor; // Importante importar el modelo Doctor
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
class CitaController extends Controller
{
    public function store(Request $request, $id)
    {
        $request->validate([
            'fecha' => 'required|date|after_or_equal:today',
            'hora' => 'required',
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

        $fechaHoraSolicitada = Carbon::parse($request->fecha . ' ' . $request->hora);

        $existe = Cita::where('doctor_id', $id)
            ->where('estado', '=', 'confirmada')
            ->where(function ($query) use ($fechaHoraSolicitada) {
                $query->where('fecha_hora', '>', $fechaHoraSolicitada->copy()->subHour())
                    ->where('fecha_hora', '<', $fechaHoraSolicitada->copy()->addHour());
            })
            ->exists();

        if ($existe) {
            return back()->with(['error' => 'Ya existe una cita agendada en ese rango de horario (±1 hora). Por favor elige otro.']);
        }

        Cita::create([
            'paciente_id' => $user->patient->id,
            'doctor_id' => $id,
            'fecha_hora' => $fechaHoraSolicitada,
            'detalles' => $request->motivo,
            'estado' => 'pendiente',
        ]);

        return redirect()->route('doctores.show', $doctor->id)
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
        $user = Auth::user();

        $esDoctorOwner = $user->doctor && $user->doctor->id === $cita->doctor_id;

        $esPacienteOwner = $user->patient && $user->patient->id === $cita->paciente_id;

        if (!$esDoctorOwner && !$esPacienteOwner) {
            return back()->with('error', 'No tienes permiso para gestionar esta cita.');
        }

        if ($esPacienteOwner && $request->estado !== 'cancelada') {
            return back()->with('error', 'Acción no permitida. Solo puedes cancelar.');
        }

        if (in_array($request->estado, ['finalizada', 'no_asistida'])) {
            if (Carbon::parse($cita->fecha_hora)->isFuture()) {
                return back()->with('error', 'No puedes finalizar una cita que aún no ha ocurrido.');
            }
        }

        $cita->update(['estado' => $request->estado]);

        $mensajes = [
            'confirmada' => 'Cita confirmada.',
            'cancelada' => 'Cita cancelada.',
            'finalizada' => 'Cita marcada como finalizada con éxito.',
            'no asistida' => 'Se registró que el paciente no asistió.'
        ];

        return back()->with('success', $mensajes[$request->estado] ?? 'Estado actualizado.');

    }
}