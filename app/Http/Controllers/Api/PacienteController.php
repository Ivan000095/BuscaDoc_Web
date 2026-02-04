<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Paciente;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PacienteController extends Controller
{
    public function index(): JsonResponse
    {
        // Retornamos todos los pacientes con su información de usuario relacionada
        $pacientes = Paciente::with('user')->get();
        return response()->json($pacientes, 200);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'                => 'required|string|max:255',
            'email'               => 'required|email|unique:users,email',
            'password'            => 'required|min:8',
            'tipo_sangre'         => 'nullable|string',
            'alergias'            => 'nullable|string',
            'cirugias'            => 'nullable|string',
            'padecimientos'       => 'nullable|string',
            'habitos'             => 'nullable|string',
            'contacto_emergencia' => 'nullable|string|max:10',
        ]);

        try {
            return DB::transaction(function () use ($validated) {
                $user = User::create([
                    'name'     => $validated['name'],
                    'email'    => $validated['email'],
                    'password' => Hash::make($validated['password']),
                    'role'     => 'paciente',
                ]);

                $paciente = Paciente::create([
                    'user_id'             => $user->id,
                    'tipo_sangre'         => $validated['tipo_sangre'],
                    'alergias'            => $validated['alergias'],
                    'cirugias'            => $validated['cirugias'],
                    'padecimientos'       => $validated['padecimientos'],
                    'habitos'             => $validated['habitos'],
                    'contacto_emergencia' => $validated['contacto_emergencia'],
                ]);

                return response()->json([
                    'message' => 'Paciente creado exitosamente',
                    'data'    => $paciente->load('user')
                ], 201);
            });
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al crear el paciente', 'error' => $e->getMessage()], 500);
        }
    }

    public function show(Paciente $paciente): JsonResponse
    {
        return response()->json($paciente->load('user'), 200);
    }

    public function update(Request $request, Paciente $paciente): JsonResponse
    {
        $validated = $request->validate([
            'name'                => 'sometimes|required|string|max:255',
            'email'               => 'sometimes|required|email|unique:users,email,' . $paciente->user_id,
            'tipo_sangre'         => 'nullable|string',
            'alergias'            => 'nullable|string',
            'cirugias'            => 'nullable|string',
            'padecimientos'       => 'nullable|string',
            'habitos'             => 'nullable|string',
            'contacto_emergencia' => 'nullable|string|max:10',
        ]);

        try {
            DB::transaction(function () use ($validated, $paciente) {
                // Actualizar datos del usuario si se proporcionan
                if (isset($validated['name']) || isset($validated['email'])) {
                    $paciente->user->update(array_filter([
                        'name'  => $validated['name'] ?? null,
                        'email' => $validated['email'] ?? null,
                    ]));
                }

                // Actualizar datos médicos del paciente
                $paciente->update($validated);
            });

            return response()->json([
                'message' => 'Paciente actualizado exitosamente',
                'data'    => $paciente->load('user')
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al actualizar el paciente'], 500);
        }
    }

    public function destroy(Paciente $paciente): JsonResponse
    {
        try {
            DB::transaction(function () use ($paciente) {
                $user = $paciente->user;
                $paciente->delete();
                if ($user) {
                    $user->delete();
                }
            });

            return response()->json(['message' => 'Paciente eliminado correctamente'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al eliminar el paciente'], 500);
        }
    }
}