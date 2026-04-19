<?php

namespace App\Http\Controllers;

use App\Models\Paciente;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use App\Models\Expediente;
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
            // Campos para el expediente inicial
            'f_nacimiento' => 'required|date',
            'genero' => 'required|in:masculino,femenino,otro',
            'tipo_sangre' => 'nullable|string',
            'alergias' => 'nullable|string',
            'padecimientos_cronicos' => 'nullable|string',
            'habitos_salud' => 'nullable|string',
        ]);

        return DB::transaction(function () use ($request, $validated) {
            $rutaFoto = $request->hasFile('foto') 
                ? $request->file('foto')->store('users', 'public') 
                : null;

            // 1. Crear el Usuario
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => 'paciente',
                'foto' => $rutaFoto,
                'f_nacimiento' => $validated['f_nacimiento'],
            ]);

            // 2. Crear el Perfil de Paciente (Cuenta principal)
            $paciente = Paciente::create([
                'user_id' => $user->id,
            ]);

            // 3. Crear el Expediente inicial (El del titular)
            Expediente::create([
                'user_id' => $user->id,
                'nombre_completo' => $user->name,
                'fecha_nacimiento' => $user->f_nacimiento,
                'genero' => $validated['genero'],
                'parentesco' => 'Propio',
                'tipo_sangre' => $validated['tipo_sangre'],
                'alergias' => $validated['alergias'],
                'padecimientos_cronicos' => $validated['padecimientos_cronicos'],
                'habitos_salud' => $validated['habitos_salud'],
            ]);

            return redirect()->route('pacientes.index')->with('success', 'Paciente y expediente creados correctamente');
        });
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
            'password' => 'nullable|min:8',
            'foto' => 'nullable|image|max:5120',
            
            // Campos que ahora pertenecen al Expediente
            'f_nacimiento' => 'required|date',
            'genero' => 'required|in:masculino,femenino,otro',
            'tipo_sangre' => 'nullable|string',
            'alergias' => 'nullable|string',
            'padecimientos_cronicos' => 'nullable|string',
            'habitos_salud' => 'nullable|string',
        ]);

        return DB::transaction(function () use ($request, $validated, $paciente) {
            $user = $paciente->user;

            // 1. Actualizar datos del Usuario
            $user->name = $validated['name'];
            $user->email = $validated['email'];
            $user->f_nacimiento = $validated['f_nacimiento'];

            if (!empty($validated['password'])) {
                $user->password = Hash::make($validated['password']);
            }

            if ($request->hasFile('foto')) {
                if ($user->foto) {
                    Storage::disk('public')->delete($user->foto);
                }
                $user->foto = $request->file('foto')->store('users', 'public');
            }

            $user->save();

            // 2. Actualizar el Expediente Principal (el del titular)
            // Buscamos el expediente que pertenece a este paciente y es el "Propio"
            $expedientePrincipal = $user->expedientes()
                ->where('parentesco', 'Expediente Propio')
                ->first();

            if ($expedientePrincipal) {
                $expedientePrincipal->update([
                    'nombre_completo' => $user->name,
                    'fecha_nacimiento' => $user->f_nacimiento,
                    'genero' => $validated['genero'],
                    'tipo_sangre' => $validated['tipo_sangre'],
                    'alergias' => $validated['alergias'],
                    'padecimientos_cronicos' => $validated['padecimientos_cronicos'],
                    'habitos_salud' => $validated['habitos_salud'],
                ]);
            }

            return redirect()->route('pacientes.index')
                ->with('success', 'Perfil y expediente actualizados correctamente');
        });
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