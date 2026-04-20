<x-layout>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                
                {{-- ENCABEZADO --}}
                <div class="d-flex align-items-center mb-5 pb-3 border-bottom">
                    <div class="bg-navy-subtle text-navy rounded-circle d-flex align-items-center justify-content-center me-3 shadow-sm" style="width: 50px; height: 50px;">
                        <x-mcr-calendar style="width: 1.6rem;" />
                    </div>
                    <div>
                        <h3 class="fw-bold text-navy mb-0">Mis Citas Médicas</h3>
                        <p class="text-muted small mb-0">Consulta y gestiona tus próximas visitas al médico.</p>
                    </div>
                </div>

                @forelse($citas as $cita)
                    @php 
                        $user = Auth::user();
                        $fecha = \Carbon\Carbon::parse($cita->fecha)->format('Y/m/d');
                        $hora = $cita->hora_inicio;
                        $fecha_hora = \Carbon\Carbon::parse($fecha . ' ' . $hora);
                        $esPasada = $fecha_hora->isPast();

                        // Solicitudes
                        $solicitudPendiente = \App\Models\SolicitudCambio::where('cita_id', $cita->id)
                            ->where('solicitado_id', $user->id)
                            ->where('estado', 'pendiente')->first();

                        $solicitudEnviada = \App\Models\SolicitudCambio::where('cita_id', $cita->id)
                            ->where('solicitante_id', $user->id)
                            ->where('estado', 'pendiente')->first();

                        $ultimoRechazo = \App\Models\SolicitudCambio::where('cita_id', $cita->id)
                            ->where('solicitante_id', $user->id)
                            ->where('estado', 'rechazada')->latest()->first();

                        $solicitudAceptada = \App\Models\SolicitudCambio::where('cita_id', $cita->id)
                            ->where('solicitante_id', $user->id)
                            ->where('estado', 'aceptada')->first(); 
                    @endphp

                    <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden card-cita hover-scale transition-all">
                        <div class="card-body p-0">
                            <div class="row g-0">
                                
                                {{-- BLOQUE DE FECHA --}}
                                <div class="col-3 col-md-2 bg-navy text-white d-flex flex-column align-items-center justify-content-center text-center p-2">
                                    <span class="d-block text-uppercase fw-bold opacity-75" style="font-size: 0.7rem; letter-spacing: 1px;">
                                        {{ $cita->fecha->translatedFormat('M') }}
                                    </span>
                                    <span class="d-block display-6 fw-bold lh-1 my-1">{{ $cita->fecha->format('d') }}</span>
                                    <span class="d-block small opacity-75 fw-medium">{{ $cita->fecha->translatedFormat('D') }}</span>
                                </div>

                                {{-- INFO DOCTOR --}}
                                <div class="col-9 col-md-6 p-4 d-flex align-items-center">
                                    <img src="{{ $cita->doctor->user->foto ? asset('storage/' . $cita->doctor->user->foto) : 'https://ui-avatars.com/api/?name=' . urlencode($cita->doctor->user->name) }}"
                                        class="rounded-circle shadow-sm border border-2 border-white me-3" width="65" height="65" style="object-fit: cover;">
                                    <div>
                                        <div class="d-flex align-items-center mb-1 text-primary">
                                            <x-mcr-clock class="me-2" style="width: 0.9rem;" />
                                            <span class="fw-bold small">{{ \Carbon\Carbon::parse($cita->hora_inicio)->format('h:i A') }}</span>
                                        </div>
                                        <h5 class="fw-bold text-navy mb-0">Dr. {{ $cita->doctor->user->name }}</h5>
                                        <span class="badge bg-light text-muted border rounded-pill mt-1" style="font-size: 0.7rem;">
                                            {{ $cita->doctor->especialidades->first()->nombre ?? 'Especialista' }}
                                        </span>
                                    </div>
                                </div>

                                {{-- ESTADO Y ACCIONES --}}
                                <div class="col-12 col-md-4 bg-surface border-start d-flex flex-column align-items-center justify-content-center p-4 gap-2 position-relative">
                                    
                                    @if(in_array($cita->estado, ['cancelada', 'rechazada', 'finalizada', 'no asistida']))
                                        <form action="{{ route('citas.destroy', $cita->id) }}" method="POST" class="position-absolute" style="top: 10px; right: 10px;">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-light rounded-circle shadow-sm border-0 text-danger" onclick="return confirm('¿Eliminar cita?')">
                                                <x-mcr-times style="width: 0.8rem;" />
                                            </button>
                                        </form>
                                    @endif

                                    {{-- Botón Reagendar Libre (Solo Pacientes) --}}
                                    @if($user->role == 'paciente' && $cita->estado == 'pendiente' && !$cita->reprogramada)
                                        <button class="btn btn-outline-navy btn-sm rounded-pill w-100 fw-bold" data-bs-toggle="modal" data-bs-target="#reprogramarLibreModal{{ $cita->id }}">
                                            <x-mcr-calendar class="me-1" style="width: 0.9rem;"/> Reagendar
                                        </button>
                                    @endif

                                    {{-- Lógica de Solicitudes Pendientes --}}
                                    @if($solicitudPendiente && !$esPasada)
                                        <div class="alert bg-warning-subtle text-warning-emphasis border-0 small p-2 w-100 text-center mb-0">
                                            <strong>¡Propuesta recibida!</strong><br>
                                            <div class="d-flex gap-2 mt-2 justify-content-center">
                                                <form action="{{ route('citas.responder-cambio', $cita->id) }}" method="POST">
                                                    @csrf <input type="hidden" name="accion" value="aceptar">
                                                    <button type="submit" class="btn btn-xs btn-success rounded-pill px-2 py-0" style="font-size: 0.7rem;">Aceptar</button>
                                                </form>
                                                <button type="button" class="btn btn-xs btn-danger rounded-pill px-2 py-0" style="font-size: 0.7rem;" data-bs-toggle="modal" data-bs-target="#modalRechazarCambio{{$cita->id}}">Rechazar</button>
                                            </div>
                                        </div>
                                    @elseif($solicitudEnviada)
                                        <div class="alert bg-light border text-muted small p-2 w-100 text-center mb-0">
                                            <span class="spinner-border spinner-border-sm me-1" style="width: 0.7rem; height: 0.7rem;"></span> Solicitud Enviada...
                                        </div>
                                    @elseif($cita->estado == 'pendiente')
                                        <span class="badge bg-warning-subtle text-warning-emphasis border rounded-pill px-3 py-2 w-100">Pendiente</span>
                                    @elseif($cita->estado == 'confirmada')
                                        <span class="badge bg-success-subtle text-success border rounded-pill px-3 py-2 w-100">Confirmada</span>
                                    @else
                                        <span class="badge bg-light text-muted border rounded-pill px-3 py-2 w-100">{{ ucfirst($cita->estado) }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5">
                        <h5 class="text-muted">No tienes citas agendadas.</h5>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- MODALES FUERA DEL FLUJO PARA EVITAR PANTALLA GRIS --}}
    @push('modals')
        @foreach($citas as $cita)
            {{-- Modal Solicitar Cambio --}}
            <div class="modal fade" id="modalSolicitarCambio{{$cita->id}}" tabindex="-1" aria-hidden="true" data-bs-backdrop="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                        <div class="modal-header bg-navy text-white border-0 py-3">
                            <h5 class="modal-title fw-bold d-flex align-items-center">
                                <x-mcr-calendar class="me-2" style="width: 1.5rem;"/> Nueva Propuesta
                            </h5>
                            <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal"></button>
                        </div>
                        <form id="formSolicitarCambio{{$cita->id}}" action="{{ route('citas.solicitar-cambio', $cita->id) }}" method="POST">
                            @csrf
                            <div class="modal-body p-4">
                                <div class="mb-4 text-center">
                                    <label class="small fw-bold text-navy text-uppercase mb-2 d-block">1. Selecciona el nuevo día</label>
                                    <input type="date" name="nueva_fecha" id="nuevaFechaCambio{{$cita->id}}" 
                                        class="form-control rounded-pill border bg-light py-2 px-4 shadow-sm mx-auto input-fecha-cambio" 
                                        style="max-width: 250px;" data-cita-id="{{ $cita->id }}" min="{{ date('Y-m-d') }}" required>
                                </div>
                                <div class="mb-4" id="seccionHorarios{{$cita->id}}" style="display:none;">
                                    <label class="small fw-bold text-navy text-uppercase mb-2 d-block text-center">2. Horarios disponibles</label>
                                    <div id="containerSlots{{$cita->id}}" class="d-flex flex-wrap justify-content-center gap-2 p-3 bg-light rounded-4 border"></div>
                                    <input type="hidden" name="nueva_hora" id="nuevaHoraCambio{{$cita->id}}" required>
                                </div>
                                <div class="mb-2">
                                    <label class="small fw-bold text-navy text-uppercase mb-2 d-block text-center">Motivo del Cambio</label>
                                    <textarea name="motivo" class="form-control rounded-4 border p-3 shadow-sm" rows="3" placeholder="Explica el motivo..." required></textarea>
                                </div>
                            </div>
                            <div class="modal-footer border-0 p-4 pt-0">
                                <button type="submit" id="btnEnviarSolicitud{{$cita->id}}" class="btn btn-navy rounded-pill w-100 py-2 fw-bold" disabled>Enviar Solicitud</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Modal Rechazar Cambio --}}
            <div class="modal fade" id="modalRechazarCambio{{$cita->id}}" tabindex="-1" aria-hidden="true" data-bs-backdrop="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                        <div class="modal-body p-5 text-center">
                            <div class="bg-danger-subtle text-danger rounded-circle d-inline-flex align-items-center justify-content-center mb-4" style="width: 70px; height: 70px;">
                                <x-mcr-times style="width: 2.5rem;" />
                            </div>
                            <h4 class="fw-bold text-navy mb-2">Rechazar Cambio</h4>
                            <p class="text-muted small mb-4">Indica el motivo del rechazo.</p>
                            <form action="{{ route('citas.responder-cambio', $cita->id) }}" method="POST">
                                @csrf <input type="hidden" name="accion" value="rechazar">
                                <textarea name="motivo_rechazo" class="form-control rounded-4 border-0 bg-light p-3 mb-4 shadow-none" rows="3" placeholder="Motivo..." required></textarea>
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-light w-100 rounded-pill py-2" data-bs-dismiss="modal">Cerrar</button>
                                    <button type="submit" class="btn btn-danger w-100 rounded-pill py-2 fw-bold">Confirmar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Modal Reprogramar Libre (Paciente) --}}
            <div class="modal fade" id="reprogramarLibreModal{{ $cita->id }}" tabindex="-1" aria-hidden="true" data-bs-backdrop="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                        <div class="modal-header bg-navy text-white border-0 py-3">
                            <h5 class="modal-title fw-bold">Reagendar Cita</h5>
                            <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal"></button>
                        </div>
                        <form action="{{ route('citas.reprogramarLibre', $cita->id) }}" method="POST">
                            @csrf @method('PUT')
                            <div class="modal-body p-4 text-center">
                                <input type="date" name="nueva_fecha" class="form-control rounded-pill border bg-light py-2 px-4 shadow-sm mx-auto input-fecha-reprogramar" 
                                    style="max-width: 250px;" data-cita-id="{{ $cita->id }}" min="{{ date('Y-m-d') }}" required>
                                <div id="slotsContainer{{ $cita->id }}" class="d-flex flex-wrap justify-content-center gap-2 mt-4 p-3 bg-light rounded-4 border">
                                    <span class="text-muted small">Selecciona una fecha...</span>
                                </div>
                                <input type="hidden" name="nueva_hora" id="horaSeleccionada{{ $cita->id }}" required>
                            </div>
                            <div class="modal-footer border-0 p-4 pt-0">
                                <button type="submit" id="btnConfirmar{{ $cita->id }}" class="btn btn-navy rounded-pill w-100 py-2" disabled>Confirmar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    @endpush

    @push('scripts')
    <script>
        // Lógica unificada para cargar horarios y seleccionar slots
        document.addEventListener('change', function(e) {
            if (e.target.classList.contains('input-fecha-cambio') || e.target.classList.contains('input-fecha-reprogramar')) {
                const citaId = e.target.dataset.citaId;
                const isRepro = e.target.classList.contains('input-fecha-reprogramar');
                const containerId = isRepro ? `slotsContainer${citaId}` : `containerSlots${citaId}`;
                const btnId = isRepro ? `btnConfirmar${citaId}` : `btnEnviarSolicitud${citaId}`;
                const sectionId = isRepro ? null : `seccionHorarios${citaId}`;
                
                const container = document.getElementById(containerId);
                const btn = document.getElementById(btnId);
                if(sectionId) document.getElementById(sectionId).style.display = 'block';

                container.innerHTML = '<span class="spinner-border spinner-border-sm text-navy"></span>';
                btn.disabled = true;

                fetch(`/api/disponibilidad/{{ $citas->first()->doctor->id ?? 0 }}?fecha=${e.target.value}`)
                    .then(res => res.json())
                    .then(data => {
                        container.innerHTML = '';
                        if (data.slots && data.slots.length > 0) {
                            data.slots.forEach(hora => {
                                container.innerHTML += `<button type="button" class="btn btn-sm rounded-pill fw-bold btn-slot-generic" 
                                    style="border: 1.5px solid #00213D; color: #00213D;" 
                                    data-hora="${hora}" data-cita-id="${citaId}" data-is-repro="${isRepro}">${hora}</button>`;
                            });
                        } else {
                            container.innerHTML = `<span class="text-muted small">${data.mensaje || 'Sin horarios.'}</span>`;
                        }
                    });
            }
        });

        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('btn-slot-generic')) {
                const citaId = e.target.dataset.citaId;
                const isRepro = e.target.dataset.isRepro === "true";
                const inputId = isRepro ? `horaSeleccionada${citaId}` : `nuevaHoraCambio${citaId}`;
                const btnId = isRepro ? `btnConfirmar${citaId}` : `btnEnviarSolicitud${citaId}`;
                
                // Limpiar otros botones del mismo contenedor
                e.target.parentElement.querySelectorAll('.btn-slot-generic').forEach(b => {
                    b.style.backgroundColor = 'transparent';
                    b.style.color = '#00213D';
                });

                // Seleccionar actual
                e.target.style.backgroundColor = '#00213D';
                e.target.style.color = '#ffffff';
                document.getElementById(inputId).value = e.target.dataset.hora;
                document.getElementById(btnId).disabled = false;
            }
        });
    </script>
    @endpush

    <style>
        .hover-scale:hover { transform: translateY(-4px); }
        html body .btn-navy { background-color: #00213D !important; color: #ffffff !important; border: none !important; }
        html body .btn-outline-navy { color: #00213D !important; border: 1.5px solid #00213D !important; background-color: transparent !important; }
        .modal { z-index: 3000 !important; }
        .modal-backdrop { z-index: 2900 !important; }
    </style>
</x-layout>