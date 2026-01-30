<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\DoctorStoreRequest;
use App\Http\Requests\API\DoctorUpdateRequest;
use App\Http\Resources\API\DoctorResource;
use App\Models\Doctor;
use Illuminate\Http\JsonResponse; // <--- Usamos JsonResponse
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection; // Para las colecciones

class DoctorController extends Controller
{
    // CAMBIO 1: Retorno AnonymousResourceCollection y uso de ::collection
    public function index(): AnonymousResourceCollection
    {
        // Para listas, usamos 'collection', no 'new DoctorResource'
        return DoctorResource::collection(Doctor::all());
    }

    // CAMBIO 2: Retorno JsonResponse o el Resource directamente
    public function store(DoctorStoreRequest $request): DoctorResource
    {
        $doctor = Doctor::create($request->validated());

        return new DoctorResource($doctor);
    }

    // CAMBIO 3: Uso de Route Model Binding correctamente
    public function show(Doctor $doctor): DoctorResource
    {
        // No necesitas Doctor::find($id). Laravel ya inyectó el modelo en $doctor
        return new DoctorResource($doctor);
    }

    public function update(DoctorUpdateRequest $request, Doctor $doctor): DoctorResource
    {
        // Usamos $doctor (singular) para mayor claridad
        $doctor->update($request->validated());

        return new DoctorResource($doctor);
    }

    public function destroy(Doctor $doctor): JsonResponse
    {
        $doctor->delete();

        // response()->noContent() devuelve un status 204
        return response()->json(null, 204);
    }
}