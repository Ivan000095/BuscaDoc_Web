<x-layout>
<div class="container py-5">
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('expedientes.index') }}" class="btn btn-white btn-sm rounded-pill shadow-sm me-3">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h4 class="fw-bold text-navy mb-0">Actualizar Expediente</h4>
            <p class="text-muted small mb-0">Modifica la información médica de: <span class="fw-bold text-primary">{{ $expediente->nombre_completo }}</span></p>
        </div>
    </div>

    <form action="{{ route('expedientes.update', $expediente->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row g-4">
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="p-4" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);">
                        <div class="d-flex align-items-center text-white">
                            <div class="bg-white bg-opacity-10 p-2 rounded-3 me-3">
                                <i class="bi bi-person-lines-fill fs-5 text-info"></i>
                            </div>
                            <h6 class="fw-bold mb-0">Datos Personales</h6>
                        </div>
                    </div>
                    <div class="card-body p-4 bg-white">
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-navy">Nombre Completo</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0 rounded-start-pill"><i class="bi bi-person text-muted"></i></span>
                                <input type="text" name="nombre_completo" class="form-control bg-light border-0 rounded-end-pill" value="{{ $expediente->nombre_completo }}" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-navy">Fecha de Nacimiento</label>
                            <input type="date" name="fecha_nacimiento" class="form-control rounded-pill" value="{{ $expediente->fecha_nacimiento }}" max="{{ date('Y-m-d') }}" required>
                        </div>

                        <div class="row">
                            <div class="col-6 mb-3">
                                <label class="form-label small fw-bold text-navy">Género</label>
                                <select name="genero" class="form-select rounded-pill" required>
                                    <option value="masculino" {{ $expediente->genero == 'masculino' ? 'selected' : '' }}>Masculino</option>
                                    <option value="femenino" {{ $expediente->genero == 'femenino' ? 'selected' : '' }}>Femenino</option>
                                </select>
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label small fw-bold text-navy">Tipo de Sangre</label>
                                <select name="tipo_sangre" class="form-select rounded-pill">
                                    @foreach(['O+', 'O-', 'A+', 'A-', 'B+', 'B-', 'AB+', 'AB-'] as $tipo)
                                        <option value="{{ $tipo }}" {{ $expediente->tipo_sangre == $tipo ? 'selected' : '' }}>{{ $tipo }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        @if(Auth::user()->role == 'doctor')
                            <input type="hidden" name="parentesco" value="Paciente">
                        @else
                            <div class="mb-0">
                                <label class="form-label small fw-bold text-navy">Parentesco / Relación</label>
                                <select name="parentesco" class="form-select rounded-pill" required>
                                    @foreach(['Expediente Propio', 'Hijo/a', 'Padre/Madre', 'Cónyuge', 'Otro'] as $relacion)
                                        <option value="{{ $relacion }}" {{ $expediente->parentesco == $relacion ? 'selected' : '' }}>{{ $relacion }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-lg-7">
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100">
                    <div class="d-flex align-items-center mb-4">
                        <div class="bg-primary bg-opacity-10 p-2 rounded-3 me-3">
                            <i class="bi bi-shield-check text-primary fs-5"></i>
                        </div>
                        <h6 class="fw-bold mb-0 text-navy">Detalles de Salud</h6>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-bold text-navy d-flex align-items-center">
                            <i class="bi bi-exclamation-triangle-fill text-danger me-2"></i> Alergias
                        </label>
                        <textarea name="alergias" class="form-control rounded-4 bg-light border-0" rows="3" placeholder="Ej. Penicilina, alimentos...">{{ $expediente->alergias }}</textarea>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-bold text-navy d-flex align-items-center">
                            <i class="bi bi-activity text-warning me-2"></i> Padecimientos Crónicos
                        </label>
                        <textarea name="padecimientos" class="form-control rounded-4 bg-light border-0" rows="3" placeholder="Ej. Diabetes, Hipertensión...">{{ $expediente->padecimientos_cronicos }}</textarea>
                    </div>

                    <div class="mb-0">
                        <label class="form-label small fw-bold text-navy d-flex align-items-center">
                            <i class="bi bi-heart-pulse-fill text-success me-2"></i> Hábitos de Salud
                        </label>
                        <textarea name="habitos" class="form-control rounded-4 bg-light border-0" rows="3" placeholder="Ej. Ejercicio regular, fumador...">{{ $expediente->habitos_salud }}</textarea>
                    </div>
                </div>
            </div>

            <div class="col-12 text-end mt-2">
                <a href="{{ route('expedientes.index') }}" class="btn btn-light rounded-pill px-4 me-2">Cancelar</a>
                <button type="submit" class="btn btn-navy rounded-pill px-5 shadow-sm">
                    <i class="bi bi-check-circle me-2"></i>Guardar Cambios
                </button>
            </div>
        </div>
    </form>
</div>

<style>
    .text-navy { color: #0f172a; }
    .btn-navy { background-color: #0f172a; color: white; border: none; transition: all 0.3s ease; }
    .btn-navy:hover { background-color: #1e293b; color: white; transform: translateY(-2px); }
    .form-control:focus, .form-select:focus { 
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.1); 
        background-color: #fff !important; 
        border-color: #0d6efd;
    }
    .input-group-text { border: none; }
</style>
</x-layout>