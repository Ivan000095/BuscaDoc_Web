<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Doctor;
use App\Models\Farmacia;

class SearchController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function apiSearch(Request $request)
    {
        $searchTerm = $request->query('search');
        $type = $request->query('type'); // 'doctor' o 'farmacia'
        $especialidadId = $request->query('especialidad_id');

        // 👇 LA SOLUCIÓN: Agregamos 'disponibilidades' y 'excepciones' aquí
        $queryDocs = Doctor::with(['user', 'especialidades', 'disponibilidades', 'excepciones']);
        $queryFarms = Farmacia::with('user');

        if ($type === 'doctor') {
            if ($searchTerm) $queryDocs->whereHas('user', fn($q) => $q->where('name', 'LIKE', "%$searchTerm%"));
            if ($especialidadId) $queryDocs->whereHas('especialidades', fn($q) => $q->where('especialidads.id', $especialidadId));
            $resultados = $queryDocs->get();
        } else {
            if ($searchTerm) $queryFarms->where('nom_farmacia', 'LIKE', "%$searchTerm%");
            $resultados = $queryFarms->get();
        }

        return response()->json([
            "success" => true,
            "data" => $resultados
        ]);
    }
}
