<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Services\FileService;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;
use App\Models\Especialidad;

class DoctorController extends Controller
{
    protected FileService $fileService;

    public function __construct(FileService $fileService)
    {
        $this->fileService = $fileService;
    }

    public function create()
    {
        $especialidades = Especialidad::all();
        return view('doctores.form', compact('especialidades'));
    }

    public function index(Request $request)
    {
        return view('doctores.index', [
            'doctors' => collect(),
        ]);
    }

    public function vistageneral(Request $request)
{
    $doctores = Doctor::with(['user', 'especialidades'])->get();
    return view('doctores.vista', compact('doctores'));
}



    public function store(Request $request)
    {
        $validated = $request->validate([
            "name" => "required|string|max:100",
            "email" => "required|email|unique:users,email",
            "password" => "required|min:8",
            "fecha" => "required|date|before:-18 years",
            "image" => "nullable|image|max:5120",
            
            "especialidad_id" => "required|exists:especialidads,id", 
            
            "cedula" => "required|string|max:50",
            "descripcion" => "required|string|max:1000",
            "costos" => "required|numeric|min:0",
            "horarioentrada" => "required",
            "horariosalida" => "required",
            "idioma" => "nullable|string",
            "latitud" => "nullable|numeric",
            "longitud" => "nullable|numeric",
        ]);

        DB::transaction(function () use ($request) {
            
            $rutaFoto = null;
            if ($request->hasFile("image")) {
                $rutaFoto = $request->file("image")->store('users', 'public');
            }
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'doctor',
                'estado' => true,
                'foto' => $rutaFoto,
                'f_nacimiento' => $request->fecha,
                'latitud' => $request->latitud,
                'longitud' => $request->longitud,
            ]);

            $doctor = Doctor::create([
                'user_id' => $user->id,
                'cedula' => $request->cedula,
                'descripcion' => $request->descripcion,
                'costo' => $request->costos,
                'idiomas' => $request->idioma,
                'horario_entrada' => $request->horarioentrada,
                'horario_salida' => $request->horariosalida,
            ]);

            $doctor->especialidades()->attach($request->especialidad_id);
        });

        return redirect()->route("doctores.index")->with("success", "Doctor registrado correctamente.");
    }

    public function update(Request $request, $id)
    {
        $doctor = Doctor::findOrFail($id);
        $user = $doctor->user;

        $validated = $request->validate([
            "name" => "required|string|max:100",
            "email" => "required|email|unique:users,email," . $user->id,
            "fecha" => "required|date|before:-18 years",
            "especialidad_id" => "required|exists:especialidads,id",
            "cedula" => "required|string",
            "costos" => "required|numeric",
            "horarioentrada" => "required",
            "horariosalida" => "required",
            "descripcion" => "nullable|string",
            "latitud" => "nullable|numeric",
            "longitud" => "nullable|numeric",
            "image" => "nullable|image|max:5120",
        ]);

        DB::transaction(function () use ($request, $doctor, $user) {
            
            // Actualizar Usuario...
            $userData = [
                'name' => $request->name,
                'email' => $request->email,
                'f_nacimiento' => $request->fecha,
                'latitud' => $request->latitud,
                'longitud' => $request->longitud,
            ];
            
            if ($request->hasFile("image")) {
                if ($user->foto) Storage::disk('public')->delete($user->foto);
                $userData['foto'] = $request->file("image")->store('users', 'public');
            }
             if ($request->hasFile("image")) {
                 $userData['foto'] = $request->file("image")->store('users', 'public');
            }

            $user->update($userData);

            // Actualizar Doctor
            $doctor->update([
                'cedula' => $request->cedula,
                'costo' => $request->costos,
                'horario_entrada' => $request->horarioentrada,
                'horario_salida' => $request->horariosalida,
                'descripcion' => $request->descripcion,
                'idiomas' => $request->idioma,
            ]);

            $doctor->especialidades()->sync([$request->especialidad_id]);
        });

        return redirect()->route('doctores.index')->with('success', 'Doctor actualizado.');
    }
    public function edit($id)
    {
        $doctor = Doctor::findOrFail($id);
        $especialidades = Especialidad::all(); // También aquí
        return view('doctores.form', compact('doctor', 'especialidades'));
    }

    public function show($id)
    {
        $doctor = Doctor::findOrFail($id);

        return view('doctores.card', compact('doctor'));
    }


    public function destroy($id)
    {
        $doctor = Doctor::findOrFail($id);
        $user = $doctor->user;

        try {
            DB::transaction(function () use ($doctor, $user) {
                
                $doctor->especialidades()->detach();

                $doctor->delete();

                if ($user) {
                    if ($user->foto) {
                        $this->fileService->delete($user->foto);
                    }
                    $user->delete();
                }
            });

            return redirect()->route('doctores.index')->with('success', 'Doctor y usuario eliminados correctamente.');

        } catch (\Exception $e) {
            // Si algo falla (ej. tiene citas agendadas y la BD no deja borrar), avisamos
            return redirect()->route('doctores.index')->with('error', 'No se pudo eliminar: ' . $e->getMessage());
        }
    }

    public function dataTable(Request $request)
    {
        $query = Doctor::with('user');
        $search = $request->input("search.value");
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                // Buscamos por nombre en la tabla 'users' relacionada
                $q->whereHas('user', function ($subQ) use ($search) {
                    $subQ->where('name', 'like', "%{$search}%");
                })
                ->orWhere("cedula", "like", "%{$search}%")
                ->orWhere("descripcion", "like", "%{$search}%");
            });
        }
        $totalRecords = Doctor::count();
        $recordsFiltered = $query->count();
        $start = $request->input("start", 0);
        $length = $request->input("length", 10);
        $doctors = $query->skip($start)->take($length)->get();
        $meses = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];
        $data = $doctors->map(function ($doctor) use ($meses) {
            $imageHtml = "";
            $fotoPath = $doctor->user->foto;
            if ($fotoPath && Storage::disk("public")->exists($fotoPath)) {
                $url = asset("storage/" . $fotoPath);
                $imageHtml = "<img src='{$url}' class='img-thumbnail' style='width: 50px; height: 50px; object-fit: cover;'>";
            } else {
                $imageHtml = '<div class="bg-light..." ><i class="bi bi-person"></i></div>';
            }
            $fechaformato = "sin fecha";
            if ($doctor->user->f_nacimiento) {
                $fechaObj = Carbon::parse($doctor->user->f_nacimiento);
                $fechaformato = $fechaObj->isoFormat('D [de] MMMM [del] YYYY');
            }
            return [
                "id" => $doctor->id,
                "name" => $doctor->user->name,
                "especialidad" => "General",
                "descripcion" => \Illuminate\Support\Str::limit($doctor->descripcion, 30),
                "fecha" => $fechaformato,
                "image" => $imageHtml,
                "cedula" => $doctor->cedula,
                "costos" => '$' . number_format($doctor->costo, 2),
                "horarioentrada" => $doctor->horario_entrada,
                "horariosalida" => $doctor->horario_salida,
                "actions" => '
                    <div class="d-flex gap-1 justify-content-end">
                        <button class="btn btn-primary btn-sm" onclick="execute(\'/doctores/' . $doctor->id . '/edit\')">Edit</button>
                        <button class="btn btn-danger btn-sm" onclick="deleteRecord(\'/doctores/' . $doctor->id . '\')">Delete</button>
                        <button class="btn btn-success btn-sm" onclick="execute(\'/doctores/' . $doctor->id . '\')"><i class="bi bi-person"></i> <span class="d-none d-sm-inline">Ver</span></button>
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
}