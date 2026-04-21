<x-layout>
    <style>
       
        :root {
            --buscadoc-navy: #0A3D62;         
            --buscadoc-navy-light: #1E5F8C;   
            --buscadoc-teal: #00A896;         
            --buscadoc-teal-light: #E6F6F4;   
            --buscadoc-bg: #F4F7F9;           
            --buscadoc-input: #EAEFF4;       
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
        <div class="row justify-content-center">
            <div class="col-lg-10">
                
                <div class="buscadoc-wrapper p-4 p-md-5 shadow-sm">
                    {{-- Encabezado del Formulario --}}
                    <div class="d-flex align-items-center mb-4 pb-3 border-bottom">
                        <div class="text-white rounded-circle d-flex align-items-center justify-content-center me-3 shadow-sm" style="width: 55px; height: 55px; background: linear-gradient(135deg, var(--buscadoc-navy) 0%, var(--buscadoc-navy-light) 100%);">
                            <x-mcl-folder-open style="width: 1.8rem;" />
                        </div>
                        <div>
                            <h3 class="fw-bold text-navy mb-0">Nuevo Expediente Médico</h3>
                            <p class="text-muted small mb-0 mt-1">Completa la información para mantener un historial preciso.</p>
                        </div>
                    </div>

                    <form action="{{ route('expedientes.store') }}" method="POST" class="card-buscadoc p-4 p-md-5 rounded-4 form-modern">
                        @csrf

                        {{-- Botón Volver --}}
                        <div class="mb-4">
                            <a href="{{ route('expedientes.index') }}" class="btn btn-link text-muted text-decoration-none small fw-bold px-0 d-inline-flex align-items-center transition-hover">
                                <x-mcr-angle-left class="me-1" style="width: 1rem;" /> Volver a mis expedientes
                            </a>
                        </div>

                        {{-- Sección 1: Datos Personales --}}
                        <div class="mb-5">
                            <div class="d-flex align-items-center mb-4 border-bottom pb-3">
                                <span class="bg-navy-subtle fw-bold rounded-circle me-3 d-flex align-items-center justify-content-center fs-6" style="width: 35px; height: 35px;">1</span>
                                <h5 class="fw-bold text-navy mb-0">Información Personal</h5>
                            </div>
                            
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-secondary text-uppercase tracking-wider">Nombre Completo</label>
                                    <div class="input-group">
                                        <span class="input-group-text input-buscadoc border-0 rounded-start-4 ps-3">
                                            <x-mcr-user-alt class="text-navy" style="width: 1.2rem;" />
                                        </span>
                                        <input type="text" name="nombre_completo" class="form-control input-buscadoc border-0 rounded-end-4 py-3 shadow-none" placeholder="Ej. Juan Pérez" required>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-secondary text-uppercase tracking-wider">Fecha de Nacimiento</label>
                                    <div class="input-group">
                                        <span class="input-group-text input-buscadoc border-0 rounded-start-4 ps-3">
                                            <x-mcr-calendar class="text-navy" style="width: 1.2rem;" />
                                        </span>
                                        <input type="date" name="fecha_nacimiento" class="form-control input-buscadoc border-0 rounded-end-4 py-3 shadow-none text-muted" max="{{ date('Y-m-d') }}" required>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label small fw-bold text-secondary text-uppercase tracking-wider">Género</label>
                                    <select name="genero" class="form-select input-buscadoc border-0 rounded-4 py-3 shadow-none text-secondary" required>
                                        <option value="" disabled selected>Seleccionar...</option>
                                        <option value="masculino">Masculino</option>
                                        <option value="femenino">Femenino</option>
                                    </select>
                                </div>
                                
                                @if(Auth::user()->role == 'doctor')
                                    <input type="hidden" name="parentesco" value="Paciente">
                                @else
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold text-secondary text-uppercase tracking-wider">Parentesco</label>
                                        <select name="parentesco" class="form-select input-buscadoc border-0 rounded-4 py-3 shadow-none text-secondary" required>
                                            <option value="" disabled selected>Seleccionar...</option>
                                            <option value="Yo mismo">Yo mismo</option>
                                            <option value="Hijo/a">Hijo/a</option>
                                            <option value="Padre/Madre">Padre/Madre</option>
                                            <option value="Cónyuge">Cónyuge</option>
                                            <option value="Otro">Otro</option>
                                        </select>
                                    </div>
                                @endif

                                <div class="col-md-4">
                                    <label class="form-label small fw-bold text-secondary text-uppercase tracking-wider">Tipo de Sangre</label>
                                    <select name="tipo_sangre" class="form-select input-buscadoc border-0 rounded-4 py-3 shadow-none text-secondary">
                                        <option value="" selected>Desconocido</option>
                                        <option value="O+">O+</option>
                                        <option value="O-">O-</option>
                                        <option value="A+">A+</option>
                                        <option value="A-">A-</option>
                                        <option value="B+">B+</option>
                                        <option value="AB+">AB+</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- Sección 2: Antecedentes Médicos --}}
                        <div class="mb-4 mt-5">
                            <div class="d-flex align-items-center mb-4 border-bottom pb-3">
                                <span class="icon-wrapper-teal fw-bold rounded-circle me-3 d-flex align-items-center justify-content-center fs-6" style="width: 35px; height: 35px;">2</span>
                                <h5 class="fw-bold text-navy mb-0">Historial y Antecedentes</h5>
                            </div>

                            <div class="row g-4">
                                <div class="col-12">
                                    <label class="form-label small fw-bold text-secondary text-uppercase tracking-wider d-flex align-items-center">
                                        <x-mcr-triangle-exclamation class="text-warning me-2" style="width: 1.1rem;" /> Alergias Conocidas
                                    </label>
                                    <textarea name="alergias" class="form-control input-buscadoc border-0 rounded-4 p-3 shadow-none" rows="2" placeholder="Ej. Penicilina, mariscos, polen..."></textarea>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-secondary text-uppercase tracking-wider d-flex align-items-center">
                                        <x-mcr-heart class="text-teal me-2" style="width: 1.1rem;" /> Padecimientos Crónicos
                                    </label>
                                    <textarea name="padecimientos_cronicos" class="form-control input-buscadoc border-0 rounded-4 p-3 shadow-none" rows="3" placeholder="Ej. Diabetes Tipo 2, Hipertensión..."></textarea>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-secondary text-uppercase tracking-wider d-flex align-items-center">
                                        <x-mcr-mug class="text-navy me-2" style="width: 1.1rem;" /> Hábitos de Salud
                                    </label>
                                    <textarea name="habitos_salud" class="form-control input-buscadoc border-0 rounded-4 p-3 shadow-none" rows="3" placeholder="Ej. Ejercicio 3 veces por semana, no fumador..."></textarea>
                                </div>
                            </div>
                        </div>

                        {{-- Botón Submit --}}
                        <div class="d-flex justify-content-end align-items-center mt-5 pt-4 border-top">
                            <button type="submit" class="btn btn-buscadoc btn-lg rounded-pill px-5 py-3 fw-bold shadow-sm d-flex align-items-center">
                                <x-mcl-bookmark class="me-2" style="width: 1.2rem; color: white;" /> Guardar Expediente
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-layout>