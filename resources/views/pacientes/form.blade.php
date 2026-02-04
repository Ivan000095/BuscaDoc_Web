<x-layout>
    <div class="container pb-5">
        {{-- Encabezado --}}
        <div class="row my-5 text-center">
            <div class="col-12">
                <h1 class="fw-bold text-primary display-5">{{ isset($paciente) ? 'Editar' : 'Registrar Nuevo' }} Paciente</h1>
                <p class="text-muted">Información médica y de contacto del paciente</p>
            </div>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger shadow-sm">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li><i class="bi bi-exclamation-triangle-fill me-2"></i>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST"
            action="{{ isset($paciente) ? route('pacientes.update', $paciente->id) : route('pacientes.store') }}"
            class="row g-4 justify-content-center needs-validation" novalidate>

            @csrf
            @if(isset($paciente))
                @method('PUT')
            @endif

            <div class="col-lg-10">
                <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                    <div class="card-header bg-primary text-white py-3">
                        <h5 class="mb-0 fw-bold"><i class="bi bi-person-plus-fill me-2"></i> Datos Generales</h5>
                    </div>
                    <div class="card-body p-4 bg-white">
                        <div class="row g-4">
                            {{-- Nombre --}}
                            <div class="col-md-6">
                                <label for="name" class="form-label fw-semibold">Nombre Completo</label>
                                <input type="text" name="name" id="name" class="form-control form-control-lg rounded-3" 
                                    value="{{ old('name', $paciente->user->name ?? '') }}" required placeholder="Ej. Juan Pérez">
                            </div>

                            {{-- Email --}}
                            <div class="col-md-6">
                                <label for="email" class="form-label fw-semibold">Correo Electrónico</label>
                                <input type="email" name="email" id="email" class="form-control form-control-lg rounded-3" 
                                    value="{{ old('email', $paciente->user->email ?? '') }}" required placeholder="juan@ejemplo.com">
                            </div>

                            {{-- Password (solo en creación) --}}
                            @if(!isset($paciente))
                            <div class="col-md-6">
                                <label for="password" class="form-label fw-semibold">Contraseña</label>
                                <input type="password" name="password" id="password" class="form-control form-control-lg rounded-3" required>
                            </div>
                            @endif

                            {{-- Tipo de Sangre --}}
                            <div class="col-md-6">
                                <label for="tipo_sangre" class="form-label fw-semibold">Tipo de Sangre</label>
                                <select name="tipo_sangre" id="tipo_sangre" class="form-select form-select-lg rounded-3" required>
                                    <option value="" selected disabled>Seleccione...</option>
                                    @foreach(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $tipo)
                                        <option value="{{ $tipo }}" {{ old('tipo_sangre', $paciente->tipo_sangre ?? '') == $tipo ? 'selected' : '' }}>
                                            {{ $tipo }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                                {{-- Contacto de Emergencia --}}
                                    <div class="col-md-6">
                                        <label for="contacto_emergencia" class="form-label fw-semibold">Contacto de Emergencia</label>
                                        <input type="text" name="contacto_emergencia" id="contacto_emergencia" class="form-control form-control-lg rounded-3" 
                                            value="{{ old('contacto_emergencia', $paciente->contacto_emergencia ?? '') }}" maxlength="10" placeholder="Número de 10 dígitos">
                                    </div>

                                    {{-- Alergias --}}
                                    <div class="col-md-6">
                                        <label for="alergias" class="form-label fw-semibold">Alergias Conocidas</label>
                                        <textarea name="alergias" id="alergias" class="form-control rounded-3" rows="2">{{ old('alergias', $paciente->alergias ?? '') }}</textarea>
                                    </div>

                                    {{-- Cirugías --}}
                                    <div class="col-md-4">
                                        <label for="cirugias" class="form-label fw-semibold">Cirugías Previas</label>
                                        <textarea name="cirugias" id="cirugias" class="form-control rounded-3" rows="3">{{ old('cirugias', $paciente->cirugias ?? '') }}</textarea>
                                    </div>

                                    {{-- Padecimientos --}}
                                    <div class="col-md-4">
                                        <label for="padecimientos" class="form-label fw-semibold">Padecimientos Crónicos</label>
                                        <textarea name="padecimientos" id="padecimientos" class="form-control rounded-3" rows="3">{{ old('padecimientos', $paciente->padecimientos ?? '') }}</textarea>
                                    </div>

                                    {{-- Hábitos --}}
                                    <div class="col-md-4">
                                        <label for="habitos" class="form-label fw-semibold">Hábitos (Fumador, Ejercicio, etc.)</label>
                                        <textarea name="habitos" id="habitos" class="form-control rounded-3" rows="3">{{ old('habitos', $paciente->habitos ?? '') }}</textarea>
                                    </div>
                        </div>
                    </div>
                    
                    <div class="card-footer bg-light p-4 text-end border-0">
                        <a href="{{ route('pacientes.index') }}" class="btn btn-outline-secondary btn-lg px-4 me-2 rounded-pill">Cancelar</a>
                        <button type="submit" class="btn btn-primary btn-lg px-5 rounded-pill shadow">
                            <i class="bi bi-save me-2"></i>{{ isset($paciente) ? 'Actualizar' : 'Guardar' }} Paciente
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</x-layout>