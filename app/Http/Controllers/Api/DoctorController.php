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
                    "image" => "10.0.2.2:8000/storage/" . $doctor->user->foto,
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

    // public function store(DoctorStoreRequest $request): Response
    // {
    //     $doctor = Doctor::create($request->validated());

    //     return new DoctorResource($doctor);
    // }

    // public function show(Request $request, Doctor $doctor): Response
    // {
    //     $doctor = Doctor::find($id);

    //     return new DoctorResource($doctor);
    // }

    // public function update(DoctorUpdateRequest $request, Doctor $doctor): Response
    // {
    //     $doctor->update($request->validated());

    //     return new DoctorResource($doctor);
    // }

    // public function destroy(Request $request, Doctor $doctor): Response
    // {
    //     $doctor->delete();

    //     return response()->noContent();
    // }
}
