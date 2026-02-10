<x-layout>

    @push('styles')
        <style>
            .bg-navy { background-color: #0d2e4e !important; }
            .text-navy { color: #0d2e4e !important; }
            /* Estilos redondeados */
            .form-control:focus, .form-select:focus {
                border-color: #0d2e4e;
                box-shadow: 0 0 0 0.25rem rgba(13, 46, 78, 0.15);
            }
            .input-group-text { background-color: #f8f9fa; border-right: 0; color: #6c757d; }
            .form-control, .form-select { border-left: 0; }
            .input-group .form-control:focus { z-index: 3; }
            
            .card-header-icon {
                width: 40px; height: 40px;
                display: flex; align-items: center; justify-content: center;
                border-radius: 50%; background-color: rgba(255, 255, 255, 0.2);
            }
            .profile-upload:hover { transform: scale(1.1); cursor: pointer; }
            .object-fit-cover { object-fit: cover; }
        </style>
    @endpush

    @if(Auth::user() && Auth::user()->role == 'admin')
        <div class="container py-5">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="fw-bold text-navy mb-0">{{ isset($paciente) ? 'Editar Expediente' : 'Nuevo Ingreso' }}</h1>
                    <p class="text-muted small mb-0">Gestión de pacientes / {{ isset($paciente) ? $paciente->user->name : 'Registro' }}</p>
                </div>
                <a href="{{ route('pacientes.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                    <i class="bi bi-arrow-left me-2"></i>Volver
                </a>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger shadow-sm border-0 rounded-4 mb-4">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-exclamation-octagon-fill fs-3 me-3 text-danger"></i>
                        <div>
                            <h6 class="fw-bold mb-1">Por favor corrige los siguientes errores:</h6>
                            <ul class="mb-0 small text-muted">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <form method="POST"
                action="{{ isset($paciente) ? route('pacientes.update', $paciente->id) : route('pacientes.store') }}"
                class="needs-validation" novalidate enctype="multipart/form-data">
                
                @csrf
                @if(isset($paciente))
                    @method('PUT')
                @endif

                <div class="row g-4">
                    
                    {{-- COLUMNA IZQUIERDA: IDENTIDAD --}}
                    <div class="col-lg-4">
                        <div class="card border-0 shadow-sm rounded-5 h-100">
                            <div class="card-header bg-navy text-white p-4 border-0 rounded-top-5">
                                <div class="d-flex align-items-center">
                                    <div class="card-header-icon me-3">
                                        <i class="bi bi-person-vcard-fill fs-5"></i>
                                    </div>
                                    <div>
                                        <h5 class="mb-0 fw-bold">Identidad</h5>
                                        <small class="opacity-75">Foto y datos de acceso</small>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="card-body p-4">
                                {{-- FOTO --}}
                                <div class="text-center mb-4">
                                    <div class="position-relative d-inline-block">
                                        <div class="rounded-circle overflow-hidden shadow-sm border border-4 border-white bg-light d-flex align-items-center justify-content-center"
                                            style="width: 130px; height: 130px;">
                                            <img id="profilePreview"
                                                src="{{ isset($paciente) && $paciente->user->foto ? asset('storage/' . $paciente->user->foto) : 'https://ui-avatars.com/api/?name=' . urlencode($paciente->user->name ?? 'Nuevo Paciente') . '&background=E6F0FF&color=0D2E4E' }}"
                                                class="w-100 h-100 object-fit-cover" alt="Foto de perfil">
                                        </div>
                                        <label for="fotoInput"
                                            class="position-absolute bottom-0 end-0 bg-navy text-white rounded-circle p-2 shadow-sm profile-upload"
                                            style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; transition: all 0.2s;">
                                            <i class="bi bi-camera-fill"></i>
                                        </label>
                                    </div>
                                    {{-- IMPORTANTE: name="foto" --}}
                                    <input type="file" name="foto" id="fotoInput" class="d-none" accept="image/jpeg,image/png,image/jpg">
                                    <div class="form-text small mt-2">Haga clic en la cámara para cambiar la foto.</div>
                                </div>
                                
                                <hr class="text-muted opacity-25">
                                
                                <div class="mb-4">
                                    <label class="form-label small fw-bold text-muted text-uppercase">Nombre Completo</label>
                                    <div class="input-group">
                                        <span class="input-group-text rounded-start-pill"><i class="bi bi-person"></i></span>
                                        <input type="text" name="name" class="form-control rounded-end-pill"
                                            value="{{ old('name', $paciente->user->name ?? '') }}" 
                                            required minlength="3" pattern="[a-zA-ZñÑáéíóúÁÉÍÓÚ\s]+"
                                            placeholder="Ej. Juan Pérez">
                                        <div class="invalid-feedback">Solo letras y espacios.</div>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label small fw-bold text-muted text-uppercase">Correo Electrónico</label>
                                    <div class="input-group">
                                        <span class="input-group-text rounded-start-pill"><i class="bi bi-envelope"></i></span>
                                        <input type="email" name="email" class="form-control rounded-end-pill"
                                            value="{{ old('email', $paciente->user->email ?? '') }}" 
                                            required placeholder="correo@ejemplo.com">
                                        <div class="invalid-feedback">Ingrese un correo válido.</div>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label small fw-bold text-muted text-uppercase">
                                        Contraseña
                                        @if(isset($paciente)) <span class="fw-normal text-lowercase">(opcional)</span> @endif
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text rounded-start-pill"><i class="bi bi-key"></i></span>
                                        <input type="password" name="password" class="form-control rounded-end-pill" 
                                            {{ isset($paciente) ? '' : 'required' }} minlength="8" placeholder="••••••••">
                                        <div class="invalid-feedback">Mínimo 8 caracteres.</div>
                                    </div>
                                </div>

                                <hr class="my-4 text-muted opacity-25">

                                <div class="mb-2">
                                    <label class="form-label small fw-bold text-muted text-uppercase">Contacto de Emergencia</label>
                                    <div class="input-group">
                                        <span class="input-group-text rounded-start-pill"><i class="bi bi-telephone-fill text-danger"></i></span>
                                        <input type="tel" name="contacto_emergencia" class="form-control rounded-end-pill"
                                            value="{{ old('contacto_emergencia', $paciente->contacto_emergencia ?? '') }}"
                                            maxlength="10" pattern="[0-9]{10}"
                                            oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                            placeholder="Solo números (10 dígitos)">
                                        <div class="invalid-feedback">Debe contener 10 dígitos.</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- COLUMNA DERECHA: HISTORIAL --}}
                    <div class="col-lg-8">
                        <div class="card border-0 shadow-sm rounded-5 h-100">
                            <div class="card-header bg-white p-4 border-bottom rounded-top-5">
                                <div class="d-flex align-items-center text-navy">
                                    <i class="bi bi-activity fs-3 me-3"></i>
                                    <h5 class="mb-0 fw-bold">Historial Clínico</h5>
                                </div>
                            </div>
                            <div class="card-body p-4">
                                <div class="row g-3 mb-4">
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold text-muted">Tipo de Sangre</label>
                                        <div class="input-group">
                                            <span class="input-group-text rounded-start-pill"><i class="bi bi-droplet"></i></span>
                                            <select name="tipo_sangre" class="form-select fw-bold text-navy rounded-end-pill" required>
                                                <option value="" selected disabled>Seleccione...</option>
                                                @foreach(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $tipo)
                                                    <option value="{{ $tipo }}" {{ old('tipo_sangre', $paciente->tipo_sangre ?? '') == $tipo ? 'selected' : '' }}>
                                                        {{ $tipo }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <div class="invalid-feedback">Seleccione una opción.</div>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <label class="form-label small fw-bold text-muted">Alergias Conocidas</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-danger-subtle text-danger rounded-start-pill"><i class="bi bi-virus"></i></span>
                                            <input type="text" name="alergias" class="form-control rounded-end-pill"
                                                value="{{ old('alergias', $paciente->alergias ?? '') }}"
                                                placeholder="Medicamentos, alimentos, látex...">
                                        </div>
                                    </div>
                                </div>

                                <div class="row g-4">
                                    <div class="col-12">
                                        <div class="p-3 bg-light rounded-4 border">
                                            <label class="form-label fw-bold text-navy"><i class="bi bi-bandaid me-2"></i>Cirugías Previas</label>
                                            <textarea name="cirugias" class="form-control bg-white border-0 shadow-sm rounded-3"
                                                rows="2" placeholder="Describa intervenciones quirúrgicas y fechas aproximadas...">{{ old('cirugias', $paciente->cirugias ?? '') }}</textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="p-3 bg-light rounded-4 border h-100">
                                            <label class="form-label fw-bold text-navy"><i class="bi bi-prescription2 me-2"></i>Padecimientos Crónicos</label>
                                            <textarea name="padecimientos" class="form-control bg-white border-0 shadow-sm rounded-3"
                                                rows="4" placeholder="Diabetes, Hipertensión, Asma, etc...">{{ old('padecimientos', $paciente->padecimientos ?? '') }}</textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="p-3 bg-light rounded-4 border h-100">
                                            <label class="form-label fw-bold text-navy"><i class="bi bi-cup-hot me-2"></i>Estilo de Vida / Hábitos</label>
                                            <textarea name="habitos" class="form-control bg-white border-0 shadow-sm rounded-3"
                                                rows="4" placeholder="Tabaquismo, Alcohol, Actividad física...">{{ old('habitos', $paciente->habitos ?? '') }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="card-footer bg-white p-4 border-top rounded-bottom-5">
                                <div class="d-flex justify-content-end gap-3">
                                    <button type="submit" class="btn btn-navy px-5 py-2 rounded-pill fw-bold shadow">
                                        <i class="bi bi-check-lg me-2"></i>
                                        {{ isset($paciente) ? 'Actualizar Expediente' : 'Registrar Paciente' }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    @else
        <div class="container d-flex flex-column justify-content-center align-items-center min-vh-100 bg-light fade-in">
            <div class="card border-0 shadow-lg rounded-5 p-5 text-center" style="max-width: 500px;">
                <div class="mb-4">
                    <div class="bg-danger-subtle text-danger d-inline-flex align-items-center justify-content-center rounded-circle"
                        style="width: 100px; height: 100px;">
                        <i class="bi bi-shield-lock-fill display-3"></i>
                    </div>
                </div>
                <h2 class="fw-bold text-navy mb-3">Acceso Restringido</h2>
                <p class="text-muted mb-4 fs-5">No tienes los permisos necesarios.</p>
                <hr class="my-4 opacity-10">
                <div class="py-2">
                    <div class="d-flex align-items-center justify-content-center gap-3">
                        <div class="spinner-border text-navy" role="status" style="width: 1.5rem; height: 1.5rem;"></div>
                        <span class="fw-bold text-navy">Redirigiendo...</span>
                    </div>
                </div>
                <div class="mt-4">
                    <a href="{{ route('welcome') }}" class="btn btn-link text-muted small">¿No has sido redirigido? Clic aquí</a>
                </div>
            </div>
        </div>
    @endif

    @push('scripts')
        <script>
            // Previsualizar imagen
            const fotoInput = document.getElementById('fotoInput');
            if(fotoInput){
                fotoInput.onchange = evt => {
                    const [file] = fotoInput.files
                    if (file) {
                        document.getElementById('profilePreview').src = URL.createObjectURL(file)
                    }
                }
            }

            // Validación de bootstrap
            (function () {
                'use strict'
                var forms = document.querySelectorAll('.needs-validation')
                Array.prototype.slice.call(forms).forEach(function (form) {
                    form.addEventListener('submit', function (event) {
                        if (!form.checkValidity()) {
                            event.preventDefault()
                            event.stopPropagation()
                        }
                        form.classList.add('was-validated')
                    }, false)
                })
            })()

            // Redirección si no es admin
            @if(!(Auth::user() && Auth::user()->role == 'admin'))
                setTimeout(function () {
                    window.location.href = "{{ route('welcome') }}";
                }, 3000);
            @endif
        </script>
    @endpush
</x-layout>