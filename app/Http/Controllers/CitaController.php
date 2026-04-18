<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cita;
use App\Models\Doctor;
use App\Models\DoctorDisponibilidad;
use App\Models\Expediente;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
class CitaController extends Controller
{
    public function store(Request $request, $id)
    {

    $user = Auth::user();

        // 1. Verificación de seguridad: ¿El usuario tiene perfil de paciente?
        if (!$user->paciente) {
            return back()->with('error', 'Tu cuenta no tiene un perfil de paciente vinculado. Por favor, completa tu registro.');
        }

        // 2. Validación de reglas (la que ya teníamos)
        $rules = [
            'fecha' => 'required|date|after_or_equal:today',
            'hora_inicio' => 'required',
            'expediente_id' => 'required',
            'motivo_consulta' => 'required|string|max:500',
        ];

    // Reglas adicionales si se crea un nuevo familiar
        if ($request->expediente_id === 'nuevo_familiar') {
            $rules['nuevo_nombre'] = 'required|string|max:80';
            $rules['nuevo_fecha_nacimiento'] = 'required|date';
            $rules['nuevo_genero'] = 'required|in:masculino,femenino';
            $rules['nuevo_parentesco'] = 'required|string|max:30';
            $rules['nuevo_tipo_sangre'] = 'nullable|string|max:5';
            $rules['nuevo_alergias'] = 'nullable|string';
            $rules['nuevo_padecimientos'] = 'nullable|string';
            $rules['nuevo_habitos'] = 'nullable|string';
        }

        $request->validate($rules);

        $doctor = Doctor::findOrFail($id);

        return DB::transaction(function () use ($request, $doctor, $user) {
            
            $finalExpedienteId = $request->expediente_id;

        // 2. Creación del Expediente Completo
        if ($finalExpedienteId === 'nuevo_familiar') {
            $nuevoExpediente = Expediente::create([
                'user_id'           => $user->id,
                'nombre_completo'       => (string) $request->nuevo_nombre,
                'fecha_nacimiento'      => $request->nuevo_fecha_nacimiento,
                'genero'                => (string) $request->nuevo_genero,
                'parentesco'            => (string) $request->nuevo_parentesco,
                'tipo_sangre'           => (string) $request->nuevo_tipo_sangre,
                // Usamos null coalescing para asegurar que si viene vacío sea null y no un array vacío
                'alergias'              => is_array($request->nuevo_alergias) ? implode(', ', $request->nuevo_alergias) : $request->nuevo_alergias,
                'padecimientos_cronicos' => is_array($request->nuevo_padecimientos) ? implode(', ', $request->nuevo_padecimientos) : $request->nuevo_padecimientos,
                'habitos_salud'         => is_array($request->nuevo_habitos) ? implode(', ', $request->nuevo_habitos) : $request->nuevo_habitos,
            ]);
            $finalExpedienteId = $nuevoExpediente->id;
        }

            // 3. Lógica de tiempos y traslapes (Igual que antes)
            $duracion = $doctor->duracion_cita ?? 30;
            $horaInicio = Carbon::parse($request->fecha . ' ' . $request->hora_inicio);
           // $horaFin = $horaInicio->copy()->addMinutes($duracion);

            $existeCita = Cita::where('doctor_id', $doctor->id)
                ->where('fecha', $request->fecha)
                ->where('estado', '!=', 'cancelada')
                ->where(function ($query) use ($horaInicio) {
                    $query->where('hora_inicio', '<', $horaInicio->format('H:i:s'))
                        ->where('hora_inicio', '>', $horaInicio->format('H:i:s'));
                })->exists();

            if ($existeCita) {
                return back()->with('error', 'El horario acaba de ser ocupado.');
            }

            // 4. Crear Cita
            Cita::create([
                'expediente_id' => $finalExpedienteId,
                'doctor_id' => $doctor->id,
                'fecha' => $request->fecha,
                'hora_inicio' => $horaInicio->format('H:i:s'),
                
                'motivo_consulta' => $request->motivo_consulta,
                'estado' => 'pendiente',
                
            ]);

            return redirect()->route('home')->with('success', 'Solicitud enviada correctamente.');
        });
    }

    public function getDisponibilidad(Request $request, $doctorId)
    {
        try {
            $fecha = $request->query('fecha');
            $date = Carbon::parse($fecha);
            

            $diaNumero = $date->dayOfWeek; 

            // Consulta usando el número del día
            $horariosLaborales = DB::table('doctor_disponibilidad')
                ->where('doctor_id', $doctorId)
                ->where('dia_semana', $diaNumero) // Aquí buscamos el número (0-6)
                ->get();

            if ($horariosLaborales->isEmpty()) {
                // Mapeo simple para el mensaje de error al usuario
                $nombres = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
                return response()->json([
                    'slots' => [],
                    'mensaje' => "El doctor no tiene turnos registrados para el día " . $nombres[$diaNumero] . "."
                ]);
            }

            $doctor = Doctor::findOrFail($doctorId);
            $intervalo = $doctor->duracion_cita ?? 30;

            $citasOcupadas = Cita::where('doctor_id', $doctorId)
                ->where('fecha', $fecha)
                ->where('estado', '!=', 'cancelada')
                ->pluck('hora_inicio')
                ->map(fn($h) => Carbon::parse($h)->format('H:i'))
                ->toArray();

            $slots = [];

            foreach ($horariosLaborales as $horario) {
                $inicio = Carbon::parse($fecha . ' ' . $horario->hora_inicio);
                $fin = Carbon::parse($fecha . ' ' . $horario->hora_fin);

                while ($inicio->lt($fin)) {
                    $horaActual = $inicio->format('H:i');
                    if (!in_array($horaActual, $citasOcupadas)) {
                        $slots[] = $horaActual;
                    }
                    $inicio->addMinutes($intervalo);
                }
            }

            return response()->json(['slots' => $slots]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }





    public function index()
    {
        $user = Auth::user();
        
        if ($user->role == 'paciente') {
            // Obtenemos los IDs de todos los expedientes del paciente (el suyo y familiares)
            $expedientesIds = $user->expedientes->pluck('id');
            
            $citas = Cita::whereIn('expediente_id', $expedientesIds)
                ->with(['doctor.user', 'expediente'])
                ->orderBy('fecha', 'desc')
                ->get();

               
            return view('pacientes.citas', compact('citas'));
        }

        if ($user->role == 'doctor') {
            $citas = Cita::where('doctor_id', $user->doctor->id)
                ->with('expediente')
                ->orderBy('fecha', 'desc')
                ->get();
                
            return view('doctores.citas', compact('citas'));
        }
        
    }

    public function updateStatus(Request $request, $id)
    {
        $cita = Cita::findOrFail($id);
        $user = Auth::user();

        // Verificamos dueños (Doctor o Paciente a través de su expediente)
        $esDoctorOwner = $user->doctor && $user->doctor->id === $cita->doctor_id;
        
        // Ahora validamos que el expediente de la cita pertenezca al paciente logueado
        $esPacienteOwner = $cita->expediente->user_id === $user->id;

        if (!$esDoctorOwner && !$esPacienteOwner) {
            return back()->with('error', 'No tienes permiso para gestionar esta cita.');
        }

        // El paciente solo puede cancelar
        if ($esPacienteOwner && $request->estado !== 'cancelada') {
            return back()->with('error', 'Acción no permitida. Solo puedes cancelar tu solicitud.');
        }

        // Validación de tiempo para finalizar o marcar inasistencia (basado en tu lógica original)
        if (in_array($request->estado, ['finalizada', 'no asistida'])) {
            // Combinamos fecha y hora_inicio para la comparación
            $fechaCita = Carbon::parse($cita->fecha->format('Y-m-d') . ' ' . $cita->hora_inicio);
            if ($fechaCita->isFuture()) {
                return back()->with('error', 'No puedes finalizar una cita que aún no ha ocurrido.');
            }
        }

        // Actualización del estado
        $cita->update(['estado' => $request->estado]);

        // Restauración de tus mensajes originales
        $mensajes = [
            'confirmada' => 'Cita confirmada.',
            'cancelada'  => 'Cita cancelada.',
            'finalizada' => 'Cita marcada como finalizada con éxito.',
            'no asistida' => 'Se registró que el paciente no asistió.'
        ];

        $mensajeFinal = $mensajes[$request->estado] ?? 'Estado de la cita actualizado.';

        return back()->with('success', $mensajeFinal);
    }

        public function destroy($id)
        {
            $cita = Cita::findOrFail($id);
            
            // Ahora permitimos eliminar (ocultar) si está cancelada, rechazada O finalizada
            if (in_array($cita->estado, ['cancelada', 'rechazada', 'finalizada'])) {
                // En Laravel, si usas SoftDeletes, esto no borra la nota médica, 
                // solo quita la cita de la lista principal.
                $cita->delete(); 
                
                return back()->with('success', 'La cita se ha quitado de tu vista.');
            }

            return back()->with('error', 'No puedes ocultar una cita que aún está pendiente.');
        }


        public function solicitarCambio(Request $request, $id)
        {
            $cita = Cita::findOrFail($id);
            $user = Auth::user();
            // Validar que no haya otra solicitud pendiente para esta cita
            $existePendiente = \App\Models\SolicitudCambio::where('cita_id', $id)
                                ->where('solicitante_id', $user->id)
                                ->where('estado', 'pendiente')
                                ->exists();

            $estadoDiferente = \App\Models\SolicitudCambio::where('cita_id', $id)
                                ->where('solicitante_id', $user->id)
                                ->where('estado','!=', 'pendiente')
                                ->first();

            if ($existePendiente && $user->role == 'doctor') {
                return redirect()->route('doctores.citas')->with('error', 'Ya hay una solicitud de cambio pendiente para esta cita.');
            }elseif($existePendiente && $user->role == 'paciente'){
                return redirect()->route('pacientes.citas')->with('error', 'Ya hay una solicitud de cambio pendiente para esta cita.');
            }

            // Identificar quién recibe la solicitud
            $solicitadoId = ($user->id == $cita->expediente->user_id) 
                            ? $cita->doctor->user_id 
                            : $cita->expediente->user_id;

            if(!$estadoDiferente){
            \App\Models\SolicitudCambio::create([
                'cita_id' => $cita->id,
                'solicitante_id' => $user->id,
                'solicitado_id' => $solicitadoId,
                'nueva_fecha' => $request->nueva_fecha,
                'nueva_hora' => $request->nueva_hora,
                'motivo' => $request->motivo,
                'estado' => 'pendiente'
            ]);
            }else{
                $estadoDiferente->update([
                    
                    'nueva_fecha' => $request->nueva_fecha,
                    'nueva_hora' => $request->nueva_hora,
                    'motivo' => $request->motivo,
                    'estado' => 'pendiente'
                    ]);

            }

            if($user->role == 'doctor'){
            return redirect()->route('doctores.citas')->with('success', 'Solicitud enviada');
            } 
            elseif ($user->role == 'paciente'){
                return redirect()->route('pacientes.citas')->with('success', 'Solicitud enviada');
            }
        }

        // 2. Aceptar o Rechazar
        public function responderCambio(Request $request, $id)
        {
            $user = Auth::user();
            $solicitud = \App\Models\SolicitudCambio::where('cita_id', $id)
                        ->where('solicitado_id', $user->id)
                        ->first();

            if ($request->accion == 'aceptar') {
                // 1. Actualizar la cita real
                if($solicitud->cita->estado == 'confirmada'){
                $solicitud->cita->update([
                    'fecha' => $solicitud->nueva_fecha,
                    'hora_inicio' => $solicitud->nueva_hora
                    
                ]);
                }else{
                $solicitud->cita->update([
                    'fecha' => $solicitud->nueva_fecha,
                    'hora_inicio' => $solicitud->nueva_hora,
                    'estado' => 'confirmada' // Por si estaba solo solicitada
                ]);
                }
                // 2. Marcar solicitud como aceptada
                $solicitud->update(['estado' => 'aceptada']);
                
                if($user->role == 'paciente'){
                return redirect()->route('pacientes.citas')->with('success', 'Se ha actualizado la fecha de la cita.');
                } 
                elseif($user->role == 'doctor'){
                return redirect()->route('doctores.citas')->with('success', 'Se ha actualizado la fecha de la cita.');
                }


                
            } else {
                // Marcar como rechazada y guardar el motivo del por qué
                $solicitud->update([
                    'estado' => 'rechazada',
                    'motivo' => $request->motivo_rechazo
                ]);
                if($user->role == 'paciente'){
                return redirect()->route('pacientes.citas')->with('success', 'Has rechazado la solicitud de cambio.');
                } 
                elseif($user->role == 'doctor'){
                return redirect()->route('doctores.citas')->with('success', 'Has rechazado la solicitud de cambio.');
                }
           
             }
        }


}