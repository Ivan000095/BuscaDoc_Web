<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Doctor;
use App\Models\Farmacia;
use Illuminate\Database\Eloquent\Builder;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $searchTerm = $request->input('search'); 
        $type = $request->input('type');
        $especialidadId = $request->input('especialidad_id');

        $doctores = collect();
        $farmacias = collect();

        if (empty($type) || $type === 'doctor') {

            $docQuery = Doctor::with(['user', 'especialidades']);

            if (!empty($searchTerm)) {
                $docQuery->whereHas('user', function(Builder $q) use ($searchTerm) {
                    $q->where('name', 'LIKE', "%{$searchTerm}%");
                });
            }

            if (!empty($especialidadId)) {
                $docQuery->whereHas('especialidades', function(Builder $q) use ($especialidadId) {
                    $q->where('especialidads.id', $especialidadId);
                });
            }

            $doctores = $docQuery->get();
        }

        if ((empty($type) || $type === 'farmacia') && empty($especialidadId)) {
            
            $farmQuery = Farmacia::with('user');

            if (!empty($searchTerm)) {
                $farmQuery->where(function($q) use ($searchTerm) {
                    $q->where('nom_farmacia', 'LIKE', "%{$searchTerm}%")
                      ->orWhereHas('user', function(Builder $userQuery) use ($searchTerm) {
                          $userQuery->where('name', 'LIKE', "%{$searchTerm}%");
                      });
                });
            }

            $farmacias = $farmQuery->get();
        }

        return view('resultados', [
            'doctores' => $doctores,
            'farmacias' => $farmacias,
            'query' => $searchTerm,
            'tipo' => $type
        ]);
    }
}