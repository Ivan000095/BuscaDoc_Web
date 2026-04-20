@php
    $user = Auth::user();
@endphp

<div class="modal fade" id="agendarCitaModal{{$user->id}}" tabindex="-1" aria-labelledby="agendarCitaModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">

            {{-- HEADER DEL MODAL --}}
            <div class="modal-header border-0 pb-0 pt-4 px-4 px-md-5">
                <div>
                    @if($user->role == 'paciente')
                        <h4 class="modal-title fw-bold" style="color: #00213D;" id="agendarCitaModalLabel">Agendar con
                            {{ $doctor->user->name }}</h4>
                    @else
                        <h4 class="modal-title fw-bold" style="color: #00213D;" id="agendarCitaModalLabel">Programar cita
                        </h4>
                    @endif
                    <p class="text-muted small mb-0">Completa los pasos para reservar el espacio.</p>
                </div>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            @php
                if ($user->role == 'doctor') {
                    $doctor = $user->doctor;
                }
            @endphp

            <form action="{{ route('citas.store', $doctor->id) }}" method="POST" id="formAgendar{{$user->id}}">
                @csrf
                <div class="modal-body px-4 px-md-5 py-4">

                    {{-- PASO 1: FECHA --}}
                    <div class="mb-4">
                        <label class="small fw-bold mb-2" style="color: #00213D; font-size: 0.9rem;">1. Selecciona el
                            día</label>
                        <input type="date" name="fecha" id="fechaCita{{$user->id}}"
                            class="form-control rounded-pill py-2 shadow-sm border" style="max-width: 250px;"
                            min="{{ date('Y-m-d') }}" required>
                    </div>

                    {{-- PASO 2: HORARIOS --}}
                    <div class="mb-4" id="seccionHorarios{{$user->id}}" style="display:none;">
                        <label class="small fw-bold mb-3" style="color: #00213D; font-size: 0.9rem;">2. Horarios
                            disponibles</label>
                        <div id="containerSlots{{$user->id}}" class="d-flex flex-wrap gap-2"></div>
                        <input type="hidden" name="hora_inicio" id="horaSeleccionada{{$user->id}}" required>
                    </div>

                    {{-- PASO 3: EXPEDIENTE --}}
                    <div class="mb-4" id="seccionExpediente{{$user->id}}" style="display:none;">
                        <label class="small fw-bold mb-2" style="color: #00213D; font-size: 0.9rem;">3. ¿Para quién es
                            la cita?</label>
                        <select name="expediente_id" id="selectExpediente"
                            class="form-select rounded-pill py-2 shadow-sm border mb-3" style="max-width: 400px;"
                            required>
                            <option value="" disabled selected>Selecciona un paciente...</option>
                            @foreach(Auth::user()->expedientes as $exp)
                                <option value="{{ $exp->id }}">{{ $exp->nombre_completo }} ({{ $exp->parentesco }})</option>
                            @endforeach

                            @if($user->role == 'paciente')
                                <option value="nuevo_familiar" class="fw-bold text-primary">+ Agregar nuevo familiar...
                                </option>
                            @elseif($user->role == 'doctor')
                                <option value="nuevo_familiar" class="fw-bold text-primary">+ Agregar nuevo paciente...
                                </option>
                            @endif
                        </select>

                        {{-- SUB-FORMULARIO NUEVO PACIENTE --}}
                        <div id="formNuevoFamiliar{{$user->id}}" class="bg-light p-4 rounded-4 mb-3 border shadow-sm"
                            style="display:none;">
                            <h6 class="small fw-bold mb-3 border-bottom pb-2" style="color: #00213D;">
                                <i class="bi bi-folder-plus me-1 text-primary"></i> Ficha del
                                {{ $user->role == 'paciente' ? 'Familiar' : 'Paciente' }}
                            </h6>

                            <div class="row g-3">
                                <div class="col-12">
                                    <input type="text" name="nuevo_nombre"
                                        class="form-control rounded-pill py-2 shadow-sm border"
                                        placeholder="Nombre completo">
                                </div>
                                <div class="col-sm-6">
                                    <label class="x-small fw-bold text-muted ms-2 mb-1"
                                        style="font-size: 0.75rem;">Fecha de Nacimiento</label>
                                    <input type="date" name="nuevo_fecha_nacimiento"
                                        class="form-control rounded-pill py-2 shadow-sm border text-muted">
                                </div>
                                <div class="col-sm-6">
                                    <label class="x-small fw-bold text-muted ms-2 mb-1"
                                        style="font-size: 0.75rem;">Género</label>
                                    <select name="nuevo_genero"
                                        class="form-select rounded-pill py-2 shadow-sm border text-muted">
                                        <option value="masculino">Masculino</option>
                                        <option value="femenino">Femenino</option>
                                    </select>
                                </div>

                                @if(Auth::user()->role == 'doctor')
                                    <input type="hidden" name="nuevo_parentesco" value="Paciente">
                                @else
                                    <div class="col-sm-6">
                                        <select name="nuevo_parentesco"
                                            class="form-select rounded-pill py-2 shadow-sm border mt-3 text-muted">
                                            <option value="" selected disabled>¿Cuál es el parentesco?</option>
                                            <option value="Hijo">Hijo</option>
                                            <option value="Hija">Hija</option>
                                            <option value="Padre">Padre</option>
                                            <option value="Madre">Madre</option>
                                            <option value="Otro">Otro</option>
                                        </select>
                                    </div>
                                @endif

                                <div class="col-sm-6">
                                    <select name="nuevo_tipo_sangre"
                                        class="form-select rounded-pill py-2 shadow-sm border mt-3 text-muted">
                                        <option value="" selected>Tipo de Sangre</option>
                                        <option value="O+">O+</option>
                                        <option value="O-">O-</option>
                                        <option value="A+">A+</option>
                                        <option value="A-">A-</option>
                                        <option value="B+">B+</option>
                                        <option value="B-">B-</option>
                                        <option value="AB+">AB+</option>
                                        <option value="AB-">AB-</option>
                                    </select>
                                </div>

                                <div class="col-12 mt-3">
                                    <textarea name="nuevo_alergias"
                                        class="form-control rounded-4 p-3 shadow-sm border mb-2" rows="2"
                                        placeholder="Alergias (opcional)"></textarea>
                                    <textarea name="nuevo_padecimientos"
                                        class="form-control rounded-4 p-3 shadow-sm border mb-2" rows="2"
                                        placeholder="Padecimientos crónicos (ej: Diabetes, Hipertensión)"></textarea>
                                    <textarea name="nuevo_habitos" class="form-control rounded-4 p-3 shadow-sm border"
                                        rows="2" placeholder="Hábitos de salud (ej: Ejercicio, fumador)"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- PASO 4: MOTIVO --}}
                    <div class="mb-2" id="seccionMotivo{{$user->id}}" style="display:none;">
                        <label class="small fw-bold mb-2" style="color: #00213D; font-size: 0.9rem;">4. Motivo de
                            consulta</label>
                        <textarea name="motivo_consulta" class="form-control rounded-4 p-3 shadow-sm border" rows="2"
                            placeholder="Describe brevemente los síntomas..." required></textarea>

                        <div class="d-grid mt-4 pt-3 border-top">
                            <button type="submit" class="btn rounded-pill py-3 text-white fw-bold shadow-sm"
                                style="background-color: #00213D; font-size: 1.1rem;">
                                Confirmar Cita
                            </button>
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.getElementById('fechaCita{{$user->id}}').addEventListener('change', function () {
        const fecha = this.value;
        const container = document.getElementById('containerSlots{{$user->id}}');
        const seccionHorarios = document.getElementById('seccionHorarios{{$user->id}}');

        container.innerHTML = '<span class="spinner-border spinner-border-sm text-primary"></span> Buscando...';
        seccionHorarios.style.display = 'block';

        fetch(`/api/disponibilidad/{{ $doctor->id }}?fecha=${fecha}`)
            .then(res => res.json())
            .then(data => {
                container.innerHTML = '';
                if (data.slots && data.slots.length > 0) {
                    data.slots.forEach(hora => {
                        // Volvemos a los botones originales de Bootstrap para evitar fallos
                        container.innerHTML += `<button type="button" class="btn btn-outline-primary btn-outline-navy rounded-pill px-3 py-1 fw-bold btn-slot" data-hora="${hora}">${hora}</button>`;
                    });
                } else {
                    const mensaje = data.mensaje ? data.mensaje : 'No hay horarios disponibles para este día.';
                    container.innerHTML = `<span class="text-muted small"><i class="bi bi-info-circle me-1"></i>${mensaje}</span>`;
                }
            });
    });

    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('btn-slot')) {
            // 1. Limpiar todos los botones de horarios
            document.querySelectorAll('.btn-slot').forEach(b => {
                b.style.backgroundColor = "transparent";
                b.style.color = "#00213D";
                b.style.borderColor = "#00213D";
            });

            // 2. Aplicar estilo "Activo" al seleccionado (Fondo Navy, Texto Blanco)
            e.target.style.backgroundColor = "#00213D";
            e.target.style.color = "#ffffff";
            e.target.style.borderColor = "#00213D";

            // 3. Guardar el valor y mostrar siguiente paso
            document.getElementById('horaSeleccionada{{$user->id}}').value = e.target.dataset.hora;
            document.getElementById('seccionExpediente{{$user->id}}').style.display = 'block';
        }
    });

    document.getElementById('selectExpediente').addEventListener('change', function () {
        const subForm = document.getElementById('formNuevoFamiliar{{$user->id}}');
        subForm.style.display = (this.value === 'nuevo_familiar') ? 'block' : 'none';
        document.getElementById('seccionMotivo{{$user->id}}').style.display = 'block';
    });
</script>