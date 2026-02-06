<x-layout>
    <div class="container pb-5">
        <div class="row my-5 text-center">
            <div class="col-12">
                <h1 class="fw-bold text-primary display-5">Editar Farmacia</h1>
                <p class="text-muted">Actualice los datos del dueño y del negocio</p>
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
            action="{{ route('admin.farmacias.update', $farmacia->id) }}"
            class="row g-4 needs-validation justify-content-center" novalidate enctype="multipart/form-data">

            @csrf
            @method('PUT')

            <!-- Datos del Dueño (Usuario) -->
            <div class="col-lg-10">
                <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                    <div class="card-header bg-primary text-white py-3">
                        <h5 class="mb-0 fw-bold"><i class="bi bi-person-badge me-2"></i> Datos del Dueño</h5>
                    </div>
                    <div class="card-body p-4 bg-white">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label for="name" class="form-label fw-bold ms-3">Nombre Completo</label>
                                <input name="name" type="text" 
                                    class="form-control form-control-lg rounded-pill bg-light border-0 shadow-sm ps-4"
                                    id="name" value="{{ old('name', $farmacia->user->name ?? '') }}" required maxlength="100">
                                <div class="invalid-feedback ms-3">El nombre es obligatorio.</div>
                            </div>

                            <div class="col-md-6">
                                <label for="fecha" class="form-label fw-bold ms-3">Fecha de Nacimiento</label>
                                <input name="fecha" type="date" 
                                    class="form-control form-control-lg rounded-pill bg-light border-0 shadow-sm ps-4"
                                    id="fecha" 
                                    value="{{ old('fecha', $farmacia->user->f_nacimiento ?? '') }}" 
                                    required
                                    max="{{ date('Y-m-d', strtotime('-18 years')) }}">
                                <div class="invalid-feedback ms-3">Debe ser mayor de 18 años.</div>
                            </div>

                            <div class="col-md-6">
                                <label for="email" class="form-label fw-bold ms-3">Correo Electrónico</label>
                                <input name="email" type="email" 
                                    class="form-control form-control-lg rounded-pill bg-light border-0 shadow-sm ps-4"
                                    id="email" value="{{ old('email', $farmacia->user->email ?? '') }}" required>
                                <div class="invalid-feedback ms-3">
                                    {{ $errors->first('email') ?: 'El correo es obligatorio.' }}
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label for="password" class="form-label fw-bold ms-3">
                                    Contraseña (opcional)
                                    <span class="text-muted small fw-normal">(Déjela vacía para no cambiarla)</span>
                                </label>
                                <input name="password" type="password"
                                    class="form-control form-control-lg rounded-pill bg-light border-0 shadow-sm ps-4" 
                                    id="password" minlength="8" placeholder="••••••••">
                                <div class="invalid-feedback ms-3">Mínimo 8 caracteres si desea cambiarla.</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Datos del Negocio -->
            <div class="col-lg-10">
                <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                    <div class="card-header bg-custom-dark text-white py-3">
                        <h5 class="mb-0 fw-bold"><i class="bi bi-shop me-2"></i> Datos del Negocio</h5>
                    </div>
                    <div class="card-body p-4 bg-white">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label for="nom_farmacia" class="form-label fw-bold ms-3">Nombre de la Farmacia</label>
                                <input name="nom_farmacia" type="text" 
                                    class="form-control form-control-lg rounded-pill bg-light border-0 shadow-sm ps-4"
                                    id="nom_farmacia" value="{{ old('nom_farmacia', $farmacia->nom_farmacia ?? '') }}" required>
                                <div class="invalid-feedback ms-3">Obligatorio.</div>
                            </div>

                            <div class="col-md-6">
                                <label for="rfc" class="form-label fw-bold ms-3">RFC</label>
                                <input name="rfc" type="text" 
                                    class="form-control form-control-lg rounded-pill bg-light border-0 shadow-sm ps-4"
                                    id="rfc" value="{{ old('rfc', $farmacia->rfc ?? '') }}" maxlength="13">
                            </div>

                            <div class="col-md-6">
                                <label for="telefono" class="form-label fw-bold ms-3">Teléfono</label>
                                <input name="telefono" type="text" 
                                    class="form-control form-control-lg rounded-pill bg-light border-0 shadow-sm ps-4"
                                    id="telefono" value="{{ old('telefono', $farmacia->telefono ?? '') }}" required>
                                <div class="invalid-feedback ms-3">Obligatorio.</div>
                            </div>

                            <div class="col-md-6">
                                <label for="horario" class="form-label fw-bold ms-3">Horario de Atención</label>
                                <input name="horario" type="text" 
                                    class="form-control form-control-lg rounded-pill bg-light border-0 shadow-sm ps-4"
                                    id="horario" value="{{ old('horario', $farmacia->horario ?? '') }}" required>
                                <div class="invalid-feedback ms-3">Ej. 08:00 - 20:00</div>
                            </div>

                            <div class="col-md-6">
                                <label for="dias_op" class="form-label fw-bold ms-3">Días de Operación</label>
                                <input name="dias_op" type="text" 
                                    class="form-control form-control-lg rounded-pill bg-light border-0 shadow-sm ps-4"
                                    id="dias_op" value="{{ old('dias_op', $farmacia->dias_op ?? '') }}" required>
                                <div class="invalid-feedback ms-3">Ej. Lun-Sáb</div>
                            </div>

                            <div class="col-12">
                                <label for="descripcion" class="form-label fw-bold ms-3">Descripción</label>
                                <textarea class="form-control form-control-lg rounded-4 bg-light border-0 shadow-sm p-4" name="descripcion"
                                    id="descripcion" rows="3" required>{{ old('descripcion', $farmacia->descripcion ?? '') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Foto de Perfil -->
            <div class="col-10">
                <label class="form-label fw-bold ms-3">Foto de Perfil del Dueño</label>
                <div class="p-4 bg-light rounded-4 border-0 shadow-sm text-center">
                    <x-image-dropzone 
                        name="image"
                        :current-image="$farmacia->user->foto ? asset('storage/'.$farmacia->user->foto) : null"
                        :current-image-alt="$farmacia->user->name"
                        :error="$errors->first('image')"
                        title="Arrastra una nueva foto aquí (opcional)"
                        subtitle="Formatos: JPG, PNG, WEBP (Máx 5MB)"
                        :show-current-image="true"
                    />
                </div>
            </div>

            <!-- Mapa -->
            <div class="col-lg-10">
                <div class="card border-0 shadow-lg rounded-4 overflow-hidden mb-4">
                    <div class="card-header bg-danger text-white py-3">
                        <h5 class="mb-0 fw-bold"><i class="bi bi-geo-alt-fill me-2"></i> Ubicación Actual</h5>
                    </div>
                    <div class="card-body p-4 bg-white">
                        <p class="text-muted ms-2 mb-3"><i class="bi bi-info-circle"></i> Arrastre el marcador para actualizar la ubicación.</p>

                        <input type="hidden" name="latitud" id="latitud" value="{{ old('latitud', $farmacia->user->latitud ?? '17.0834') }}">
                        <input type="hidden" name="longitud" id="longitud" value="{{ old('longitud', $farmacia->user->longitud ?? '-92.5236') }}">

                        <div class="shadow-sm rounded-4 overflow-hidden border border-light">
                            <div id="map" style="height: 400px; width: 100%;"></div>
                        </div>
                    </div>
                </div>

                <!-- Botones -->
                <div class="d-grid gap-2 d-md-flex justify-content-md-end mb-5">
                    <a href="{{ route('admin.farmacias.index') }}" class="btn btn-outline-secondary btn-lg rounded-pill px-5">Cancelar</a>
                    <button class="btn btn-primary btn-lg rounded-pill px-5 shadow fw-bold" type="submit">
                        <i class="bi bi-save me-2"></i> Actualizar Farmacia
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
                const lat = parseFloat(document.getElementById('latitud').value) || 17.0834;
                const lng = parseFloat(document.getElementById('longitud').value) || -92.5236;

                document.getElementById('latitud').value = lat;
                document.getElementById('longitud').value = lng;

                const myLatLng = { lat, lng };

                map = new google.maps.Map(document.getElementById("map"), {
                    zoom: 15,
                    center: myLatLng,
                    mapTypeId: google.maps.MapTypeId.ROADMAP,
                });

                marker = new google.maps.Marker({
                    position: myLatLng,
                    map: map,
                    draggable: true,
                    title: "Ubicación de la Farmacia",
                    animation: google.maps.Animation.DROP
                });

                marker.addListener("dragend", function (event) {
                    updateCoordinates(event.latLng.lat(), event.latLng.lng());
                });

                map.addListener("click", function (event) {
                    marker.setPosition(event.latLng);
                    updateCoordinates(event.latLng.lat(), event.latLng.lng());
                });
            }

            function updateCoordinates(lat, lng) {
                document.getElementById('latitud').value = lat;
                document.getElementById('longitud').value = lng;
            }
        </script>
    @endsection
</x-layout>