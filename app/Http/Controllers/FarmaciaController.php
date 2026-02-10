<?php

namespace App\Http\Controllers;

use App\Models\Farmacia;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;

class FarmaciaController extends Controller
{

    //1. Público: Catálogo de farmacias (pacientes/visitantes)
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


    //2. Dueño: Solo su farmacia
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
            'horario_entrada' => 'nullable|string|max:255',
            'horario_salida' => 'nullable|string|max:255',
        ]);
        $farmacia->update($validated);
        return redirect()->route('farmacias.mi')->with('success', 'Actualizada.');
    }

    // 3. Administrador: CRUD completo
    public function adminIndex(Request $request)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Acceso no autorizado');
        }

        if ($request->ajax()) {
            return $this->dataTable($request);
        }

        return view('farmacias.admin.index');
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

        $validated = $request->validate([
            //Campos del nuevo usuario
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'f_nacimiento' => 'required|date|before:-18 years',
            'image' => 'nullable|image|max:5120',

            //Campos del la farmacia
            'nom_farmacia' => 'required|string|max:255',
            'rfc' => 'required|string|max:255|unique:farmacias,rfc',
            'telefono' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'horario_entrada' => 'required|string|max:255',
            'horario_salida' => 'required|string|max:255',
            'latitud' => 'required|numeric|between:-90,90',
            'longitud' => 'required|numeric|between:-180,180',
        ]);

        DB::transaction(function () use ($request, $validated) {
            $rutaFoto = null;
            if ($request->hasFile('image')) {
                $rutaFoto = $request->file('image')->store('users', 'public');
            }
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => 'farmacia',
                'estado' => true,
                'foto' => $rutaFoto,
                'f_nacimiento' => $validated['f_nacimiento'],
                'latitud' => $validated['latitud'],
                'longitud' => $validated['longitud'],
            ]);

            Farmacia::create([
                'user_id' => $user->id,
                'nom_farmacia' => $validated['nom_farmacia'],
                'rfc' => $validated['rfc'],
                'telefono' => $validated['telefono'],
                'descripcion' => $validated['descripcion'],
                'horario_entrada' => $validated['horario_entrada'],
                'horario_salida' => $validated['horario_salida'],
            ]);
        });

        return redirect()->route('admin.farmacias.index')
            ->with('success', 'Farmacia registrada exitosamente.');
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
        $farmacia = Farmacia::with('user')->findOrFail($id);

        $validated = $request->validate([
            // Usuario
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email,' . $farmacia->user->id,
            'f_nacimiento' => 'required|date|before:-18 years',
            'password' => 'nullable|min:8',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',

            // Farmacia
            'nom_farmacia' => 'required|string|max:255',
            'rfc' => 'nullable|string|max:13|unique:farmacias,rfc,' . $farmacia->id,
            'telefono' => 'required|string|max:55',
            'horario_entrada' => 'required|string|max:255',
            'horario_salida' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'latitud' => 'required|numeric|between:-90,90',
            'longitud' => 'required|numeric|between:-180,180',
        ]);

        DB::transaction(function () use ($request, $validated, $farmacia) {
            // Actualizar usuario
            $user = $farmacia->user;
            $user->name = $validated['name'];
            $user->email = $validated['email'];
            $user->f_nacimiento = $validated['f_nacimiento'];
            $user->latitud = $validated['latitud'];
            $user->longitud = $validated['longitud'];

            // Cambiar contraseña solo si se envía
            if (!empty($validated['password'])) {
                $user->password = Hash::make($validated['password']);
            }

            if ($request->hasFile('image')) {
                if ($user->foto) {
                    \Storage::disk('public')->delete($user->foto);
                }
                $user->foto = $request->file('image')->store('users', 'public');
            }

            $user->save();

            // Actualizar farmacia
            $farmacia->update([
                'nom_farmacia' => $validated['nom_farmacia'],
                'rfc' => $validated['rfc'] ?? null,
                'telefono' => $validated['telefono'],
                'horario_entrada' => $validated['horario_entrada'],
                'horario_salida' => $validated['horario_salida'],
                'descripcion' => $validated['descripcion'],
            ]);
        });

        return redirect()->route('admin.farmacias.index')
            ->with('success', 'Farmacia actualizada exitosamente.');
    }

    public function adminDestroy($id)
    {
        $this->authorizeAdmin();
        Farmacia::destroy($id);
        return redirect()->route('admin.farmacias.index')->with('success', 'Eliminada.');
    }

    public function dataTable(Request $request)
    {
        // Cargamos la relación con 'user' (la tabla users tiene el nombre y foto)
        $query = \App\Models\Farmacia::with('user');
        
        // --- LÓGICA DE BÚSQUEDA ---
        $search = $request->input("search.value");
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($subQ) use ($search) {
                    $subQ->where('name', 'like', "%{$search}%");
                })
                ->orWhere('nom_farmacia', 'like', "%{$search}%")
                ->orWhere('rfc', 'like', "%{$search}%")
                ->orWhere('descripcion', 'like', "%{$search}%");
            });
        }

        // --- PAGINACIÓN ---
        $totalRecords = \App\Models\Farmacia::count();
        $recordsFiltered = $query->count();

        $start = $request->input("start", 0);
        $length = $request->input("length", 10);

        $farmacias = $query->skip($start)->take($length)->get();

        // --- MAPEO DE DATOS ---
        $data = $farmacias->map(function ($farmacia) {
            
            // 1. FOTO (Desde la relación user)
            $imageHtml = "";
            $fotoPath = $farmacia->user?->foto;
            
            if ($fotoPath && \Illuminate\Support\Facades\Storage::disk("public")->exists($fotoPath)) {
                $url = asset("storage/" . $fotoPath);
                $imageHtml = "<img src='{$url}' class='rounded-circle shadow-sm' style='width: 40px; height: 40px; object-fit: cover;'>";
            } else {
                // Avatar por defecto si no hay foto
                $initials = substr($farmacia->user?->name ?? 'F', 0, 1);
                $imageHtml = "<div class='bg-secondary-subtle text-navy fw-bold rounded-circle d-flex align-items-center justify-content-center' style='width: 40px; height: 40px;'>{$initials}</div>";
            }

            // 2. FECHA DE NACIMIENTO (Desde user)
            $fechaNac = '—';
            if ($farmacia->user?->f_nacimiento) {
                // Usamos Carbon para formatear bonito
                $fechaNac = \Carbon\Carbon::parse($farmacia->user->f_nacimiento)->translatedFormat('d M Y');
            }

            // 3. HORARIO (Concatenamos entrada y salida que SÍ existen en tu BD)
            $entrada = $farmacia->horario_entrada ? \Carbon\Carbon::parse($farmacia->horario_entrada)->format('H:i') : '??';
            $salida = $farmacia->horario_salida ? \Carbon\Carbon::parse($farmacia->horario_salida)->format('H:i') : '??';
            $horarioTexto = "$entrada - $salida";

            return [
                "id" => $farmacia->id,
                "nombre_dueño" => $farmacia->user?->name ?? 'Sin Asignar',
                "nom_farmacia" => $farmacia->nom_farmacia,
                "rfc" => $farmacia->rfc ?? '—',
                "telefono" => $farmacia->telefono ?? '—',
                "horario" => $horarioTexto, // Variable creada arriba
                // ELIMINÉ 'dias_op' PORQUE NO EXISTE EN TU BD
                "fecha_nacimiento" => $fechaNac,
                "foto" => $imageHtml,
                "acciones" => '
                    <div class="d-flex justify-content-end gap-2">
                        <button class="btn btn-outline-navy btn-sm rounded-pill" onclick="execute(\'' . route('admin.farmacias.edit', $farmacia->id) . '\')">
                            <i class="bi bi-pencil-fill"></i>
                        </button>
                        <button class="btn btn-danger btn-sm rounded-pill" onclick="deleteRecord(\'' . route('admin.farmacias.destroy', $farmacia->id) . '\')">
                            <i class="bi bi-trash-fill"></i>
                        </button>
                    </div>
                ',
            ];
        });

        return response()->json([
            "draw" => (int) $request->input("draw"),
            "recordsTotal" => $totalRecords,
            "recordsFiltered" => $recordsFiltered,
            "data" => $data,
        ]);
    }

    private function authorizeAdmin()
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            abort(403, 'Acceso denegado.');
        }
    }
}