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
use App\Utils;
use App\Models\Comentario;
use App\Models\Respuesta;
use App\Models\Cita;
use Barryvdh\DomPDF\Facade\Pdf;

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

    public function vistageneral(Request $request) // O como se llame tu método actual
    {
        // Iniciamos la consulta con sus relaciones
        $query = Doctor::with([
            'user',
            'especialidades',
            'reviews.autor',
            'questions.autor'
        ]);

        // FILTRO 1: Búsqueda por nombre de doctor
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            // Buscamos dentro de la tabla 'users' relacionada
            $query->whereHas('user', function($q) use ($searchTerm) {
                $q->where('name', 'LIKE', "%{$searchTerm}%");
            });
        }

        // FILTRO 2: Búsqueda por especialidad
        if ($request->filled('especialidad')) {
            $especialidad = $request->especialidad;
            // Buscamos dentro de la relación de especialidades
            $query->whereHas('especialidades', function($q) use ($especialidad) {
                $q->where('nombre', $especialidad);
            });
        }

        // Ejecutamos la consulta ya filtrada
        $doctores = $query->get();

        // 1. Ordenamos por los mejores evaluados
        $doctoresOrdenados = $doctores->sortByDesc('promedio_calificacion');

        // 2. Agrupamos por la especialidad principal
        $doctoresPorEspecialidad = $doctoresOrdenados->groupBy(function ($doctor) {
            return $doctor->especialidades->first()->nombre ?? 'Médico General';
        });

        // Traemos todas las especialidades para llenar el <select> del buscador
        $especialidades = Especialidad::all();

        // Retornamos la MISMA vista con los resultados filtrados
        return view('doctores.vista', compact('doctoresPorEspecialidad', 'especialidades'));
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
            "duracion_cita" => "required|integer|min:15|max:120",
            "horarios" => "required|array",
            "horarios.*.dia" => "required|integer|between:0,6",
            "horarios.*.inicio" => "required|date_format:H:i",
            "horarios.*.fin" => "required|date_format:H:i|after:horarios.*.inicio",
            "idioma" => "nullable|string",
            "latitud" => "nullable|numeric",
            "longitud" => "nullable|numeric",
            "citas" => "nullable",
        ]);

        DB::transaction(function () use ($request) {
            $rutaFoto = $request->hasFile("image") 
                ? $request->file("image")->store('users', 'public') 
                : null;

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
                'duracion_cita' => $request->duracion_cita, 
                'idiomas' => $request->idioma,
                'citas' => $request->has('citas'),
            ]);

            // Guardar disponibilidad en la nueva tabla
            foreach ($request->horarios as $bloque) {
                $doctor->disponibilidades()->create([
                    'dia_semana' => $bloque['dia'],
                    'hora_inicio' => $bloque['inicio'],
                    'hora_fin' => $bloque['fin'],
                ]);
            }

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
                "cedula" => "required|string|max:50",
                "descripcion" => "required|string|max:1000",
                "costos" => "required|numeric|min:0",
                "duracion_cita" => "required|integer|min:15|max:120",
                "horarios" => "required|array",
                "horarios.*.dia" => "required|integer|between:0,6",
                "horarios.*.inicio" => "required|date_format:H:i",
                "horarios.*.fin" => "required|date_format:H:i|after:horarios.*.inicio",
                "idioma" => "nullable|string",
                "latitud" => "nullable|numeric",
                "longitud" => "nullable|numeric",
                "image" => "nullable|image|max:5120",
            ]);

            DB::transaction(function () use ($request, $doctor, $user) {
                // 1. Preparar datos del Usuario
                $userData = [
                    'name' => $request->name,
                    'email' => $request->email,
                    'f_nacimiento' => $request->fecha,
                    'latitud' => $request->latitud,
                    'longitud' => $request->longitud,
                ];

                // Manejo de la foto
                if ($request->hasFile("image")) {
                    if ($user->foto) {
                        Storage::disk('public')->delete($user->foto);
                    }
                    $userData['foto'] = $request->file("image")->store('users', 'public');
                }

                $user->update($userData);

                // 2. Actualizar datos del Doctor
                $doctor->update([
                    'cedula' => $request->cedula,
                    'descripcion' => $request->descripcion,
                    'costo' => $request->costos,
                    'duracion_cita' => $request->duracion_cita,
                    'idiomas' => $request->idioma,
                    'citas' => $request->has('citas'),
                ]);

                // 3. Actualizar Especialidades
                $doctor->especialidades()->sync([$request->especialidad_id]);

                // 4. Actualizar Horarios (Disponibilidades)
                // Eliminamos los anteriores y creamos los nuevos para simplificar la actualización
                $doctor->disponibilidades()->delete();
                foreach ($request->horarios as $bloque) {
                    $doctor->disponibilidades()->create([
                        'dia_semana' => $bloque['dia'],
                        'hora_inicio' => $bloque['inicio'],
                        'hora_fin' => $bloque['fin'],
                    ]);
                }
            });

            return redirect()->route('doctores.index')->with('success', 'Doctor actualizado correctamente.');
        }
    public function edit($id)
    {
        $doctor = Doctor::findOrFail($id);
        $especialidades = Especialidad::all();
        return view('doctores.form', compact('doctor', 'especialidades'));
    }

    public function show($id)
    {
        $doctor = Doctor::with(['user','especialidades', 'disponibilidades'])->findOrFail($id);

        return view('doctores.card', compact('doctor'));
    }

    public function destroy($id)
    {
        $doctor = Doctor::findOrFail($id);
        $user = $doctor->user;

        try {
            DB::transaction(function () use ($doctor, $user) {
                $doctor->especialidades()->detach();

                if ($user) {
                    Respuesta::where('id_respondedor', $user->id)->delete();
                    $comentariosRecibidos = Comentario::where('id_destinatario', $user->id)->get();

                    foreach ($comentariosRecibidos as $comentario) {
                        $comentario->respuestas()->delete();
                        $comentario->delete();
                    }
                    Comentario::where('id_autor', $user->id)->delete();
                    // Cita::where('doctor_id', $doctor->id)->delete(); 
                    if ($user->foto) {
                        $this->fileService->delete($user->foto);
                    }
                    $doctor->delete();
                    $user->delete();
                } else {
                    $doctor->delete();
                }
            });

            return redirect()->route('doctores.index')->with('success', 'Doctor y todos sus datos vinculados fueron eliminados.');

        } catch (\Exception $e) {
            return redirect()->route('doctores.index')->with('error', 'No se pudo eliminar: ' . $e->getMessage());
        }
    }

    public function dataTable(Request $request)
    {
        // Cargamos la relación 'disponibilidades' para el semáforo de horario
        $query = Doctor::with(['user', 'especialidades', 'disponibilidades']);
        
        $search = $request->input("search.value");
        if (!empty($search)) {
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            })->orWhere("cedula", "like", "%{$search}%");
        }

        $totalRecords = Doctor::count();
        $recordsFiltered = $query->count();
        $doctors = $query->skip($request->input("start", 0))->take($request->input("length", 10))->get();

        $data = $doctors->map(function ($doctor) {
            // Lógica de semáforo de horario (Abierto/Cerrado)
            $hoy = now()->dayOfWeek; 
            $horaActual = now()->format('H:i:s');
            $disponibilidadHoy = $doctor->disponibilidades->where('dia_semana', $hoy);
            
            $estaAbierto = false;
            foreach($disponibilidadHoy as $bloque) {
                if($horaActual >= $bloque->hora_inicio && $horaActual <= $bloque->hora_fin) {
                    $estaAbierto = true;
                    break;
                }
            }

            $horarioHtml = $disponibilidadHoy->isEmpty() 
                ? '<span class="badge bg-secondary rounded-pill">No labora hoy</span>'
                : ($estaAbierto 
                    ? '<span class="badge bg-success rounded-pill">Disponible</span>' 
                    : '<span class="badge bg-danger rounded-pill">Fuera de horario</span>');

            return [
                "id" => $doctor->id,
                "name" => $doctor->user->name,
                "especialidad" => $doctor->especialidades->pluck('nombre')->join(', '),
                "cedula" => $doctor->cedula ?? 'N/A',
                "descripcion" => \Illuminate\Support\Str::limit($doctor->descripcion, 40),
                "costos" => '$' . number_format($doctor->costo, 2),
                "horario" => $horarioHtml,
                "citas" => $doctor->citas 
                    ? '<span class="badge bg-success rounded-pill">Sí</span>' 
                    : '<span class="badge bg-secondary rounded-pill">No</span>',
                "fecha" => $doctor->user->f_nacimiento ? Carbon::parse($doctor->user->f_nacimiento)->isoFormat('LL') : 'N/A',
                "image" => $doctor->user->foto 
                    ? '<img src="'.asset('storage/'.$doctor->user->foto).'" class="rounded-circle" style="width:40px; height:40px; object-fit:cover;">'
                    : '<div class="bg-light rounded-circle d-flex align-items-center justify-content-center" style="width:40px; height:40px;"><i class="bi bi-person"></i></div>',
                "actions" => '
                    <div class="d-flex justify-content-end gap-2">
                        <button class="btn btn-outline-navy btn-sm rounded-pill" onclick="execute(\'' . route('doctores.edit', $doctor->id) . '\')">
                            <i class="bi bi-pencil-fill"></i>
                        </button>
                        <button class="btn btn-danger btn-sm rounded-pill" onclick="deleteRecord(\'' . route('doctores.destroy', $doctor->id) . '\')">
                            <i class="bi bi-trash-fill"></i>
                        </button>
                    </div>',
            ];
        });

        return response()->json([
            "draw" => (int) $request->input("draw"),
            "recordsTotal" => $totalRecords,
            "recordsFiltered" => $recordsFiltered,
            "data" => $data,
        ]);
    }

    public function generarReporte(Request $request)
    {
        $query = Doctor::with(['user', 'especialidades']);

        if ($request->filled('fecha_inicio')) {
            $query->whereDate('created_at', '>=', $request->fecha_inicio);
        }
        if ($request->filled('fecha_fin')) {
            $query->whereDate('created_at', '<=', $request->fecha_fin);
        }

        if ($request->citas_activas !== 'todos') {
            $query->where('citas', $request->citas_activas);
        }

        switch ($request->orden) {
            case 'costo_alto':
                $query->orderBy('costo', 'desc');
                break;
            case 'costo_bajo':
                $query->orderBy('costo', 'asc');
                break;
            case 'antiguos':
                $query->orderBy('created_at', 'asc');
                break;
            case 'recientes':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        $doctores = $query->get();

        $pdf = Pdf::loadView('doctores.pdf', compact('doctores', 'request'))
                  ->setPaper('a4', 'landscape');

        return $pdf->download('Reporte_Doctores_BuscaDoc_' . now()->format('Ymd') . '.pdf');
    }
}