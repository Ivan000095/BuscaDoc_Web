<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\DoctorStoreRequest;
use App\Http\Requests\API\DoctorUpdateRequest;
use App\Http\Resources\API\DoctorResource;
use App\Models\Doctor;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Respuesta;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DoctorController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Doctor::with([
                'user',
                'especialidades',
                'reviews.respuestas',
                'reviews.autor',
                'questions.respuestas',
                'questions.autor'
            ]);

            if ($request->has("search")) {
                $search = $request->input("search");
                $query->where(function ($q) use ($search) {
                    $q->whereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where("name", "like", "%{$search}%");
                    })
                        ->orWhere("descripcion", "like", "%{$search}%");
                });
            }

            $sortBy = $request->input("sort_by", "created_at");
            $sortDirection = $request->input("sort_direction", "desc");
            $query->orderBy($sortBy, $sortDirection);

            $perPage = $request->input("per_page", 15);
            $perPage = min($perPage, 100);
            $doctors = $query->paginate($perPage);

            $doctors->getCollection()->transform(function ($doctor) {
                $promedio = round($doctor->reviews->avg('calificacion') ?? 0, 1);
                return [
                    "id" => $doctor->id,
                    "name" => $doctor->user->name,
                    "especialidad" => $doctor->especialidades->pluck('nombre')->join(', '),
                    "descripcion" => \Illuminate\Support\Str::limit($doctor->descripcion, 30),
                    "fecha" => $doctor->user->f_nacimiento,
                    // Puse el host compartido para el emulador de flutter, pero después debería de tener el host de la página
                    "image" => "http://localhost:8000/storage/" . $doctor->user->foto,
                    "promedio" => $promedio,
                    "cedula" => $doctor->cedula,
                    "costos" => '$' . number_format($doctor->costo, 2),
                    "horarioentrada" => $doctor->horario_entrada,
                    "horariosalida" => $doctor->horario_salida,
                    "latitud" => $doctor->user->latitud,
                    "longitud" => $doctor->user->longitud,

                    "comentarios" => $doctor->reviews->map(function ($review) {
                        return [
                            "id" => $review->id,
                            "autor" => $review->autor ? $review->autor->name : 'Usuario Anónimo',
                            "contenido" => $review->contenido,
                            "calificacion" => $review->calificacion,
                            "fecha" => $review->created_at->format('d/m/Y'),
                            "respuestas" => $review->respuestas
                        ];
                    }),

                    "preguntas" => $doctor->questions->map(function ($question) {
                        return [
                            "id" => $question->id,
                            "autor" => $question->autor ? $question->autor->name : 'Usuario Anónimo',
                            "contenido" => $question->contenido,
                            "fecha" => $question->created_at->format('d/m/Y'),
                            "respuestas" => $question->respuestas
                        ];
                    }),
                ];
            });

            return response()->json([
                "success" => true,
                "data" => $doctors->items(),
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

    public function show($id): JsonResponse
    {
        try {
            $doctor = Doctor::with([
                'user',
                'especialidades',
                'reviews.respuestas',
                'reviews.autor',
                'questions.respuestas',
                'questions.autor'
            ])->find($id);

            if (!$doctor) {
                return response()->json([
                    "success" => false,
                    "message" => "Doctor no encontrado",
                ], 404);
            }

            $promedio = round($doctor->reviews->avg('calificacion') ?? 0, 1);

            $data = [
                "id" => $doctor->id,
                "name" => $doctor->user->name ?? 'Sin nombre',
                "especialidad" => $doctor->especialidades->pluck('nombre')->join(', '),
                "descripcion" => $doctor->descripcion,
                "fecha" => $doctor->user->f_nacimiento ?? null,
                "image" => $doctor->user->foto ? "10.0.2.2:8000/storage/" . $doctor->user->foto : null,
                "promedio" => $promedio,
                "cedula" => $doctor->cedula,
                "costos" => '$' . number_format($doctor->costo, 2),
                "horarioentrada" => $doctor->horario_entrada,
                "horariosalida" => $doctor->horario_salida,
                "latitud" => $doctor->user->latitud,
                "longitud" => $doctor->user->longitud,

                "comentarios" => $doctor->reviews->map(function ($review) {
                    return [
                        "id" => $review->id,
                        "autor" => $review->autor ? $review->autor->name : 'Usuario Anónimo',
                        "contenido" => $review->contenido,
                        "calificacion" => $review->calificacion,
                        "fecha" => $review->created_at->format('d/m/Y'),
                        "respuestas" => $review->respuestas
                    ];
                }),

                "preguntas" => $doctor->questions->map(function ($question) {
                    return [
                        "id" => $question->id,
                        "autor" => $question->autor ? $question->autor->name : 'Usuario Anónimo',
                        "contenido" => $question->contenido,
                        "fecha" => $question->created_at->format('d/m/Y'),
                        "respuestas" => $question->respuestas
                    ];
                }),
            ];

            return response()->json([
                "success" => true,
                "data" => $data
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                "success" => false,
                "message" => "Error al obtener el doctor",
                "error" => $e->getMessage(),
            ], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $request->validate([
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

            $doctorRegistrado = null;

            DB::transaction(function () use ($request, &$doctorRegistrado) {

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

                $doctor->load(['user', 'especialidades']);
                $doctorRegistrado = $doctor;
            });

            return response()->json([
                "success" => true,
                "message" => "Doctor registrado correctamente vía API.",
                "data" => $doctorRegistrado
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                "success" => false,
                "message" => "Error de validación en el formulario",
                "errors" => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                "success" => false,
                "message" => "Ocurrió un error crítico al registrar el doctor",
                "error" => $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request, $id): JsonResponse
    {
        try {
            $doctor = Doctor::find($id);

            if (!$doctor) {
                return response()->json([
                    "success" => false,
                    "message" => "Doctor no encontrado",
                ], 404);
            }

            // Validamos los datos entrantes
            $validatedData = $request->validate([
                'cedula' => 'sometimes|string|max:255',
                'descripcion' => 'sometimes|string',
                'costo' => 'sometimes|numeric',
                'horario_entrada' => 'sometimes',
                'horario_salida' => 'sometimes',
            ]);

            $doctor->update($validatedData);

            return response()->json([
                "success" => true,
                "message" => "Doctor actualizado exitosamente",
                "data" => $doctor
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                "success" => false,
                "message" => "Error de validación",
                "errors" => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                "success" => false,
                "message" => "Error al actualizar el doctor",
                "error" => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $doctor = Doctor::find($id);

            if (!$doctor) {
                return response()->json([
                    "success" => false,
                    "message" => "Doctor no encontrado",
                ], 404);
            }

            $doctor->especialidades()->detach();
            $doctor->delete();
            $doctor->user->delete();

            return response()->json([
                "success" => true,
                "message" => "Doctor eliminado exitosamente"
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                "success" => false,
                "message" => "Error al eliminar el doctor",
                "error" => $e->getMessage(),
            ], 500);
        }
    }
}
