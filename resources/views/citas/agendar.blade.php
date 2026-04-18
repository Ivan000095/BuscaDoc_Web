<div class="modal fade" id="agendarCitaModal" tabindex="-1" aria-labelledby="agendarCitaModalLabel" aria-hidden="true">
    
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-navy" id="agendarCitaModalLabel">
                    Agendar con {{ $doctor->user->name }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="{{ route('citas.store', $doctor->id) }}" method="POST" id="formAgendar">
                @csrf
                <div class="modal-body">
                    
                    <div class="mb-4">
                        <label class="small fw-bold text-navy mb-1">1. Selecciona el día</label>
                        <input type="date" name="fecha" id="fechaCita" class="form-control form-control-pill" min="{{ date('Y-m-d') }}" required>
                    </div>

                    <div class="mb-4" id="seccionHorarios" style="display:none;">
                        <label class="small fw-bold text-navy mb-2">2. Horarios disponibles</label>
                        <div id="containerSlots" class="d-flex flex-wrap gap-2"></div>
                        <input type="hidden" name="hora_inicio" id="horaSeleccionada" required>
                    </div>

                    <div class="mb-4" id="seccionExpediente" style="display:none;">
                        <label class="small fw-bold text-navy mb-1">3. ¿Para quién es la cita?</label>
                        <select name="expediente_id" id="selectExpediente" class="form-select form-control-pill mb-3" required>
                            <option value="" disabled selected>Selecciona un paciente</option>
                            @foreach(Auth::user()->expedientes as $exp)
                                <option value="{{ $exp->id }}">{{ $exp->nombre_completo }} ({{ $exp->parentesco }})</option>
                            @endforeach
                            <option value="nuevo_familiar" class="fw-bold text-primary">+ Agregar nuevo familiar...</option>
                        </select>

                        <div id="formNuevoFamiliar" class="bg-light p-3 rounded-4 mb-3" style="display:none;">
                            <h6 class="small fw-bold text-navy mb-3"><i class="bi bi-file-earmark-medical me-1"></i> Ficha Médica del Familiar</h6>
                            <div class="row g-2">
                                <div class="col-12 mb-2">
                                    <input type="text" name="nuevo_nombre" class="form-control form-control-pill" placeholder="Nombre completo">
                                </div>
                                <div class="col-6 mb-2">
                                    <label class="x-small fw-bold ms-2">Fecha de Nacimiento</label>
                                    <input type="date" name="nuevo_fecha_nacimiento" class="form-control form-control-pill" >
                                </div>
                                <div class="col-6 mb-2">
                                    <label class="x-small fw-bold ms-2">Género</label>
                                    <select name="nuevo_genero" class="form-select form-control-pill" >
                                        <option value="masculino">Masculino</option>
                                        <option value="femenino">Femenino</option>
                                        
                                    </select>
                                </div>
                                <div class="col-6 mb-2">
                                    <select name="nuevo_parentesco" class="form-select form-control-pill">
                                        <option value="">Cual es el parentesco?</option>
                                        <option value="Hijo">Hijo</option>
                                        <option value="Hija">Hija</option>
                                        <option value="Padre">Padre</option>
                                        <option value="Madre">Madre</option>
                                        <option value="Otro">Otro</option>
                                    </select>
                                </div>
                                <div class="col-6 mb-2">
                                    <select name="nuevo_tipo_sangre" class="form-select form-control-pill">
                                        <option value="">Tipo de Sangre</option>
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

                                <div class="col-12 mb-2">
                                    <textarea name="nuevo_alergias" class="form-control rounded-4" rows="2" placeholder="Alergias (opcional)"></textarea>
                                </div>
                                <div class="col-12 mb-2">
                                    <textarea name="nuevo_padecimientos" class="form-control rounded-4" rows="2" placeholder="Padecimientos crónicos (ej: Diabetes, Hipertensión)"></textarea>
                                </div>
                                <div class="col-12 mb-2">
                                    <textarea name="nuevo_habitos" class="form-control rounded-4" rows="2" placeholder="Hábitos de salud (ej: Ejercicio, fumador)"></textarea>
                                </div>
                            </div>
                        </div>
                    <div class="mb-2" id="seccionMotivo" style="display:none;">
                        <label class="small fw-bold text-navy mb-1">4. Motivo de consulta</label>
                        <textarea name="motivo_consulta" class="form-control rounded-4" rows="2" placeholder="Describe brevemente..." required></textarea>
                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-navy rounded-pill py-2 shadow-sm">Confirmar Cita</button>
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // JS para manejar la interactividad
    document.getElementById('fechaCita').addEventListener('change', function() {
        const fecha = this.value;
        const container = document.getElementById('containerSlots');
        const seccionHorarios = document.getElementById('seccionHorarios');
        
        container.innerHTML = '<span class="spinner-border spinner-border-sm text-primary"></span>';
        seccionHorarios.style.display = 'block';

        fetch(`/api/disponibilidad/{{ $doctor->id }}?fecha=${fecha}`)
            .then(res => res.json())
            .then(data => {
                container.innerHTML = '';
                if (data.slots && data.slots.length > 0) {
                    data.slots.forEach(hora => {
                        container.innerHTML += `<button type="button" class="btn btn-outline-primary btn-sm rounded-pill btn-slot" data-hora="${hora}">${hora}</button>`;
                    });
                } else {
                    // Mostrar el mensaje de "No labora" o "Sin disponibilidad"
                    const mensaje = data.mensaje ? data.mensaje : 'No hay horarios disponibles para este día.';
                    container.innerHTML = `<span class="text-muted small"><i class="bi bi-info-circle me-1"></i>${mensaje}</span>`;
                }
            });
    });

    document.addEventListener('click', function(e) {
        if(e.target.classList.contains('btn-slot')) {
            document.querySelectorAll('.btn-slot').forEach(b => b.classList.replace('btn-primary', 'btn-outline-primary'));
            e.target.classList.replace('btn-outline-primary', 'btn-primary');
            document.getElementById('horaSeleccionada').value = e.target.dataset.hora;
            document.getElementById('seccionExpediente').style.display = 'block';
        }
    });

    document.getElementById('selectExpediente').addEventListener('change', function() {
        const subForm = document.getElementById('formNuevoFamiliar');
        subForm.style.display = (this.value === 'nuevo_familiar') ? 'block' : 'none';
        document.getElementById('seccionMotivo').style.display = 'block';
    });
</script>