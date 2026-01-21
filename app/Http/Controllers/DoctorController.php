<?php

namespace App\Http\Controllers;

use App\Http\Requests\DoctorStoreRequest;
use App\Http\Requests\DoctorUpdateRequest;
use App\Models\Doctor;
use DateTime;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Services\FileService;


class DoctorController extends Controller
{

    protected FileService $fileService;

    public function __construct(FileService $fileService)
    {
        $this->fileService = $fileService;
    }

    public function index(Request $request)
    {
        // $doctors = Doctor::all();

        return view('doctores.index', [
            'doctors' => collect(),
        ]);
    }

    public function create(Request $request)
    {
        return view('doctores.form');
    }

    public function store(Request $request)
    {
        try {
            // Debug: Verificar datos entrantes
            Log::info("Doctor Store method called");
            Log::info("All request data:", $request->all());

            // Validar los inputs específicos de Doctor
            // NOTA: No validamos la imagen aquí dentro para procesarla aparte, 
            // tal como en tu ejemplo original.
            $validated = $request->validate([
                "name" => "required|string|max:100",
                "especialidad" => "required|string|max:100",
                "descripcion" => "required|string|max:1000",
                "cedula" => "required|string|max:50",
                "telefono" => "required|string|max:20",
                "idioma" => "nullable|string|max:50",
                "direccion" => "required|string|max:255",
                "costos" => "required|numeric|min:0",
                "horarioentrada" => "required",
                "horariosalida" => "required",
                "fecha" => "required|date",
            ]);

            // Verificamos si viene un ID (campo oculto en el form)
            $id = $request->input("id", null);

            if ($id) {
                // ==========================================
                // LOGICA DE ACTUALIZACIÓN (UPDATE)
                // ==========================================
                $doctor = Doctor::findOrFail($id);
                $oldImage = $doctor->image;

                // Actualizar campos básicos
                $doctor->fill($validated);
                $doctor->save();

                // Procesar nueva imagen si se subió
                if ($request->hasFile("image")) {
                    $uploadResult = $this->fileService->upload(
                        $request->file("image"),
                        "doctors" // <--- Carpeta específica para doctores
                    );

                    if ($uploadResult["success"]) {
                        $doctor->image = $uploadResult["path"];
                        $doctor->save();

                        // Eliminar imagen anterior si existe y es distinta
                        if ($oldImage && $oldImage !== $doctor->image) {
                            $this->fileService->delete($oldImage);
                        }
                    }
                }
            } else {
                // ==========================================
                // LOGICA DE CREACIÓN (CREATE)
                // ==========================================
                $doctor = Doctor::create($validated);

                // Procesar imagen para doctor nuevo
                if ($request->hasFile("image")) {
                    Log::info("Processing new doctor image");

                    $uploadResult = $this->fileService->upload(
                        $request->file("image"),
                        "doctors" // <--- Carpeta específica para doctores
                    );

                    if ($uploadResult["success"]) {
                        $doctor->image = $uploadResult["path"];
                        $doctor->save();
                        Log::info("Image path saved: " . $uploadResult["path"]);
                    } else {
                        Log::error("Image upload failed: " . $uploadResult["message"]);
                    }
                }
            }

            // Redirección final
            return redirect()
                ->route("doctores.index") // Asegúrate que esta ruta exista en web.php
                ->with("success", $id ? "Doctor actualizado correctamente." : "Doctor registrado exitosamente.");

        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error("Error en store doctor: " . $e->getMessage());
            return redirect()
                ->back()
                ->withInput()
                ->with("error", "Error del sistema: " . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $doctor = Doctor::findOrFail($id);

        return view('doctores.form', compact('doctor'));
    }

    public function show($id)
    {
        $doctor = Doctor::findOrFail($id);

        return view('doctores.card', compact('doctor'));
    }

    public function update(Request $request, $id)
    {
        try {
            // 1. Validamos (Casi igual que store)
            $validated = $request->validate([
                "name" => "required|string|max:100",
                "especialidad" => "required|string|max:100",
                "descripcion" => "required|string|max:1000",
                "cedula" => "required|string|max:50",
                "telefono" => "required|string|max:20",
                "idioma" => "nullable|string|max:50",
                "direccion" => "required|string|max:255",
                "costos" => "required|numeric|min:0",
                "horarioentrada" => "required",
                "horariosalida" => "required",
                "fecha" => "required|date",
                // La imagen es 'nullable' al editar, porque puede que no quieran cambiarla
                "image" => "nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:5120",
            ]);

            // 2. Buscamos el doctor manualmente (para evitar errores de rutas)
            $doctor = Doctor::findOrFail($id);
            $oldImage = $doctor->image;

            // 3. Preparamos datos (sacamos la imagen del array para procesarla aparte)
            $dataToUpdate = $validated;
            unset($dataToUpdate['image']);

            // 4. Actualizamos la información de texto
            $doctor->update($dataToUpdate);

            // 5. Procesamos la imagen SOLO si subieron una nueva
            if ($request->hasFile("image")) {

                // Usamos tu servicio de archivos
                $uploadResult = $this->fileService->upload(
                    $request->file("image"),
                    "doctors"
                );

                if ($uploadResult["success"]) {
                    $doctor->image = $uploadResult["path"];
                    $doctor->save();

                    // Borramos la imagen vieja para no llenar el servidor de basura
                    if ($oldImage && $oldImage !== $doctor->image) {
                        $this->fileService->delete($oldImage);
                    }
                }
            }

            return redirect()->route('doctores.index')
                ->with('success', 'Doctor actualizado correctamente.');

        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al actualizar: ' . $e->getMessage())
                ->withInput();
        }
    }

    // Quita "Request $request" y "Doctor $doctor", usa solo "$id"
    public function destroy($id)
    {
        // 1. Buscamos el doctor manualmente
        $doctor = Doctor::find($id);

        // Si no existe, regresamos con error
        if (!$doctor) {
            return redirect()->route("doctores.index")
                ->with("error", "El doctor no existe o ya fue eliminado.");
        }

        // 2. Eliminar imagen si existe
        if ($doctor->image) {
            // Asegúrate que tu fileService esté disponible
            $this->fileService->delete($doctor->image);
        }

        // 3. Eliminar registro de la BD
        $doctor->delete();

        return redirect()
            ->route("doctores.index")
            ->with("success", "Doctor eliminado exitosamente!!!");
    }

    public function dataTable(Request $request)
    {
        $request->validate([
            "draw" => "integer",
            "start" => "integer|min:0",
            "length" => "integer|min:1|max:100",
            "search.value" => "nullable|string|max:255",
        ]);

        $query = Doctor::query();

        $search = $request->input("search.value");
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where("name", "like", "%{$search}%")
                    ->orWhere("especialidad", "like", "%{$search}%")
                    ->orWhere("cedula", "like", "%{$search}%")
                    ->orWhere("descripcion", "like", "%{$search}%");
            });
        }

        $totalRecords = Doctor::count();

        $recordsFiltered = $query->count();

        $columns = [
            "name",
            "especialidad",
            "descripcion",
            "fecha",
            "image",
            "telefono",
            "idioma",
            "cedula",
            "direccion",
            "costos",
            "horarioentrada",
            "horariosalida",
            "id"
        ];

        $orderColumnIndex = $request->input("order.0.column", 0);
        $orderDir = $request->input("order.0.dir", "asc");

        $orderByColumn = $columns[$orderColumnIndex] ?? "id";
        $query->orderBy($orderByColumn, $orderDir);

        $start = $request->input("start", 0);
        $length = $request->input("length", 10);

        $doctors = $query->skip($start)->take($length)->get();

        $meses = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];

        $data = $doctors->map(function ($doctor) use ($meses) {

            $imageHtml = "";
            if ($doctor->image && Storage::disk("public")->exists($doctor->image)) {
                $url = asset("storage/" . $doctor->image);
                $imageHtml = "<img src='{$url}' alt='{$doctor->name}' class='img-thumbnail' style='width: 50px; height: 50px; object-fit: cover;'>";
            } else {
                $imageHtml = '<div class="bg-light d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; border-radius: 4px;"><i class="bi bi-person text-muted"></i></div>';
            }

            $fechaformato = "sin fecha";

            if ($doctor->fecha) {
                $dia = $doctor->fecha->day;
                $mes = $meses[($doctor->fecha->month) - 1];
                $anio = $doctor->fecha->year;

                $fechaformato = "{$dia} de {$mes} del {$anio}";
            }

            // function formato_horario($hora){
            //     $horario = new DateTime($hora);
            //     $hora_num = $horario->format('H');
            //     if ($hora_num > 12) {
            //         return "{($hora_num-12)} PM";
            //     }
            //     elseif ($hora_num < 12) {
            //         return "{$hora_num} AM";
            //     }
            //     else {
            //         return "12:00 PM";
            //     }
            // }

            return [
                "id" => $doctor->id,
                "name" => $doctor->name,
                "especialidad" => $doctor->especialidad ?? 'N/A',
                "descripcion" => \Illuminate\Support\Str::limit($doctor->descripcion, 30),
                "fecha" => $fechaformato,
                "image" => $imageHtml,
                "telefono" => $doctor->telefono,
                "idioma" => $doctor->idioma,
                "cedula" => $doctor->cedula,
                "direccion" => $doctor->direccion,
                "costos" => '$' . number_format($doctor->costos, 2),
                // Dentro del return
                "horarioentrada" => $doctor->horarioentrada ? date('g:i A', strtotime($doctor->horarioentrada)) : '',
                "horariosalida" => $doctor->horariosalida ? date('g:i A', strtotime($doctor->horariosalida)) : '',
                "actions" => '
                <div class="d-flex gap-1 justify-content-end">
                    <button class="btn btn-primary btn-sm" onclick="execute(\'/doctores/' . $doctor->id . '/edit\')">
                        <i class="bi bi-pencil"></i> <span class="d-none d-sm-inline">Edit</span>
                    </button>
                    <button class="btn btn-danger btn-sm" onclick="deleteRecord(\'/doctores/' . $doctor->id . '\')">
                        <i class="bi bi-trash"></i> <span class="d-none d-sm-inline">Delete</span>
                    </button>
                    <button class="btn btn-success btn-sm" onclick="execute(\'/doctores/' . $doctor->id . '\')">
                        <i class="bi bi-person"></i> <span class="d-none d-sm-inline">Ver</span>
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
}
