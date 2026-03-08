<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Farmacia;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class FarmaciaController extends Controller
{
    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $farmacias = Farmacia::with('user:id,name,email,foto,latitud,longitud,f_nacimiento')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $farmacias->map(function ($f) {
                return [
                    'id' => $f->id,
                    'nom_farmacia' => $f->nom_farmacia,
                    'rfc' => $f->rfc,
                    'telefono' => $f->telefono,
                    'horario' => $f->horario,
                    'dias_op' => $f->dias_op,
                    'descripcion' => $f->descripcion,
                    'created_at' => $f->created_at,
                    'dueño' => [
                        'id' => $f->user->id,
                        'nombre' => $f->user->name,
                        'email' => $f->user->email,
                        'fecha_nacimiento' => $f->user->f_nacimiento,
                        'foto' => $f->user->foto ? asset('storage/' . $f->user->foto) : null,
                        'ubicacion' => [
                            'lat' => $f->user->latitud,
                            'lng' => $f->user->longitud,
                        ],
                    ],
                ];
            })
        ], 200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            // Datos del usuario
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'f_nacimiento' => 'required|date|before:-18 years',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',

            // Datos de la farmacia
            'nom_farmacia' => 'required|string|max:255',
            'rfc' => 'required|string|max:13|unique:farmacias,rfc',
            'telefono' => 'required|string|max:55',
            'ario' => 'required|string|max:255',
            'dias_op' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'latitud' => 'required|numeric|between:-90,90',
            'longitud' => 'required|numeric|between:-180,180',
        ]);

        try {
            return DB::transaction(function () use ($validated, $request) {
                // Crear usuario
                $user = User::create([
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'password' => Hash::make($validated['password']),
                    'role' => 'farmacia',
                    'estado' => true,
                    'f_nacimiento' => $validated['f_nacimiento'],
                    'latitud' => $validated['latitud'],
                    'longitud' => $validated['longitud'],
                ]);

                // Subir foto si existe
                if ($request->hasFile('foto')) {
                    $user->foto = $request->file('foto')->store('users', 'public');
                    $user->save();
                }

                // Crear farmacia
                $farmacia = Farmacia::create([
                    'user_id' => $user->id,
                    'nom_farmacia' => $validated['nom_farmacia'],
                    'rfc' => $validated['rfc'],
                    'telefono' => $validated['telefono'],
                    'horario' => $validated['horario'],
                    'dias_op' => $validated['dias_op'],
                    'descripcion' => $validated['descripcion'],
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Farmacia creada exitosamente',
                    'data' => $farmacia->load('user'),
                ], 201);
            });
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear la farmacia',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * @param Farmacia $farmacia
     * @return JsonResponse
     */
    public function show(Farmacia $farmacia): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $farmacia->load('user'),
        ], 200);
    }

    /**
     * @param Request $request
     * @param Farmacia $farmacia
     * @return JsonResponse
     */
    public function update(Request $request, Farmacia $farmacia): JsonResponse
    {
        $validated = $request->validate([
            // Usuario
            'name' => 'sometimes|required|string|max:100',
            'email' => 'sometimes|required|email|unique:users,email,' . $farmacia->user->id,
            'f_nacimiento' => 'sometimes|required|date|before:-18 years',
            'password' => 'nullable|min:8',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',

            // Farmacia
            'nom_farmacia' => 'sometimes|required|string|max:255',
            'rfc' => 'sometimes|nullable|string|max:13|unique:farmacias,rfc,' . $farmacia->id,
            'telefono' => 'sometimes|required|string|max:55',
            'horario' => 'sometimes|required|string|max:255',
            'dias_op' => 'sometimes|required|string|max:255',
            'descripcion' => 'sometimes|required|string',
            'latitud' => 'sometimes|required|numeric|between:-90,90',
            'longitud' => 'sometimes|required|numeric|between:-180,180',
        ]);

        try {
            DB::transaction(function () use ($request, $validated, $farmacia) {
                $user = $farmacia->user;

                // Actualizar usuario
                if (isset($validated['name']) || isset($validated['email']) || isset($validated['f_nacimiento'])) {
                    $user->update(array_filter([
                        'name' => $validated['name'] ?? null,
                        'email' => $validated['email'] ?? null,
                        'f_nacimiento' => $validated['f_nacimiento'] ?? null,
                        'latitud' => $validated['latitud'] ?? null,
                        'longitud' => $validated['longitud'] ?? null,
                    ]));
                }

                // Contraseña
                if (!empty($validated['password'])) {
                    $user->password = Hash::make($validated['password']);
                    $user->save();
                }

                // Foto
                if ($request->hasFile('foto')) {
                    if ($user->foto) {
                        Storage::disk('public')->delete($user->foto);
                    }
                    $user->foto = $request->file('foto')->store('users', 'public');
                    $user->save();
                }

                // Actualizar farmacia
                $farmacia->update(array_filter([
                    'nom_farmacia' => $validated['nom_farmacia'] ?? null,
                    'rfc' => $validated['rfc'] ?? null,
                    'telefono' => $validated['telefono'] ?? null,
                    'horario' => $validated['horario'] ?? null,
                    'dias_op' => $validated['dias_op'] ?? null,
                    'descripcion' => $validated['descripcion'] ?? null,
                ]));
            });

            return response()->json([
                'success' => true,
                'message' => 'Farmacia actualizada exitosamente',
                'data' => $farmacia->load('user'),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar la farmacia',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * @param Farmacia $farmacia
     * @return JsonResponse
     */
    public function destroy(Farmacia $farmacia): JsonResponse
    {
        try {
            DB::transaction(function () use ($farmacia) {
                $user = $farmacia->user;
                $farmacia->delete();
                if ($user) {
                    $user->delete();
                }
            });

            return response()->json([
                'success' => true,
                'message' => 'Farmacia y usuario eliminados correctamente',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la farmacia',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}