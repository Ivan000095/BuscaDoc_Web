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

class AuthController extends Controller
{
    /**
     * Registro de usuario
     */
    public function register(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                "name" => "required|string|max:255",
                "email" => "required|email|unique:users,email",
                "password" => "required|string|min:8|confirmed",
            ]);

            $user = User::create([
                "name" => $validated["name"],
                "email" => $validated["email"],
                "password" => Hash::make($validated["password"]),
            ]);

            $token = $user->createToken("mobile-app")->plainTextToken;

            return response()->json(
                [
                    "success" => true,
                    "message" => "Usuario registrado exitosamente",
                    "data" => [
                        "user" => [
                            "id" => $user->id,
                            "name" => $user->name,
                            "email" => $user->email,
                            "created_at" => $user->created_at,
                        ],
                        "token" => $token,
                    ],
                ],
                201,
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

    /**
     * Login de usuario
     */
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

            // Revocar tokens existentes del mismo dispositivo (opcional)
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

    /**
     * Logout de usuario
     */
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

            // Revocar el token actual
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

    /**
     * Logout de todos los dispositivos
     */
    public function logoutAll(Request $request): JsonResponse
    {
        try {
            // Revocar todos los tokens del usuario
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

    /**
     * Obtener información del usuario autenticado
     */
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

    /**
     * Actualizar perfil del usuario
     */
    public function updateProfile(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            $validated = $request->validate([
                "name" => "sometimes|required|string|max:255",
                "email" =>
                    "sometimes|required|email|unique:users,email," . $user->id,
                "current_password" => "required_with:password|string",
                "password" => "sometimes|required|string|min:8|confirmed",
            ]);

            // Verificar contraseña actual si se quiere cambiar datos sensibles
            if (isset($validated["password"]) || isset($validated["email"])) {
                if (
                    !isset($validated["current_password"]) ||
                    !Hash::check(
                        $validated["current_password"],
                        $user->password,
                    )
                ) {
                    throw ValidationException::withMessages([
                        "current_password" => [
                            "La contraseña actual es incorrecta.",
                        ],
                    ]);
                }
            }

            // Actualizar campos
            if (isset($validated["name"])) {
                $user->name = $validated["name"];
            }

            if (isset($validated["email"])) {
                $user->email = $validated["email"];
                $user->email_verified_at = null; // Resetear verificación si cambia email
            }

            if (isset($validated["password"])) {
                $user->password = Hash::make($validated["password"]);
            }

            $user->save();

            return response()->json(
                [
                    "success" => true,
                    "message" => "Perfil actualizado exitosamente",
                    "data" => [
                        "user" => [
                            "id" => $user->id,
                            "name" => $user->name,
                            "email" => $user->email,
                            "email_verified_at" => $user->email_verified_at,
                            "updated_at" => $user->updated_at,
                        ],
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
                    "message" => "Error al actualizar perfil",
                    "error" => $e->getMessage(),
                ],
                500,
            );
        }
    }

    /**
     * Listar tokens activos del usuario
     */
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

    /**
     * Revocar un token específico
     */
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
