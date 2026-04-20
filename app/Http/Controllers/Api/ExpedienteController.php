<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Expediente;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ExpedienteController extends Controller
{
    /**
     * Listar todos los expedientes del usuario logueado (Propio y Familiares)
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            $expedientes = Expediente::where('user_id', $user->id)
                                     ->orderBy('parentesco', 'asc') // Opcional: ordenar para que "Propio" salga primero o por alfabeto
                                     ->get();

            return response()->json([
                'success' => true,
                'data' => $expedientes
            ], 200);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Crear un nuevo expediente (Normalmente para un familiar)
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nombre_completo'    => 'required|string|max:80',
            'fecha_nacimiento'   => 'required|date|before_or_equal:today',
            'genero'             => 'required|in:masculino,femenino,otro',
            'parentesco'         => 'required|string|max:30', // Ej: Hijo, Madre, Esposo
            'tipo_sangre'        => 'nullable|string|max:5',
            'alergias'           => 'nullable',
            'padecimientos'      => 'nullable', // Viene como padecimientos en tu request web
            'habitos'            => 'nullable', // Viene como habitos en tu request web
        ]);

        try {
            $user = $request->user();

            $expediente = Expediente::create([
                'user_id'                => $user->id,
                'nombre_completo'        => $validated['nombre_completo'],
                'fecha_nacimiento'       => $validated['fecha_nacimiento'],
                'genero'                 => $validated['genero'],
                'parentesco'             => $validated['parentesco'],
                'tipo_sangre'            => $validated['tipo_sangre'] ?? null,
                // Manejo inteligente: Si la App móvil envía un array, lo convierte a string con comas. Si manda string, lo deja igual.
                'alergias'               => is_array($request->alergias) ? implode(', ', $request->alergias) : $request->alergias,
                'padecimientos_cronicos' => is_array($request->padecimientos) ? implode(', ', $request->padecimientos) : $request->padecimientos,
                'habitos_salud'          => is_array($request->habitos) ? implode(', ', $request->habitos) : $request->habitos,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Expediente creado correctamente',
                'data' => $expediente
            ], 201);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Mostrar los detalles de un expediente en específico
     */
    public function show(Request $request, $id): JsonResponse
    {
        try {
            // Buscamos el expediente asegurándonos de que pertenezca al usuario logueado
            $expediente = Expediente::where('id', $id)
                                    ->where('user_id', $request->user()->id)
                                    ->first();

            if (!$expediente) {
                return response()->json(['success' => false, 'message' => 'Expediente no encontrado o no autorizado'], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $expediente
            ], 200);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Actualizar los datos médicos de un expediente
     */
    public function update(Request $request, $id): JsonResponse
    {
        $validated = $request->validate([
            'nombre_completo'    => 'sometimes|string|max:80',
            'fecha_nacimiento'   => 'sometimes|date|before_or_equal:today',
            'genero'             => 'sometimes|in:masculino,femenino,otro',
            'parentesco'         => 'sometimes|string|max:30',
            'tipo_sangre'        => 'nullable|string|max:5',
            'alergias'           => 'nullable',
            'padecimientos'      => 'nullable',
            'habitos'            => 'nullable',
        ]);

        try {
            $user = $request->user();
            $expediente = Expediente::where('id', $id)->where('user_id', $user->id)->first();

            if (!$expediente) {
                return response()->json(['success' => false, 'message' => 'Expediente no encontrado o no autorizado'], 404);
            }

            // Sincronización especial: Si se actualiza el "Expediente Propio", 
            // reflejamos el nombre y la fecha en la tabla de usuarios.
            DB::transaction(function () use ($request, $validated, $user, $expediente) {
                
                $expediente->update([
                    'nombre_completo'        => $validated['nombre_completo'] ?? $expediente->nombre_completo,
                    'fecha_nacimiento'       => $validated['fecha_nacimiento'] ?? $expediente->fecha_nacimiento,
                    'genero'                 => $validated['genero'] ?? $expediente->genero,
                    'parentesco'             => $validated['parentesco'] ?? $expediente->parentesco,
                    'tipo_sangre'            => $request->has('tipo_sangre') ? $request->tipo_sangre : $expediente->tipo_sangre,
                    'alergias'               => $request->has('alergias') ? (is_array($request->alergias) ? implode(', ', $request->alergias) : $request->alergias) : $expediente->alergias,
                    'padecimientos_cronicos' => $request->has('padecimientos') ? (is_array($request->padecimientos) ? implode(', ', $request->padecimientos) : $request->padecimientos) : $expediente->padecimientos_cronicos,
                    'habitos_salud'          => $request->has('habitos') ? (is_array($request->habitos) ? implode(', ', $request->habitos) : $request->habitos) : $expediente->habitos_salud,
                ]);

                if (in_array($expediente->parentesco, ['Propio', 'Expediente Propio'])) {
                    $user->update([
                        'name' => $expediente->nombre_completo,
                        'f_nacimiento' => $expediente->fecha_nacimiento,
                    ]);
                }
            });

            return response()->json([
                'success' => true,
                'message' => 'Expediente actualizado correctamente',
                'data' => $expediente
            ], 200);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Eliminar un expediente de un familiar
     */
    public function destroy(Request $request, $id): JsonResponse
    {
        try {
            $user = $request->user();
            $expediente = Expediente::where('id', $id)->where('user_id', $user->id)->first();

            if (!$expediente) {
                return response()->json(['success' => false, 'message' => 'Expediente no encontrado o no autorizado'], 404);
            }

            // REGLA DE NEGOCIO: No permitir borrar el expediente propio desde aquí.
            // Para borrar el propio, el paciente debe eliminar su cuenta completa.
            if (in_array($expediente->parentesco, ['Propio', 'Expediente Propio'])) {
                return response()->json([
                    'success' => false, 
                    'message' => 'No puedes eliminar tu expediente principal. Si deseas hacerlo, debes eliminar tu cuenta.'
                ], 403);
            }

            $expediente->delete();

            return response()->json([
                'success' => true,
                'message' => 'Expediente del familiar eliminado correctamente'
            ], 200);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
}