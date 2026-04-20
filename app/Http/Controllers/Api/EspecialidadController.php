<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Especialidad;
use Illuminate\Http\JsonResponse;

class EspecialidadController extends Controller
{
    /**
     * Devuelve una lista simple de especialidades (para selects/buscadores)
     */
    public function index(Request $request)
    {
        try {
            // Usamos map en lugar de transform para crear una nueva colección limpia
            $especialidades = Especialidad::whereHas('doctors')->get()->map(function ($esp) {
                return [
<<<<<<< HEAD
                    "id" => $especialidad->id,
                    "name" => $especialidad->nombre,
                    
=======
                    "id" => $esp->id,
                    "name" => $esp->nombre, // Mapeamos 'nombre' a 'name' para que tu modelo de Flutter (fromJson) lo entienda
                    "descripcion" => $esp->descripcion ?? ''
>>>>>>> main
                ];
            });

            return response()->json([
                "success" => true,
                "data" => $especialidades,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                "success" => false,
                "message" => "Error al obtener especialidades",
                "error" => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Devuelve las especialidades con sus doctores anidados (Para la matriz de tarjetas en Flutter)
     */
    public function apiDashboard()
    {
        try {
            $especialidades = Especialidad::with(['doctors.user'])
                ->has('doctors')
                ->get()
                ->map(function ($esp) {
                    return [
                        'id' => $esp->id,
                        // Enviamos 'name' para ser consistentes con index() y tu modelo Flutter
                        'name' => $esp->nombre, 
                        'doctors' => $esp->doctors->map(function ($doctor) {
                            return [
                                'id' => $doctor->id,
                                'costo' => $doctor->costo,
                                'user' => [
                                    'id' => $doctor->user->id ?? 0,
                                    'name' => $doctor->user->name ?? 'Anónimo',
                                    'foto' => $doctor->user->foto, // Esto puede ser nulo, Flutter lo maneja
                                ]
                            ];
                        })
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $especialidades
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => "Error al obtener datos del dashboard",
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}