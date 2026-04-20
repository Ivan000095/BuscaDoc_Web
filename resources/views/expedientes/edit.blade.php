<x-layout>
    <div class="container py-5">
        
        {{-- ENCABEZADO --}}
        <div class="d-flex align-items-center mb-4 pb-2 border-bottom">
            <a href="{{ route('expedientes.index') }}" class="btn btn-light rounded-circle shadow-sm me-3 d-flex align-items-center justify-content-center btn-back-custom" style="width: 45px; height: 45px;">
                <x-mcr-angle-left style="width: 1.2rem; color: var(--brand-navy);" />
            </a>
            <div>
                <h3 class="fw-bold text-navy mb-0">Actualizar Expediente</h3>
                <p class="text-muted small mb-0">Modificando la información de: <span class="fw-bold px-2 py-1 bg-navy-subtle rounded text-navy">{{ $expediente->nombre_completo }}</span></p>
            </div>
        </div>

        <form action="{{ route('expedientes.update', $expediente->id) }}" method="POST" class="form-modern">
            @csrf
            @method('PUT')

            <div class="row g-4 mb-4">
                {{-- COLUMNA IZQUIERDA: Datos Personales --}}
                <div class="col-lg-5">
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100">
                        {{-- Cabecera de la tarjeta --}}
                        <div class="p-4 text-white d-flex align-items-center" style="background: linear-gradient(135deg, var(--brand-navy) 0%, var(--brand-navy-light) 100%);">
                            <div class="bg-white bg-opacity-25 p-2 rounded-3 me-3 d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                                <x-mcr-user-alt style="width: 1.5rem;" />
                            </div>
                            <h5 class="fw-bold mb-0">Datos Personales</h5>
                        </div>
                        
                        <div class="card-body p-4 bg-white">
                            <div class="mb-4">
                                <label class="form-label small fw-bold text-secondary text-uppercase tracking-wider">Nombre Completo</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0 rounded-start-4 ps-3">
                                        <x-mcr-user-alt style="width: 1.2rem;" />
                                    </span>
                                    <input type="text" name="nombre_completo" class="form-control bg-light border-0 rounded-end-4 py-2 custom-input shadow-none" value="{{ $expediente->nombre_completo }}" required>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label small fw-bold text-secondary text-uppercase tracking-wider">Fecha de Nacimiento</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0 rounded-start-4 ps-3">
                                        <x-mcr-calendar class="text-muted" style="width: 1.2rem;" />
                                    </span>
                                    <input type="date" name="fecha_nacimiento" class="form-control bg-light border-0 rounded-end-4 py-2 custom-input shadow-none" value="{{ $expediente->fecha_nacimiento }}" max="{{ date('Y-m-d') }}" required>
                                </div>
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-6">
                                    <label class="form-label small fw-bold text-secondary text-uppercase tracking-wider">Género</label>
                                    <select name="genero" class="form-select rounded-4 bg-light border-0 py-2 custom-input shadow-none" required>
                                        <option value="masculino" {{ $expediente->genero == 'masculino' ? 'selected' : '' }}>Masculino</option>
                                        <option value="femenino" {{ $expediente->genero == 'femenino' ? 'selected' : '' }}>Femenino</option>
                                    </select>
                                </div>
                                <div class="col-6">
                                    <label class="form-label small fw-bold text-secondary text-uppercase tracking-wider">Tipo de Sangre</label>
                                    <select name="tipo_sangre" class="form-select rounded-4 bg-light border-0 py-2 custom-input shadow-none">
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
                                    <label class="form-label small fw-bold text-secondary text-uppercase tracking-wider">Parentesco</label>
                                    <select name="parentesco" class="form-select rounded-4 bg-light border-0 py-2 custom-input shadow-none" required>
                                        @foreach(['Yo mismo', 'Hijo/a', 'Padre/Madre', 'Cónyuge', 'Otro'] as $relacion)
                                            <option value="{{ $relacion }}" {{ $expediente->parentesco == $relacion ? 'selected' : '' }}>{{ $relacion }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- COLUMNA DERECHA: Detalles Médicos --}}
                <div class="col-lg-7">
                    <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white h-100">
                        
                        <div class="d-flex align-items-center mb-4 pb-3 border-bottom">
                            <div class="bg-primary-subtle text-primary p-2 rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                                <x-mcr-shield style="width: 1.5rem;" />
                            </div>
                            <h5 class="fw-bold mb-0 text-navy">Detalles de Salud</h5>
                        </div>

                        <div class="mb-4">
                            <label class="form-label small fw-bold text-secondary text-uppercase tracking-wider d-flex align-items-center">
                                <x-mcr-triangle-exclamation class="text-warning me-2" style="width: 1.1rem;" /> Alergias Conocidas
                            </label>
                            <textarea name="alergias" class="form-control rounded-4 bg-light border-0 p-3 custom-input shadow-none" rows="3" placeholder="Ej. Penicilina, alimentos...">{{ $expediente->alergias }}</textarea>
                        </div>

                        <div class="mb-4">
                            <label class="form-label small fw-bold text-secondary text-uppercase tracking-wider d-flex align-items-center">
                                <x-mcr-heart class="text-info me-2" style="width: 1.1rem;" /> Padecimientos Crónicos
                            </label>
                            <textarea name="padecimientos_cronicos" class="form-control rounded-4 bg-light border-0 p-3 custom-input shadow-none" rows="3" placeholder="Ej. Diabetes, Hipertensión...">{{ $expediente->padecimientos_cronicos }}</textarea>
                        </div>

                        <div class="mb-0">
                            <label class="form-label small fw-bold text-secondary text-uppercase tracking-wider d-flex align-items-center">
                                <x-mcr-mug class="text-success me-2" style="width: 1.1rem;" /> Hábitos de Salud
                            </label>
                            <textarea name="habitos_salud" class="form-control rounded-4 bg-light border-0 p-3 custom-input shadow-none" rows="3" placeholder="Ej. Ejercicio regular, fumador...">{{ $expediente->habitos_salud }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            {{-- BOTONES DE ACCIÓN --}}
            <div class="d-flex justify-content-end align-items-center bg-white p-4 rounded-4 shadow-sm">
                <a href="{{ route('expedientes.index') }}" class="btn btn-light rounded-pill px-4 py-2 me-3 fw-bold text-muted transition-hover">
                    Cancelar
                </a>
                <button type="submit" class="btn btn-navy rounded-pill px-5 py-2 fw-bold shadow-sm d-flex align-items-center transition-hover">
                    <x-mcl-check-circle class="me-2 icon-white" style="width: 1.2rem;" /> Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</x-layout>