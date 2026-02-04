<?php

namespace App\Http\Controllers;

use App\Models\Paciente;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class PacienteController extends Controller
{
    public function index(): View
    {
        $pacientes = Paciente::with('user')->get();
        return view('pacientes.index', compact('pacientes'));
    }

    public function create(): View
    {
        return view('pacientes.form');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'tipo_sangre' => 'nullable|string',
            'alergias' => 'nullable|string',
            'cirugias' => 'nullable|string',
            'padecimientos' => 'nullable|string',
            'habitos' => 'nullable|string',
            'contacto_emergencia' => 'nullable|string|max:10',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'role' => 'paciente',
        ]);

        Paciente::create([
            'user_id' => $user->id,
            'tipo_sangre' => $validated['tipo_sangre'],
            'alergias' => $validated['alergias'],
            'cirugias' => $validated['cirugias'],
            'padecimientos' => $validated['padecimientos'],
            'habitos' => $validated['habitos'],
            'contacto_emergencia' => $validated['contacto_emergencia'],
        ]);

        return redirect()->route('pacientes.index')->with('success', 'Paciente creado correctamente');
    }

    public function edit(Paciente $paciente): View
    {
        return view('pacientes.form', compact('paciente'));
    }

    public function update(Request $request, Paciente $paciente): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $paciente->user_id,
            'tipo_sangre' => 'nullable|string',
            'alergias' => 'nullable|string',
            'cirugias' => 'nullable|string',
            'padecimientos' => 'nullable|string',
            'habitos' => 'nullable|string',
            'contacto_emergencia' => 'nullable|string|max:10',
        ]);

        // Actualizar datos de usuario
        $paciente->user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        // Actualizar datos médicos
        $paciente->update($validated);

        return redirect()->route('pacientes.index')->with('success', 'Paciente actualizado correctamente');
    }

    public function destroy(Paciente $paciente): RedirectResponse
    {
        // Al eliminar al usuario, se debería eliminar el paciente si tienes ON DELETE CASCADE
        // o puedes eliminar ambos manualmente:
        $user = $paciente->user;
        $paciente->delete();
        $user->delete();

        return redirect()->route('pacientes.index')->with('success', 'Paciente eliminado correctamente');
    }
}