<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
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
            $usuario = User::find($id);

            if (!$usuario) {
                return response()->json([
                    "success" => false,
                    "message" => "Usuario no encontrado",
                ], 404);
            }

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
                if ($usuario->role === 'paciente') {
                    DB::table('expedientes')
                        ->where('user_id', $usuario->id)
                        ->whereIn('parentesco', ['Propio', 'Expediente Propio'])
                        ->update([
                            'nombre_completo' => $usuario->name,
                            'fecha_nacimiento' => $usuario->f_nacimiento,
                        ]);
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
            $usuario = User::find($id);

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

            return response()->json([
                "success" => true,
                "message" => "Cuenta eliminada de forma exitosa.",
            ], 200);

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
}
