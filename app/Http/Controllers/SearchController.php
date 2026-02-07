<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Doctor;
use App\Models\Farmacia;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->input('search');

        $doctores = Doctor::with(['user', 'especialidades'])
            ->whereHas('user', function($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%");
            })
            ->orWhereHas('especialidades', function($q) use ($query) {
                $q->where('nombre', 'LIKE', "%{$query}%");
            })
            ->get();

        $farmacias = Farmacia::with('user')
            ->where('nom_farmacia', 'LIKE', "%{$query}%")
            ->get();

        return view('resultados', compact('doctores', 'farmacias', 'query'));
    }
}
