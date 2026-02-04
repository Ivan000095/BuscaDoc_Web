<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\DoctorStoreRequest;
use App\Http\Requests\API\DoctorUpdateRequest;
use App\Http\Resources\API\DoctorResource;
use App\Models\Doctor;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class DoctorController extends Controller
{
    public function index(Request $request): Response
    {
        $doctors = Doctor::all();

        return new DoctorResource($Doctor);
    }

    public function store(DoctorStoreRequest $request): Response
    {
        $doctor = Doctor::create($request->validated());

        return new DoctorResource($Doctor);
    }

    public function show(Request $request, Doctor $doctor): Response
    {
        $doctor = Doctor::find($id);

        return new DoctorResource($Doctor);
    }

    public function update(DoctorUpdateRequest $request, Doctor $doctor): Response
    {
        $doctor->update($request->validated());

        return new DoctorResource($Doctor);
    }

    public function destroy(Request $request, Doctor $doctor): Response
    {
        $doctor->delete();

        return response()->noContent();
    }
}
