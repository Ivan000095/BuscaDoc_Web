<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $usuario = User::find($id);

            if (!$usuario) {
                return response()->json([
                    "success" => false,
                    "message" => "Usuario no encontrado",
                ], 404);
            }

            $data = [];

            if ($usuario->role === 'doctor') {
                $usuario->load([
                    'doctor.especialidades',
                    'doctor.reviews.respuestas',
                    'doctor.reviews.autor',
                    'doctor.questions.respuestas',
                    'doctor.questions.autor'
                ]);

                $doctor = $usuario->doctor;

                if (!$doctor) {
                    return response()->json([
                        "success" => false,
                        "message" => "El perfil médico de este usuario no está configurado",
                    ], 404);
                }

                $promedio = round($doctor->reviews->avg('calificacion') ?? 0, 1);

                $data = [
                    "id" => $usuario->id,
                    "doctor_id" => $doctor->id,
                    "role" => "doctor",
                    "name" => $usuario->name ?? 'Sin nombre',
                    "especialidad" => $doctor->especialidades->pluck('nombre')->join(', '),
                    "descripcion" => $doctor->descripcion,
                    "fecha" => $usuario->f_nacimiento ?? null,
                    "image" => $usuario->foto ? "http://127.0.0.1:8000/storage/" . $usuario->foto : null,
                    "promedio" => $promedio,
                    "cedula" => $doctor->cedula,
                    "costos" => '$' . number_format($doctor->costo, 2),
                    "horarioentrada" => $doctor->horario_entrada,
                    "horariosalida" => $doctor->horario_salida,
                    "latitud" => $usuario->latitud,
                    "longitud" => $usuario->longitud,

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
            } elseif ($usuario->role === 'paciente') {
                $usuario->load(['patient']);

                $data = [
                    "id" => $usuario->id,
                    "role" => "paciente",
                    "name" => $usuario->name ?? 'Sin nombre',
                    "email" => $usuario->email,
                    "fecha" => $usuario->f_nacimiento ?? null,
                    "image" => $usuario->foto ? "http://10.0.2.2:8000/storage/" . $usuario->foto : null,
                    "latitud" => $usuario->latitud,
                    "longitud" => $usuario->longitud,
                    "tipo_sangre" => $usuario->patient->tipo_sangre,
                    "alergias" => $usuario->patient->alergias,
                    "cirugias" => $usuario->patient->cirugias,
                    "padecimientos" => $usuario->patient->padecimientos,
                    "habitos" => $usuario->patient->habitos,
                    "contacto_emergencia" => $usuario->patient->contacto_emergencia,
                ];
            }

            // 4. Si es Admin u otro rol
            else {
                return response()->json([
                    "success" => false,
                    "message" => "El rol de este usuario no es válido para esta consulta",
                ], 400);
            }

            // Devolvemos la data unificada
            return response()->json([
                "success" => true,
                "data" => $data
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                "success" => false,
                "message" => "Error al obtener la información del perfil",
                "error" => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        DB::beginTransaction();

        try {
            $usuario = User::find($id);

            if (!$usuario) {
                return response()->json([
                    "success" => false,
                    "message" => "Usuario no encontrado",
                ], 404);
            }

            $usuario->name = $request->input('name', $usuario->name);
            $usuario->email = $request->input('email', $usuario->email);

            $usuario->save();

            if ($usuario->role === 'doctor') {

                $doctor = $usuario->doctor;

                if (!$doctor) {
                    DB::rollBack();
                    return response()->json([
                        "success" => false,
                        "message" => "El perfil médico de este usuario no está configurado",
                    ], 404);
                }

                $doctor->descripcion = $request->input('descripcion', $doctor->descripcion);
                $doctor->cedula = $request->input('cedula', $doctor->cedula);

                $costoFormato = str_replace(['$', ','], '', $request->input('costos', $doctor->costo));
                $doctor->costo = floatval($costoFormato);

                $doctor->horario_entrada = $request->input('horarioentrada', $doctor->horario_entrada);
                $doctor->horario_salida = $request->input('horariosalida', $doctor->horario_salida);
                $doctor->save();

                if ($request->has('especialidad_id')) {
                    $doctor->especialidades()->sync([$request->input('especialidad_id')]);
                }

            } elseif ($usuario->role === 'paciente') {

                $paciente = $usuario->patient;

                if (!$paciente) {
                    $paciente = new \App\Models\Paciente();
                    $paciente->user_id = $usuario->id;
                }

                $paciente->tipo_sangre = $request->input('tipo_sangre', $paciente->tipo_sangre);
                $paciente->alergias = $request->input('alergias', $paciente->alergias);
                $paciente->cirugias = $request->input('cirugias', $paciente->cirugias);
                $paciente->padecimientos = $request->input('padecimientos', $paciente->padecimientos);
                $paciente->habitos = $request->input('habitos', $paciente->habitos);
                $paciente->contacto_emergencia = $request->input('contacto_emergencia', $paciente->contacto_emergencia);
                $paciente->save();

            } else {
                DB::rollBack();
                return response()->json([
                    "success" => false,
                    "message" => "El rol de este usuario no es válido para actualización",
                ], 400);
            }

            DB::commit();

            return response()->json([
                "success" => true,
                "message" => "Perfil actualizado correctamente",
                "data" => [
                    "id" => $usuario->id,
                    "name" => $usuario->name,
                    "role" => $usuario->role
                ]
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                "success" => false,
                "message" => "Error al actualizar la información del perfil",
                "error" => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
