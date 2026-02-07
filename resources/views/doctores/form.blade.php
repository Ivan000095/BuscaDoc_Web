<x-layout>
    <div class="container pb-5">
        {{-- Encabezado con estilo --}}
        <div class="row my-5 text-center">
            <div class="col-12">
                <h1 class="fw-bold text-navy display-5">{{ isset($doctor) ? 'Editar' : 'Agregar' }} Doctor</h1>
                <p class="text-muted">Complete la información profesional y de ubicación</p>
            </div>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST"
            action="{{ isset($doctor) ? route('doctores.update', $doctor->id) : route('doctores.store') }}"
            class="row g-4 needs-validation justify-content-center" novalidate enctype="multipart/form-data">

            @csrf
            @if(isset($doctor))
                @method('PUT')
            @endif

            <div class="col-lg-10">
                <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                    <div class="card-header bg-custom-dark text-white py-3">
                        <h5 class="mb-0 fw-bold"><i class="bi bi-person-badge me-2"></i> Datos de Cuenta</h5>
                    </div>
                    <div class="card-body p-4 bg-white">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label for="name" class="form-label fw-bold ms-3">Nombre Completo</label>
                                <input name="name" type="text" 
                                    class="form-control form-control-lg rounded-pill bg-light border-0 shadow-sm ps-4"
                                    id="name" value="{{ old('name', $doctor->user->name ?? '') }}" required maxlength="100" placeholder="Ej. Juan Pérez">
                                <div class="invalid-feedback ms-3">El nombre es obligatorio.</div>
                            </div>

                            <div class="col-md-6">
                                <label for="fecha" class="form-label fw-bold ms-3">Fecha de Nacimiento</label>
                                <input name="fecha" type="date" 
                                    class="form-control form-control-lg rounded-pill bg-light border-0 shadow-sm ps-4"
                                    id="fecha" 
                                    value="{{ old('fecha', $doctor->user->f_nacimiento ?? '') }}" 
                                    required
                                    max="{{ date('Y-m-d', strtotime('-18 years')) }}"
                                    onchange="validarEdad(this)">
                                <div class="invalid-feedback ms-3">Debe ser mayor de 18 años.</div>
                            </div>

                            <div class="col-md-6">
                                <label for="email" class="form-label fw-bold ms-3">Correo Electrónico</label>
                                <input name="email" type="email" 
                                    class="form-control form-control-lg rounded-pill bg-light border-0 shadow-sm ps-4"
                                    id="email" value="{{ old('email', $doctor->user->email ?? '') }}" required placeholder="correo@ejemplo.com">
                                <div class="invalid-feedback ms-3">
                                    {{ $errors->first('email') ?: 'El correo es obligatorio y único.' }}
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label for="password" class="form-label fw-bold ms-3">
                                    Contraseña
                                    @if(isset($doctor))
                                        <span class="text-muted small fw-normal">(Opcional al editar)</span>
                                    @endif
                                </label>
                                <input name="password" type="password"
                                    class="form-control form-control-lg rounded-pill bg-light border-0 shadow-sm ps-4" 
                                    id="password" {{ isset($doctor) ? '' : 'required' }} minlength="8" placeholder="••••••••">
                                <div class="invalid-feedback ms-3">La contraseña es obligatoria (mínimo 8 caracteres).</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-10">
                <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                    <div class="card-header bg-custom-dark text-white py-3">
                        <h5 class="mb-0 fw-bold"><i class="bi bi-briefcase-fill me-2"></i> Perfil Profesional</h5>
                    </div>
                    <div class="card-body p-4 bg-white">
                        <div class="row g-4">
                            
                            {{-- Gaspar --}}
                            <div class="col-md-6">
                                <label for="especialidad_id" class="form-label fw-bold ms-3">Especialidad</label>
                                <select name="especialidad_id" id="especialidad_id" 
                                    class="form-select form-select-lg rounded-pill bg-light border-0 shadow-sm ps-4" required>
                                    <option value="" selected disabled>Seleccione una especialidad...</option>
                                    
                                    @foreach($especialidades as $esp)
                                        <option value="{{ $esp->id }}" 
                                            {{ (isset($doctor) && $doctor->especialidades->contains($esp->id)) ? 'selected' : '' }}>
                                            {{ $esp->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback ms-3">Seleccione una especialidad.</div>
                            </div>
                            <div class="col-md-6">
                                <label for="cedula" class="form-label fw-bold ms-3">Cédula Profesional</label>
                                <input name="cedula" type="text" 
                                    class="form-control form-control-lg rounded-pill bg-light border-0 shadow-sm ps-4"
                                    id="cedula" value="{{ old('cedula', $doctor->cedula ?? '') }}" required placeholder="Núm. de Cédula">
                                <div class="invalid-feedback ms-3">La cédula es obligatoria.</div>
                            </div>

                            {{-- Costo Consulta --}}
                            <div class="col-md-4">
                                <label for="costos" class="form-label fw-bold ms-3">Costo Consulta</label>
                                <div class="input-group input-group-lg has-validation shadow-sm rounded-pill overflow-hidden border-0">
                                    <span class="input-group-text bg-light border-0 ps-4 fw-bold">$</span>
                                    <input name="costos" type="number" step="0.01"
                                        class="form-control bg-light border-0" id="costos"
                                        value="{{ old('costos', $doctor->costo ?? '') }}" required placeholder="0.00">
                                    <div class="invalid-feedback ms-3">Indique el costo.</div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label for="horarioentrada" class="form-label fw-bold ms-3">Horario Entrada</label>
                                <input name="horarioentrada" type="time"
                                    class="form-control form-control-lg rounded-pill bg-light border-0 shadow-sm ps-4 text-center" id="horarioentrada"
                                    value="{{ old('horarioentrada', $doctor->horario_entrada ?? '') }}" required>
                            </div>

                            <div class="col-md-4">
                                <label for="horariosalida" class="form-label fw-bold ms-3">Horario Salida</label>
                                <input name="horariosalida" type="time"
                                    class="form-control form-control-lg rounded-pill bg-light border-0 shadow-sm ps-4 text-center" id="horariosalida"
                                    value="{{ old('horariosalida', $doctor->horario_salida ?? '') }}" required>
                            </div>

                            <div class="col-md-12">
                                <label for="idioma" class="form-label fw-bold ms-3">Idioma(s)</label>
                                <input name="idioma" type="text" 
                                    class="form-control form-control-lg rounded-pill bg-light border-0 shadow-sm ps-4" id="idioma"
                                    value="{{ old('idioma', $doctor->idiomas ?? '') }}" placeholder="Ej. Español, Inglés">
                            </div>

                            <div class="col-12">
                                <label for="descripcion" class="form-label fw-bold ms-3">Descripción / Perfil</label>
                                <textarea class="form-control form-control-lg rounded-4 bg-light border-0 shadow-sm p-4" name="descripcion"
                                    id="descripcion" rows="4" required placeholder="Escriba una breve descripción...">{{ old('descripcion', $doctor->descripcion ?? '') }}</textarea>
                            </div>
                            
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-10">
                <label class="form-label fw-bold ms-3">Foto de Perfil</label>
                <div class="p-4 bg-light rounded-4 border-0 shadow-sm text-center">
                    <x-image-dropzone 
                        name="image"
                        :current-image="(isset($doctor) && $doctor->user->foto) ? asset('storage/'.$doctor->user->foto) : null"
                        :current-image-alt="isset($doctor) ? $doctor->user->name : ''"
                        :error="$errors->first('image')"
                        title="Arrastra la foto de perfil aquí"
                        subtitle="Formatos: JPG, PNG, WEBP (Máx 5MB)"
                        :show-current-image="true"
                    />
                </div>
            </div>
            <div class="col-lg-10">
                <div class="card border-0 shadow-lg rounded-4 overflow-hidden mb-4">
                    <div class="card-header bg-custom-dark text-white py-3">
                        <h5 class="mb-0 fw-bold"><i class="bi bi-geo-alt-fill me-2"></i> Ubicación del Consultorio</h5>
                    </div>
                    <div class="card-body p-4 bg-white">
                        <p class="text-muted ms-2 mb-3"><i class="bi bi-info-circle"></i> Arrastra el marcador rojo para indicar la ubicación exacta.</p>
                        
                        <input type="hidden" name="latitud" id="latitud" value="{{ old('latitud', $doctor->user->latitud ?? '') }}">
                        <input type="hidden" name="longitud" id="longitud" value="{{ old('longitud', $doctor->user->longitud ?? '') }}">

                        <div class="shadow-sm rounded-4 overflow-hidden border border-light">
                            <div id="map" style="height: 400px; width: 100%;"></div>
                        </div>
                    </div>
                </div>

                {{-- BOTONES DE ACCIÓN --}}
                <div class="d-grid gap-2 d-md-flex justify-content-md-end mb-5">
                    <a href="{{ route('doctores.index') }}" class="btn btn-outline-secondary btn-lg rounded-pill px-5">Cancelar</a>
                    <button class="btn btn-navy btn-lg rounded-pill px-5 shadow fw-bold" type="submit">
                        <i class="bi bi-save me-2"></i>
                        {{ isset($doctor) ? 'Actualizar Doctor' : 'Guardar Doctor' }}
                    </button>
                </div>
            </div>

        </form>
    </div>

    @section('js')
        <script>
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
                    mapTypeId: google.maps.MapTypeId.ROADMAP,
                    styles: []
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
        </script>

        @stack('scripts')
    @endsection
</x-layout>