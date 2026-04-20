<x-layout>
    @push('styles')
    <style>
        /* Blindaje de colores y estilo BuscaDoc */
        :root { --brand-navy: #0d2e4e; --brand-navy-light: #1a5f7a; }
        .text-navy { color: var(--brand-navy) !important; }
        .bg-navy { background-color: var(--brand-navy) !important; }
        
        .form-control-pill { border-radius: 50px !important; padding-left: 1.5rem; padding-right: 1.5rem; border: 1px solid #e2e8f0; background-color: #f8fafc; transition: all 0.3s ease; }
        .form-control-pill:focus { background-color: #fff; border-color: var(--brand-navy); box-shadow: 0 0 0 4px rgba(13, 46, 78, 0.1) !important; }
        
        .input-group-custom { border-radius: 50px; overflow: hidden; border: 1px solid #e2e8f0; background-color: #f8fafc; display: flex; align-items: center; }
        .input-group-custom .input-group-text { background: transparent; border: none; padding-left: 1.25rem; color: #94a3b8; }
        .input-group-custom .form-control { border: none; background: transparent; padding-top: 0.75rem; padding-bottom: 0.75rem; }

        .card-modern { border: none; border-radius: 2rem; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.05); }
        .schedule-row { transition: all 0.2s ease; border: 1px solid transparent; }
        .schedule-row:hover { border-color: #e2e8f0; background-color: #fff !important; transform: translateX(5px); }
        
        .btn-navy { background-color: var(--brand-navy); color: white; border-radius: 50px; font-weight: 600; transition: all 0.3s; }
        .btn-navy:hover { background-color: var(--brand-navy-light); color: white; transform: translateY(-2px); box-shadow: 0 5px 15px rgba(13, 46, 78, 0.2); }
        
        svg { fill: currentColor; }
    </style>
    @endpush

    @if(Auth::user() && Auth::user()->role == 'admin')
        <div class="container py-5" x-data="{ 
            citasActivas: {{ (isset($doctor) && $doctor->citas) ? 'true' : 'false' }},
            horarios: {{ isset($doctor) && $doctor->disponibilidades->count() > 0 
                ? $doctor->disponibilidades->map(fn($h) => ['dia' => (string)$h->dia_semana, 'inicio' => substr($h->hora_inicio, 0, 5), 'fin' => substr($h->hora_fin, 0, 5)])->toJson() 
                : '[{dia: \'1\', inicio: \'09:00\', fin: \'14:00\'}]' }},
            addHorario() { this.horarios.push({dia: '1', inicio: '09:00', fin: '18:00'}) },
            removeHorario(index) { this.horarios.splice(index, 1) }
        }">
            
            {{-- HEADER --}}
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-5 gap-3">
                <div>
                    <h1 class="fw-bold text-navy mb-1">{{ isset($doctor) ? 'Editar Especialista' : 'Registrar Nuevo Doctor' }}</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('doctores.index') }}" class="text-decoration-none text-muted">Panel Administrativo</a></li>
                            <li class="breadcrumb-item active text-navy fw-bold">{{ isset($doctor) ? $doctor->user->name : 'Nuevo Registro' }}</li>
                        </ol>
                    </nav>
                </div>
                <a href="{{ route('doctores.index') }}" class="btn btn-light rounded-pill px-4 shadow-sm border">
                    <x-mcr-angle-left class="mb-1"/> Volver al listado
                </a>
            </div>

            <form method="POST"
                action="{{ isset($doctor) ? route('doctores.update', $doctor->id) : route('doctores.store') }}"
                class="needs-validation" novalidate enctype="multipart/form-data">
                @csrf
                @if(isset($doctor)) @method('PUT') @endif

                <div class="row g-4">
                    {{-- COLUMNA IZQUIERDA: DATOS PERSONALES --}}
                    <div class="col-lg-4">
                        <div class="card card-modern h-100 overflow-hidden">
                            <div class="bg-navy p-4 text-white">
                                <div class="d-flex align-items-center">
                                    <x-mcl-user-circle style="width: 1.5rem;" class="icon-white me-2"/>
                                    <h5 class="mb-0 fw-bold">Datos de Usuario</h5>
                                </div>
                            </div>
                            
                            <div class="card-body p-4">
                                {{-- FOTO --}}
                                <div class="text-center mb-4">
                                    <div class="position-relative d-inline-block">
                                        <div class="rounded-circle overflow-hidden shadow-sm border border-4 border-white bg-light" style="width: 140px; height: 140px;">
                                            <img id="profilePreview"
                                                src="{{ isset($doctor) && $doctor->user->foto ? asset('storage/' . $doctor->user->foto) : 'https://ui-avatars.com/api/?name=Doc&background=f1f5f9&color=0d2e4e&size=256' }}"
                                                class="w-100 h-100 object-fit-cover">
                                        </div>
                                        <label for="fotoInput" class="icon-white position-absolute bottom-0 end-0 bg-navy text-white rounded-circle p-2 shadow pointer-event" style="cursor:pointer; width: 42px; height: 42px; display: flex; align-items:center; justify-content:center;">
                                            <x-mcl-camera style=" width: 1.2rem;"/>
                                        </label>
                                        <input type="file" name="image" id="fotoInput" class="d-none" accept="image/*">
                                    </div>
                                    <p class="text-muted small mt-3">Sube una foto profesional en formato JPG o PNG.</p>
                                </div>

                                {{-- CAMPOS DE CUENTA --}}
                                <div class="mb-3">
                                    <label class="form-label small fw-bold text-muted text-uppercase">Nombre Completo</label>
                                    <input type="text" name="name" class="form-control form-control-pill" value="{{ old('name', $doctor->user->name ?? '') }}" required placeholder="Nombre del médico">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label small fw-bold text-muted text-uppercase">Fecha de Nacimiento</label>
                                    <input type="date" name="fecha" class="form-control form-control-pill" 
                                        value="{{ old('fecha', isset($doctor->user->f_nacimiento) ? \Carbon\Carbon::parse($doctor->user->f_nacimiento)->format('Y-m-d') : '') }}" 
                                        required max="{{ date('Y-m-d', strtotime('-24 years')) }}">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label small fw-bold text-muted text-uppercase">Email Institucional</label>
                                    <input type="email" name="email" class="form-control form-control-pill" value="{{ old('email', $doctor->user->email ?? '') }}" required placeholder="doctor@buscadoc.com">
                                </div>

                                <div class="mb-0">
                                    <label class="form-label small fw-bold text-muted text-uppercase">Contraseña @if(isset($doctor)) (Opcional) @endif</label>
                                    <input type="password" name="password" class="form-control form-control-pill" {{ isset($doctor) ? '' : 'required' }} minlength="8" placeholder="••••••••">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- COLUMNA DERECHA: PERFIL PROFESIONAL --}}
                    <div class="col-lg-8">
                        <div class="card card-modern border-0">
                            <div class="card-body p-4 p-md-5">
                                <div class="d-flex align-items-center mb-4 text-navy border-bottom pb-3">
                                    <x-mcr-folder class="me-3" style="width: 2rem;"/>
                                    <h4 class="mb-0 fw-bold">Información de Especialidad</h4>
                                </div>

                                <div class="row g-4">
                                    {{-- ESPECIALIDAD --}}
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold text-muted text-uppercase">Especialidad Médica</label>
                                        <select name="especialidad_id" class="form-select form-control-pill fw-bold text-navy" required>
                                            <option value="" disabled selected>Selecciona especialidad...</option>
                                            @foreach($especialidades as $esp)
                                                <option value="{{ $esp->id }}" {{ (isset($doctor) && $doctor->especialidades->contains($esp->id)) ? 'selected' : '' }}>
                                                    {{ $esp->nombre }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    {{-- CÉDULA --}}
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold text-muted text-uppercase">Cédula Profesional</label>
                                        <input type="text" name="cedula" class="form-control form-control-pill" value="{{ old('cedula', $doctor->cedula ?? '') }}" required placeholder="Ej. 12345678">
                                    </div>

                                    {{-- COSTO Y DURACIÓN --}}
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold text-muted text-uppercase">Costo de Consulta</label>
                                        <div class="input-group-custom">
                                            <span class="input-group-text">$</span>
                                            <input type="number" name="costos" step="0.01" class="form-control" value="{{ old('costos', $doctor->costo ?? '') }}" required placeholder="0.00">
                                        </div>
                                    </div>

                                    <div class="col-md-6" x-show="citasActivas">
                                        <label class="form-label small fw-bold text-muted text-uppercase">Duración Estimada</label>
                                        <select name="duracion_cita" class="form-select form-control-pill">
                                            <option value="15" {{ (isset($doctor) && $doctor->duracion_cita == 15) ? 'selected' : '' }}>15 minutos</option>
                                            <option value="30" {{ (!isset($doctor) || $doctor->duracion_cita == 30) ? 'selected' : '' }}>30 minutos</option>
                                            <option value="45" {{ (isset($doctor) && $doctor->duracion_cita == 45) ? 'selected' : '' }}>45 minutos</option>
                                            <option value="60" {{ (isset($doctor) && $doctor->duracion_cita == 60) ? 'selected' : '' }}>1 hora</option>
                                        </select>
                                    </div>

                                    {{-- SWITCH CITAS --}}
                                    <div class="col-12">
                                        <div class="p-3 rounded-4 border bg-light d-flex justify-content-between align-items-center">
                                            <div class="d-flex align-items-center">
                                                <x-mcr-calendar class="text-navy me-3" style="width: 1.5rem;"/>
                                                <div>
                                                    <h6 class="mb-0 fw-bold text-navy">Gestión de Agenda</h6>
                                                    <p class="mb-0 text-muted small">Permite que los pacientes agenden citas desde la web.</p>
                                                </div>
                                            </div>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input shadow-none" type="checkbox" name="citas" id="citas" value="1" x-model="citasActivas" style="width: 3rem; height: 1.5rem;">
                                            </div>
                                        </div>
                                    </div>

                                    {{-- HORARIOS DINÁMICOS --}}
                                    <div class="col-12 mt-4">
                                        <label class="form-label fw-bold text-navy"><x-mcr-clock class="me-2" style="width: 1.2rem;"/>Disponibilidad Semanal</label>
                                        <div class="bg-light p-3 rounded-4 border">
                                            <template x-for="(horario, index) in horarios" :key="index">
                                                <div class="row g-2 mb-2 align-items-center bg-white p-3 rounded-4 border shadow-sm mx-0 schedule-row">
                                                    <div class="col-md-4">
                                                        <select :name="`horarios[${index}][dia]`" x-model="horario.dia" class="form-select border-0 bg-light rounded-pill small fw-bold">
                                                            <option value="1">Lunes</option><option value="2">Martes</option>
                                                            <option value="3">Miércoles</option><option value="4">Jueves</option>
                                                            <option value="5">Viernes</option><option value="6">Sábado</option>
                                                            <option value="0">Domingo</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <input type="time" :name="`horarios[${index}][inicio]`" x-model="horario.inicio" class="form-control border-0 bg-light rounded-pill text-center">
                                                    </div>
                                                    <div class="col-md-3">
                                                        <input type="time" :name="`horarios[${index}][fin]`" x-model="horario.fin" class="form-control border-0 bg-light rounded-pill text-center">
                                                    </div>
                                                    <div class="col-md-2 text-center">
                                                        <button type="button" @click="removeHorario(index)" class="btn btn-outline-danger btn-sm rounded-circle border-0" x-show="horarios.length > 1">
                                                            <i class="bi bi-trash3-fill"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </template>
                                            <button type="button" @click="addHorario()" class="btn btn-sm btn-navy rounded-pill px-4 mt-3 py-2">
                                                <x-mcr-plus-circle class="icon-white me-2" style="width: 1rem;"/>Añadir Bloque de Horario
                                            </button>
                                        </div>
                                    </div>

                                    {{-- DESCRIPCIÓN --}}
                                    <div class="col-12">
                                        <label class="form-label small fw-bold text-muted text-uppercase">Descripción Profesional</label>
                                        <textarea name="descripcion" class="form-control rounded-4 p-3 border shadow-sm" rows="4" required placeholder="Escriba la trayectoria y biografía del doctor...">{{ old('descripcion', $doctor->descripcion ?? '') }}</textarea>
                                    </div>

                                    {{-- MAPA --}}
                                    <div class="col-12 mt-4">
                                        <label class="form-label fw-bold text-navy mb-3"><x-mcr-location-pin class="me-2" style="width: 1.2rem;"/>Ubicación en Ocosingo</label>
                                        <input type="hidden" name="latitud" id="latitud" value="{{ old('latitud', $doctor->user->latitud ?? '') }}">
                                        <input type="hidden" name="longitud" id="longitud" value="{{ old('longitud', $doctor->user->longitud ?? '') }}">
                                        <div class="rounded-4 overflow-hidden shadow-sm border" id="map" style="height: 350px; width: 100%;"></div>
                                        <p class="text-muted small mt-2"><i class="bi bi-info-circle me-1"></i> Arrastre el marcador para fijar la ubicación del consultorio.</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="card-footer bg-light p-4 border-top-0 rounded-bottom-5 text-end">
                                <button type="submit" class="btn btn-navy px-5 py-3 shadow">
                                    <x-mcl-check-circle class="icon-white me-2" style="width: 1.2rem;"/>
                                    {{ isset($doctor) ? 'Actualizar Información' : 'Registrar Doctor en Sistema' }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    @endif

    @push('scripts')
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.min.js"></script>
    <script>
        // Previsualización de foto
        document.getElementById('fotoInput').onchange = evt => {
            const [file] = fotoInput.files
            if (file) { document.getElementById('profilePreview').src = URL.createObjectURL(file) }
        }

        // Validación nativa de Bootstrap
        (function () {
            'use strict';
            var forms = document.querySelectorAll('.needs-validation');
            Array.prototype.slice.call(forms).forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    if (!form.checkValidity()) {
                        event.preventDefault(); event.stopPropagation();
                    }
                    form.classList.add('was-validated')
                }, false)
            })
        })()
    </script>

    <script src="https://maps.googleapis.com/maps/api/js?key={{ env('API_KEY') }}&callback=initMap" async defer></script>
    <script>
        let map; let marker;
        function initMap() {
            const initialLat = parseFloat(document.getElementById('latitud').value) || 16.9080;
            const initialLng = parseFloat(document.getElementById('longitud').value) || -92.0946;
            const myLatLng = { lat: initialLat, lng: initialLng };

            map = new google.maps.Map(document.getElementById("map"), {
                zoom: 15, center: myLatLng, 
                styles: [ { "featureType": "poi", "stylers": [{ "visibility": "off" }] } ]
            });

            marker = new google.maps.Marker({
                position: myLatLng, map: map, draggable: true,
                animation: google.maps.Animation.DROP
            });

            marker.addListener("dragend", function (event) { updateInputs(event.latLng.lat(), event.latLng.lng()); });
            map.addListener("click", function (event) { marker.setPosition(event.latLng); updateInputs(event.latLng.lat(), event.latLng.lng()); });
        }
        function updateInputs(lat, lng) {
            document.getElementById('latitud').value = lat;
            document.getElementById('longitud').value = lng;
        }
    </script>
    @endpush
</x-layout>