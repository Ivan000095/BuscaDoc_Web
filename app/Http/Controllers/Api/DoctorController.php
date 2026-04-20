<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class DoctorController extends Controller
{
    /**
     * Listar doctores con filtros y nuevas relaciones
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Doctor::with(['user', 'especialidades', 'disponibilidades']);

            if ($request->has("search")) {
                $search = $request->input("search");
                $query->whereHas('user', function ($q) use ($search) {
                    $q->where("name", "like", "%{$search}%");
                })->orWhere("descripcion", "like", "%{$search}%");
            }

            if ($request->has("especialidad_id")) {
                $query->whereHas('especialidades', function ($q) use ($request) {
                    $q->where('especialidads.id', $request->input("especialidad_id"));
                });
            }

            $doctors = $query->paginate(min($request->input("per_page", 15), 100));

            $doctors->getCollection()->transform(function ($doctor) {
                return [
                    "id" => $doctor->id,
                    "name" => $doctor->user->name,
                    "especialidad" => $doctor->especialidades->pluck('nombre')->join(', '),
                    "image" => $doctor->user->foto ? asset('storage/' . $doctor->user->foto) : null,
                    "costos" => number_format($doctor->costo, 2),
                    "duracion_cita" => $doctor->duracion_cita,
                    "disponibilidades" => $doctor->disponibilidades, // Ahora devuelve array de días/horas
                    "latitud" => $doctor->user->latitud,
                    "longitud" => $doctor->user->longitud,
                    "promedio" => round($doctor->reviews->avg('calificacion') ?? 0, 1),
                ];
            });

            return response()->json(["success" => true, "data" => $doctors->items(), "pagination" => [
                "current_page" => $doctors->currentPage(),
                "total" => $doctors->total(),
            ]], 200);
        } catch (\Exception $e) {
            return response()->json(["success" => false, "error" => $e->getMessage()], 500);
        }
    }

    /**
     * Registrar nuevo doctor con múltiples horarios
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                "name" => "required|string|max:100",
                "email" => "required|email|unique:users,email",
                "password" => "required|min:8",
                "fecha" => "required|date|before:-18 years",
                "especialidad_id" => "required|exists:especialidads,id",
                "cedula" => "required|string|unique:doctors,cedula",
                "descripcion" => "required|string",
                "costos" => "required|numeric",
                "duracion_cita" => "required|integer|min:15",
                "horarios" => "required|array", // Array de bloques de tiempo
                "horarios.*.dia" => "required|integer|between:0,6",
                "horarios.*.inicio" => "required|date_format:H:i",
                "horarios.*.fin" => "required|date_format:H:i|after:horarios.*.inicio",
                "image" => "nullable|image|max:5120",
            ]);

            $doctor = DB::transaction(function () use ($request, $validated) {
                $rutaFoto = $request->hasFile("image") ? $request->file("image")->store('users', 'public') : null;

                $user = User::create([
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'password' => Hash::make($validated['password']),
                    'role' => 'doctor',
                    'foto' => $rutaFoto,
                    'f_nacimiento' => $validated['fecha'],
                    'latitud' => $request->latitud,
                    'longitud' => $request->longitud,
                ]);

                $doctor = Doctor::create([
                    'user_id' => $user->id,
                    'cedula' => $validated['cedula'],
                    'descripcion' => $validated['descripcion'],
                    'costo' => $validated['costos'],
                    'duracion_cita' => $validated['duracion_cita'],
                    'idiomas' => $request->idioma,
                    'citas' => $request->boolean('citas'),
                ]);

                $doctor->especialidades()->attach($validated['especialidad_id']);

                foreach ($validated['horarios'] as $h) {
                    $doctor->disponibilidades()->create([
                        'dia_semana' => $h['dia'],
                        'hora_inicio' => $h['inicio'],
                        'hora_fin' => $h['fin'],
                    ]);
                }

                return $doctor->load(['user', 'disponibilidades']);
            });

            return response()->json(["success" => true, "data" => $doctor], 201);
        } catch (\Exception $e) {
            return response()->json(["success" => false, "message" => $e->getMessage()], 500);
        }
    }

    /**
     * Actualización completa (API compatible)
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $doctor = Doctor::with('user')->findOrFail($id);

            $validated = $request->validate([
                'name' => 'sometimes|string',
                'email' => 'sometimes|email|unique:users,email,' . $doctor->user->id,
                'cedula' => 'sometimes|unique:doctors,cedula,' . $doctor->id,
                'horarios' => 'sometimes|array',
                'duracion_cita' => 'sometimes|integer',
                'image' => 'nullable|image',
            ]);

            DB::transaction(function () use ($request, $doctor, $validated) {
                // Actualizar Usuario
                if ($request->hasFile("image")) {
                    if ($doctor->user->foto) Storage::disk('public')->delete($doctor->user->foto);
                    $doctor->user->foto = $request->file("image")->store('users', 'public');
                }
                
                $doctor->user->update($request->only(['name', 'email', 'latitud', 'longitud']));
                if ($request->password) $doctor->user->update(['password' => Hash::make($request->password)]);

                // Actualizar Doctor
                $doctor->update([
                    'cedula' => $validated['cedula'] ?? $doctor->cedula,
                    'descripcion' => $request->descripcion ?? $doctor->descripcion,
                    'costo' => $request->costos ?? $doctor->costo,
                    'duracion_cita' => $validated['duracion_cita'] ?? $doctor->duracion_cita,
                ]);

                // Actualizar Horarios si se envían
                if ($request->has('horarios')) {
                    $doctor->disponibilidades()->delete();
                    foreach ($request->horarios as $h) {
                        $doctor->disponibilidades()->create([
                            'dia_semana' => $h['dia'],
                            'hora_inicio' => $h['inicio'],
                            'hora_fin' => $h['fin'],
                        ]);
                    }
                }
            });

            return response()->json(["success" => true, "data" => $doctor->load('disponibilidades')]);
        } catch (\Exception $e) {
            return response()->json(["success" => false, "message" => $e->getMessage()], 500);
        }
    }

    public function destroy($id): JsonResponse
    {
        $doctor = Doctor::findOrFail($id);
        $doctor->user->delete(); // OnDelete Cascade debería limpiar lo demás
        return response()->json(["success" => true, "message" => "Eliminado"]);
    }



}