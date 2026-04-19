<?php

namespace App\Http\Controllers;

use App\Models\Expediente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExpedienteController extends Controller
{
    /**
     * Muestra la ficha médica detallada.
     * 
     */
        public function create()
    {
        // Solo retornamos la vista donde estará el formulario que hicimos antes
        return view('expedientes.create');
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        // 1. Validación de los datos
        $request->validate([
            'nombre_completo'    => 'required|string|max:80',
            'fecha_nacimiento'   => 'required|date|before_or_equal:today',
            'genero'             => 'required|in:masculino,femenino',
            'parentesco'         => 'required|string|max:30',
            'tipo_sangre'        => 'nullable|string|max:5',
            'alergias'           => 'nullable',
            'padecimientos'      => 'nullable',
            'habitos'            => 'nullable',
        ]);

        // 2. Creación del registro
        Expediente::create([
            'user_id'                => $user->id,
            'nombre_completo'        => $request->nombre_completo,
            'fecha_nacimiento'       => $request->fecha_nacimiento,
            'genero'                 => $request->genero,
            'parentesco'             => $request->parentesco,
            'tipo_sangre'            => $request->tipo_sangre,
            // Procesamos arrays a string si usas checkboxes o select múltiple
            'alergias'               => is_array($request->alergias) 
                                        ? implode(', ', $request->alergias) 
                                        : $request->alergias,
            'padecimientos_cronicos' => is_array($request->padecimientos) 
                                        ? implode(', ', $request->padecimientos) 
                                        : $request->padecimientos,
            'habitos_salud'          => is_array($request->habitos) 
                                        ? implode(', ', $request->habitos) 
                                        : $request->habitos,
        ]);

        // 3. Redirección con mensaje de éxito
        return redirect()->route('expedientes.index')->with('success', 'Expediente creado correctamente.');
    }

    public function index()
    {
        $user = Auth::user();
        
        // Obtenemos todos los expedientes ligados al usuario logueado
        $expedientes = Expediente::where('user_id', $user->id)
                        ->orderBy('nombre_completo', 'asc')
                        ->get();

        return view('expedientes.index', compact('expedientes'));
    }


    public function show($id)
    {
        // 1. Buscamos el expediente o lanzamos error 404 si no existe
        $expediente = Expediente::findOrFail($id);

        // 2. Seguridad: Verificar que el expediente pertenezca al usuario logueado
        // (O que el usuario sea el doctor que tiene una cita con este expediente)
        $user = Auth::user();
        
        $expediente = Expediente::with(['notas' => function($query) {
            $query->orderBy('created_at', 'desc');
        }])->findOrFail($id);


        // 3. Retornamos la vista que creamos anteriormente
        return view('expedientes.show', compact('expediente'));
    }

    public function edit($id)
    {
        $expediente = Expediente::findOrFail($id);
        
        // Seguridad: Solo el dueño puede editar
        if ($expediente->user_id !== Auth::id()) {
            return back()->with('error', 'No tienes permiso para editar este expediente.');
        }

        return view('expedientes.edit', compact('expediente'));
    }

    public function update(Request $request, $id)
    {
        $expediente = Expediente::findOrFail($id);

        if ($expediente->user_id !== Auth::id()) {
            return back()->with('error', 'Acción no autorizada.');
        }

        $request->validate([
            'nombre_completo'    => 'required|string|max:80',
            'fecha_nacimiento'   => 'required|date|before_or_equal:today',
            'genero'             => 'required|in:masculino,femenino',
            
            'tipo_sangre'        => 'nullable|string|max:5',
            'alergias'           => 'nullable',
            'padecimientos'      => 'nullable',
            'habitos'            => 'nullable',
        ]);

        $expediente->update([
            'nombre_completo'        => $request->nombre_completo,
            'fecha_nacimiento'       => $request->fecha_nacimiento,
            'genero'                 => $request->genero,
            'parentesco'             => $request->parentesco,
            'tipo_sangre'            => $request->tipo_sangre,
            'alergias'               => is_array($request->alergias) ? implode(', ', $request->alergias) : $request->alergias,
            'padecimientos_cronicos' => is_array($request->padecimientos) ? implode(', ', $request->padecimientos) : $request->padecimientos,
            'habitos_salud'          => is_array($request->habitos) ? implode(', ', $request->habitos) : $request->habitos,
        ]);

        return redirect()->route('expedientes.show', $id)->with('success', 'Expediente actualizado correctamente.');
    }




}