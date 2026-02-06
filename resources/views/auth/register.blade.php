@extends('layouts.app')

@section('content')
    {{-- Alpine.js --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <style>
        body {
            background-color: #f9fafb;
        }

        .form-control-pill,
        .form-select-pill {
            border-radius: 50px;
            background-color: #f1f5f9;
            border: 1px solid transparent;
            padding: 12px 25px;
            font-size: 0.95rem;
            width: 100%;
            transition: all 0.2s;
        }

        input[type=file].form-control-pill {
            padding: 9px 25px;
        }
        .text-navy {
            color: #0d2e4e!important;
        }
        .form-control-pill:focus,
        .form-select-pill:focus {
            background-color: #fff;
            border-color: #0d2e4e;
            box-shadow: 0 0 0 4px rgba(13, 46, 78, 0.1);
            outline: none;
        }

        .btn-navy {
            background-color: #0d2e4e;
            color: white;
            border-radius: 50px;
            padding: 12px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-navy:hover {
            background-color: #16436d;
            color: white;
            transform: translateY(-2px);
        }

        .register-card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
        }

        .divider-text {
            display: flex;
            align-items: center;
            text-align: center;
            color: #6c757d;
            font-size: 0.75rem;
            font-weight: bold;
            letter-spacing: 1px;
            margin: 25px 0;
        }

        .divider-text::before,
        .divider-text::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid #e2e8f0;
        }

        .divider-text:not(:empty)::before {
            margin-right: 1em;
        }

        .divider-text:not(:empty)::after {
            margin-left: 1em;
        }

        .file-upload-wrapper {
            position: relative;
            width: 100%;
            height: 50px;
        }

        .file-upload-input {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            cursor: pointer;
            z-index: 2;
        }

        .file-upload-label {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f1f5f9;
            border: 2px dashed #cbd5e1;
            border-radius: 50px;
            color: #64748b;
            font-weight: 600;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            z-index: 1;
        }

        .file-upload-wrapper:hover .file-upload-label {
            background-color: #e2e8f0;
            border-color: #0d2e4e;
            color: #0d2e4e;
        }

        .file-upload-label.has-file {
            background-color: #e0f2fe;
            border-style: solid;
            border-color: #0d2e4e;
            color: #0d2e4e;
        }

        .custom-select-btn {
            text-align: left;
            display: flex;
            align-items: center;
            justify-content: space-between;
            cursor: pointer;
            background-color: #f1f5f9; /* Mismo color que tus inputs */
        }

        .custom-options {
            position: absolute;
            top: 110%; /* Aparece justo debajo del botón */
            left: 0;
            width: 100%;
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            z-index: 100; /* IMPORTANTE: Para que flote sobre los campos de abajo */
            overflow: hidden;
            border: 1px solid #e2e8f0;
        }

        .custom-option {
            padding: 12px 20px;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 10px; /* Espacio entre icono y texto */
            color: #475569;
            font-weight: 500;
        }

        .custom-option:hover {
            background-color: #f1f5f9;
            color: #0d2e4e; /* Tu color Navy */
        }

        .custom-option.selected {
            background-color: #e0f2fe;
            color: #0d2e4e;
            font-weight: bold;
        }

        .bg-navy {
            background-color: #0d2e4e!important;
        }
    </style>

    <div class="container py-5">
        <div class="row align-items-center justify-content-center" style="min-height: 85vh;">

            {{-- COLUMNA IZQUIERDA --}}
            <div class="col-lg-5 text-center text-lg-start mb-5 mb-lg-0 pe-lg-5">
                <div class="mb-4">
                    <img src="{{ asset('images/logo_negro.png') }}" width="280px" alt="Logo">
                </div>
                <p class="fs-5 text-muted lh-base">
                    Únete a la plataforma de salud más completa. Gestiona citas, recetas y servicios en un solo lugar.
                </p>
            </div>

            {{-- COLUMNA DERECHA --}}
            <div class="col-lg-7">
                <div class="card register-card p-4 p-md-5 bg-white">
                    <div class="card-body p-0">
                        <h3 class="text-center fw-bold mb-2">Crear Cuenta</h3>
                        <p class="text-center text-muted small mb-4">Completa tus datos para comenzar</p>

                        <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data"
                            x-data="{ role: '{{ old('role', 'paciente') }}' }">
                            @csrf

                            {{-- personal data --}}
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label text-muted small ps-3 fw-bold">Nombre Completo</label>
                                    <input type="text" name="name"
                                        class="form-control form-control-pill @error('name') is-invalid @enderror"
                                        value="{{ old('name') }}" required placeholder="Ej: Juan Pérez">
                                    @error('name') <span class="invalid-feedback ps-3">{{ $message }}</span> @enderror
                                </div>

                                {{-- correo --}}
                                <div class="col-12">
                                    <label class="form-label text-muted small ps-3 fw-bold">Correo Electrónico</label>
                                    <input type="email" name="email"
                                        class="form-control form-control-pill @error('email') is-invalid @enderror"
                                        value="{{ old('email') }}" required placeholder="juan@mail.com">
                                    @error('email') <span class="invalid-feedback ps-3">{{ $message }}</span> @enderror
                                </div>

                                {{-- fecha --}}
                                <div class="col-md-6">
                                    <label class="form-label text-muted small ps-3 fw-bold">Fecha de Nacimiento</label>
                                    <input type="date" name="f_nacimiento"
                                        class="form-control form-control-pill @error('f_nacimiento') is-invalid @enderror"
                                        value="{{ old('f_nacimiento') }}" required
                                        max="{{ date('Y-m-d', strtotime('-18 years')) }}" onchange="validarEdad(this)">
                                    <div class="invalid-feedback ms-3">Debe ser mayor de 18 años.</div>
                                    @error('f_nacimiento') <span class="invalid-feedback ps-3">{{ $message }}</span>
                                    @enderror
                                </div>

                                {{-- foto --}}
                                <div class="col-md-6">
                                    <label class="form-label text-muted small ps-3 fw-bold">Foto de Perfil</label>

                                    {{-- Previsualización de la foto --}}
                                    <div class="d-flex align-items-center gap-3 mb-2">
                                        {{-- Imagen real (oculta al inicio) --}}
                                        <img id="imagePreview" src="#" alt="Tu foto" class="rounded-circle border shadow-sm"
                                            style="width: 60px; height: 60px; object-fit: cover; display: none;">

                                        {{-- input oculto de lafoto --}}
                                        <div class="file-upload-wrapper flex-grow-1">
                                            <input type="file" name="foto" id="foto_input"
                                                class="file-upload-input @error('foto') is-invalid @enderror"
                                                accept="image/*" onchange="previewAndLabel(this)">

                                            <label for="foto_input" class="file-upload-label" id="foto_label_text">
                                                <span class="small">Seleccionar...</span>
                                            </label>
                                        </div>
                                    </div>
                                    @error('foto') <span
                                        class="text-danger small ps-3 d-block"><strong>{{ $message }}</strong></span>
                                    @enderror
                                </div>
                            </div>

                            <div class="divider-text">TIPO DE PERFIL</div>
                            
                            {{-- seletsion de rol --}}
                            <div class="mb-4 position-relative" x-data="{ open: false }">
                                <label class="form-label text-muted small fw-bold mb-1">¿Cómo usarás la plataforma?</label>
                                <input type="hidden" name="role" x-model="role">
                                <div @click="open = !open" @click.outside="open = false" 
                                    class="form-select-pill custom-select-btn border-0 shadow-sm fw-bold text-dark">
                                    
                                    {{-- Texto dinámico según la selección --}}
                                    <span x-text="role === 'paciente' ? 'Paciente (Busco atención)' : 
                                                (role === 'doctor' ? 'Doctor (Ofrezco servicios)' : 
                                                'Farmacia (Vendo productos)')">
                                    </span>

                                    {{-- Flechita hacia abajo --}}
                                    <i class="bi bi-chevron-down small text-muted" :class="open ? 'rotate-180' : ''"></i>
                                </div>

                                {{-- opciones --}}
                                <div x-show="open" x-transition 
                                    class="custom-options" 
                                    style="display: none;">
                                    
                                    {{-- paciente --}}
                                    <div class="custom-option" 
                                        :class="role === 'paciente' ? 'selected' : ''"
                                        @click="role = 'paciente'; open = false">
                                        <x-icons.patient class="text-primary" style="width: 24px; height: 24px;" class="text-navy"/>
                                        <span>Paciente (Busco atención de hombres)</span>
                                    </div>

                                    {{-- doc --}}
                                    <div class="custom-option" 
                                        :class="role === 'doctor' ? 'selected' : ''"
                                        @click="role = 'doctor'; open = false"> 
                                        <x-icons.doctor class="text-info" style="width: 24px; height: 24px;" class="text-navy"/>
                                        <span>Doctor (Ofrezco servicios)</span>
                                    </div>

                                    {{-- farmacia --}}
                                    <div class="custom-option" 
                                        :class="role === 'farmacia' ? 'selected' : ''"
                                        @click="role = 'farmacia'; open = false">
                                        <x-icons.pharmacy class="text-success" style="width: 24px; height: 24px;" class="text-navy"/>
                                        <span>Farmacia (Vendo productos)</span>
                                    </div>

                                </div>
                            </div>

                            {{-- si es doc --}}
                            <div x-show="role === 'doctor'" x-transition>
                                <h6 class="text-primary border-bottom pb-2 mb-3 small fw-bold text-navy">DATOS DE TRABAJO</h6>
                                <div class="row g-3">
                                    <div class="col-md-12">
                                        <input type="textarea" name="descripcion" class="form-control form-control-pill"
                                            placeholder="Descripción de usted y su trabajo" value="{{ old('descripcion') }}">
                                    </div>
                                    <div class="col-md-6">
                                        <input type="text" name="cedula" class="form-control form-control-pill"
                                            placeholder="Cédula Profesional" value="{{ old('cedula') }}">
                                    </div>
                                    <div class="col-md-6">
                                        <input type="number" name="costo" class="form-control form-control-pill"
                                            placeholder="Costo estimado por consulta ($)" value="{{ old('costo') }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="small text-muted ms-3 fw-bold">Horario de entrada</label>
                                        <input type="time" name="horario_entrada" class="form-control form-control-pill"
                                            value="{{ old('horario_entrada') }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="small text-muted ms-3 fw-bold">Horario de salida</label>
                                        <input type="time" name="horario_salida" class="form-control form-control-pill"
                                            value="{{ old('horario_salida') }}">
                                    </div>
                                    <div class="col-md-12">
                                        <label class="small text-muted ms-3 fw-bold">Escriba los idiomas que domine</label>
                                        <input type="text" name="idiomas" class="form-control form-control-pill"
                                            value="{{ old('idiomas') }}">
                                    </div>
                                    <div class="col-12">
                                        <label class="small text-muted ms-3 fw-bold">Seleccione una especialidad</label>
                                        <div class="bg-light p-3 rounded-4 border d-flex flex-wrap gap-2">
                                            @foreach($especialidades ?? [] as $esp)
                                                <div class="form-check form-check-inline mb-0">
                                                    <input class="form-check-input" type="checkbox" name="especialidades[]"
                                                        value="{{ $esp->id }}" id="esp_{{ $esp->id }}">
                                                    <label class="form-check-label small"
                                                        for="esp_{{ $esp->id }}">{{ $esp->nombre }}</label>
                                                </div>
                                            @endforeach
                                            @if(empty($especialidades)) <small class="text-muted fst-italic">No hay
                                            especialidades cargadas.</small> @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- si es paciente --}}
                            <div x-show="role === 'paciente'" x-transition>
                                <h6 class="text-info border-bottom pb-2 mb-3 small fw-bold text-navy">DATOS MÉDICOS BÁSICOS</h6>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <select name="tipo_sangre" class="form-select form-select-pill">
                                            <option value="" disabled selected>Tipo de Sangre</option>
                                            @foreach(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $ts)
                                                <option value="{{ $ts }}" {{ old('tipo_sangre') == $ts ? 'selected' : '' }}>
                                                    {{ $ts }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <input type="text" name="contacto_emergencia" class="form-control form-control-pill"
                                            placeholder="Contacto Emergencia" value="{{ old('contacto_emergencia') }}">
                                    </div>
                                    <div class="col-12">
                                        <textarea name="alergias" class="form-control form-control-pill" rows="2"
                                            placeholder="Escriba que alergias tiene"
                                            style="border-radius: 20px; resize: none;">{{ old('alergias') }}</textarea>
                                    </div>
                                    <div class="col-md-12">
                                        <input type="text" name="cirugias" class="form-control form-control-pill"
                                            placeholder="Escriba que cirugías ha tenido" value="{{ old('cirugias') }}">
                                    </div>
                                    <div class="col-md-12">
                                        <input type="text" name="padecimientos" class="form-control form-control-pill"
                                            placeholder="Describa que padecimientos tiene (diabetes, depresión, gastritis, etc..)" value="{{ old('padecimientos') }}">
                                    </div>
                                    <div class="col-md-12">
                                        <input type="text" name="habitos" class="form-control form-control-pill"
                                            placeholder="Describa los hábitos que tiene (si fuma, o toma, o hace deporte, etc...)" value="{{ old('padecimientos') }}">
                                    </div>
                                </div>
                            </div>

                            {{-- si es farm --}}
                            <div x-show="role === 'farmacia'" x-transition>
                                <h6 class="text-success border-bottom pb-2 mb-3 small fw-bold text-navy">DATOS DEL NEGOCIO</h6>
                                <div class="row g-3">
                                    <div class="col-md-12">
                                        <input type="textarea" name="descripcion" class="form-control form-control-pill"
                                            placeholder="Descripción de la farmacia" value="{{ old('descripcion') }}">
                                    </div>
                                    <div class="col-12">
                                        <input type="text" name="nom_farmacia" class="form-control form-control-pill"
                                            placeholder="Nombre de la Farmacia" value="{{ old('nom_farmacia') }}">
                                    </div>
                                    <div class="col-md-6">
                                        <input type="text" name="rfc" class="form-control form-control-pill"
                                            placeholder="RFC" value="{{ old('rfc') }}">
                                    </div>
                                    <div class="col-md-6">
                                        <input type="text" name="telefono" class="form-control form-control-pill"
                                            placeholder="Teléfono" value="{{ old('telefono') }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="small text-muted ms-3 fw-bold">Horario de entrada</label>
                                        <input type="time" name="horario_entrada" class="form-control form-control-pill"
                                            value="{{ old('horario_entrada') }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="small text-muted ms-3 fw-bold">Horario de salida</label>
                                        <input type="time" name="horario_salida" class="form-control form-control-pill"
                                            value="{{ old('horario_salida') }}">
                                    </div>
                                </div>
                            </div>

                            {{-- mapa, si es doc o farmacia --}}
                            <div x-show="role === 'doctor' || role === 'farmacia'" x-transition>
                                <div class="divider-text mt-4">UBICACIÓN</div>
                                
                                <div class="row g-3">
                                    <div class="col-12">
                                        <div class="card border-0 shadow-lg rounded-4 overflow-hidden mb-4">
                                            <div class="card-header bg-navy text-white py-3">
                                                {{-- Cambiamos el texto dinámicamente según el rol --}}
                                                <h5 class="mb-0 fw-bold">
                                                    <i class="bi bi-geo-alt-fill me-2"></i> 
                                                    <span x-text="role === 'farmacia' ? 'Ubicación del Negocio' : 'Ubicación del Consultorio'"></span>
                                                </h5>
                                            </div>
                                            <div class="card-body p-4 bg-white">
                                                <p class="text-muted ms-2 mb-3"><i class="bi bi-info-circle"></i> Arrastra el marcador rojo para indicar la ubicación exacta.</p>
                                                
                                                {{-- Solo UN juego de inputs con IDs únicos --}}
                                                <input type="hidden" name="latitud" id="latitud" value="{{ old('latitud') }}">
                                                <input type="hidden" name="longitud" id="longitud" value="{{ old('longitud') }}">

                                                <div class="shadow-sm rounded-4 overflow-hidden border border-light">
                                                    {{-- Solo UN div de mapa con ID único --}}
                                                    <div id="map" style="height: 400px; width: 100%;"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="divider-text mt-4">SEGURIDAD</div>

                            {{-- pswd --}}
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label text-muted small ps-3 fw-bold">Contraseña</label>
                                    <input type="password" name="password"
                                        class="form-control form-control-pill @error('password') is-invalid @enderror"
                                        required placeholder="••••••••">
                                    @error('password') <span class="invalid-feedback ps-3">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted small ps-3 fw-bold">Confirmar</label>
                                    <input type="password" name="password_confirmation"
                                        class="form-control form-control-pill" required placeholder="••••••••">
                                </div>
                            </div>

                            <div class="d-grid mb-4">
                                <button type="submit" class="btn btn-navy shadow-sm py-3 fs-6">
                                    {{ __('Registrar Cuenta') }} <i class="bi bi-arrow-right-circle ms-2"></i>
                                </button>
                            </div>

                            <div class="text-center small">
                                ¿Ya tienes una cuenta?
                                <a href="{{ route('login') }}" class="fw-bold text-decoration-none"
                                    style="color: #0d2e4e;">Iniciar sesión</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function previewAndLabel(input) {
            const label = document.getElementById('foto_label_text');
            const labelTextSpan = label.querySelector('span');
            const placeholder = document.getElementById('placeholderIcon');
            const preview = document.getElementById('imagePreview');

            if (input.files && input.files[0]) {
                // 1. Cambiar texto del botón
                let fileName = input.files[0].name;
                if (fileName.length > 15) fileName = fileName.substring(0, 12) + '...';
                labelTextSpan.textContent = fileName;
                label.classList.add('has-file');

                // 2. Mostrar previsualización
                var reader = new FileReader();
                reader.onload = function (e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block'; // Mostrar imagen
                    placeholder.style.display = 'none'; // Ocultar icono gris
                }
                reader.readAsDataURL(input.files[0]);
            } else {
                // Resetear si cancela
                labelTextSpan.textContent = 'Seleccionar...';
                label.classList.remove('has-file');
                preview.style.display = 'none';
                placeholder.style.display = 'flex';
            }
        }
    </script>


<script>
        let map;
        let marker;

        // Hacemos la función global
        window.initMap = function() {
            // Coordenadas por defecto (Chiapas o lo que tengas)
            const defLat = 16.9080; 
            const defLng = -92.0946;

            // Intentamos obtener valores previos (old) si existen
            const inputLatVal = document.getElementById('latitud').value;
            const inputLngVal = document.getElementById('longitud').value;

            const myLat = inputLatVal ? parseFloat(inputLatVal) : defLat;
            const myLng = inputLngVal ? parseFloat(inputLngVal) : defLng;
            const myLatLng = { lat: myLat, lng: myLng };

            map = new google.maps.Map(document.getElementById("map"), {
                zoom: 15,
                center: myLatLng,
                mapTypeId: google.maps.MapTypeId.ROADMAP
            });

            marker = new google.maps.Marker({
                position: myLatLng,
                map: map,
                draggable: true,
                title: "Ubicación Seleccionada",
                animation: google.maps.Animation.DROP
            });

            // Eventos
            marker.addListener("dragend", function (event) {
                updateInputs(event.latLng.lat(), event.latLng.lng());
            });

            map.addListener("click", function (event) {
                marker.setPosition(event.latLng);
                updateInputs(event.latLng.lat(), event.latLng.lng());
            });
            
            // Inicializar inputs si estaban vacíos
            if(!inputLatVal) updateInputs(myLat, myLng);
        }

        function updateInputs(lat, lng) {
            document.getElementById('latitud').value = lat;
            document.getElementById('longitud').value = lng;
        }
    </script>

    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDzSz-VqueMjM2OEaddCFuNLSl7LsCpqzQ&callback=initMap" async defer></script>

@endsection