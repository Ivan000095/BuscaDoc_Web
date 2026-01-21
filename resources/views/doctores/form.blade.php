<x-layout>
    <div class="container">
        <div class="row my-4">
            <div class="col-12">
                <h1 class="mb-0">{{ isset($doctor) ? 'Editar' : 'Agregar' }} Doctor</h1>
            </div>
        </div>

        {{-- 
            NOTA: Ajusta la ruta 'action' según tus rutas definidas. 
            Por ejemplo: route('doctores.store') o route('doctores.update', $doctor->id)
        --}}
        <form method="POST" 
              action="{{ isset($doctor) ? url('/doctores/'. $doctor->id) : url('/doctores') }}" 
              class="row g-3 needs-validation" 
              novalidate 
              enctype="multipart/form-data">
            
            @csrf
            {{-- Si es edición, necesitamos el método PUT --}}
            @if(isset($doctor))
                @method('PUT')
            @endif

            <input type="hidden" name="id" value="{{ isset($doctor) ? $doctor->id : '' }}">

            {{-- Fila 1: Nombre y Especialidad --}}
            <div class="col-md-6">
                <label for="name" class="form-label">Nombre del Doctor</label>
                <input name="name" type="text" class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" 
                       id="name" value="{{ old('name', $doctor->name ?? '') }}" required maxlength="100">
                <div class="invalid-feedback">
                    {{ $errors->first('name') ?: 'El nombre es obligatorio.' }}
                </div>
            </div>

            <div class="col-md-6">
                <label for="especialidad" class="form-label">Especialidad</label>
                <input name="especialidad" type="text" class="form-control {{ $errors->has('especialidad') ? 'is-invalid' : '' }}" 
                       id="especialidad" value="{{ old('especialidad', $doctor->especialidad ?? '') }}" required maxlength="100">
                <div class="invalid-feedback">
                    {{ $errors->first('especialidad') ?: 'La especialidad es obligatoria.' }}
                </div>
            </div>

            {{-- Fila 2: Cédula, Teléfono e Idioma --}}
            <div class="col-md-4">
                <label for="cedula" class="form-label">Cédula Profesional</label>
                <input name="cedula" type="text" class="form-control {{ $errors->has('cedula') ? 'is-invalid' : '' }}" 
                       id="cedula" value="{{ old('cedula', $doctor->cedula ?? '') }}" required>
                <div class="invalid-feedback">
                    {{ $errors->first('cedula') ?: 'Campo requerido.' }}
                </div>
            </div>

            <div class="col-md-4">
                <label for="telefono" class="form-label">Teléfono</label>
                <input name="telefono" type="tel" class="form-control {{ $errors->has('telefono') ? 'is-invalid' : '' }}" 
                       id="telefono" value="{{ old('telefono', $doctor->telefono ?? '') }}" required>
                <div class="invalid-feedback">
                    {{ $errors->first('telefono') ?: 'Campo requerido.' }}
                </div>
            </div>

            <div class="col-md-4">
                <label for="idioma" class="form-label">Idioma</label>
                <input name="idioma" type="text" class="form-control {{ $errors->has('idioma') ? 'is-invalid' : '' }}" 
                       id="idioma" value="{{ old('idioma', $doctor->idioma ?? '') }}">
            </div>

            {{-- Fila 3: Costos y Horarios --}}
            <div class="col-md-4">
                <label for="costos" class="form-label">Costo Consulta</label>
                <div class="input-group has-validation">
                    <span class="input-group-text">$</span>
                    <input name="costos" type="text" class="form-control {{ $errors->has('costos') ? 'is-invalid' : '' }}" 
                           id="costos" value="{{ old('costos', $doctor->costos ?? '') }}" required>
                    <div class="invalid-feedback">
                        {{ $errors->first('costos') ?: 'Indique el costo.' }}
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <label for="horarioentrada" class="form-label">Horario Entrada</label>
                <input name="horarioentrada" type="time" class="form-control {{ $errors->has('horarioentrada') ? 'is-invalid' : '' }}" 
                       id="horarioentrada" value="{{ old('horarioentrada', $doctor->horarioentrada ?? '') }}" required>
            </div>

            <div class="col-md-4">
                <label for="horariosalida" class="form-label">Horario Salida</label>
                <input name="horariosalida" type="time" class="form-control {{ $errors->has('horariosalida') ? 'is-invalid' : '' }}" 
                       id="horariosalida" value="{{ old('horariosalida', $doctor->horariosalida ?? '') }}" required>
            </div>

            {{-- Fila 4: Fecha y Dirección --}}
            <div class="col-md-4">
                <label for="fecha" class="form-label">Fecha (Ingreso/Nacimiento)</label>
                <input name="fecha" type="date" class="form-control {{ $errors->has('fecha') ? 'is-invalid' : '' }}" 
                       id="fecha" value="{{ old('fecha', $doctor->fecha ?? '') }}" required>
            </div>

            <div class="col-md-8">
                <label for="direccion" class="form-label">Dirección</label>
                <input name="direccion" type="text" class="form-control {{ $errors->has('direccion') ? 'is-invalid' : '' }}" 
                       id="direccion" value="{{ old('direccion', $doctor->direccion ?? '') }}" required>
                <div class="invalid-feedback">
                    {{ $errors->first('direccion') ?: 'La dirección es obligatoria.' }}
                </div>
            </div>

            {{-- Fila 5: Descripción --}}
            <div class="col-12">
                <label for="descripcion" class="form-label">Descripción / Perfil</label>
                <textarea class="form-control {{ $errors->has('descripcion') ? 'is-invalid' : '' }}" 
                          name="descripcion" id="descripcion" rows="3" required maxlength="255">{{ old('descripcion', $doctor->descripcion ?? '') }}</textarea>
                <div class="invalid-feedback">
                    {{ $errors->first('descripcion') ?: 'Campo requerido.' }}
                </div>
            </div>

            {{-- Componente de Imagen --}}
            {{-- Asumiendo que guardas solo el nombre del archivo en la BD y usas 'storage' --}}
            <x-image-dropzone
                name="image"
                :current-image="isset($doctor) && $doctor->image ? asset('storage/'.$doctor->image) : null"
                :current-image-alt="isset($doctor) ? $doctor->name : ''"
                :error="$errors->first('image')"
                currentimageclass="col-sm-4 col-md-5 col-lg-4"
                dropzoneclass="col-sm-8 col-md-7 col-lg-8"
                title="Arrastra la foto del doctor aquí"
                subtitle="o haz clic para seleccionar"
                help-text="Formatos: JPG, PNG, WEBP (Max 5MB)"
                :max-size="5"
                :show-current-image="true"
                dropzone-height="200px"
            />

            <div class="col-12 mt-4">
                <button class="btn btn-primary" type="submit">
                    {{ isset($doctor) ? 'Actualizar Doctor' : 'Guardar Doctor' }}
                </button>
                <a href="{{ route('doctores.index') }}" class="btn btn-secondary ms-2">Cancelar</a>
            </div>
        </form>
    </div>

    @section('styles')
        @stack('styles')
    @endsection

    @section('js')
        <script>
            // Validación de formulario Bootstrap
            (function() {
                'use strict';
                var forms = document.querySelectorAll('.needs-validation');
                Array.prototype.slice.call(forms).forEach(function(form) {
                    form.addEventListener('submit', function(event) {
                        if (!form.checkValidity()) {
                            event.preventDefault();
                            event.stopPropagation();
                        }
                        form.classList.add('was-validated')
                    }, false)
                })
            })()
        </script>
        @stack('scripts')
    @endsection
</x-layout>