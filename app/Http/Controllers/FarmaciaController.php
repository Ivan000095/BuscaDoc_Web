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
            'horario' => 'nullable|string|max:255',
            'dias_op' => 'nullable|string|max:255',
        ]);
        $farmacia->update($validated);
        return redirect()->route('farmacias.mi')->with('success', 'Actualizada.');
    }



    
    // 3. Administrador: CRUD completo
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

    $validated = $request->validate([
        //Campos del nuevo usuario
        'name' => 'required|string|max:100',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|min:8',
        'fecha' => 'required|date|before:-18 years',
        'image' => 'nullable|image|max:5120',

        //Campos del la farmacia
        'nom_farmacia' => 'required|string|max:255',
        'rfc' => 'required|string|max:255|unique:farmacias,rfc',
        'telefono' => 'required|string|max:255',
        'descripcion' => 'nullable|string',
        'horario' => 'required|string|max:255',
        'dias_op' => 'required|string|max:255',
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
            'f_nacimiento' => $validated['fecha'],
            'latitud' => $validated['latitud'],
            'longitud' => $validated['longitud'],
        ]);

        Farmacia::create([
            'user_id' => $user->id,
            'nom_farmacia' => $validated['nom_farmacia'],
            'rfc' => $validated['rfc'],
            'telefono' => $validated['telefono'],
            'descripcion' => $validated['descripcion'],
            'horario' => $validated['horario'],
            'dias_op' => $validated['dias_op'],
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
        'horario' => 'required|string|max:255',
        'dias_op' => 'required|string|max:255',
        'descripcion' => 'required|string',
        'latitud' => 'required|numeric|between:-90,90',
        'longitud' => 'required|numeric|between:-180,180',
    ]);

    DB::transaction(function () use ($request, $validated, $farmacia) {
        // Actualizar usuario
        $user = $farmacia->user;
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->f_nacimiento = $validated['fecha'];
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
            'horario' => $validated['horario'],
            'dias_op' => $validated['dias_op'],
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
    $query = Farmacia::with('user');
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

    $totalRecords = Farmacia::count();
    $recordsFiltered = $query->count();

    $start = $request->input("start", 0);
    $length = $request->input("length", 10);

    $farmacias = $query->skip($start)->take($length)->get();

    $meses = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];

    $data = $farmacias->map(function ($farmacia) use ($meses) {
        // Foto del dueño
        $imageHtml = "";
        $fotoPath = $farmacia->user?->foto;
        if ($fotoPath && Storage::disk("public")->exists($fotoPath)) {
            $url = asset("storage/" . $fotoPath);
            $imageHtml = "<img src='{$url}' class='img-thumbnail' style='width: 50px; height: 50px; object-fit: cover;'>";
        } else {
            $imageHtml = '<div class="bg-light d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; border-radius: 4px;">
                <i class="bi bi-person text-muted"></i>
            </div>';
        }

        // Fecha de nacimiento formateada
        $fechaformato = "sin fecha";
        if ($farmacia->user?->f_nacimiento) {
            $fechaObj = Carbon::parse($farmacia->user->f_nacimiento);
            $dia = $fechaObj->format('j');
            $mes = $meses[$fechaObj->format('n') - 1];
            $anio = $fechaObj->format('Y');
            $fechaformato = "{$dia} de {$mes} del {$anio}";
        }

        return [
            "id" => $farmacia->id,
            "nombre_dueño" => $farmacia->user?->name ?? '—',
            "nom_farmacia" => $farmacia->nom_farmacia,
            "rfc" => $farmacia->rfc ?? '—',
            "telefono" => $farmacia->telefono,
            "horario" => $farmacia->horario,
            "dias_op" => $farmacia->dias_op,
            "fecha_nacimiento" => $fechaformato,
            "foto" => $imageHtml,
            "acciones" => '
                <div class="d-flex gap-1 justify-content-end">
                    <button class="btn btn-primary btn-sm" onclick="execute(\'' . route('admin.farmacias.edit', $farmacia->id) . '\')">Editar</button>
                    <button class="btn btn-danger btn-sm" onclick="deleteRecord(\'' . route('admin.farmacias.destroy', $farmacia->id) . '\')">Eliminar</button>
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