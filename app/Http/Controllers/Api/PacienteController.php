<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Paciente;
use App\Models\User;
use App\Models\Expediente;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class PacienteController extends Controller
{
    /**
     * Registro: Incluye fcm_token para notificaciones push
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:80',
            'email' => 'required|email|max:80|unique:users,email',
            'password' => 'required_without:google_id|min:8',
            'google_id' => 'nullable|string|max:100|unique:users,google_id',
            'fcm_token' => 'nullable|string', // Nuevo campo para Firebase
            'foto' => 'nullable|image|max:5120',
            'f_nacimiento' => 'required|date',
            'latitud' => 'nullable|numeric',
            'longitud' => 'nullable|numeric',
            'genero' => 'required|in:masculino,femenino,otro',
            'tipo_sangre' => 'nullable|string',
            'alergias' => 'nullable|string',
            'padecimientos_cronicos' => 'nullable|string',
            'habitos_salud' => 'nullable|string',
        ]);

        try {
            $nuevoPaciente = DB::transaction(function () use ($request, $validated) {
                // 1. Crear el Usuario con FCM Token
                $user = User::create([
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'password' => isset($validated['password']) ? Hash::make($validated['password']) : Hash::make(bin2hex(random_bytes(8))),
                    'role' => 'paciente',
                    'foto' => $request->hasFile('foto') ? $request->file('foto')->store('users', 'public') : null,
                    'f_nacimiento' => $validated['f_nacimiento'],
                    'latitud' => $validated['latitud'],
                    'longitud' => $validated['longitud'],
                    'google_id' => $validated['google_id'],
                    'fcm_token' => $validated['fcm_token'], // Guardamos el token
                    'estado' => true,
                ]);

                // 2. Vínculo mínimo en Pacientes
                $paciente = Paciente::create(['user_id' => $user->id]);

                // 3. Crear el Expediente Propio
                Expediente::create([
                    'user_id' => $user->id, 
                    'nombre_completo' => $user->name,
                    'parentesco' => 'Propio',
                    'genero' => $validated['genero'],
                    'fecha_nacimiento' => $user->f_nacimiento,
                    'tipo_sangre' => $validated['tipo_sangre'],
                    'alergias' => $validated['alergias'],
                    'padecimientos_cronicos' => $validated['padecimientos_cronicos'],
                    'habitos_salud' => $validated['habitos_salud'],
                ]);

                return $paciente->load(['user.expedientes']);
            });

            return response()->json(['success' => true, 'data' => $nuevoPaciente], 201);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Actualizar: Permite renovar el fcm_token cuando el usuario cambia de dispositivo
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $paciente = Paciente::with('user')->findOrFail($id);
            $user = $paciente->user;

            $validated = $request->validate([
                'name' => 'sometimes|string|max:80',
                'email' => 'sometimes|email|unique:users,email,' . $user->id,
                'fcm_token' => 'nullable|string', // Permitir actualización de token
                'f_nacimiento' => 'sometimes|date',
                'genero' => 'sometimes|in:masculino,femenino,otro',
                'tipo_sangre' => 'nullable|string',
                'alergias' => 'nullable|string',
                'padecimientos_cronicos' => 'nullable|string',
                'habitos_salud' => 'nullable|string',
                'estado' => 'sometimes|boolean',
            ]);

            DB::transaction(function () use ($request, $user) {
                // Actualizar Usuario incluyendo fcm_token
                $user->update($request->only([
                    'name', 'email', 'f_nacimiento', 'latitud', 'longitud', 
                    'estado', 'google_id', 'fcm_token'
                ]));

                // Sincronizar con el Expediente Propio
                $expediente = Expediente::where('user_id', $user->id)
                    ->whereIn('parentesco', ['Propio', 'Expediente Propio'])
                    ->first();

                if ($expediente) {
                    $expediente->update([
                        'nombre_completo' => $request->name ?? $expediente->nombre_completo,
                        'fecha_nacimiento' => $request->f_nacimiento ?? $expediente->fecha_nacimiento,
                        'genero' => $request->genero ?? $expediente->genero,
                        'tipo_sangre' => $request->tipo_sangre ?? $expediente->tipo_sangre,
                        'alergias' => $request->alergias ?? $expediente->alergias,
                        'padecimientos_cronicos' => $request->padecimientos_cronicos ?? $expediente->padecimientos_cronicos,
                        'habitos_salud' => $request->habitos_salud ?? $expediente->habitos_salud,
                    ]);
                }
            });

            return response()->json([
                'success' => true,
                'message' => 'Perfil médico y token de notificaciones actualizados',
                'data' => $paciente->load(['user.expedientes'])
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }


    public function destroy($id): JsonResponse
        {
            try {
                // Buscamos el paciente con su usuario para tener acceso a la foto
                $paciente = Paciente::with('user')->find($id);

                if (!$paciente) {
                    return response()->json([
                        "success" => false,
                        "message" => "Paciente no encontrado",
                    ], 404);
                }

                $user = $paciente->user;

                DB::transaction(function () use ($paciente, $user) {
                    // 1. Eliminar la foto del storage si existe
                    if ($user->foto && Storage::disk('public')->exists($user->foto)) {
                        Storage::disk('public')->delete($user->foto);
                    }

                    // 2. Eliminar el usuario 
                    // Al eliminar el User, se eliminan por cascada el Paciente 
                    // y los Expedientes vinculados por user_id.
                    $user->delete();
                });

                return response()->json([
                    "success" => true,
                    "message" => "Cuenta de paciente y expedientes eliminados correctamente"
                ], 200);

            } catch (\Exception $e) {
                return response()->json([
                    "success" => false,
                    "message" => "Error al intentar eliminar el paciente",
                    "error" => $e->getMessage()
                ], 500);
            }
        }




}