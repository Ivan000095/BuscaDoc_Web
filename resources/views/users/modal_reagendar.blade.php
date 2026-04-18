        <div class="modal fade" id="modalSolicitarCambio{{$user->id}}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg rounded-4">
                    <div class="modal-header bg-navy text-white border-0">
                        <h5 class="modal-title fw-bold"><i class="bi bi-clock-history me-2"></i>Nueva Propuesta</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <form id="formSolicitarCambio{{$user->id}}" action="{{ route('citas.solicitar-cambio', $cita->id) }}" method="POST">
                        @csrf
                        <div class="modal-body p-4">
                        <div class="mb-4">
                            <label class="small fw-bold text-navy mb-1">1. Selecciona el día</label>
                            <input type="date" name="nueva_fecha" id="nuevaFechaCambio" class="form-control form-control-pill" min="{{ date('Y-m-d') }}" required>
                        </div>

                        <div class="mb-4" id="seccionHorarios2" style="display:none;">
                            <label class="small fw-bold text-navy mb-2">2. Horarios disponibles</label>
                            <div id="containerSlots2" class="d-flex flex-wrap gap-2"></div>
                            <input type="hidden" name="nueva_hora" id="nuevaHoraCambio" required>
                        </div>

                            <div class="mb-3">
                                <label class="small fw-bold text-navy">Motivo del Cambio</label>
                                <textarea name="motivo" class="form-control rounded-3" rows="3" placeholder="Explica por qué necesitas mover la cita..." required></textarea>
                            </div>
                        </div>
                        <div class="modal-footer border-0">
                            <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-navy rounded-pill px-4">Enviar Solicitud</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modalRechazarCambio{{$user->id}}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg rounded-4">
                    <div class="modal-body p-4 text-center">
                        <div class="text-danger mb-3">
                            <i class="bi bi-x-circle fs-1"></i>
                        </div>
                        <h5 class="fw-bold text-navy">Rechazar Cambio</h5>
                        <p class="text-muted small">Indica el motivo por el cual no puedes aceptar este nuevo horario.</p>
                       


                        <form id="formRechazarCambio{{$user->id}}" action="{{ route('citas.responder-cambio', $cita->id) }}" method="POST">
                            @csrf
                            <input type="hidden" name="accion" value="rechazar">
                            <textarea name="motivo_rechazo" class="form-control rounded-3 mb-3" rows="3" placeholder="Ej: Ya tengo el horario ocupado..." required></textarea>
                            
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-light w-100 rounded-pill" data-bs-dismiss="modal">Cerrar</button>
                                <button type="submit" class="btn btn-danger w-100 rounded-pill">Confirmar Rechazo</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>


        <script>






                document.getElementById('nuevaFechaCambio').addEventListener('change', function() {
                    const fecha = this.value;
                    const container = document.getElementById('containerSlots2');
                    const seccionHorarios = document.getElementById('seccionHorarios2');
                    
                    container.innerHTML = '<span class="spinner-border spinner-border-sm text-primary"></span>';
                    seccionHorarios.style.display = 'block';

                    fetch(`/api/disponibilidad/{{ $cita->doctor->id }}?fecha=${fecha}`)
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
                        document.getElementById('nuevaHoraCambio').value = e.target.dataset.hora;
                        
                    }
                });







        </script>