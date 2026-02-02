<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

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

        // Paginamos de 10 en 10
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
        $user = User::findOrFail($id);
        return view('users.form', compact('user'));
    }

    public function show(User $user)
    {
        if ($user->role === 'doctor') {
            $user->load('doctor');
        }
        return view('users.show', compact('user'));

    }
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|min:8', // Opcional al editar
            'image' => 'nullable|image|max:5120',
        ]);

        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role ?? $user->role,
            'estado' => $request->has('estado') ? true : false,
            'f_nacimiento' => $request->f_nacimiento,
            'latitud' => $request->latitud,
            'longitud' => $request->longitud,
        ];

        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }

        if ($request->hasFile('image')) {
            if ($user->foto) {
                Storage::disk('public')->delete($user->foto);
            }
            $userData['foto'] = $request->file('image')->store('users', 'public');
        }
        $user->update($userData);
        return redirect()->route('users.index')
            ->with('success', 'Usuario actualizado correctamente.');
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