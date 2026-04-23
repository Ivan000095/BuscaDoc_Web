<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Paciente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function show(string $id)
    {
        try {
            $usuario = User::with(['doctor.especialidades', 'doctor.disponibilidades', 'paciente'])->findOrFail($id);

            $data = match ($usuario->role) {
                'doctor' => $this->formatDoctorData($usuario),
                'paciente' => $this->formatPacienteData($usuario),
                default => throw new \Exception("Rol no válido: {$usuario->role}"),
            };

            return response()->json(['success' => true, 'data' => $data], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException) {
            return response()->json(['success' => false, 'message' => 'Usuario no encontrado'], 404);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al cargar el perfil', 'error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, string $id)
    {
        DB::beginTransaction();

        try {
            $usuario = User::findOrFail($id);
            $usuario->update([
                'name' => $request->input('name', $usuario->name),
                'email' => $request->input('email', $usuario->email),
            ]);

            match ($usuario->role) {
                'doctor' => $this->updateDoctorProfile($usuario, $request),
                'paciente' => $this->updatePacienteProfile($usuario, $request),
                default => throw new \Exception("Rol no válido para actualización"),
            };

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Perfil actualizado correctamente',
                'data' => ['id' => $usuario->id, 'name' => $usuario->name, 'role' => $usuario->role]
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Usuario no encontrado'], 404);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error al actualizar', 'error' => $e->getMessage()], 500);
        }
    }

    public function destroy(string $id)
    {
        try {
            $usuario = User::findOrFail($id);
            if (!$this->canDeleteUser($usuario)) {
                return response()->json([
                    'success' => false,
                    'message' => $usuario->role === 'doctor' 
                        ? 'No se puede eliminar: tienes citas pendientes por atender.'
                        : 'No se puede eliminar: tienes citas pendientes programadas.',
                ], 400);
            }

            DB::beginTransaction();
            if ($usuario->role === 'doctor' && $usuario->doctor) {
                $usuario->doctor->especialidades()->detach();
                $usuario->doctor->delete();
            } elseif ($usuario->role === 'paciente' && $usuario->paciente) {
                $usuario->paciente->delete();
            }

            if ($usuario->foto && Storage::disk('public')->exists($usuario->foto)) {
                Storage::disk('public')->delete($usuario->foto);
            }

            $usuario->delete();

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Cuenta eliminada exitosamente'], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException) {
            return response()->json(['success' => false, 'message' => 'Usuario no encontrado'], 404);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error al eliminar', 'error' => $e->getMessage()], 500);
        }
    }

    public function guardarFcmToken(Request $request)
    {
        $request->validate(['fcm_token' => 'required|string']);
        
        $request->user()->update(['fcm_token' => $request->fcm_token]);
        
        return response()->json(['success' => true]);
    }

    private function formatDoctorData(User $user): array
    {
        $doctor = $user->doctor;
        
        if (!$doctor) {
            throw new \Exception('Perfil médico no configurado');
        }

        return [
            'id' => $user->id,
            'doctor_id' => $doctor->id,
            'role' => 'doctor',
            'name' => $user->name ?? 'Sin nombre',
            'email' => $user->email,
            'especialidad' => $doctor->especialidades->pluck('nombre')->join(', '),
            'descripcion' => $doctor->descripcion,
            'fecha' => $user->f_nacimiento,
            'image' => $user->foto ? asset('storage/' . $user->foto) : null,
            'cedula' => $doctor->cedula,
            'costos' => '$' . number_format($doctor->costo, 2),
            'idiomas' => $doctor->idiomas ?? '',
            'duracion_cita' => $doctor->duracion_cita ?? 30,
            'citas' => (bool) ($doctor->citas ?? false),
            'horarios' => $doctor->disponibilidades->map(function($h) {
                return [
                    'dia' => $h->dia_semana,
                    'dia_semana' => $h->dia_semana,
                    'inicio' => $h->hora_inicio,
                    'hora_inicio' => $h->hora_inicio,
                    'fin' => $h->hora_fin,
                    'hora_fin' => $h->hora_fin,
                ];
            })->toArray(),

            'horarioentrada' => $doctor->horario_entrada,
            'horariosalida' => $doctor->horario_salida,
            
            'latitud' => $user->latitud,
            'longitud' => $user->longitud,
            'estado' => $user->estado,
        ];
    }

    private function formatPacienteData(User $user): array
    {
        $paciente = $user->paciente;
        
        return [
            'id' => $user->id,
            'role' => 'paciente',
            'name' => $user->name ?? 'Sin nombre',
            'email' => $user->email,
            'fecha' => $user->f_nacimiento,
            'image' => $user->foto ? asset('storage/' . $user->foto) : null,
            'latitud' => $user->latitud,
            'longitud' => $user->longitud,
            'tipo_sangre' => $paciente?->tipo_sangre,
            'alergias' => $paciente?->alergias,
            'cirugias' => $paciente?->cirugias,
            'padecimientos' => $paciente?->padecimientos,
            'habitos' => $paciente?->habitos,
            'contacto_emergencia' => $paciente?->contacto_emergencia,
        ];
    }

    private function updateDoctorProfile(User $user, Request $request): void
    {
        $doctor = $user->doctor;
        
        if (!$doctor) {
            throw new \Exception('Perfil médico no configurado');
        }

        $doctor->update([
            'descripcion' => $request->input('descripcion', $request->input('descripcion_doc', $doctor->descripcion)),
            'cedula' => $request->input('cedula', $doctor->cedula),
            'costo' => $this->parseCosto($request->input('costo', $request->input('costos', $doctor->costo))),
            'idiomas' => $request->input('idiomas', $doctor->idiomas),
            'duracion_cita' => $request->integer('duracion_cita', $doctor->duracion_cita ?? 30),
            'citas' => $request->boolean('citas', $doctor->citas ?? false),
            
            'horario_entrada' => $request->input('horario_entrada', $request->input('horarioentrada', $doctor->horario_entrada)),
            'horario_salida' => $request->input('horario_salida', $request->input('horariosalida', $doctor->horario_salida)),
        ]);

        if ($request->has('especialidades')) {
            $especialidadesIds = $request->input('especialidades');
            if (is_array($especialidadesIds)) {
                $doctor->especialidades()->sync($especialidadesIds);
            }
        } elseif ($request->has('especialidad_id')) {
            $doctor->especialidades()->sync([$request->input('especialidad_id')]);
        }

        if ($request->has('horarios')) {
            $nuevosHorarios = $request->input('horarios');
            
            if (is_array($nuevosHorarios)) {
                $doctor->disponibilidades()->delete();

                foreach ($nuevosHorarios as $horario) {
                    if (is_array($horario) && isset($horario['dia'], $horario['inicio'], $horario['fin'])) {
                        $doctor->disponibilidades()->create([
                            'dia_semana' => (int) $horario['dia'],
                            'hora_inicio' => $horario['inicio'],
                            'hora_fin' => $horario['fin'],
                        ]);
                    }
                }
            }
        }

        if ($request->hasFile('foto') || $request->hasFile('image')) {
            $file = $request->file('foto') ?? $request->file('image');
            $this->updateUserPhoto($user, $file);
        }
    }

    private function updatePacienteProfile(User $user, Request $request): void
    {
        $paciente = $user->paciente ?? new Paciente(['user_id' => $user->id]);
        
        $paciente->fill([
            'tipo_sangre' => $request->input('tipo_sangre'),
            'alergias' => $request->input('alergias'),
            'cirugias' => $request->input('cirugias'),
            'padecimientos' => $request->input('padecimientos'),
            'habitos' => $request->input('habitos'),
            'contacto_emergencia' => $request->input('contacto_emergencia'),
        ])->save();

        if ($request->hasFile('foto') || $request->hasFile('image')) {
            $file = $request->file('foto') ?? $request->file('image');
            $this->updateUserPhoto($user, $file);
        }
    }

    private function parseCosto(mixed $costo): float
    {
        if (is_numeric($costo)) return floatval($costo);
        if (is_string($costo)) {
            $clean = str_replace(['$', ',', ' '], '', trim($costo));
            return floatval($clean) ?: 0.0;
        }
        return 0.0;
    }

    private function updateUserPhoto(User $user, $file): void
    {
        if ($user->foto && Storage::disk('public')->exists($user->foto)) {
            Storage::disk('public')->delete($user->foto);
        }
        $user->foto = $file->store('users', 'public');
        $user->save();
    }

    private function canDeleteUser(User $user): bool
    {
        if ($user->role === 'doctor' && $user->doctor) {
            return !DB::table('citas')
                ->where('doctor_id', $user->doctor->id)
                ->where('estado', 'pendiente')
                ->exists();
        }
        
        if ($user->role === 'paciente' && $user->paciente) {
            return !DB::table('citas')
                ->where('paciente_id', $user->paciente->id)
                ->where('estado', 'pendiente')
                ->exists();
        }
        
        return true;
    }
}