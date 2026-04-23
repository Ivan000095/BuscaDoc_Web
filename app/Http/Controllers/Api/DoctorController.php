<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\User;
use App\Models\Especialidad;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class DoctorController extends Controller
{
    /**
     * Listar doctores con filtros básicos
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $hoy = \Carbon\Carbon::now('America/Mexico_City');
            $diaSemana = $hoy->dayOfWeek;
            $fechaHoy = $hoy->toDateString();

            $query = Doctor::with([
                'user', 
                'especialidades',
                'disponibilidades',
                'excepciones' => function($q) use ($fechaHoy) {
                    $q->where('fecha', $fechaHoy);
                }
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

            if ($request->has("especialidad_id")) {
                $query->whereHas('especialidades', function ($q) use ($request) {
                    $q->where('especialidads.id', $request->input("especialidad_id"));
                });
            }

            $sortBy = $request->input("sort_by", "created_at");
            $sortDirection = $request->input("sort_direction", "desc");
            $query->orderBy($sortBy, $sortDirection);
            
            // 1. Obtenemos TODOS los registros sin límite de páginas
            $doctors = $query->get();

            // 2. Aplicamos la transformación directo a la colección
            $doctors->transform(function ($doctor) use ($diaSemana) {
                $entrada = null;
                $salida = null;

                $excepcion = $doctor->excepciones->first();
                
                $disponibilidadHoy = $doctor->disponibilidades->where('dia_semana', $diaSemana)->first();

                if ($excepcion) {
                    if ($excepcion->trabaja) {
                        $entrada = $excepcion->hora_inicio;
                        $salida = $excepcion->hora_fin;
                    }
                } elseif ($disponibilidadHoy) {
                    $entrada = $disponibilidadHoy->hora_inicio;
                    $salida = $disponibilidadHoy->hora_fin;
                }

                $horaEntrada = $entrada ? \Carbon\Carbon::parse($entrada)->format('H:i') : 'Descanso';
                $horaSalida = $salida ? \Carbon\Carbon::parse($salida)->format('H:i') : '';

                return [
                    "id" => $doctor->id,
                    "user_id" => $doctor->user->id,
                    "name" => $doctor->user->name,
                    "especialidad" => $doctor->especialidades->pluck('nombre')->join(', '),
                    "descripcion" => \Illuminate\Support\Str::limit($doctor->descripcion, 100),
                    "fecha" => $doctor->user->f_nacimiento,
                    "image" => $doctor->user->foto ? asset('storage/' . $doctor->user->foto) : null,
                    "promedio" => round($doctor->reviews->avg('calificacion') ?? 0, 1),
                    "cedula" => $doctor->cedula,
                    "role" => $doctor->user->role,
                    "costos" => number_format($doctor->costo, 2),
                    "citas" => $doctor->citas,
                    
                    "horarioentrada" => $horaEntrada,
                    "horariosalida" => $horaSalida,
                    
                    "idioma" => $doctor->idiomas,
                    "latitud" => $doctor->user->latitud,
                    "longitud" => $doctor->user->longitud,

                    "disponibilidades" => $doctor->disponibilidades,
                ];
            });

            // 3. Devolvemos la data limpia sin metadata de paginación
            return response()->json([
                "success" => true,
                "data" => $doctors,
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
     * Mostrar un doctor específico
     */
   public function show($id)
    {
        $doctor = Doctor::with(['user', 'especialidades', 'disponibilidades', 'excepciones'])
                ->findOrFail($id);

        // Mapeamos los días de la semana para que Flutter los entienda fácil
        $dias = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
        $tablaHorarios = [];

        foreach ($doctor->disponibilidades as $disp) {
            $tablaHorarios[] = [
                'dia' => $dias[$disp->dia_semana],
                'rango' => \Carbon\Carbon::parse($disp->hora_inicio)->format('H:i') . ' - ' . \Carbon\Carbon::parse($disp->hora_fin)->format('H:i')
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $doctor,
            'horarios_semanales' => $tablaHorarios // Agregamos esto para la tabla
        ]);
    }

    /**
     * Registrar nuevo doctor
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                "name" => "required|string|max:100",
                "email" => "required|email|unique:users,email",
                "password" => "required|min:8",
                "fecha" => "required|date|before:-18 years",
                "image" => "nullable|image|mimes:jpg,jpeg,png|max:5120",
                "especialidad_id" => "required|exists:especialidads,id",
                "cedula" => "required|string|max:50|unique:doctors,cedula",
                "descripcion" => "required|string|max:1000",
                "costos" => "required|numeric|min:0",
                "horarioentrada" => "required|date_format:H:i",
                "horariosalida" => "required|date_format:H:i",
                "idioma" => "nullable|string|max:100",
                "latitud" => "nullable|numeric",
                "longitud" => "nullable|numeric",
            ]);

            $doctorRegistrado = null;

            DB::transaction(function () use ($request, &$doctorRegistrado) {
                // Subir imagen si existe
                $rutaFoto = null;
                if ($request->hasFile("image")) {
                    $rutaFoto = $request->file("image")->store('users', 'public');
                }

                // Crear usuario
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

                // Crear perfil de doctor
                $doctor = Doctor::create([
                    'user_id' => $user->id,
                    'cedula' => $request->cedula,
                    'descripcion' => $request->descripcion,
                    'costo' => $request->costos,
                    'idiomas' => $request->idioma,
                    'horario_entrada' => $request->horarioentrada,
                    'horario_salida' => $request->horariosalida,
                    'citas' => $request->boolean('citas') ?? false,
                ]);

                // Relacionar especialidad
                $doctor->especialidades()->attach($request->especialidad_id);

                $doctorRegistrado = $doctor->load(['user', 'especialidades']);
            });

            return response()->json([
                "success" => true,
                "message" => "Doctor registrado correctamente",
                "data" => $doctorRegistrado
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                "success" => false,
                "message" => "Error de validación",
                "errors" => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                "success" => false,
                "message" => "Error al registrar el doctor",
                "error" => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Actualizar doctor
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $doctor = Doctor::with('user')->find($id);

            if (!$doctor) {
                return response()->json([
                    "success" => false,
                    "message" => "Doctor no encontrado",
                ], 404);
            }

            $validated = $request->validate([
                'cedula' => 'sometimes|string|max:50|unique:doctors,cedula,' . $doctor->id,
                'descripcion' => 'sometimes|string|max:1000',
                'costos' => 'sometimes|numeric|min:0',
                'horarioentrada' => 'sometimes|date_format:H:i',
                'horariosalida' => 'sometimes|date_format:H:i',
                'idioma' => 'nullable|string|max:100',
                'latitud' => 'nullable|numeric',
                'longitud' => 'nullable|numeric',
                'image' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
                // Datos del usuario relacionado
                'name' => 'sometimes|string|max:100',
                'email' => 'sometimes|email|unique:users,email,' . $doctor->user->id,
                'password' => 'nullable|min:8',
                'fecha' => 'sometimes|date|before:-18 years',
            ]);

            DB::transaction(function () use ($request, $doctor, $validated) {
                // Actualizar imagen si se envía nueva
                if ($request->hasFile("image")) {
                    // Eliminar imagen anterior si existe
                    if ($doctor->user->foto && Storage::disk('public')->exists($doctor->user->foto)) {
                        Storage::disk('public')->delete($doctor->user->foto);
                    }
                    $rutaFoto = $request->file("image")->store('users', 'public');
                    $doctor->user->foto = $rutaFoto;
                }

                // Actualizar datos del usuario
                $doctor->user->update([
                    'name' => $validated['name'] ?? $doctor->user->name,
                    'email' => $validated['email'] ?? $doctor->user->email,
                    'f_nacimiento' => $validated['fecha'] ?? $doctor->user->f_nacimiento,
                    'latitud' => $validated['latitud'] ?? $doctor->user->latitud,
                    'longitud' => $validated['longitud'] ?? $doctor->user->longitud,
                ]);

                // Actualizar contraseña si se proporciona
                if (!empty($validated['password'])) {
                    $doctor->user->password = Hash::make($validated['password']);
                }

                $doctor->user->save();

                // Actualizar datos del doctor
                $doctor->update([
                    'cedula' => $validated['cedula'] ?? $doctor->cedula,
                    'descripcion' => $validated['descripcion'] ?? $doctor->descripcion,
                    'costo' => $validated['costos'] ?? $doctor->costo,
                    'idiomas' => $validated['idioma'] ?? $doctor->idiomas,
                    'horario_entrada' => $validated['horarioentrada'] ?? $doctor->horario_entrada,
                    'horario_salida' => $validated['horariosalida'] ?? $doctor->horario_salida,
                    'latitud' => $validated['latitud'] ?? $doctor->user->latitud,
                    'longitud' => $validated['longitud'] ?? $doctor->user->longitud,
                ]);
            });

            $doctor->refresh()->load(['user', 'especialidades']);

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

    /**
     * Eliminar doctor
     */
    public function destroy($id): JsonResponse
    {
        try {
            $doctor = Doctor::with('user')->find($id);

            if (!$doctor) {
                return response()->json([
                    "success" => false,
                    "message" => "Doctor no encontrado",
                ], 404);
            }

            DB::transaction(function () use ($doctor) {
                // Eliminar imagen del almacenamiento si existe
                if ($doctor->user->foto && Storage::disk('public')->exists($doctor->user->foto)) {
                    Storage::disk('public')->delete($doctor->user->foto);
                }

                // Desvincular especialidades
                $doctor->especialidades()->detach();

                // Eliminar doctor y usuario
                $doctor->delete();
                $doctor->user->delete();
            });

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