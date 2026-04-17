<?php

namespace App\Http\Controllers;

use App\Models\Paciente;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Barryvdh\DomPDF\Facade\Pdf;

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
            'foto' => 'nullable|image|max:5120',
            'tipo_sangre' => 'nullable|string',
            'alergias' => 'nullable|string',
            'cirugias' => 'nullable|string',
            'padecimientos' => 'nullable|string',
            'habitos' => 'nullable|string',
            'contacto_emergencia' => 'nullable|string|max:10',
        ]);

        $rutaFoto = null;
        if ($request->hasFile('foto')) {
            $rutaFoto = $request->file('foto')->store('users', 'public');
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'paciente',
            'foto' => $rutaFoto,
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
            'password' => 'nullable|min:8', // Contraseña opcional al editar
            'foto' => 'nullable|image|max:5120',
            'tipo_sangre' => 'nullable|string',
            'alergias' => 'nullable|string',
            'cirugias' => 'nullable|string',
            'padecimientos' => 'nullable|string',
            'habitos' => 'nullable|string',
            'contacto_emergencia' => 'nullable|string|max:10',
        ]);

        $user = $paciente->user;

        $user->name = $validated['name'];
        $user->email = $validated['email'];

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        if ($request->hasFile('foto')) {
            // Borrar foto anterior si existe
            if ($user->foto) {
                Storage::disk('public')->delete($user->foto);
            }
            $user->foto = $request->file('foto')->store('users', 'public');
        }

        $user->save();

        $pacienteData = collect($validated)->except(['name', 'email', 'password', 'foto'])->toArray();
        $paciente->update($pacienteData);

        return redirect()->route('pacientes.index')->with('success', 'Paciente actualizado correctamente');
    }

    public function destroy(Paciente $paciente): RedirectResponse
    {
        $user = $paciente->user;
        $paciente->delete();
        $user->delete();
        return redirect()->route('pacientes.index')->with('success', 'Paciente eliminado correctamente');
    }

    public function generarReporte(Request $request)
    {
        $query = Paciente::with('user');

        if ($request->filled('fecha_inicio')) {
            $query->whereDate('created_at', '>=', $request->fecha_inicio);
        }
        if ($request->filled('fecha_fin')) {
            $query->whereDate('created_at', '<=', $request->fecha_fin);
        }

        if ($request->filled('tipo_sangre') && $request->tipo_sangre !== 'todos') {
            $query->where('tipo_sangre', $request->tipo_sangre);
        }

        switch ($request->orden) {
            case 'antiguos':
                $query->orderBy('created_at', 'asc');
                break;
            case 'recientes':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        $pacientes = $query->get();

        $pdf = Pdf::loadView('pacientes.pdf', compact('pacientes', 'request'))
                  ->setPaper('a4', 'landscape');

        return $pdf->download('Reporte_Pacientes_BuscaDoc_' . now()->format('Ymd') . '.pdf');
    }
}