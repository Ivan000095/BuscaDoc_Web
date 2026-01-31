<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\DoctorStoreRequest;
use App\Http\Requests\API\DoctorUpdateRequest;
use App\Http\Resources\API\DoctorResource;
use App\Models\Doctor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class DoctorController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $doctores = Doctor::with(['user', 'especialidades'])->get();

        return DoctorResource::collection($doctores);
    }

    public function store(DoctorStoreRequest $request): DoctorResource
    {
        // Iniciamos una "Transacción" (Todo o Nada)
        $doctor = DB::transaction(function () use ($request) {
            
            // PASO 1: Guardar la Imagen (si viene)
            $rutaFoto = null;
            if ($request->hasFile("image")) {
                $rutaFoto = $request->file("image")->store('users', 'public');
            }

            // PASO 2: Crear el USUARIO (Tabla users)
            // Aquí van name, email, password, etc.
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

            $newDoctor = Doctor::create([
                'user_id' => $user->id, // <--- ¡AQUÍ SE SOLUCIONA TU ERROR!
                'cedula' => $request->cedula,
                'descripcion' => $request->descripcion,
                'costo' => $request->costos,
                'idiomas' => $request->idioma,
                'horario_entrada' => $request->horarioentrada,
                'horario_salida' => $request->horariosalida,
            ]);

            if ($request->filled('especialidad_id')) {
                $newDoctor->especialidades()->attach($request->especialidad_id);
            }

            return $newDoctor;
        });

        return new DoctorResource($doctor->load(['user', 'especialidades']));
    }

    public function show(Doctor $doctor): DoctorResource
    {
        $doctor->load(['User', 'especialidades']);
        return new DoctorResource($doctor);
    }

    public function update(DoctorUpdateRequest $request, Doctor $doctor): DoctorResource
    {
        $updatedDoctor = DB::transaction(function () use ($request, $doctor) {
            $user = $doctor->user;

            $userData = [
                'name' => $request->name,
                'email' => $request->email,
                'f_nacimiento' => $request->fecha,
                'latitud' => $request->latitud,
                'longitud' => $request->longitud,
            ];

            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }

            if ($request->hasFile("image")) {    
                $userData['foto'] = $request->file("image")->store('users', 'public');
            }

            $user->update($userData);

            $doctor->update([
                'cedula' => $request->cedula,
                'descripcion' => $request->descripcion,
                'costo' => $request->costos,
                'idiomas' => $request->idioma,
                'horario_entrada' => $request->horarioentrada,
                'horario_salida' => $request->horariosalida,
            ]);
            if ($request->filled('especialidad_id')) {
                $doctor->especialidades()->sync([$request->especialidad_id]);
            }

            return $doctor;
        });

        return new DoctorResource($updatedDoctor->load(['user', 'especialidades']));
    }

    public function destroy(Doctor $doctor): JsonResponse
    {
        DB::transaction(function () use ($doctor) {
            $user = $doctor->user;
            $doctor->especialidades()->detach();
            $doctor->delete();
            if ($user) {
                if ($user->foto) {
                    // Asegúrate de importar: use Illuminate\Support\Facades\Storage;
                    Storage::disk('public')->delete($user->foto);
                }               
                $user->delete();
            }
        });
        return response()->json(null, 204);
    }
}