{{-- MODAL SOLICITAR CAMBIO --}}
<div class="modal fade" id="modalSolicitarCambio{{$cita->id}}" tabindex="-1" aria-hidden="true" data-bs-backdrop="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-navy text-white border-0 py-3">
                <h5 class="modal-title fw-bold d-flex align-items-center">
                    <x-mcl-calendar class="me-2" style="width: 1.5rem;"/> Nueva Propuesta
                </h5>
                <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal"></button>
            </div>

            <form id="formSolicitarCambio{{$cita->id}}" action="{{ route('citas.solicitar-cambio', $cita->id) }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    {{-- PASO 1 --}}
                    <div class="mb-4">
                        <label class="small fw-bold text-navy text-uppercase tracking-wider mb-2 d-block">1. Selecciona el nuevo día</label>
                        <input type="date" name="nueva_fecha" id="nuevaFechaCambio{{$cita->id}}" 
                            class="form-control rounded-pill border bg-light py-2 px-4 shadow-sm input-fecha-cambio" 
                            style="max-width: 250px;" data-cita-id="{{ $cita->id }}"
                            min="{{ date('Y-m-d') }}" required>
                    </div>

                    {{-- PASO 2 --}}
                    <div class="mb-4" id="seccionHorarios{{$cita->id}}" style="display:none;">
                        <label class="small fw-bold text-navy text-uppercase tracking-wider mb-2 d-block">2. Horarios disponibles</label>
                        <div id="containerSlots{{$cita->id}}" class="d-flex flex-wrap gap-2 p-3 bg-light rounded-4 border">
                            <span class="text-muted small">Buscando...</span>
                        </div>
                        <input type="hidden" name="nueva_hora" id="nuevaHoraCambio{{$cita->id}}" required>
                    </div>

                    {{-- MOTIVO --}}
                    <div class="mb-2">
                        <label class="small fw-bold text-navy text-uppercase tracking-wider mb-2 d-block">Motivo del Cambio</label>
                        <textarea name="motivo" class="form-control rounded-4 border p-3 shadow-sm" rows="3" 
                            placeholder="Explica por qué necesitas mover la cita..." required></textarea>
                    </div>
                </div>

                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" id="btnEnviarSolicitud{{$cita->id}}" class="btn btn-navy rounded-pill px-4 shadow-sm fw-bold" disabled>
                        Enviar Solicitud
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL RECHAZAR CAMBIO --}}
<div class="modal fade" id="modalRechazarCambio{{$cita->id}}" tabindex="-1" aria-hidden="true" data-bs-backdrop="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-body p-5 text-center">
                <div class="mb-4">
                    <div class="bg-danger-subtle text-danger rounded-circle d-inline-flex align-items-center justify-content-center shadow-sm" style="width: 70px; height: 70px;">
                        <x-mcr-times style="width: 3rem;" />
                    </div>
                </div>
                
                <h4 class="fw-bold text-navy mb-2">Rechazar Cambio</h4>
                <p class="text-muted small mb-4 px-3">Indica brevemente por qué no puedes aceptar la propuesta de horario del paciente.</p>

                <form id="formRechazarCambio{{$cita->id}}" action="{{ route('citas.responder-cambio', $cita->id) }}" method="POST">
                    @csrf
                    <input type="hidden" name="accion" value="rechazar">
                    
                    <textarea name="motivo_rechazo" class="form-control rounded-4 border-0 bg-light p-3 mb-4 shadow-none" 
                        rows="3" placeholder="Ej: El consultorio estará cerrado en ese horario..." required></textarea>
                    
                    <div class="d-flex gap-2 mt-2">
                        <button type="button" class="btn btn-light w-100 rounded-pill py-2 fw-bold" data-bs-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-danger w-100 rounded-pill py-2 fw-bold shadow-sm">Confirmar Rechazo</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // 1. Manejar cambio de fecha en el modal de solicitud
    document.getElementById('nuevaFechaCambio{{$cita->id}}').addEventListener('change', function() {
        const fecha = this.value;
        const citaId = '{{$cita->id}}';
        const container = document.getElementById(`containerSlots${citaId}`);
        const seccionHorarios = document.getElementById(`seccionHorarios${citaId}`);
        const btnSubmit = document.getElementById(`btnEnviarSolicitud${citaId}`);
        
        container.innerHTML = '<div class="w-100 text-center"><span class="spinner-border spinner-border-sm text-navy"></span></div>';
        seccionHorarios.style.display = 'block';
        btnSubmit.disabled = true;

        fetch(`/api/disponibilidad/{{ $cita->doctor->id }}?fecha=${fecha}`)
            .then(res => res.json())
            .then(data => {
                container.innerHTML = '';
                if (data.slots && data.slots.length > 0) {
                    data.slots.forEach(hora => {
                        const btn = document.createElement('button');
                        btn.type = 'button';
                        btn.className = 'btn btn-sm rounded-pill fw-bold btn-slot-cambio transition-all';
                        btn.style.border = '1.5px solid #00213D';
                        btn.style.color = '#00213D';
                        btn.textContent = hora;
                        btn.dataset.hora = hora;
                        btn.dataset.citaId = citaId;
                        container.appendChild(btn);
                    });
                } else {
                    container.innerHTML = `<span class="text-muted small italic">${data.mensaje || 'Sin horarios disponibles.'}</span>`;
                }
            });
    });

    // 2. Manejar clic en los slots (usando delegación para que funcione con contenido dinámico)
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('btn-slot-cambio')) {
            const citaId = e.target.dataset.citaId;
            const container = document.getElementById(`containerSlots${citaId}`);
            const inputHora = document.getElementById(`nuevaHoraCambio${citaId}`);
            const btnSubmit = document.getElementById(`btnEnviarSolicitud${citaId}`);

            // Limpiar otros botones
            container.querySelectorAll('.btn-slot-cambio').forEach(btn => {
                btn.style.backgroundColor = 'transparent';
                btn.style.color = '#00213D';
            });

            // Activar el seleccionado
            e.target.style.backgroundColor = '#00213D';
            e.target.style.color = '#ffffff';
            inputHora.value = e.target.dataset.hora;
            btnSubmit.disabled = false;
        }
    });
</script>