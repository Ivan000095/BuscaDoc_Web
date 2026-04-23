<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Support\Facades\DB;
use App\Models\Expediente; 
use App\Models\Doctor;
use App\Models\Paciente;
use Illuminate\Support\Facades\Storage;

class AuthController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $rutaFoto = null;

        try {
            $validated = $request->validate([
                "name" => "required|string|max:255",
                "email" => "required|email|unique:users,email",
                "password" => "required|string|min:8|confirmed",
                "role" => "required|in:doctor,paciente,farmacia",
                "f_nacimiento" => "required|date",
                "foto" => "nullable|image|max:2048",
                "latitud" => "nullable|numeric",
                "longitud" => "nullable|numeric",
                "cedula" => "required_if:role,doctor|string|max:50",
                "costo" => "required_if:role,doctor|numeric|min:0",
                "duracion_cita" => "required_if:role,doctor|integer|min:5|max:180",
                "citas" => "required_if:role,doctor|boolean",
                "horarios" => "required_if:role,doctor|array",
                "horarios.*.dia" => "required_if:role,doctor|integer|between:0,6",
                "horarios.*.inicio" => "required_if:role,doctor|date_format:H:i",
                "horarios.*.fin" => "required_if:role,doctor|date_format:H:i|after:horarios.*.inicio",
                "especialidades" => "required_if:role,doctor|array",
                "especialidades.*" => "exists:especialidads,id",
                "idiomas" => "nullable|string|max:255",
                "descripcion_doc" => "nullable|string|max:500",
                "tipo_sangre" => "required_if:role,paciente|string",
                "contacto_emergencia" => "required_if:role,paciente|string",
            ]);

            $user = DB::transaction(function () use ($request, $validated, &$rutaFoto) {
                if ($request->hasFile('foto')) {
                    $rutaFoto = $request->file('foto')->store('perfiles', 'public');
                }

                $user = User::create([
                    "name" => $validated["name"],
                    "email" => $validated["email"],
                    "password" => Hash::make($validated["password"]),
                    "role" => $validated["role"],
                    "f_nacimiento" => $validated["f_nacimiento"],
                    "foto" => $rutaFoto,
                    "latitud" => $validated["latitud"] ?? 16.91173660,
                    "longitud" => $validated["longitud"] ?? -92.09460000,
                ]);

                switch ($validated['role']) {
                    case 'doctor':
                        $doctor = Doctor::create([
                            'user_id' => $user->id,
                            'cedula' => $validated['cedula'],
                            'costo' => $validated['costo'],
                            'duracion_cita' => $validated['duracion_cita'] ?? 30,
                            'citas' => $validated['citas'] ?? true,
                            'idiomas' => $request->input('idiomas', 'Español'),
                            'descripcion' => $request->input('descripcion_doc', 'Sin descripción'),
                        ]);

                        if ($request->has('especialidades') && !empty($validated['especialidades'])) {
                            $doctor->especialidades()->sync($validated['especialidades']);
                        }

                        if ($request->has('horarios') && !empty($validated['horarios'])) {
                            foreach ($validated['horarios'] as $horario) {
                                $doctor->disponibilidades()->create([
                                    'dia_semana' => (int) $horario['dia'],
                                    'hora_inicio' => $horario['inicio'],
                                    'hora_fin' => $horario['fin'],
                                ]);
                            }
                        }
                    break;

                    case 'paciente':
                        Expediente::create([
                            'user_id' => $user->id,
                            'nombre_completo' => $validated['name'],
                            'fecha_nacimiento' => $validated['f_nacimiento'],
                            'genero' => strtolower($request->input('genero', 'masculino')), 
                            'parentesco' => $request->input('parentesco', 'Expediente Propio'),
                            'tipo_sangre' => $validated['tipo_sangre'],
                            'contacto_emergencia' => $validated['contacto_emergencia'],
                            'alergias' => $request->input('alergias'),
                            'padecimientos_cronicos' => $request->input('padecimientos'),
                            'habitos_salud' => $request->input('habitos'),
                        ]);
                    break;
                }

                return $user;
            });

            $token = $user->createToken("mobile-app")->plainTextToken;

            return response()->json([
                "success" => true,
                "message" => "Usuario registrado exitosamente",
                "data" => [
                    "user" => [
                        "id" => $user->id,
                        "name" => $user->name,
                        "email" => $user->email,
                        "role" => $user->role,
                        "foto" => $user->foto ? url('storage/' . $user->foto) : null,
                    ],
                    "token" => $token,
                ],
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                "success" => false,
                "message" => "Error de validación",
                "errors" => $e->errors(),
            ], 422);
            
        } catch (\Exception $e) {
            if ($rutaFoto && Storage::disk('public')->exists($rutaFoto)) {
                Storage::disk('public')->delete($rutaFoto);
            }
            
            Log::error('Error en registro API: ' . $e->getMessage());

            return response()->json([
                "success" => false,
                "message" => "Error interno del servidor",
                "error" => $e->getMessage(),
            ], 500);
        }
    }

    public function login(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                "email" => "required|email",
                "password" => "required|string",
                "device_name" => "required|string",
            ]);

            $user = User::where("email", $validated["email"])->first();
            if (
                !$user ||
                !Hash::check($validated["password"], $user->password)
            ) {
                throw ValidationException::withMessages([
                    "email" => [
                        "Las credenciales proporcionadas son incorrectas.",
                    ],
                ]);
            }

            $user->tokens()->where("name", $validated["device_name"])->delete();

            $token = $user->createToken($validated["device_name"])
                ->plainTextToken;

            return response()->json(
                [
                    "success" => true,
                    "message" => "Login exitoso",
                    "data" => [
                        "user" => [
                            "id" => $user->id,
                            "name" => $user->name,
                            "email" => $user->email,
                            "role" => $user->role,
                            "foto" => "https://buscadoc.online/storage/" . $user->foto,
                            "email_verified_at" => $user->email_verified_at,
                        ],
                        "token" => $token,
                    ],
                ],
                200,
            );
        } catch (ValidationException $e) {
            return response()->json(
                [
                    "success" => false,
                    "message" => "Error de validación",
                    "errors" => $e->errors(),
                ],
                422,
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    "success" => false,
                    "message" => "Error interno del servidor",
                    "error" => $e->getMessage(),
                ],
                500,
            );
        }
    }

    public function logout(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $currentToken = $user->currentAccessToken();

            if (!$currentToken) {
                return response()->json(
                    [
                        "success" => false,
                        "message" => "Token ya inválido",
                        "error" => "No hay token activo para revocar",
                    ],
                    400,
                );
            }

            Log::info("Logout attempt", [
                "user_id" => $user->id,
                "token_id" => $currentToken->id,
                "token_name" => $currentToken->name,
            ]);

            $deleted = $currentToken->delete();

            Log::info("Token deletion result", [
                "deleted" => $deleted,
                "token_id" => $currentToken->id,
            ]);

            if (!$deleted) {
                throw new \Exception("No se pudo eliminar el token");
            }

            return response()->json(
                [
                    "success" => true,
                    "message" => "Logout exitoso",
                    "data" => [
                        "token_revoked" => true,
                        "token_id" => $currentToken->id,
                    ],
                ],
                200,
            );
        } catch (\Exception $e) {
            Log::error("Logout error", [
                "error" => $e->getMessage(),
                "trace" => $e->getTraceAsString(),
            ]);

            return response()->json(
                [
                    "success" => false,
                    "message" => "Error al cerrar sesión",
                    "error" => $e->getMessage(),
                ],
                500,
            );
        }
    }

    public function logoutAll(Request $request): JsonResponse
    {
        try {
            $request->user()->tokens()->delete();

            return response()->json(
                [
                    "success" => true,
                    "message" => "Sesión cerrada en todos los dispositivos",
                ],
                200,
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    "success" => false,
                    "message" => "Error al cerrar sesiones",
                    "error" => $e->getMessage(),
                ],
                500,
            );
        }
    }

    public function me(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            return response()->json(
                [
                    "success" => true,
                    "data" => [
                        "user" => [
                            "id" => $user->id,
                            "name" => $user->name,
                            "email" => $user->email,
                            "email_verified_at" => $user->email_verified_at,
                            "created_at" => $user->created_at,
                            "updated_at" => $user->updated_at,
                        ],
                    ],
                ],
                200,
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    "success" => false,
                    "message" => "Error al obtener información del usuario",
                    "error" => $e->getMessage(),
                ],
                500,
            );
        }
    }

    public function updateProfile(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            
            $validated = $request->validate([
                "name" => "sometimes|required|string|max:255",
                "email" => "sometimes|required|email|unique:users,email," . $user->id,
                "f_nacimiento" => "sometimes|nullable|date",
                "latitud" => "sometimes|nullable|numeric",
                "longitud" => "sometimes|nullable|numeric",
                "foto" => "nullable|image|max:2048",
                "current_password" => "required_with:password,email|string",
                "password" => "sometimes|required|string|min:8|confirmed",
            ]);

            if (isset($validated["password"]) || isset($validated["email"])) {
                if (
                    !isset($validated["current_password"]) ||
                    !Hash::check($validated["current_password"], $user->password)
                ) {
                    throw ValidationException::withMessages([
                        "current_password" => ["La contraseña actual es incorrecta."],
                    ]);
                }
            }

            $result = DB::transaction(function () use ($request, $user, $validated) {
                
                if ($request->hasFile('foto')) {
                    if ($user->foto && Storage::disk('public')->exists($user->foto)) {
                        Storage::disk('public')->delete($user->foto);
                    }
                    $user->foto = $request->file('foto')->store('perfiles', 'public');
                }

                if (isset($validated["name"])) $user->name = $validated["name"];
                if (isset($validated["email"])) {
                    $user->email = $validated["email"];
                    $user->email_verified_at = null;
                }
                if (isset($validated["f_nacimiento"])) $user->f_nacimiento = $validated["f_nacimiento"];
                if (isset($validated["latitud"])) $user->latitud = $validated["latitud"];
                if (isset($validated["longitud"])) $user->longitud = $validated["longitud"];
                if (isset($validated["password"])) $user->password = Hash::make($validated["password"]);
                
                $user->save();

                $doctorData = null;

                if ($user->role === 'doctor' && ($request->isMethod('put') || $request->isMethod('patch'))) {
                    
                    $doctor = $user->doctor;
                    
                    if ($doctor) {
                        $doctorValidated = $request->validate([
                            "cedula" => "sometimes|string|max:50",
                            "costo" => "sometimes|numeric|min:0",
                            "duracion_cita" => "sometimes|integer|min:5|max:180",
                            "citas" => "sometimes|boolean",
                            "descripcion_doc" => "sometimes|nullable|string|max:500",
                            "idiomas" => "sometimes|nullable|string|max:255",
                            "especialidades" => "sometimes|nullable|array",
                            "especialidades.*" => "exists:especialidads,id",
                            "horarios" => "sometimes|nullable|array",
                            "horarios.*.dia" => "integer|between:0,6",
                            "horarios.*.inicio" => "date_format:H:i",
                            "horarios.*.fin" => "date_format:H:i|after:horarios.*.inicio",
                        ]);

                        if (isset($doctorValidated["cedula"])) $doctor->cedula = $doctorValidated["cedula"];
                        if (isset($doctorValidated["costo"])) $doctor->costo = $doctorValidated["costo"];
                        if (isset($doctorValidated["duracion_cita"])) $doctor->duracion_cita = $doctorValidated["duracion_cita"];
                        if (isset($doctorValidated["citas"])) $doctor->citas = $doctorValidated["citas"];
                        if (isset($doctorValidated["descripcion_doc"])) $doctor->descripcion = $doctorValidated["descripcion_doc"];
                        if (isset($doctorValidated["idiomas"])) $doctor->idiomas = $doctorValidated["idiomas"];
                        
                        $doctor->save();

                        if ($request->has('especialidades')) {
                            $especialidadesIds = $request->input('especialidades');
                            if (is_array($especialidadesIds)) {
                                $doctor->especialidades()->sync($especialidadesIds);
                            }
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

                        $doctor->load(['especialidades', 'disponibilidades']);
                        $doctorData = $doctor;
                    }
                }

                return ['user' => $user, 'doctor' => $doctorData];
            });

            return response()->json([
                "success" => true,
                "message" => "Perfil actualizado exitosamente",
                "data" => [
                    "user" => [
                        "id" => $result['user']->id,
                        "name" => $result['user']->name,
                        "email" => $result['user']->email,
                        "role" => $result['user']->role,
                        "foto" => $result['user']->foto ? url('storage/' . $result['user']->foto) : null,
                        "f_nacimiento" => $result['user']->f_nacimiento,
                        "latitud" => $result['user']->latitud,
                        "longitud" => $result['user']->longitud,
                        "updated_at" => $result['user']->updated_at,
                    ],
                    "doctor" => $result['doctor'] ? [
                        "id" => $result['doctor']->id,
                        "cedula" => $result['doctor']->cedula,
                        "costo" => $result['doctor']->costo,
                        "duracion_cita" => $result['doctor']->duracion_cita,
                        "citas" => $result['doctor']->citas,
                        "descripcion" => $result['doctor']->descripcion,
                        "idiomas" => $result['doctor']->idiomas,
                        "especialidades" => $result['doctor']->especialidades->pluck('id'),
                        "horarios" => $result['doctor']->disponibilidades->map(function($h) {
                            return [
                                'dia' => $h->dia_semana,
                                'inicio' => $h->hora_inicio,
                                'fin' => $h->hora_fin,
                            ];
                        }),
                    ] : null,
                ],
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                "success" => false,
                "message" => "Error de validación",
                "errors" => $e->errors(),
            ], 422);
            
        } catch (\Exception $e) {
            \Log::error('Error en updateProfile: ' . $e->getMessage());
            
            return response()->json([
                "success" => false,
                "message" => "Error al actualizar perfil",
                "error" => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    public function tokens(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $tokens = $user
                ->tokens()
                ->get(["id", "name", "last_used_at", "created_at"]);

            return response()->json(
                [
                    "success" => true,
                    "data" => [
                        "tokens" => $tokens->map(function ($token) {
                            return [
                                "id" => $token->id,
                                "name" => $token->name,
                                "last_used_at" => $token->last_used_at,
                                "created_at" => $token->created_at,
                                "is_current" =>
                                    $token->id ===
                                    request()->user()->currentAccessToken()->id,
                            ];
                        }),
                    ],
                ],
                200,
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    "success" => false,
                    "message" => "Error al obtener tokens",
                    "error" => $e->getMessage(),
                ],
                500,
            );
        }
    }

    public function revokeToken(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                "token_id" =>
                    "required|integer|exists:personal_access_tokens,id",
            ]);

            $user = $request->user();
            $token = $user
                ->tokens()
                ->where("id", $validated["token_id"])
                ->first();

            if (!$token) {
                return response()->json(
                    [
                        "success" => false,
                        "message" => "Token no encontrado",
                    ],
                    404,
                );
            }

            $token->delete();

            return response()->json(
                [
                    "success" => true,
                    "message" => "Token revocado exitosamente",
                ],
                200,
            );
        } catch (ValidationException $e) {
            return response()->json(
                [
                    "success" => false,
                    "message" => "Error de validación",
                    "errors" => $e->errors(),
                ],
                422,
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    "success" => false,
                    "message" => "Error al revocar token",
                    "error" => $e->getMessage(),
                ],
                500,
            );
        }
    }
}