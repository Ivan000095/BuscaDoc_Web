<x-layout>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                {{-- Encabezado del Formulario --}}
                <div class="d-flex align-items-center mb-4">
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                        <i class="bi bi-file-earmark-medical fs-4"></i>
                    </div>
                    <div>
                        <h3 class="fw-bold text-navy mb-0">Nuevo Expediente Médico</h3>
                        <p class="text-muted small mb-0">Completa la información para mantener un historial preciso.</p>
                    </div>
                </div>

                <form action="{{ route('expedientes.store') }}" method="POST" class="bg-white p-5 rounded-4 shadow-sm border-0">
                    @csrf

                    {{-- Sección 1: Datos Personales --}}
                    <div class="mb-5">
                        <a href="{{ route('expedientes.index') }}" class="btn btn-link text-muted text-decoration-none small fw-bold">
                            <i class="bi bi-arrow-left me-1"></i> Volver a la lista
                        </a>
                        <div class="d-flex align-items-center mb-4">
                            <span class="badge bg-primary-subtle text-primary rounded-pill me-2">1</span>
                            <h5 class="fw-bold text-navy mb-0">Información Personal</h5>
                        </div>
                        
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-secondary text-uppercase tracking-wider">Nombre Completo</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0 rounded-start-pill"><i class="bi bi-person text-muted"></i></span>
                                    <input type="text" name="nombre_completo" class="form-control bg-light border-0 rounded-end-pill py-2" placeholder="Ej. Juan Pérez" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-secondary text-uppercase tracking-wider">Fecha de Nacimiento</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0 rounded-start-pill"><i class="bi bi-calendar3 text-muted"></i></span>
                                    <input type="date" name="fecha_nacimiento" class="form-control bg-light border-0 rounded-end-pill py-2" max="{{ date('Y-m-d') }}" required>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-secondary text-uppercase tracking-wider">Género</label>
                                <select name="genero" class="form-select bg-light border-0 rounded-pill py-2" required>
                                    <option value="" disabled selected>Seleccionar...</option>
                                    <option value="masculino">Masculino</option>
                                    <option value="femenino">Femenino</option>
                                </select>
                            </div>
                            
                             @if(Auth::user()->role == 'doctor')

                                    <input type="hidden" name="parentesco" value="Paciente">
                            @else
                            <div class="col-md-4">

                                    <label class="form-label small fw-bold text-secondary text-uppercase tracking-wider">Parentesco / Relación</label>
                                    <select name="parentesco" class="form-select bg-light border-0 rounded-pill py-2" required>
                                        
                                        <option value="Hijo/a">Hijo/a</option>
                                        <option value="Padre/Madre">Padre/Madre</option>
                                        <option value="Cónyuge">Cónyuge</option>
                                        <option value="Otro">Otro</option>
                                    </select>
                                
                            </div>
                            @endif
                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-secondary text-uppercase tracking-wider">Tipo de Sangre</label>
                                <select name="tipo_sangre" class="form-select bg-light border-0 rounded-pill py-2">
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
                    <div class="mb-4">
                        <div class="d-flex align-items-center mb-4">
                            <span class="badge bg-primary-subtle text-primary rounded-pill me-2">2</span>
                            <h5 class="fw-bold text-navy mb-0">Historial y Antecedentes</h5>
                        </div>

                        <div class="row g-4">
                            <div class="col-12">
                                <label class="form-label small fw-bold text-secondary text-uppercase tracking-wider">Alergias Conocidas</label>
                                <textarea name="alergias" class="form-control bg-light border-0 rounded-4 p-3" rows="2" placeholder="Ej. Penicilina, mariscos, polen..."></textarea>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-secondary text-uppercase tracking-wider">Padecimientos Crónicos</label>
                                <textarea name="padecimientos" class="form-control bg-light border-0 rounded-4 p-3" rows="3" placeholder="Ej. Diabetes Tipo 2, Hipertensión..."></textarea>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-secondary text-uppercase tracking-wider">Hábitos de Salud</label>
                                <textarea name="habitos" class="form-control bg-light border-0 rounded-4 p-3" rows="3" placeholder="Ej. Ejercicio 3 veces por semana, no fumador..."></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-5 pt-4 border-top">

                        <button type="submit" class="btn btn-navy rounded-pill px-5 py-2 fw-bold shadow-sm">
                            Guardar Expediente
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        .tracking-wider { letter-spacing: 0.05em; }
        .bg-primary-subtle { background-color: #e7f1ff !important; }
        .text-navy { color: #001f3f; }
        .btn-navy { background-color: #001f3f; color: white; transition: all 0.3s; }
        .btn-navy:hover { background-color: #003366; color: white; transform: translateY(-2px); }
        .form-control:focus, .form-select:focus { 
            background-color: #fff !important; 
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.1); 
            border: 1px solid #0d6efd !important;
        }
    </style>
</x-layout>