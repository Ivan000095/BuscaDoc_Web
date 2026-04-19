<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Farmacia;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class FarmaciaController extends Controller
{
    private function formatFarmacia(Farmacia $f): array
    {
        $horarioCompleto = null;
        if ($f->horario_entrada && $f->horario_salida) {
            $horarioCompleto = $f->horario_entrada . ' - ' . $f->horario_salida;
        }

        return [
            'id' => $f->id,
            'user_id' => $f->user_id,
            'nom_farmacia' => $f->nom_farmacia,
            'descripcion' => $f->descripcion,
            'horario_entrada' => $f->horario_entrada,
            'horario_salida' => $f->horario_salida,
            'horario_completo' => $horarioCompleto,
            'telefono' => $f->telefono,
            'rfc' => $f->rfc,
            'promedio' => round($f->reviews->avg('calificacion') ?? 0, 1),
            'total_resenas' => $f->reviews->where('tipo', 'resena')->count(),
            'created_at' => $f->created_at?->toISOString(),
            'updated_at' => $f->updated_at?->toISOString(),
            
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
            
            // Alias para compatibilidad con Flutter
            'responsableNombre' => $f->user?->name,
            'idUsuario' => $f->user_id,
        ];
    }

    public function index(Request $request): JsonResponse
    {
        $perPage = min(max($request->integer('per_page', 15), 1), 100);

        $farmacias = Farmacia::with([
            'user:id,name,email,foto,latitud,longitud,f_nacimiento',
            'reviews'
        ])->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Lista de farmacias obtenida',
            'data' => $farmacias->getCollection()->map(fn($f) => $this->formatFarmacia($f)),
            'pagination' => [
                'current_page' => $farmacias->currentPage(),
                'last_page' => $farmacias->lastPage(),
                'per_page' => $farmacias->perPage(),
                'total' => $farmacias->total(),
            ]
        ], 200);
    }

    public function show(Farmacia $farmacia): JsonResponse
    {
        $farmacia->load([
            'user:id,name,email,foto,latitud,longitud,f_nacimiento',
            'reviews'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Farmacia encontrada',
            'data' => $this->formatFarmacia($farmacia)
        ], 200);
    }
}