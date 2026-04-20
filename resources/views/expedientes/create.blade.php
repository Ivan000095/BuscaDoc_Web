<x-layout>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                
                {{-- Encabezado del Formulario --}}
                <div class="d-flex align-items-center mb-4">
                    <div class="bg-navy text-white rounded-circle d-flex align-items-center justify-content-center me-3 shadow-sm" style="width: 55px; height: 55px;">
                        <x-mcl-folder-open style="width: 1.8rem;" />
                    </div>
                    <div>
                        <h3 class="fw-bold text-navy mb-0">Nuevo Expediente Médico</h3>
                        <p class="text-muted small mb-0">Completa la información para mantener un historial preciso.</p>
                    </div>
                </div>

                <form action="{{ route('expedientes.store') }}" method="POST" class="bg-white p-4 p-md-5 rounded-4 shadow-sm border-0 form-modern">
                    @csrf

                    {{-- Botón Volver --}}
                    <div class="mb-4">
                        <a href="{{ route('expedientes.index') }}" class="btn btn-link text-muted text-decoration-none small fw-bold px-0 btn-back-custom d-inline-flex align-items-center">
                            <x-mcr-angle-left class="me-1" style="width: 1rem;" /> Volver a mis expedientes
                        </a>
                    </div>

                    {{-- Sección 1: Datos Personales --}}
                    <div class="mb-5">
                        <div class="d-flex align-items-center mb-4 border-bottom pb-3">
                            <span class="badge bg-navy-subtle text-navy rounded-circle me-3 d-flex align-items-center justify-content-center fs-6" style="width: 35px; height: 35px;">1</span>
                            <h5 class="fw-bold text-navy mb-0">Información Personal</h5>
                        </div>
                        
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-secondary text-uppercase tracking-wider">Nombre Completo</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0 rounded-start-4 ps-3"><x-mcr-user-alt class="text-muted" style="width: 1.2rem;" /></span>
                                    <input type="text" name="nombre_completo" class="form-control bg-light border-0 rounded-end-4 py-3 shadow-none custom-input" placeholder="Ej. Juan Pérez" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-secondary text-uppercase tracking-wider">Fecha de Nacimiento</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0 rounded-start-4 ps-3"><x-mcr-calendar class="text-muted" style="width: 1.2rem;" /></span>
                                    <input type="date" name="fecha_nacimiento" class="form-control bg-light border-0 rounded-end-4 py-3 shadow-none custom-input text-muted" max="{{ date('Y-m-d') }}" required>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-secondary text-uppercase tracking-wider">Género</label>
                                <select name="genero" class="form-select bg-light border-0 rounded-4 py-3 shadow-none custom-input text-secondary" required>
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
                                    <select name="parentesco" class="form-select bg-light border-0 rounded-4 py-3 shadow-none custom-input text-secondary" required>
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
                                <select name="tipo_sangre" class="form-select bg-light border-0 rounded-4 py-3 shadow-none custom-input text-secondary">
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
                            <span class="badge bg-navy-subtle text-navy rounded-circle me-3 d-flex align-items-center justify-content-center fs-6" style="width: 35px; height: 35px;">2</span>
                            <h5 class="fw-bold text-navy mb-0">Historial y Antecedentes</h5>
                        </div>

                        <div class="row g-4">
                            <div class="col-12">
                                <label class="form-label small fw-bold text-secondary text-uppercase tracking-wider">
                                    <x-mcr-triangle-exclamation class="text-warning me-1" style="width: 1rem;" /> Alergias Conocidas
                                </label>
                                <textarea name="alergias" class="form-control bg-light border-0 rounded-4 p-3 shadow-none custom-input" rows="2" placeholder="Ej. Penicilina, mariscos, polen..."></textarea>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-secondary text-uppercase tracking-wider">
                                    <x-mcr-heart class="text-info me-1" style="width: 1rem;" /> Padecimientos Crónicos
                                </label>
                                {{-- OJO: Nombre actualizado para la BD --}}
                                <textarea name="padecimientos_cronicos" class="form-control bg-light border-0 rounded-4 p-3 shadow-none custom-input" rows="3" placeholder="Ej. Diabetes Tipo 2, Hipertensión..."></textarea>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-secondary text-uppercase tracking-wider">
                                    <x-mcr-mug class="text-success me-1" style="width: 1rem;" /> Hábitos de Salud
                                </label>
                                {{-- OJO: Nombre actualizado para la BD --}}
                                <textarea name="habitos_salud" class="form-control bg-light border-0 rounded-4 p-3 shadow-none custom-input" rows="3" placeholder="Ej. Ejercicio 3 veces por semana, no fumador..."></textarea>
                            </div>
                        </div>
                    </div>

                    {{-- Botón Submit --}}
                    <div class="d-flex justify-content-end align-items-center mt-5 pt-4 border-top">
                        <button type="submit" class="btn btn-navy btn-lg rounded-pill px-5 py-3 fw-bold shadow-sm d-flex align-items-center">
                            <x-mcl-bookmark class="me-2" style="width: 1.2rem;" /> Guardar Expediente
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</x-layout>