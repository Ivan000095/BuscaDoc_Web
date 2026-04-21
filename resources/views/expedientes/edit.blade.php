<x-layout>
    <style>
        
        :root {
            --buscadoc-navy: #0A3D62;         /* Azul marino principal */
            --buscadoc-navy-light: #1E5F8C;   /* Azul marino claro para degradados */
            --buscadoc-teal: #00A896;         /* Turquesa médico para acentos */
            --buscadoc-teal-light: #E6F6F4;   /* Fondo turquesa muy suave para íconos */
            --buscadoc-bg: #F4F7F9;           /* Fondo general gris/azul muy claro */
            --buscadoc-input: #EAEFF4;        /* Fondo para inputs (menos blanco) */
        }

        .buscadoc-wrapper {
            background-color: var(--buscadoc-bg);
            border-radius: 1.5rem;
        }

        .text-navy { color: var(--buscadoc-navy) !important; }
        .text-teal { color: var(--buscadoc-teal) !important; }
        
        .bg-navy-subtle { 
            background-color: rgba(10, 61, 98, 0.1) !important; 
            color: var(--buscadoc-navy);
        }

        .icon-wrapper-teal {
            background-color: var(--buscadoc-teal-light);
            color: var(--buscadoc-teal);
        }

        .card-buscadoc {
            background-color: #ffffff;
            border: none;
            box-shadow: 0 8px 24px rgba(10, 61, 98, 0.06);
        }

        .card-buscadoc-accent {
            border-top: 5px solid var(--buscadoc-teal);
        }

        .input-buscadoc {
            background-color: var(--buscadoc-input) !important;
            border: 1px solid transparent;
            transition: all 0.3s ease;
        }

        .input-buscadoc:focus {
            background-color: #ffffff !important;
            border-color: var(--buscadoc-teal);
            box-shadow: 0 0 0 0.25rem rgba(0, 168, 150, 0.15);
        }

        .btn-buscadoc {
            background-color: var(--buscadoc-teal);
            color: white;
            border: none;
            transition: all 0.3s ease;
        }

        .btn-buscadoc:hover {
            background-color: #008f7f;
            color: white;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 168, 150, 0.3);
        }
    </style>

    <div class="container py-5">
        <div class="buscadoc-wrapper p-4 p-md-5 shadow-sm">
            
            {{-- ENCABEZADO --}}
            <div class="d-flex align-items-center mb-4 pb-3 border-bottom">
                <a href="{{ route('expedientes.index') }}" class="btn btn-white rounded-circle shadow-sm me-3 d-flex align-items-center justify-content-center transition-hover" style="width: 45px; height: 45px; background-color: white;">
                    <x-mcr-angle-left style="width: 1.2rem; color: var(--buscadoc-navy);" />
                </a>
                <div>
                    <h3 class="fw-bold text-navy mb-0">Actualizar Expediente</h3>
                    <p class="text-muted small mb-0 mt-1">Modificando la información de: <span class="fw-bold px-2 py-1 bg-navy-subtle rounded">{{ $expediente->nombre_completo }}</span></p>
                </div>
            </div>

            <form action="{{ route('expedientes.update', $expediente->id) }}" method="POST" class="form-modern">
                @csrf
                @method('PUT')

                <div class="row g-4 mb-4">
                    {{-- COLUMNA IZQUIERDA: Datos Personales --}}
                    <div class="col-lg-5">
                        <div class="card card-buscadoc rounded-4 overflow-hidden h-100">
                            {{-- Cabecera de la tarjeta --}}
                            <div class="p-4 text-white d-flex align-items-center" style="background: linear-gradient(135deg, var(--buscadoc-navy) 0%, var(--buscadoc-navy-light) 100%);">
                                <div class="bg-white bg-opacity-25 p-2 rounded-3 me-3 d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                                    <x-mcr-user-alt style="width: 1.5rem;" />
                                </div>
                                <h5 class="fw-bold mb-0">Datos Personales</h5>
                            </div>
                            
                            <div class="card-body p-4">
                                <div class="mb-4">
                                    <label class="form-label small fw-bold text-secondary text-uppercase tracking-wider">Nombre Completo</label>
                                    <div class="input-group">
                                        <span class="input-group-text input-buscadoc border-0 rounded-start-4 ps-3">
                                            <x-mcr-user-alt class="text-navy" style="width: 1.2rem;" />
                                        </span>
                                        <input type="text" name="nombre_completo" class="form-control input-buscadoc rounded-end-4 py-2 shadow-none" value="{{ $expediente->nombre_completo }}" required>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label small fw-bold text-secondary text-uppercase tracking-wider">Fecha de Nacimiento</label>
                                    <div class="input-group">
                                        <span class="input-group-text input-buscadoc border-0 rounded-start-4 ps-3">
                                            <x-mcr-calendar class="text-navy" style="width: 1.2rem;" />
                                        </span>
                                        <input type="date" name="fecha_nacimiento" class="form-control input-buscadoc rounded-end-4 py-2 shadow-none" value="{{ $expediente->fecha_nacimiento }}" max="{{ date('Y-m-d') }}" required>
                                    </div>
                                </div>

                                <div class="row g-3 mb-4">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold text-secondary text-uppercase tracking-wider">Género</label>
                                        <select name="genero" class="form-select rounded-4 input-buscadoc py-2 shadow-none" required>
                                            <option value="masculino" {{ $expediente->genero == 'masculino' ? 'selected' : '' }}>Masculino</option>
                                            <option value="femenino" {{ $expediente->genero == 'femenino' ? 'selected' : '' }}>Femenino</option>
                                        </select>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold text-secondary text-uppercase tracking-wider">Tipo de Sangre</label>
                                        <select name="tipo_sangre" class="form-select rounded-4 input-buscadoc py-2 shadow-none">
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
                                        <select name="parentesco" class="form-select rounded-4 input-buscadoc py-2 shadow-none" required>
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
                        <div class="card card-buscadoc card-buscadoc-accent rounded-4 p-4 p-md-5 h-100">
                            
                            <div class="d-flex align-items-center mb-4 pb-3 border-bottom">
                                <div class="icon-wrapper-teal p-2 rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                                    <x-mcr-shield style="width: 1.5rem;" />
                                </div>
                                <h5 class="fw-bold mb-0 text-navy">Detalles de Salud</h5>
                            </div>

                            <div class="mb-4">
                                <label class="form-label small fw-bold text-secondary text-uppercase tracking-wider d-flex align-items-center">
                                    <x-mcr-triangle-exclamation class="text-warning me-2" style="width: 1.1rem;" /> Alergias Conocidas
                                </label>
                                <textarea name="alergias" class="form-control rounded-4 input-buscadoc p-3 shadow-none" rows="3" placeholder="Ej. Penicilina, alimentos...">{{ $expediente->alergias }}</textarea>
                            </div>

                            <div class="mb-4">
                                <label class="form-label small fw-bold text-secondary text-uppercase tracking-wider d-flex align-items-center">
                                    <x-mcr-heart class="text-teal me-2" style="width: 1.1rem;" /> Padecimientos Crónicos
                                </label>
                                <textarea name="padecimientos_cronicos" class="form-control rounded-4 input-buscadoc p-3 shadow-none" rows="3" placeholder="Ej. Diabetes, Hipertensión...">{{ $expediente->padecimientos_cronicos }}</textarea>
                            </div>

                            <div class="mb-0">
                                <label class="form-label small fw-bold text-secondary text-uppercase tracking-wider d-flex align-items-center">
                                    <x-mcr-mug class="text-navy me-2" style="width: 1.1rem;" /> Hábitos de Salud
                                </label>
                                <textarea name="habitos_salud" class="form-control rounded-4 input-buscadoc p-3 shadow-none" rows="3" placeholder="Ej. Ejercicio regular, fumador...">{{ $expediente->habitos_salud }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- BOTONES DE ACCIÓN --}}
                <div class="d-flex justify-content-end align-items-center p-4 mt-2">
                    <a href="{{ route('expedientes.index') }}" class="btn btn-link text-decoration-none px-4 py-2 me-3 fw-bold text-muted transition-hover">
                        Cancelar
                    </a>
                    <button type="submit" class="btn btn-buscadoc rounded-pill px-5 py-3 fw-bold shadow-sm d-flex align-items-center">
                        <x-mcl-check-circle class="me-2" style="width: 1.2rem; color: white;" /> Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layout>