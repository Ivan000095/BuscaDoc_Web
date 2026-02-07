<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Models\Especialidad;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%");
        }

        $users = $query->orderBy('id', 'desc')->paginate(10);

        return view('users.index', compact('users'));
    }

    public function create()
    {
        return view('users.form');
    }
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'role' => 'required|in:admin,paciente,doctor',
            'image' => 'nullable|image|max:5120',
            'f_nacimiento' => 'nullable|date',
        ]);

        $rutaFoto = null;
        if ($request->hasFile('image')) {
            $rutaFoto = $request->file('image')->store('users', 'public');
        }

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'estado' => $request->has('estado') ? true : false,
            'foto' => $rutaFoto,
            'f_nacimiento' => $request->f_nacimiento,
            'latitud' => $request->latitud,
            'longitud' => $request->longitud,
        ]);

        return redirect()->route('users.index')
            ->with('success', 'Usuario creado correctamente.');
    }
    public function edit($id)
    {
        $user = User::with(['doctor', 'patient', 'farmacia'])->findOrFail($id);

        // Verificamos que el usuario solo edite su propio perfil (Seguridad básica)
        if (auth()->id() !== $user->id && auth()->user()->role !== 'admin') {
            abort(403);
        }

        $especialidades = Especialidad::all(); // Para el doctor
        return view('users.edit', compact('user', 'especialidades'));
    }

    // Guardar los cambios
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // 1. Validaciones ajustadas a lo que realmente envías en la vista
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', \Illuminate\Validation\Rule::unique('users')->ignore($user->id)],
            'foto' => 'nullable|image|max:2048',
            'f_nacimiento' => 'nullable|date',
        ]);

        DB::transaction(function () use ($request, $user) {

            if ($request->hasFile('foto')) {
                if ($user->foto) {
                    Storage::disk('public')->delete($user->foto);
                }
                $user->foto = $request->file('foto')->store('perfiles', 'public');
            }

            // AHORA SÍ: Actualizamos los campos principales incluyendo name y email
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'f_nacimiento' => $request->f_nacimiento,
            ]);

            if ($user->role === 'doctor') {
                $user->doctor()->updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'cedula' => $request->cedula,
                        'descripcion' => $request->descripcion,
                        'costo' => $request->costo,
                        'horario_entrada' => $request->horario_entrada,
                        'horario_salida' => $request->horario_salida,
                    ]
                );
            } elseif ($user->role === 'paciente') {
                $user->patient()->updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'tipo_sangre' => $request->tipo_sangre,
                        'alergias' => $request->alergias,
                        'padecimientos' => $request->padecimientos,
                        'habitos' => $request->habitos, // Añadido
                        'contacto_emergencia' => $request->contacto_emergencia,
                    ]
                );
            } elseif ($user->role === 'farmacia') {
                $user->farmacia()->updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'nom_farmacia' => $request->nom_farmacia,
                        'rfc' => $request->rfc,
                        'descripcion' => $request->descripcion, // Añadido
                        'horario_entrada' => $request->horario_entrada, // Corregido
                        'horario_salida' => $request->horario_salida,   // Corregido
                    ]
                );
            }
        });

        return redirect()->route('users.show', $user->id)
            ->with('success', '¡Perfil actualizado correctamente!');
    }

    public function show(User $user)
    {
        $user->role == 'doctor' ? $user->load('doctor') : ($user->role == 'farmacia' ? $user->load('farmacia') : $user->load('patient'));
        return view('users.show', compact('user'));

    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        if (auth()->id() == $user->id) {
            return redirect()->route('users.index')
                ->with('error', 'No puedes eliminar tu propia cuenta mientras estás conectado.');
        }
        if ($user->foto) {
            Storage::disk('public')->delete($user->foto);
        }
        // $user->doctor()->delete(); 

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'Usuario eliminado correctamente.');
    }
}