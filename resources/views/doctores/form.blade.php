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
            border-radius: 50%; background-color: rgba(255,255,255,0.2);
        }
        .profile-upload:hover { transform: scale(1.1); cursor: pointer; }
        .object-fit-cover { object-fit: cover; }
    </style>
    @endpush

    @if(Auth::user() && Auth::user()->role == 'admin')
        <div class="container py-5">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="fw-bold text-navy mb-0">{{ isset($doctor) ? 'Editar Doctor' : 'Nuevo Doctor' }}</h1>
                    <p class="text-muted small mb-0">Gestión de personal médico / {{ isset($doctor) ? $doctor->user->name : 'Registro' }}</p>
                </div>
                <a href="{{ route('doctores.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
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
                action="{{ isset($doctor) ? route('doctores.update', $doctor->id) : route('doctores.store') }}"
                class="needs-validation" novalidate enctype="multipart/form-data">

                @csrf
                @if(isset($doctor))
                    @method('PUT')
                @endif

                <div class="row g-4">
                    
                    <div class="col-lg-4">
                        <div class="card border-0 shadow-sm rounded-5 h-100">
                            <div class="card-header bg-navy text-white p-4 border-0 rounded-top-5">
                                <div class="d-flex align-items-center">
                                    <div class="card-header-icon me-3">
                                        <i class="bi bi-person-badge-fill fs-5"></i>
                                    </div>
                                    <div>
                                        <h5 class="mb-0 fw-bold">Cuenta</h5>
                                        <small class="opacity-75">Foto y credenciales</small>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="card-body p-4">
                                
                                {{-- FOTO DE PERFIL --}}
                                <div class="text-center mb-4">
                                    <div class="position-relative d-inline-block">
                                        <div class="rounded-circle overflow-hidden shadow-sm border border-4 border-white bg-light d-flex align-items-center justify-content-center" 
                                            style="width: 130px; height: 130px;">
                                            <img id="profilePreview"
                                                src="{{ isset($doctor) && $doctor->user->foto ? asset('storage/' . $doctor->user->foto) : 'https://ui-avatars.com/api/?name=' . urlencode($doctor->user->name ?? 'Nuevo Doctor') . '&background=E6F0FF&color=0D2E4E' }}"
                                                class="w-100 h-100 object-fit-cover"
                                                alt="Foto de perfil">
                                        </div>
                                        
                                        <label for="fotoInput" class="position-absolute bottom-0 end-0 bg-navy text-white rounded-circle p-2 shadow-sm profile-upload" 
                                            style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; transition: all 0.2s;">
                                            <i class="bi bi-camera-fill"></i>
                                        </label>
                                    </div>
                                    <input type="file" name="image" id="fotoInput" class="d-none" accept="image/jpeg,image/png,image/jpg">
                                    <div class="form-text small mt-2">Haga clic en la cámara para cambiar la foto.</div>
                                </div>

                                <hr class="text-muted opacity-25">

                                {{-- NOMBRE --}}
                                <div class="mb-4">
                                    <label class="form-label small fw-bold text-muted text-uppercase">Nombre Completo</label>
                                    <div class="input-group">
                                        <span class="input-group-text rounded-start-pill"><i class="bi bi-person"></i></span>
                                        <input type="text" name="name" class="form-control rounded-end-pill" 
                                            value="{{ old('name', $doctor->user->name ?? '') }}" 
                                            required minlength="3" maxlength="100" 
                                            pattern="[a-zA-ZñÑáéíóúÁÉÍÓÚ\s]+"
                                            title="Solo letras y espacios"
                                            placeholder="Ej. Juan Pérez">
                                        <div class="invalid-feedback">Ingrese un nombre válido (solo letras).</div>
                                    </div>
                                </div>

                                {{-- FECHA DE NACIMIENTO --}}
                                <div class="mb-4">
                                    <label class="form-label small fw-bold text-muted text-uppercase">Fecha de Nacimiento</label>
                                    <div class="input-group">
                                        <span class="input-group-text rounded-start-pill"><i class="bi bi-calendar-date"></i></span>
                                        {{-- CORREGIDO: Formato Y-m-d para que el input type="date" lo lea --}}
                                        <input type="date" name="fecha" class="form-control rounded-end-pill" 
                                            value="{{ old('f_nacimiento', isset($doctor->user->f_nacimiento) ? \Carbon\Carbon::parse($doctor->user->f_nacimiento)->format('Y-m-d') : '') }}" 
                                            required max="{{ date('Y-m-d', strtotime('-18 years')) }}">
                                        <div class="invalid-feedback">Debe ser mayor de 18 años.</div>
                                    </div>
                                </div>

                                {{-- EMAIL --}}
                                <div class="mb-4">
                                    <label class="form-label small fw-bold text-muted text-uppercase">Correo Electrónico</label>
                                    <div class="input-group">
                                        <span class="input-group-text rounded-start-pill"><i class="bi bi-envelope"></i></span>
                                        <input type="email" name="email" class="form-control rounded-end-pill" 
                                            value="{{ old('email', $doctor->user->email ?? '') }}" required placeholder="correo@ejemplo.com">
                                        <div class="invalid-feedback">Ingrese un correo válido.</div>
                                    </div>
                                </div>

                                {{-- PASSWORD --}}
                                <div class="mb-2">
                                    <label class="form-label small fw-bold text-muted text-uppercase">
                                        Contraseña
                                        @if(isset($doctor)) <span class="fw-normal text-lowercase">(opcional)</span> @endif
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text rounded-start-pill"><i class="bi bi-key"></i></span>
                                        <input type="password" name="password" class="form-control rounded-end-pill" 
                                            {{ isset($doctor) ? '' : 'required' }} minlength="8" placeholder="••••••••">
                                        <div class="invalid-feedback">Mínimo 8 caracteres.</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- COLUMNA DERECHA: PERFIL PROFESIONAL --}}
                    <div class="col-lg-8">
                        <div class="card border-0 shadow-sm rounded-5 h-100">
                            <div class="card-header bg-white p-4 border-bottom rounded-top-5">
                                <div class="d-flex align-items-center text-navy">
                                    <i class="bi bi-briefcase-fill fs-3 me-3"></i>
                                    <h5 class="mb-0 fw-bold">Perfil Profesional</h5>
                                </div>
                            </div>

                            <div class="card-body p-4">
                                
                                <div class="row g-3 mb-4">
                                    {{-- ESPECIALIDAD --}}
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold text-muted">Especialidad</label>
                                        <div class="input-group">
                                            <span class="input-group-text rounded-start-pill"><i class="bi bi-award"></i></span>
                                            <select name="especialidad_id" class="form-select fw-bold text-navy rounded-end-pill" required>
                                                <option value="" selected disabled>Seleccione...</option>
                                                @foreach($especialidades as $esp)
                                                    <option value="{{ $esp->id }}" {{ (isset($doctor) && $doctor->especialidades->contains($esp->id)) ? 'selected' : '' }}>
                                                        {{ $esp->nombre }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <div class="invalid-feedback">Seleccione una opción.</div>
                                        </div>
                                    </div>
                                    {{-- CÉDULA --}}
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold text-muted">Cédula Profesional</label>
                                        <div class="input-group">
                                            <span class="input-group-text rounded-start-pill"><i class="bi bi-card-heading"></i></span>
                                            <input type="text" name="cedula" class="form-control rounded-end-pill" 
                                                value="{{ old('cedula', $doctor->cedula ?? '') }}" 
                                                required minlength="5" placeholder="Núm. de Cédula">
                                            <div class="invalid-feedback">Obligatorio (min 5 caracteres).</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row g-3 mb-4">
                                    {{-- COSTO --}}
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold text-muted">Costo Consulta</label>
                                        <div class="input-group">
                                            <span class="input-group-text fw-bold text-success rounded-start-pill">$</span>
                                            <input type="number" name="costos" step="0.01" min="0" class="form-control rounded-end-pill" 
                                                value="{{ old('costos', $doctor->costo ?? '') }}" required placeholder="0.00">
                                            <div class="invalid-feedback">Ingrese un monto válido.</div>
                                        </div>
                                    </div>
                                    {{-- HORARIO ENTRADA --}}
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold text-muted">Horario Entrada</label>
                                        <div class="input-group">
                                            <span class="input-group-text rounded-start-pill"><i class="bi bi-clock"></i></span>
                                            <input type="time" name="horarioentrada" class="form-control rounded-end-pill" 
                                                value="{{ old('horarioentrada', $doctor->horario_entrada ?? '') }}" required>
                                        </div>
                                    </div>
                                    {{-- HORARIO SALIDA --}}
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold text-muted">Horario Salida</label>
                                        <div class="input-group">
                                            <span class="input-group-text rounded-start-pill"><i class="bi bi-clock-fill"></i></span>
                                            <input type="time" name="horariosalida" class="form-control rounded-end-pill" 
                                                value="{{ old('horariosalida', $doctor->horario_salida ?? '') }}" required>
                                        </div>
                                    </div>
                                </div>
                                {{-- TRABAJA CON CITAS --}}
                                <div class="col-md-12">
                                    <div class="p-3 bg-light rounded-4 border d-flex align-items-center justify-content-between">
                                        <div>
                                            <label class="form-label fw-bold text-navy mb-0">
                                                <i class="bi bi-calendar-check me-2"></i>¿Trabaja con citas?
                                            </label>
                                            <div class="form-text mt-0">Indique si el doctor requiere agenda previa.</div>
                                        </div>
                                        <div class="form-check form-switch form-switch-lg">
                                            <input type="hidden" name="citas" value="0"> {{-- Asegura que se envíe 0 si no está marcado --}}
                                            <input class="form-check-input" type="checkbox" role="switch" id="citas" name="citas" value="1" 
                                                {{ old('citas', $doctor->citas ?? '') == '1' ? 'checked' : '' }} 
                                                style="width: 3em; height: 1.5em; cursor: pointer;">
                                        </div>
                                    </div>
                                </div>

                                {{-- IDIOMAS --}}
                                <div class="mb-4">
                                    <label class="form-label small fw-bold text-muted">Idiomas</label>
                                    <div class="input-group">
                                        <span class="input-group-text rounded-start-pill"><i class="bi bi-translate"></i></span>
                                        <input type="text" name="idioma" class="form-control rounded-end-pill" 
                                            value="{{ old('idioma', $doctor->idiomas ?? '') }}" 
                                            placeholder="Ej. Español, Inglés"
                                            pattern="[a-zA-ZñÑáéíóúÁÉÍÓÚ\s,]+">
                                        <div class="invalid-feedback">Solo texto separado por comas.</div>
                                    </div>
                                </div>

                                {{-- DESCRIPCIÓN --}}
                                <div class="mb-4">
                                    <div class="p-3 bg-light rounded-4 border">
                                        <label class="form-label fw-bold text-navy"><i class="bi bi-file-person me-2"></i>Descripción / Biografía</label>
                                        <textarea name="descripcion" class="form-control bg-white border-0 shadow-sm rounded-3" rows="3" 
                                            required placeholder="Escriba una breve descripción profesional...">{{ old('descripcion', $doctor->descripcion ?? '') }}</textarea>
                                    </div>
                                </div>

                                <hr class="text-muted opacity-25 mb-4">

                                {{-- MAPA --}}
                                <div class="mb-2">
                                    <label class="form-label fw-bold text-navy mb-3"><i class="bi bi-geo-alt-fill me-2"></i>Ubicación del Consultorio</label>
                                    <input type="hidden" name="latitud" id="latitud" value="{{ old('latitud', $doctor->user->latitud ?? '') }}">
                                    <input type="hidden" name="longitud" id="longitud" value="{{ old('longitud', $doctor->user->longitud ?? '') }}">
                                    
                                    <div class="shadow-sm rounded-4 overflow-hidden border border-light">
                                        <div id="map" style="height: 300px; width: 100%;"></div>
                                    </div>
                                    <div class="form-text small mt-2"><i class="bi bi-info-circle me-1"></i> Arrastra el marcador rojo para indicar la ubicación exacta.</div>
                                </div>

                            </div>
                            
                            <div class="card-footer bg-white p-4 border-top rounded-bottom-5">
                                <div class="d-flex justify-content-end gap-3">
                                    <button type="submit" class="btn btn-navy px-5 py-2 rounded-pill fw-bold shadow">
                                        <i class="bi bi-check-lg me-2"></i>
                                        {{ isset($doctor) ? 'Actualizar Doctor' : 'Guardar Doctor' }}
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
                <p class="text-muted mb-4 fs-5">
                    No tienes los permisos necesarios para acceder a esta sección. <i class="bi bi-emoji-frown-fill text-navy"></i>
                </p>
                <hr class="my-4 opacity-10">
                <div class="py-2">
                    <div class="d-flex align-items-center justify-content-center gap-3">
                        <div class="spinner-border text-navy" role="status"
                            style="width: 1.5rem; height: 1.5rem; border-width: 3px;">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                        <span class="fw-bold text-navy">Redirigiendo al inicio...</span>
                    </div>
                </div>
                <div class="mt-4">
                    <a href="{{ route('welcome') }}" class="btn btn-link text-muted text-decoration-none small">
                        ¿No has sido redirigido? Haz clic aquí
                    </a>
                </div>
            </div>
        </div>
    @endif

    @push('scripts')
    <script>
        document.getElementById('fotoInput').onchange = evt => {
            const [file] = fotoInput.files
            if (file) {
                document.getElementById('profilePreview').src = URL.createObjectURL(file)
            }
        }

        (function () {
            'use strict';
            var forms = document.querySelectorAll('.needs-validation');
            Array.prototype.slice.call(forms).forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated')
                }, false)
            })
        })()
    </script>

    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDzSz-VqueMjM2OEaddCFuNLSl7LsCpqzQ&callback=initMap" async defer></script>

    <script>
        let map;
        let marker;

        function initMap() {
            const initialLat = parseFloat(document.getElementById('latitud').value) || 16.9080;
            const initialLng = parseFloat(document.getElementById('longitud').value) || -92.0946;
            const myLatLng = { lat: initialLat, lng: initialLng };

            map = new google.maps.Map(document.getElementById("map"), {
                zoom: 15,
                center: myLatLng,
                mapTypeId: google.maps.MapTypeId.ROADMAP
            });

            marker = new google.maps.Marker({
                position: myLatLng,
                map: map,
                draggable: true,
                title: "Ubicación del Consultorio",
                animation: google.maps.Animation.DROP
            });

            marker.addListener("dragend", function (event) {
                updateInputs(event.latLng.lat(), event.latLng.lng());
            });

            map.addListener("click", function (event) {
                marker.setPosition(event.latLng);
                updateInputs(event.latLng.lat(), event.latLng.lng());
            });
        }

        function updateInputs(lat, lng) {
            document.getElementById('latitud').value = lat;
            document.getElementById('longitud').value = lng;
        }

        @if(!(Auth::user() && Auth::user()->role == 'admin'))
            setTimeout(function () {
                window.location.href = "{{ route('welcome') }}";
            }, 3000);
        @endif
    </script>
    @endpush
</x-layout>