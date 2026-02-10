<x-layout>
    @push('styles')
    <style>
        .bg-navy { background-color: #0d2e4e !important; }
        .text-navy { color: #0d2e4e !important; }
        
        /* Estilos para inputs más redondos y suaves */
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

    <div class="container py-5">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="fw-bold text-navy mb-0">{{ isset($farmacia) ? 'Editar Farmacia' : 'Nueva Farmacia' }}</h1>
                <p class="text-muted small mb-0">Gestión de sucursales / {{ isset($farmacia) ? $farmacia->nom_farmacia : 'Registro' }}</p>
            </div>
            <a href="{{ route('admin.farmacias.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
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
            action="{{ isset($farmacia) ? route('admin.farmacias.update', $farmacia->id) : route('admin.farmacias.store') }}"
            class="needs-validation" novalidate enctype="multipart/form-data">

            @csrf
            @if(isset($farmacia))
                @method('PUT')
            @endif

            <div class="row g-4">
                
                {{-- COLUMNA IZQUIERDA: DUEÑO --}}
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm rounded-5 h-100">
                        <div class="card-header bg-navy text-white p-4 border-0 rounded-top-5">
                            <div class="d-flex align-items-center">
                                <div class="card-header-icon me-3">
                                    <i class="bi bi-person-badge-fill fs-5"></i>
                                </div>
                                <div>
                                    <h5 class="mb-0 fw-bold">Dueño</h5>
                                    <small class="opacity-75">Cuenta de usuario</small>
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
                                             src="{{ isset($farmacia) && $farmacia->user->foto ? asset('storage/' . $farmacia->user->foto) : 'https://ui-avatars.com/api/?name=' . urlencode($farmacia->user->name ?? 'Nuevo Dueño') . '&background=E6F0FF&color=0D2E4E' }}"
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
                                        value="{{ old('name', $farmacia->user->name ?? '') }}" 
                                        required minlength="3" maxlength="100" 
                                        pattern="[a-zA-ZñÑáéíóúÁÉÍÓÚ\s]+"
                                        title="Solo se permiten letras y espacios"
                                        placeholder="Ej. Ana López">
                                    <div class="invalid-feedback">Ingrese un nombre válido (solo letras).</div>
                                </div>
                            </div>

                            {{-- FECHA DE NACIMIENTO (CORREGIDO) --}}
                            <div class="mb-4">
                                <label class="form-label small fw-bold text-muted text-uppercase">Fecha de Nacimiento</label>
                                <div class="input-group">
                                    <span class="input-group-text rounded-start-pill"><i class="bi bi-calendar-date"></i></span>
                                    {{-- CAMBIO IMPORTANTE: name="fecha" y formateo con Carbon --}}
                                    <input type="date" name="fecha" class="form-control rounded-end-pill" 
                                        value="{{ old('fecha', isset($farmacia->user->f_nacimiento) ? \Carbon\Carbon::parse($farmacia->user->f_nacimiento)->format('Y-m-d') : '') }}" 
                                        required max="{{ date('Y-m-d', strtotime('-18 years')) }}">
                                    <div class="invalid-feedback">Este campo es obligatorio (debe ser mayor de 18 años).</div>
                                </div>
                            </div>

                            {{-- EMAIL --}}
                            <div class="mb-4">
                                <label class="form-label small fw-bold text-muted text-uppercase">Correo Electrónico</label>
                                <div class="input-group">
                                    <span class="input-group-text rounded-start-pill"><i class="bi bi-envelope"></i></span>
                                    <input type="email" name="email" class="form-control rounded-end-pill" 
                                        value="{{ old('email', $farmacia->user->email ?? '') }}" required placeholder="correo@ejemplo.com">
                                    <div class="invalid-feedback">Ingrese un correo válido.</div>
                                </div>
                            </div>

                            {{-- PASSWORD --}}
                            <div class="mb-2">
                                <label class="form-label small fw-bold text-muted text-uppercase">
                                    Contraseña
                                    @if(isset($farmacia)) <span class="fw-normal text-lowercase">(opcional)</span> @endif
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text rounded-start-pill"><i class="bi bi-key"></i></span>
                                    <input type="password" name="password" class="form-control rounded-end-pill" 
                                        {{ isset($farmacia) ? '' : 'required' }} minlength="8" placeholder="••••••••">
                                    <div class="invalid-feedback">Mínimo 8 caracteres.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- COLUMNA DERECHA: NEGOCIO --}}
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm rounded-5 h-100">
                        <div class="card-header bg-white p-4 border-bottom rounded-top-5">
                            <div class="d-flex align-items-center text-navy">
                                <i class="bi bi-shop fs-3 me-3"></i>
                                <h5 class="mb-0 fw-bold">Perfil del Negocio</h5>
                            </div>
                        </div>

                        <div class="card-body p-4">
                            
                            {{-- NOMBRE FARMACIA --}}
                            <div class="row g-3 mb-4">
                                <div class="col-md-12">
                                    <label class="form-label small fw-bold text-muted">Nombre de la Farmacia</label>
                                    <div class="input-group">
                                        <span class="input-group-text rounded-start-pill"><i class="bi bi-building"></i></span>
                                        <input type="text" name="nom_farmacia" class="form-control fw-bold text-navy rounded-end-pill" 
                                            value="{{ old('nom_farmacia', $farmacia->nom_farmacia ?? '') }}" required placeholder="Ej. Farmacia San José">
                                        <div class="invalid-feedback">Este campo es obligatorio.</div>
                                    </div>
                                </div>
                            </div>

                            <div class="row g-3 mb-4">
                                {{-- RFC --}}
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted">RFC</label>
                                    <div class="input-group">
                                        <span class="input-group-text rounded-start-pill"><i class="bi bi-card-heading"></i></span>
                                        <input type="text" name="rfc" class="form-control rounded-end-pill" 
                                            value="{{ old('rfc', $farmacia->rfc ?? '') }}" 
                                            minlength="12" maxlength="13" 
                                            pattern="[A-Z0-9]+" 
                                            style="text-transform: uppercase;"
                                            oninput="this.value = this.value.toUpperCase()"
                                            placeholder="XAXX010101000">
                                        <div class="invalid-feedback">Formato inválido (12-13 caracteres alfanuméricos).</div>
                                    </div>
                                </div>
                                {{-- TELEFONO (CORREGIDO: Solo números) --}}
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted">Teléfono</label>
                                    <div class="input-group">
                                        <span class="input-group-text rounded-start-pill"><i class="bi bi-telephone"></i></span>
                                        <input type="tel" name="telefono" class="form-control rounded-end-pill" 
                                            value="{{ old('telefono', $farmacia->telefono ?? '') }}" 
                                            required 
                                            maxlength="10" 
                                            pattern="[0-9]{10}"
                                            oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                            placeholder="Ej. 9671234567">
                                        <div class="invalid-feedback">Debe contener 10 dígitos numéricos.</div>
                                    </div>
                                </div>
                            </div>

                            {{-- HORARIOS --}}
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted">Horario Entrada</label>
                                    <div class="input-group">
                                        <span class="input-group-text rounded-start-pill"><i class="bi bi-clock"></i></span>
                                        <input type="time" name="horario_entrada" class="form-control rounded-end-pill" 
                                            value="{{ old('horario_entrada', $farmacia->horario_entrada ?? '') }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted">Horario Salida</label>
                                    <div class="input-group">
                                        <span class="input-group-text rounded-start-pill"><i class="bi bi-clock-fill"></i></span>
                                        <input type="time" name="horario_salida" class="form-control rounded-end-pill" 
                                            value="{{ old('horario_salida', $farmacia->horario_salida ?? '') }}" required>
                                    </div>
                                </div>
                            </div>

                            {{-- DESCRIPCION --}}
                            <div class="mb-4">
                                <div class="p-3 bg-light rounded-4 border">
                                    <label class="form-label fw-bold text-navy"><i class="bi bi-file-text me-2"></i>Descripción</label>
                                    <textarea name="descripcion" class="form-control bg-white border-0 shadow-sm rounded-3" rows="3" 
                                        required placeholder="Breve descripción de la farmacia...">{{ old('descripcion', $farmacia->descripcion ?? '') }}</textarea>
                                </div>
                            </div>

                            <hr class="text-muted opacity-25 mb-4">

                            {{-- UBICACIÓN --}}
                            <div class="mb-2">
                                <label class="form-label fw-bold text-navy mb-3"><i class="bi bi-geo-alt-fill me-2"></i>Ubicación</label>
                                <input type="hidden" name="latitud" id="latitud" value="{{ old('latitud', $farmacia->user->latitud ?? '') }}">
                                <input type="hidden" name="longitud" id="longitud" value="{{ old('longitud', $farmacia->user->longitud ?? '') }}">
                                
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
                                    {{ isset($farmacia) ? 'Actualizar Farmacia' : 'Guardar Farmacia' }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

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
                title: "Ubicación de la Farmacia",
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
    </script>
    @endpush
</x-layout>