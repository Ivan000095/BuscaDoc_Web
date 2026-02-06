<x-layout>
    <div class="container pb-5">
        {{-- Encabezado --}}
        <div class="row my-5 text-center">
            <div class="col-12">
                <h1 class="fw-bold text-primary display-5">Registrar Nueva Farmacia</h1>
                <p class="text-muted">Complete los datos del dueño y de la farmacia</p>
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

        <form method="POST" action="{{ route('admin.farmacias.store') }}" 
            class="row g-4 needs-validation justify-content-center" novalidate>

            @csrf

            <div class="col-lg-10">
                <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                    <div class="card-header bg-primary text-white py-3">
                        <h5 class="mb-0 fw-bold"><i class="bi bi-person-badge me-2"></i> Datos del Dueño (Usuario)</h5>
                    </div>
                    <div class="card-body p-4 bg-white">
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="crearNuevoUsuario" name="crear_nuevo" value="1" {{ old('crear_nuevo') ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold" for="crearNuevoUsuario">
                                Crear una nueva cuenta de usuario para esta farmacia
                            </label>
                        </div>

                        <div id="usuarioExistente" style="{ { old('crear_nuevo') ? 'display:none;' : '' }}">
                            <label for="user_id" class="form-label fw-bold ms-3">Seleccionar usuario existente</label>
                            <select name="user_id" id="user_id" class="form-select form-select-lg rounded-pill bg-light border-0 shadow-sm ps-4">
                                <option value="">-- Elija un usuario --</option>
                                @foreach($usuarios as $u)
                                    <option value="{{ $u->id }}" {{ old('user_id') == $u->id ? 'selected' : '' }}>
                                        {{ $u->name }} ({{ $u->email }})
                                    </option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback ms-3">Seleccione un usuario o cree uno nuevo.</div>
                        </div>

                        <div id="nuevoUsuario" style="{{ !old('crear_nuevo') ? 'display:none;' : '' }}">
                            <div class="row g-4 mt-2">
                                <div class="col-md-6">
                                    <label for="name" class="form-label fw-bold ms-3">Nombre Completo *</label>
                                    <input name="name" type="text" 
                                           class="form-control form-control-lg rounded-pill bg-light border-0 shadow-sm ps-4"
                                           value="{{ old('name') }}" maxlength="100" placeholder="Ej. María López">
                                    <div class="invalid-feedback ms-3">El nombre es obligatorio.</div>
                                </div>
                                <div class="col-md-6">
                                    <label for="email" class="form-label fw-bold ms-3">Correo Electrónico *</label>
                                    <input name="email" type="email" 
                                           class="form-control form-control-lg rounded-pill bg-light border-0 shadow-sm ps-4"
                                           value="{{ old('email') }}" placeholder="correo@ejemplo.com">
                                    <div class="invalid-feedback ms-3">El correo es obligatorio y debe ser único.</div>
                                </div>
                                <div class="col-md-6">
                                    <label for="password" class="form-label fw-bold ms-3">Contraseña *</label>
                                    <input name="password" type="password"
                                           class="form-control form-control-lg rounded-pill bg-light border-0 shadow-sm ps-4"
                                           minlength="8" placeholder="••••••••">
                                    <div class="invalid-feedback ms-3">Mínimo 8 caracteres.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-10">
                <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                    <div class="card-header bg-custom-dark text-white py-3">
                        <h5 class="mb-0 fw-bold"><i class="bi bi-shop me-2"></i> Datos de la Farmacia</h5>
                    </div>
                    <div class="card-body p-4 bg-white">
                        <div class="row g-4">
                            <div class="col-md-8">
                                <label for="nom_farmacia" class="form-label fw-bold ms-3">Nombre de la Farmacia *</label>
                                <input name="nom_farmacia" type="text" 
                                       class="form-control form-control-lg rounded-pill bg-light border-0 shadow-sm ps-4"
                                       value="{{ old('nom_farmacia') }}" required maxlength="255" placeholder="Farmacia San José">
                                <div class="invalid-feedback ms-3">El nombre es obligatorio.</div>
                            </div>

                            <div class="col-md-4">
                                <label for="rfc" class="form-label fw-bold ms-3">RFC</label>
                                <input name="rfc" type="text" 
                                       class="form-control form-control-lg rounded-pill bg-light border-0 shadow-sm ps-4"
                                       value="{{ old('rfc') }}" maxlength="255" placeholder="XAXX010101000">
                            </div>

                            <div class="col-md-6">
                                <label for="telefono" class="form-label fw-bold ms-3">Teléfono</label>
                                <input name="telefono" type="text" 
                                       class="form-control form-control-lg rounded-pill bg-light border-0 shadow-sm ps-4"
                                       value="{{ old('telefono') }}" maxlength="255" placeholder="967 123 4567">
                            </div>

                            <div class="col-md-6">
                                <label for="horario" class="form-label fw-bold ms-3">Horario de Atención</label>
                                <input name="horario" type="text" 
                                       class="form-control form-control-lg rounded-pill bg-light border-0 shadow-sm ps-4"
                                       value="{{ old('horario') }}" maxlength="255" placeholder="8:00 AM - 9:00 PM">
                            </div>

                            <div class="col-md-6">
                                <label for="dias_op" class="form-label fw-bold ms-3">Días de Operación</label>
                                <input name="dias_op" type="text" 
                                       class="form-control form-control-lg rounded-pill bg-light border-0 shadow-sm ps-4"
                                       value="{{ old('dias_op') }}" maxlength="255" placeholder="Lunes a Sábado">
                            </div>

                            <div class="col-12">
                                <label for="descripcion" class="form-label fw-bold ms-3">Descripción</label>
                                <textarea class="form-control form-control-lg rounded-4 bg-light border-0 shadow-sm p-4" 
                                          name="descripcion" rows="3" placeholder="Información adicional...">{{ old('descripcion') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- BOTONES DE ACCIÓN --}}
            <div class="col-lg-10 d-grid gap-2 d-md-flex justify-content-md-end mb-5">
                <a href="{{ route('admin.farmacias.index') }}" class="btn btn-outline-secondary btn-lg rounded-pill px-5">Cancelar</a>
                <button class="btn btn-primary btn-lg rounded-pill px-5 shadow fw-bold" type="submit">
                    <i class="bi bi-save me-2"></i> Registrar Farmacia
                </button>
            </div>
        </form>
    </div>

    @section('js')
        <script>
            (function () {
                'use strict';
                const checkbox = document.getElementById('crearNuevoUsuario');
                const usuarioExistente = document.getElementById('usuarioExistente');
                const nuevoUsuario = document.getElementById('nuevoUsuario');

                if (checkbox) {
                    checkbox.addEventListener('change', function () {
                        if (this.checked) {
                            usuarioExistente.style.display = 'none';
                            nuevoUsuario.style.display = 'block';
                        } else {
                            usuarioExistente.style.display = 'block';
                            nuevoUsuario.style.display = 'none';
                        }
                    });
                }

                // Validación Bootstrap
                const forms = document.querySelectorAll('.needs-validation');
                Array.prototype.slice.call(forms).forEach(function (form) {
                    form.addEventListener('submit', function (event) {
                        if (!form.checkValidity()) {
                            event.preventDefault();
                            event.stopPropagation();
                        }
                        form.classList.add('was-validated');
                    }, false);
                });
            })();
        </script>
    @endsection
</x-layout>