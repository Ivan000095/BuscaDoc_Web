<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\User;
use App\Models\Cita;


class HomeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function getHomeData(Request $request): JsonResponse
{
    try {
        $user = $request->user();

        $data = [
            'role' => $user->role,
            'proxima_cita' => null,
            'ultima_review' => null,
            'ultima_question' => null,
            'rutas' => []
        ];

        if ($user->role === 'paciente' && $user->patient) {
            $data['proxima_cita'] = Cita::with(['doctor.user'])
                ->where('paciente_id', $user->patient->id)
                ->where('fecha_hora', '>=', now())
                ->where('estado', '!=', 'cancelada')
                ->orderBy('fecha_hora', 'asc')
                ->first();

            $data['rutas'] = User::whereNotNull('latitud')
                ->whereNotNull('longitud')
                ->whereIn('role', ['doctor', 'farmacia'])
                ->select('id', 'name', 'role', 'latitud', 'longitud', 'foto')
                ->get();
        }

        if ($user->role === 'doctor' && $user->doctor) {
            $data['proxima_cita'] = $user->doctor->citas()
                ->with(['paciente.user'])
                ->where('fecha_hora', '>=', now())
                ->whereIn('estado', ['pendiente', 'confirmada'])
                ->orderBy('fecha_hora', 'asc')
                ->first();

            $data['ultima_review'] = $user->doctor->reviews()
                ->with('autor:id,name,foto') 
                ->latest()
                ->first();

            $data['ultima_question'] = $user->doctor->questions()
                ->with('autor:id,name,foto')
                ->latest()
                ->first();
        }

        return response()->json([
            'success' => true,
            'message' => 'Datos del dashboard cargados correctamente.',
            'data' => $data
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error interno al cargar el dashboard.',
            'error' => $e->getMessage()
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
