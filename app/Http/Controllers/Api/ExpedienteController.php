<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Expediente;
use Illuminate\Http\Request;

class ExpedienteController extends Controller
{
    /**
     * Muestra la lista de expedientes del usuario logueado en la App.
     */
    public function index(Request $request)
    {
        try {
            $user = $request->user();
            
            $expedientes = Expediente::where('user_id', $user->id)
                ->orderByRaw("CASE WHEN LOWER(parentesco) = 'yo mismo' THEN 0 ELSE 1 END")
                ->orderBy('nombre_completo', 'asc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $expedientes
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar los expedientes.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Guarda un nuevo expediente desde la App.
     */
    public function store(Request $request)
    {
        try {
            $user = $request->user();

            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
                'nombre_completo'        => 'required|string|max:80',
                'fecha_nacimiento'       => 'required|date|before_or_equal:today',
                'genero'                 => 'required|in:masculino,femenino',
                'parentesco'             => 'required|string|max:30',
                'tipo_sangre'            => 'nullable|string|max:5',
                'alergias'               => 'nullable',
                'padecimientos_cronicos' => 'nullable',
                'habitos_salud'          => 'nullable',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error de validación',
                    'errors' => $validator->errors()
                ], 422);
            }

            $expediente = Expediente::create([
                'user_id'                => $user->id,
                'nombre_completo'        => $request->nombre_completo,
                'fecha_nacimiento'       => $request->fecha_nacimiento,
                'genero'                 => $request->genero,
                'parentesco'             => $request->parentesco,
                'tipo_sangre'            => $request->tipo_sangre,
                'alergias'               => is_array($request->alergias) ? implode(', ', $request->alergias) : $request->alergias,
                'padecimientos_cronicos' => is_array($request->padecimientos_cronicos) ? implode(', ', $request->padecimientos_cronicos) : $request->padecimientos_cronicos,
                'habitos_salud'          => is_array($request->habitos_salud) ? implode(', ', $request->habitos_salud) : $request->habitos_salud,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Expediente creado correctamente.',
                'data' => $expediente
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error interno al crear el expediente.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Muestra el detalle de un expediente específico en la App (Incluye notas médicas).
     */
    public function show(Request $request, $id)
    {
        try {
            $user = $request->user();
            
            $expediente = Expediente::with(['notas' => function($query) {
                $query->orderBy('created_at', 'desc');
            }])->findOrFail($id);

            // Validar que el paciente solo vea sus propios expedientes
            // (Si el usuario es doctor, necesitarás agregar la validación de que tenga una cita asignada)
            if ($user->role === 'paciente' && $expediente->user_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Acceso denegado.'
                ], 403);
            }

            return response()->json([
                'success' => true,
                'data' => $expediente
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Expediente no encontrado.',
            ], 404);
        }
    }

    /**
     * Actualiza un expediente desde la App.
     */
    public function update(Request $request, $id)
    {
        try {
            $user = $request->user();
            $expediente = Expediente::findOrFail($id);

            if ($expediente->user_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Acción no autorizada.'
                ], 403);
            }

            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
                'nombre_completo'        => 'required|string|max:80',
                'fecha_nacimiento'       => 'required|date|before_or_equal:today',
                'genero'                 => 'required|in:masculino,femenino',
                'parentesco'             => 'required|string|max:30',
                'tipo_sangre'            => 'nullable|string|max:5',
                'alergias'               => 'nullable',
                'padecimientos_cronicos' => 'nullable',
                'habitos_salud'          => 'nullable',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error de validación',
                    'errors' => $validator->errors()
                ], 422);
            }

            $expediente->update([
                'nombre_completo'        => $request->nombre_completo,
                'fecha_nacimiento'       => $request->fecha_nacimiento,
                'genero'                 => $request->genero,
                'parentesco'             => $request->parentesco,
                'tipo_sangre'            => $request->tipo_sangre,
                'alergias'               => is_array($request->alergias) ? implode(', ', $request->alergias) : $request->alergias,
                'padecimientos_cronicos' => is_array($request->padecimientos_cronicos) ? implode(', ', $request->padecimientos_cronicos) : $request->padecimientos_cronicos,
                'habitos_salud'          => is_array($request->habitos_salud) ? implode(', ', $request->habitos_salud) : $request->habitos_salud,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Expediente actualizado correctamente.',
                'data' => $expediente
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el expediente.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}