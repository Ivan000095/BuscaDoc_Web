<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Obtener el perfil completo del usuario (Doctor o Paciente)
     */
    public function show(string $id): JsonResponse
    {
        try {
            $usuario = User::with(['doctor.especialidades', 'patient'])->findOrFail($id);

            $data = match ($usuario->role) {
                'doctor' => $this->formatDoctorData($usuario),
                'paciente' => $this->formatPacienteData($usuario),
                default => throw new \Exception("Rol no válido: {$usuario->role}"),
            };

            // Lógica según el rol definida para Buscadoc
            if ($usuario->role === 'doctor') {
                $usuario->load([
                    'doctor.especialidades',
                    'doctor.disponibilidades', // Nueva estructura de horarios
                    'doctor.reviews.autor',
                ]);
            } elseif ($usuario->role === 'paciente') {
                // Buscamos la información médica en el expediente propio vinculado al user_id
                $usuario->load(['expedientes' => function($query) {
                    $query->whereIn('parentesco', ['Propio', 'Expediente Propio']);
                }]);
            }

            return response()->json([
                "success" => true,
                "data" => $usuario
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException) {
            return response()->json(['success' => false, 'message' => 'Usuario no encontrado'], 404);
        } catch (\Exception $e) {
            return response()->json([
                "success" => false,
                "message" => "Error al obtener el perfil",
                "error" => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Actualizar datos comunes del usuario (FCM, Coordenadas, Estado)
     */
    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $usuario = User::findOrFail($id);

            $validated = $request->validate([
                'name' => 'sometimes|string|max:80',
                'email' => 'sometimes|email|unique:users,email,' . $usuario->id,
                'fcm_token' => 'nullable|string',
                'latitud' => 'nullable|numeric',
                'longitud' => 'nullable|numeric',
                'f_nacimiento' => 'sometimes|date',
                'foto' => 'nullable|image|max:5120',
                'estado' => 'sometimes|boolean',
            ]);

            DB::transaction(function () use ($request, $usuario, $validated) {
                if ($request->hasFile('foto')) {
                    if ($usuario->foto) Storage::disk('public')->delete($usuario->foto);
                    $usuario->foto = $request->file('foto')->store('users', 'public');
                }

                $usuario->update($request->only([
                    'name', 'email', 'f_nacimiento', 'latitud', 'longitud', 'fcm_token', 'estado'
                ]));

                // Si es paciente, sincronizamos nombre y fecha en su expediente propio
                        if ($usuario->role === 'doctor') {
                            $this->updateDoctorProfile($usuario, $request);
                        } elseif ($usuario->role === 'paciente') {
                            $this->updatePacienteProfile($usuario, $request);
                        }
            });

            return response()->json([
                "success" => true,
                "message" => "Usuario actualizado correctamente",
                "data" => $usuario
            ]);
        } catch (\Exception $e) {
            return response()->json(["success" => false, "error" => $e->getMessage()], 500);
        }
    }

    /**
     * Eliminar cuenta y limpiar archivos del servidor Ubuntu
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $usuario = User::findOrFail($id);

            if (!$usuario) {
                return response()->json(["success" => false, "message" => "Usuario no encontrado"], 404);
            }

            DB::transaction(function () use ($usuario) {
                // 1. Limpiar foto del storage
                if ($usuario->foto && Storage::disk('public')->exists($usuario->foto)) {
                    Storage::disk('public')->delete($usuario->foto);
                }

                // 2. Eliminar usuario (La cascada en DB debe limpiar Doctor/Paciente/Expedientes)
                $usuario->delete();
            });

            return response()->json(['success' => true, 'message' => 'Cuenta eliminada exitosamente'], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException) {
            return response()->json(['success' => false, 'message' => 'Usuario no encontrado'], 404);
        } catch (\Exception $e) {
            return response()->json([
                "success" => false,
                "message" => "Error al intentar eliminar la cuenta.",
                "error" => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Guardar o actualizar el token de Firebase
     */
    public function guardarFcmToken(Request $request): JsonResponse
    {
        $request->validate(['fcm_token' => 'required|string']);
        
        $user = $request->user(); // O buscar por ID si no usas auth sanctum aún
        if ($user) {
            $user->update(['fcm_token' => $request->fcm_token]);
            return response()->json(['success' => true, 'message' => 'Token actualizado']);
        }

        return response()->json(['success' => false, 'message' => 'No autenticado'], 401);
    }

    // ─────────────────────────────────────────────────────
    // MÉTODOS AUXILIARES PRIVADOS
    // ─────────────────────────────────────────────────────

    private function formatDoctorData(User $usuario): array
        {
            $doctor = $usuario->doctor;

            return [
                'id' => $usuario->id,
                'name' => $usuario->name,
                'email' => $usuario->email,
                'role' => $usuario->role,
                'foto' => $usuario->foto ? asset('storage/' . $usuario->foto) : null,
                'estado' => $usuario->estado,
                'perfil_medico' => $doctor ? [
                    'id' => $doctor->id,
                    'cedula' => $doctor->cedula,
                    'descripcion' => $doctor->descripcion,
                    'costo' => $this->parseCosto($doctor->costo),
                    'duracion_cita' => $doctor->duracion_cita,
                    'especialidades' => $doctor->especialidades->pluck('nombre'),
                    'disponibilidades' => $doctor->disponibilidades->map(function ($d) {
                        return [
                            'dia_semana' => $d->dia_semana,
                            'hora_inicio' => date('H:i', strtotime($d->hora_inicio)),
                            'hora_fin' => date('H:i', strtotime($d->hora_fin)),
                        ];
                    }),
                ] : null,
            ];
        }

        /**
         * Formatear la salida de datos para un Paciente (Usando Expedientes)
         */
        private function formatPacienteData(User $usuario): array
        {
            // Buscamos su información médica en su expediente propio
            $expedientePropio = $usuario->expedientes?->whereIn('parentesco', ['Propio', 'Expediente Propio'])->first();

            return [
                'id' => $usuario->id,
                'name' => $usuario->name,
                'email' => $usuario->email,
                'role' => $usuario->role,
                'foto' => $usuario->foto ? asset('storage/' . $usuario->foto) : null,
                'f_nacimiento' => $usuario->f_nacimiento,
                'estado' => $usuario->estado,
                'expediente_principal' => $expedientePropio ? [
                    'id' => $expedientePropio->id,
                    'genero' => $expedientePropio->genero,
                    'tipo_sangre' => $expedientePropio->tipo_sangre,
                    'alergias' => $expedientePropio->alergias,
                    'padecimientos_cronicos' => $expedientePropio->padecimientos_cronicos,
                    'habitos_salud' => $expedientePropio->habitos_salud,
                ] : null,
            ];
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
        
        if ($user->role === 'paciente' && $user->patient) {
            return !DB::table('citas')
                ->where('paciente_id', $user->patient->id)
                ->where('estado', 'pendiente')
                ->exists();
        }
        
        return true;
    }
}