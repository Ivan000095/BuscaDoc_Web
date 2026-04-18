<?php

namespace App\Http\Controllers;

use App\Models\Expediente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExpedienteController extends Controller
{
    /**
     * Muestra la ficha médica detallada.
     */
    public function show($id)
    {
        // 1. Buscamos el expediente o lanzamos error 404 si no existe
        $expediente = Expediente::findOrFail($id);

        // 2. Seguridad: Verificar que el expediente pertenezca al usuario logueado
        // (O que el usuario sea el doctor que tiene una cita con este expediente)
        $user = Auth::user();
        


        // 3. Retornamos la vista que creamos anteriormente
        return view('expedientes.show', compact('expediente'));
    }
}