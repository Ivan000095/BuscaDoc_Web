<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Especialidad;
use Illuminate\Http\JsonResponse;

class EspecialidadController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $query = Especialidad::whereHas('doctors')->get();
            $especialidades = $query;

            $especialidades->transform(function ($especialidad) {
                return [
                    "id" => $especialidad->id,
                    "name" => $especialidad->nombre,
                    "descripcion" => $especialidad->descripcion
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

    public function apiDashboard()
    {
        try {
            $especialidades = Especialidad::with(['doctors.user'])
                ->has('doctors')
                ->get();

            $data = $especialidades->map(function ($especialidad) {
                return [
                    'id' => $especialidad->id,
                    'nombre' => $especialidad->nombre,
                    'doctors' => $especialidad->doctors->map(function ($doctor) {
                        return [
                            'id' => $doctor->id,
                            'costo' => $doctor->costo,
                            'user' => [
                                'id' => $doctor->user->id,
                                'name' => $doctor->user->name,
                                'foto' => $doctor->user->foto,
                            ]
                        ];
                    })
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $data
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => "Error al obtener datos del dashboard",
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
