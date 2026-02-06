<?php

namespace App\Http\Controllers;

use App\Models\Farmacia;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class FarmaciaController extends Controller
{
    // =============================
    // 👀 1. Público: Catálogo de farmacias (pacientes/visitantes)
    // =============================

    public function index()
    {
        $farmacias = Farmacia::with('user')->get();
        return view('farmacias.catalogo', compact('farmacias'));
    }

    public function show($id)
    {
        $farmacia = Farmacia::with('user')->findOrFail($id);
        return view('farmacias.detalle', compact('farmacia'));
    }

    // =============================
    // 👤 2. Dueño: Solo su farmacia
    // =============================

    public function miFarmacia()
    {
        $farmacia = Auth::user()->farmacia;
        if (!$farmacia) {
            return redirect()->route('perfil')->with('error', 'No tienes una farmacia registrada.');
        }
        return view('farmacias.mi.index', compact('farmacia'));
    }

    public function editarMiFarmacia()
    {
        $farmacia = Auth::user()->farmacia;
        return view('farmacias.mi.edit', compact('farmacia'));
    }

    public function actualizarMiFarmacia(Request $request)
    {
        $farmacia = Auth::user()->farmacia;
        $validated = $request->validate([
            'nom_farmacia' => 'required|string|max:255',
            'rfc' => 'nullable|string|max:255|unique:farmacias,rfc,' . $farmacia->id,
            'telefono' => 'nullable|string|max:255',
            'descripcion' => 'nullable|string',
            'horario' => 'nullable|string|max:255',
            'dias_op' => 'nullable|string|max:255',
        ]);
        $farmacia->update($validated);
        return redirect()->route('farmacias.mi')->with('success', 'Actualizada.');
    }

    // =============================
    // 👨‍💼 3. Administrador: CRUD completo
    // =============================

    public function adminIndex()
    {
        $this->authorizeAdmin();
        $farmacias = Farmacia::with('user')->get();
        return view('farmacias.admin.index', compact('farmacias'));
    }

    public function adminCreate()
    {
        $this->authorizeAdmin();
        $usuarios = User::where('role', 'farmacia')->orWhere('role', 'admin')->get();
        return view('farmacias.admin.create', compact('usuarios'));
    }

    public function adminStore(Request $request)
    {
        $this->authorizeAdmin();

        // Validación condicional
        $rules = [
            'nom_farmacia' => 'required|string|max:255',
            'rfc' => 'nullable|string|max:255|unique:farmacias,rfc',
            'telefono' => 'nullable|string|max:255',
            'descripcion' => 'nullable|string',
            'horario' => 'nullable|string|max:255',
            'dias_op' => 'nullable|string|max:255',
        ];

        if ($request->crear_nuevo) {
            // Validar datos del nuevo usuario
            $rules = array_merge($rules, [
                'name' => 'required|string|max:100',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|min:8',
            ]);
        } else {
            // Validar que se seleccione un usuario existente
            $rules['user_id'] = 'required|exists:users,id';
        }

        $validated = $request->validate($rules);

        DB::transaction(function () use ($request, $validated) {
            if ($request->crear_nuevo) {
                // Crear nuevo usuario
                $user = User::create([
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'password' => Hash::make($validated['password']),
                    'role' => 'farmacia',
                    'estado' => true,
                ]);
                $userId = $user->id;
            } else {
                // Usar usuario existente
                $userId = $validated['user_id'];
            }

            // Crear farmacia
            Farmacia::create([
                'user_id' => $userId,
                'nom_farmacia' => $validated['nom_farmacia'],
                'rfc' => $validated['rfc'] ?? null,
                'telefono' => $validated['telefono'] ?? null,
                'descripcion' => $validated['descripcion'] ?? null,
                'horario' => $validated['horario'] ?? null,
                'dias_op' => $validated['dias_op'] ?? null,
            ]);
        });

        return redirect()->route('admin.farmacias.index')->with('success', 'Farmacia registrada exitosamente.');
    }

    public function adminEdit($id)
    {
        $this->authorizeAdmin();
        $farmacia = Farmacia::findOrFail($id);
        $usuarios = User::all(); // o filtrar por rol si prefieres
        return view('farmacias.admin.edit', compact('farmacia', 'usuarios'));
    }

    public function adminUpdate(Request $request, $id)
    {
        $this->authorizeAdmin();
        $farmacia = Farmacia::findOrFail($id);

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'nom_farmacia' => 'required|string|max:255',
            'rfc' => 'nullable|string|max:255|unique:farmacias,rfc,' . $farmacia->id,
            'telefono' => 'nullable|string|max:255',
            'descripcion' => 'nullable|string',
            'horario' => 'nullable|string|max:255',
            'dias_op' => 'nullable|string|max:255',
        ]);

        $farmacia->update($validated);
        return redirect()->route('admin.farmacias.index')->with('success', 'Farmacia actualizada.');
    }

    public function adminDestroy($id)
    {
        $this->authorizeAdmin();
        Farmacia::destroy($id);
        return redirect()->route('admin.farmacias.index')->with('success', 'Eliminada.');
    }

    // =============================
    // 🔒 Helper: verificar admin
    // =============================

    private function authorizeAdmin()
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            abort(403, 'Acceso denegado.');
        }
    }
}