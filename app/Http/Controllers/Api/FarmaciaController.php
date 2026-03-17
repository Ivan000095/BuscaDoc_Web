<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Farmacia;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class FarmaciaController extends Controller
{
    /**
     * Formatea una farmacia para la respuesta de la API
     * Basado EXCLUSIVAMENTE en las columnas reales de tu BD:
     * id, user_id, nom_farmacia, descripcion, horario_entrada, horario_salida
     */
    private function formatFarmacia(Farmacia $f): array
    {
        // Combinamos entrada y salida para dar un horario legible
        $horarioCompleto = null;
        if ($f->horario_entrada && $f->horario_salida) {
            $horarioCompleto = $f->horario_entrada . ' - ' . $f->horario_salida;
        }

        return [
            'id' => $f->id,
            'nom_farmacia' => $f->nom_farmacia,
            // Eliminamos rfc y telefono porque NO existen en tu tabla 'farmacias'
            // Si los necesitas, deberás agregarlos a la BD con una migración.
            'descripcion' => $f->descripcion,
            
            // Campos de horario corregidos (sin la 'd' extra y usando los nombres reales)
            'horario_entrada' => $f->horario_entrada,
            'horario_salida' => $f->horario_salida,
            'horario_completo' => $horarioCompleto,
            
            'created_at' => $f->created_at?->toISOString(),
            
            'dueño' => [
                'id' => $f->user?->id,
                'nombre' => $f->user?->name,
                'email' => $f->user?->email,
                'fecha_nacimiento' => $f->user?->f_nacimiento,
                'foto' => $f->user?->foto ? asset('storage/' . $f->user->foto) : null,
                'ubicacion' => [
                    'lat' => $f->user?->latitud,
                    'lng' => $f->user?->longitud,
                ],
            ],
        ];
    }

    public function index(Request $request): JsonResponse
    {
        $perPage = min(max($request->integer('per_page', 15), 1), 100);

        $farmacias = Farmacia::with('user:id,name,email,foto,latitud,longitud,f_nacimiento')
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Lista de farmacias obtenida',
            'data' => $farmacias->getCollection()->map(fn($f) => $this->formatFarmacia($f)),
            'pagination' => [
                'current_page' => $farmacias->currentPage(),
                'last_page' => $farmacias->lastPage(),
                'total' => $farmacias->total(),
            ]
        ], 200);
    }
    public function show(Farmacia $farmacia): JsonResponse
    {
        $farmacia->load('user:id,name,email,foto,latitud,longitud,f_nacimiento');

        return response()->json([
            'success' => true,
            'message' => 'Farmacia encontrada',
            'data' => $this->formatFarmacia($farmacia)
        ], 200);
    }
}