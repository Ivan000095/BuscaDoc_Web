<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\DoctorStoreRequest;
use App\Http\Requests\API\DoctorUpdateRequest;
use App\Http\Resources\API\DoctorResource;
use App\Models\Doctor;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;
use App\Services\FileService;

class DoctorController extends Controller
{
protected FileService $fileService;

    public function __construct(FileService $fileService)
    {
        $this->fileService = $fileService;
    }

    /**
     * Listar todos los doctores
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Doctor::query();

            // Búsqueda (Adaptada para campos de Doctor)
            if ($request->has("search")) {
                $search = $request->input("search");
                $query->where(function ($q) use ($search) {
                    $q->where("name", "like", "%{$search}%")
                      ->orWhere("especialidad", "like", "%{$search}%") // Buscar por especialidad
                      ->orWhere("cedula", "like", "%{$search}%")       // Buscar por cédula
                      ->orWhere("descripcion", "like", "%{$search}%");
                });
            }

            // Ordenamiento
            $sortBy = $request->input("sort_by", "created_at");
            $sortDirection = $request->input("sort_direction", "desc");
            
            // Validar que la columna de ordenamiento exista para evitar errores SQL
            $allowedSorts = ['id', 'name', 'especialidad', 'costos', 'created_at'];
            if (in_array($sortBy, $allowedSorts)) {
                $query->orderBy($sortBy, $sortDirection);
            } else {
                $query->orderBy('created_at', 'desc');
            }

            // Paginación
            $perPage = $request->input("per_page", 500);
            $perPage = min($perPage, 100); 

            $doctors = $query->paginate($perPage);

            // Formatear respuesta (Mapeo de campos de Doctor)
            $data = $doctors->map(function ($doctor) {
                return $this->formatDoctorData($doctor);
            });

            return response()->json([
                "success" => true,
                "data" => $data,
                "pagination" => [
                    "current_page" => $doctors->currentPage(),
                    "last_page" => $doctors->lastPage(),
                    "per_page" => $doctors->perPage(),
                    "total" => $doctors->total(),
                    "from" => $doctors->firstItem(),
                    "to" => $doctors->lastItem(),
                ],
                "meta" => [
                    "search" => $request->input("search"),
                    "sort_by" => $sortBy,
                    "sort_direction" => $sortDirection,
                ],
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                "success" => false,
                "message" => "Error al obtener doctores",
                "error" => $e->getMessage(),
            ], 500);
        }
    }

    public function indexFiltro(Request $request): JsonResponse
    {
        try {
            $query = Doctor::query();

            // Búsqueda (Adaptada para campos de Doctor)
            if ($request->has("search")) {
                $search = $request->input("search");
                $query->where(function ($q) use ($search) {
                    $q->where("nombre", "like", "Elva");
                });
            }

            // Ordenamiento
            $sortBy = $request->input("sort_by", "created_at");
            $sortDirection = $request->input("sort_direction", "desc");
            
            // Validar que la columna de ordenamiento exista para evitar errores SQL
            $allowedSorts = ['id', 'name', 'especialidad', 'costos', 'created_at'];
            if (in_array($sortBy, $allowedSorts)) {
                $query->orderBy($sortBy, $sortDirection);
            } else {
                $query->orderBy('created_at', 'desc');
            }

            // Paginación
            $perPage = $request->input("per_page", 500);
            $perPage = min($perPage, 100); 

            $doctors = $query->paginate($perPage);

            // Formatear respuesta (Mapeo de campos de Doctor)
            $data = $doctors->map(function ($doctor) {
                return $this->formatDoctorData($doctor);
            });

            return response()->json([
                "success" => true,
                "data" => $data,
                "pagination" => [
                    "current_page" => $doctors->currentPage(),
                    "last_page" => $doctors->lastPage(),
                    "per_page" => $doctors->perPage(),
                    "total" => $doctors->total(),
                    "from" => $doctors->firstItem(),
                    "to" => $doctors->lastItem(),
                ],
                "meta" => [
                    "search" => $request->input("search"),
                    "sort_by" => $sortBy,
                    "sort_direction" => $sortDirection,
                ],
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                "success" => false,
                "message" => "Error al obtener doctores",
                "error" => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Crear un nuevo doctor
     */
    public function store(Request $request): JsonResponse
    {
        try {
            // Validaciones adaptadas a tu formulario anterior
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
                "image" => "nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:5120",
            ]);

            // Crear doctor (Excluyendo image del array inicial)
            $doctorData = $validated;
            unset($doctorData['image']); // La imagen se procesa aparte
            
            $doctor = Doctor::create($doctorData);

            // Procesar imagen si se subió
            if ($request->hasFile("image")) {
                $uploadResult = $this->fileService->upload(
                    $request->file("image"),
                    "doctors" // <--- Carpeta específica para doctores
                );

                if ($uploadResult["success"]) {
                    $doctor->image = $uploadResult["path"];
                    $doctor->save();
                }
            }

            return response()->json([
                "success" => true,
                "message" => "Doctor creado exitosamente",
                "data" => [
                    "doctor" => $this->formatDoctorData($doctor),
                ],
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                "success" => false,
                "message" => "Error de validación",
                "errors" => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                "success" => false,
                "message" => "Error al crear doctor",
                "error" => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Mostrar un doctor específico
     */
    public function show(Doctor $doctor): JsonResponse
    {
        try {
            return response()->json([
                "success" => true,
                "data" => [
                    "doctor" => $this->formatDoctorData($doctor),
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "success" => false,
                "message" => "Error al obtener doctor",
                "error" => $e->getMessage(),
            ], 500);
        }
    }


    /**
     * Actualizar un doctor
     */
    public function update(Request $request, Doctor $doctor): JsonResponse
    {
        try {
            $validated = $request->validate([
                "name" => "sometimes|string|max:100",
                "especialidad" => "sometimes|string|max:100",
                "descripcion" => "sometimes|string|max:1000",
                "cedula" => "sometimes|string|max:50",
                "telefono" => "sometimes|string|max:20",
                "idioma" => "nullable|string|max:50",
                "direccion" => "sometimes|string|max:255",
                "costos" => "sometimes|numeric|min:0",
                "horarioentrada" => "sometimes",
                "horariosalida" => "sometimes",
                "fecha" => "sometimes|date",
                "image" => "nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:5120",
                "remove_image" => "sometimes|boolean",
            ]);

            // Actualizar campos básicos
            // Filtramos solo los campos que existen en el modelo para evitar errores con 'remove_image' o 'image'
            $fillableFields = [
                'name', 'especialidad', 'descripcion', 'cedula', 'telefono', 
                'idioma', 'direccion', 'costos', 'horarioentrada', 'horariosalida', 'fecha'
            ];
            
            $dataToUpdate = array_intersect_key($validated, array_flip($fillableFields));
            $doctor->fill($dataToUpdate);
            $doctor->save();

            // Manejar imagen
            if ($request->input("remove_image", false)) {
                if ($doctor->image) {
                    $this->fileService->delete($doctor->image);
                    $doctor->image = null;
                    $doctor->save();
                }
            } elseif ($request->hasFile("image")) {
                $oldImage = $doctor->image;

                $uploadResult = $this->fileService->upload(
                    $request->file("image"),
                    "doctors" // <--- Carpeta doctors
                );

                if ($uploadResult["success"]) {
                    $doctor->image = $uploadResult["path"];
                    $doctor->save();

                    // Eliminar imagen anterior
                    if ($oldImage) {
                        $this->fileService->delete($oldImage);
                    }
                }
            }

            return response()->json([
                "success" => true,
                "message" => "Doctor actualizado exitosamente",
                "data" => [
                    "doctor" => $this->formatDoctorData($doctor),
                ],
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                "success" => false,
                "message" => "Error de validación",
                "errors" => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                "success" => false,
                "message" => "Error al actualizar doctor",
                "error" => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Eliminar un doctor
     */
    public function destroy(Doctor $doctor): JsonResponse
    {
        try {
            if ($doctor->image) {
                $this->fileService->delete($doctor->image);
            }

            $doctor->delete();

            return response()->json([
                "success" => true,
                "message" => "Doctor eliminado exitosamente",
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                "success" => false,
                "message" => "Error al eliminar doctor",
                "error" => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Subir imagen para un doctor (endpoint específico)
     */
    public function uploadImage(Request $request, Doctor $doctor): JsonResponse 
    {
        try {
            $validated = $request->validate([
                "image" => "required|image|mimes:jpeg,png,jpg,gif,svg,webp|max:5120",
            ]);

            $oldImage = $doctor->image;

            $uploadResult = $this->fileService->upload(
                $request->file("image"),
                "doctors"
            );

            if (!$uploadResult["success"]) {
                return response()->json([
                    "success" => false,
                    "message" => "Error al subir imagen",
                    "error" => $uploadResult["message"],
                ], 400);
            }

            $doctor->image = $uploadResult["path"];
            $doctor->save();

            if ($oldImage) {
                $this->fileService->delete($oldImage);
            }

            return response()->json([
                "success" => true,
                "message" => "Imagen subida exitosamente",
                "data" => [
                    "image_url" => asset("storage/" . $doctor->image),
                    "image_path" => $doctor->image,
                ],
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                "success" => false,
                "message" => "Error de validación",
                "errors" => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                "success" => false,
                "message" => "Error al subir imagen",
                "error" => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Eliminar imagen de un doctor
     */
    public function deleteImage(Doctor $doctor): JsonResponse
    {
        try {
            if (!$doctor->image) {
                return response()->json([
                    "success" => false,
                    "message" => "El doctor no tiene imagen",
                ], 400);
            }

            $this->fileService->delete($doctor->image);
            $doctor->image = null;
            $doctor->save();

            return response()->json([
                "success" => true,
                "message" => "Imagen eliminada exitosamente",
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                "success" => false,
                "message" => "Error al eliminar imagen",
                "error" => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Obtener estadísticas de doctores
     */
    public function stats(): JsonResponse
    {
        try {
            $total = Doctor::count();
            $withImages = Doctor::whereNotNull("image")->where('image', '!=', '')->count();
            $withoutImages = $total - $withImages;
            
            // Estadísticas sobre costos
            $avgCosto = Doctor::avg("costos");
            $maxCosto = Doctor::max("costos");
            $minCosto = Doctor::min("costos");

            return response()->json([
                "success" => true,
                "data" => [
                    "total_doctors" => $total,
                    "doctors_with_images" => $withImages,
                    "doctors_without_images" => $withoutImages,
                    "average_cost" => round((float) $avgCosto, 2),
                    "max_cost" => (float) $maxCosto,
                    "min_cost" => (float) $minCosto,
                    "percentage_with_images" => $total > 0
                        ? round(($withImages / $total) * 100, 2)
                        : 0,
                ],
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                "success" => false,
                "message" => "Error al obtener estadísticas",
                "error" => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Helper para formatear la data del doctor y reutilizar código
     */
    private function formatDoctorData($doctor)
    {
        // Asumimos que hasImage() existe en tu modelo Doctor, sino usa check manual
        $hasImage = $doctor->image && \Storage::disk('public')->exists($doctor->image);

        return [
            "id" => $doctor->id,
            "name" => $doctor->name,
            "especialidad" => $doctor->especialidad,
            "descripcion" => $doctor->descripcion,
            "cedula" => $doctor->cedula,
            "telefono" => $doctor->telefono,
            "idioma" => $doctor->idioma,
            "direccion" => $doctor->direccion,
            "costos" => (float) $doctor->costos,
            "horarioentrada" => $doctor->horarioentrada,
            "horariosalida" => $doctor->horariosalida,
            "fecha" => $doctor->fecha,
            "image" => $hasImage
                ? asset("storage/" . $doctor->image)
                : null,
            "has_image" => $hasImage,
            "created_at" => $doctor->created_at,
            "updated_at" => $doctor->updated_at,
        ];
    }

    public function dataTable(Request $request)
    {
        // Validar params de DataTables (opcional, pero seguro)
        $request->validate([
            "draw" => "integer",
            "start" => "integer|min:0",
            "length" => "integer|min:1|max:100",
            "search.value" => "nullable|string|max:255",
        ]);

        // Query base
        $query = Doctor::query();

        // Búsqueda en varios campos
        $search = $request->input("search.value");
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where("id", "like", "%{$search}%")
                    ->orWhere("especialidad", "like", "%{$search}%")
                    ->orWhere("name", "like", "%{$search}%")
                    ->orWhere("descripcion", "like", "%{$search}%")
                    ->orWhere("fecha", "like", "%{$search}%")
                    ->orWhere("image", "like", "%{$search}%")
                    ->orWhere("telefono", "like", "%{$search}%")
                    ->orWhere("idioma", "like", "%{$search}%")
                    ->orWhere("cedula", "like", "%{$search}%")
                    ->orWhere("direccion", "like", "%{$search}%")
                    ->orWhere("costos", "like", "%{$search}%")
                    ->orWhere("horarioentrada", "like", "%{$search}%")
                    ->orWhere("horariosalida", "like", "%{$search}%");
            });
        }

        // Total de registros sin filtros (para recordsTotal)
        $totalRecords = Doctor::count();

        // Registros filtrados (recordsFiltered)
        $filteredRecords = clone $query;
        $recordsFiltered = $filteredRecords->count();

        // get y set Ordenación (columna y dirección)
        $columns = ["id", "name", "especialidad", "descripcion", "fecha", "image", "telefono", "idioma", "cedula", "direccion", "costos", "horarioentrada", "horariosalida"]; // Orden de columnas en tabla
        $orderColumn = $request->input("order.0.column", 0);
        $orderDir = $request->input("order.0.dir", "asc");
        $query->orderBy($columns[$orderColumn] ?? "id", $orderDir);

        // Paginación
        $start = $request->input("start", 0);
        $length = $request->input("length", 10);
        $data = $query->skip($start)->take($length)->get();

        // Formatear los datos para el componente DataTables
        // TODO: Formatear del lado del cliente
        $data = $data->map(function ($doctor) {
            $imageHtml = "";
            if (
                $doctor->image &&
                Storage::disk("public")->exists($doctor->image)
            ) {
                $imageHtml =
                    '<img src="' .
                    asset("storage/" . $doctor->image) .
                    '" alt="' .
                    $doctor->name .
                    '" class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">';
            } else {
                $imageHtml =
                    '<div class="bg-light d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; border-radius: 4px;"><i class="bi bi-image text-muted"></i></div>';
            }

            return [
                "image" => $imageHtml,
                "name" => $doctor->name,
                "especialidad" => $doctor->especialidad,
                "descripcion" => $doctor->descripcion,
                "fecha" => $doctor->fecha,
                "telefono" => $doctor->telefono,
                "idioma" => $doctor->idioma,
                "cedula" => $doctor->cedula,
                "direccion" => $doctor->direccion,
                "costos" => $doctor->costos,
                "horarioentrada" => $doctor->horarioentrada,
                "horariosalida" => $doctor->horariosalida,
                "actions" =>
                    '
                    <button class="btn btn-primary btn-sm" onclick="execute(\'/doctors/' . $doctor->id .'/edit\')">
                        <i class="bi bi-pencil"></i> <span class="d-none d-sm-inline">Edit</span>
                    </button>
                    <button class="btn btn-danger btn-sm" onclick="deleteRecord(\'/doctors/' . $doctor->id .'\')">
                        <i class="bi bi-trash"></i> <span class="d-none d-sm-inline">Delete</span>
                    </button>
                ',
            ];
        });

        // Respuesta JSON en formato requerido por DataTables
        return response()->json([
            "draw" => (int) $request->input("draw"), // Eco del draw para sync
            "recordsTotal" => $totalRecords,
            "recordsFiltered" => $recordsFiltered,
            "data" => $data,
        ]);
    }
}
