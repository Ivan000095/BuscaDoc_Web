<?php

namespace App\Http\Controllers;

use App\Http\Requests\DoctorStoreRequest;
use App\Http\Requests\DoctorUpdateRequest;
use App\Models\Doctor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Services\FileService;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;
use App\Models\Especialidad;
use App\Utils;

class DoctorController extends Controller
{
    public function index(Request $request): View
    {
        $doctors = Doctor::all();

        return view('doctores.index', [
            'doctors' => $doctors,
        ]);
    }

    public function create(Request $request): View
    {
        return view('doctor.create');
    }

    public function store(DoctorStoreRequest $request): Response
    {
        $doctor = Doctor::create($request->validated());

        return redirect()->route('doctores.index');
    }

    public function edit(Request $request, Doctor $doctor): Response
    {
        return view('doctor.edit', [
            'doctor' => $doctor,
        ]);
    }

    public function update(DoctorUpdateRequest $request, Doctor $doctor): Response
    {
        $doctor->update($request->validated());

        return redirect()->route('doctores.index');
    }

    public function destroy(Request $request, Doctor $doctor): Response
    {
        $doctor->delete();

        return redirect()->route('doctors.index');
    }
}
