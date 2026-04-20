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
                        <p class="text-muted small mb-0">Administra tus citas y propuestas de cambio.</p>
                    </div>
                </div>

                @forelse($citas as $cita)
                    @php 
                        $user = Auth::user();
                        // 1. Solicitud que YO recibí (alguien más quiere cambiar y yo debo aceptar)
                        $solicitudRecibida = \App\Models\SolicitudCambio::where('cita_id', $cita->id)
                            ->where('solicitado_id', $user->id)
                            ->where('estado', 'pendiente')->first();

                        // 2. Solicitud que YO envié (estoy esperando que el otro acepte)
                        $solicitudEnviada = \App\Models\SolicitudCambio::where('cita_id', $cita->id)
                            ->where('solicitante_id', $user->id)
                            ->where('estado', 'pendiente')->first();

                        $fecha = \Carbon\Carbon::parse($cita->fecha)->format('Y/m/d');
                        $esPasada = \Carbon\Carbon::parse($fecha . ' ' . $cita->hora_inicio)->isPast();
                    @endphp

                    <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden card-cita transition-all hover-scale">
                        <div class="card-body p-0">
                            <div class="row g-0">
                                
                                {{-- FECHA --}}
                                <div class="col-3 col-md-2 bg-navy text-white d-flex flex-column align-items-center justify-content-center text-center p-2">
                                    <span class="d-block text-uppercase fw-bold opacity-75" style="font-size: 0.7rem;">{{ $cita->fecha->translatedFormat('M') }}</span>
                                    <span class="d-block display-6 fw-bold lh-1 my-1">{{ $cita->fecha->format('d') }}</span>
                                    <span class="d-block small opacity-75">{{ $cita->fecha->translatedFormat('D') }}</span>
                                </div>

                                {{-- INFO --}}
                                <div class="col-9 col-md-6 p-4 d-flex align-items-center">
                                    <img src="{{ $cita->doctor->user->foto ? asset('storage/' . $cita->doctor->user->foto) : 'https://ui-avatars.com/api/?name=' . urlencode($cita->doctor->user->name) }}"
                                        class="rounded-circle shadow-sm border border-2 border-white me-3" width="65" height="65" style="object-fit: cover;">
                                    <div>
                                        <div class="text-primary mb-1">
                                            <x-mcr-clock class="me-1" style="width: 0.9rem;" />
                                            <span class="fw-bold small">{{ \Carbon\Carbon::parse($cita->hora_inicio)->format('h:i A') }}</span>
                                        </div>
                                        <h5 class="fw-bold text-navy mb-0">Dr. {{ $cita->doctor->user->name }}</h5>
                                        <p class="text-muted small mb-0">{{ $cita->expediente->nombre_completo }} (Paciente)</p>
                                    </div>
                                </div>

                                {{-- ESTADO / ACCIONES --}}
                                <div class="col-12 col-md-4 bg-surface border-start d-flex flex-column align-items-center justify-content-center p-4 gap-2 position-relative">
                                    
                                    {{-- Botón para proponer cambio (Aparece si no hay nada pendiente) --}}
                                    @if(!$solicitudRecibida && !$solicitudEnviada && $cita->estado == 'pendiente' || $cita->estado == 'confirmada' && !$esPasada)
                                        <button class="btn btn-outline-navy btn-sm rounded-pill w-100 fw-bold shadow-sm" 
                                                data-bs-toggle="modal" data-bs-target="#modalSolicitarCambio{{ $cita->id }}">
                                            <x-mcr-calendar class="me-1" style="width: 1rem;"/> Proponer Cambio
                                        </button>
                                    @endif

                                    {{-- SI YO RECIBÍ UNA SOLICITUD --}}
                                    @if($solicitudRecibida)
                                        <div class="bg-warning-subtle border border-warning-subtle rounded-4 p-2 w-100 text-center shadow-sm">
                                            <small class="fw-bold text-warning-emphasis d-block mb-2">¡Nueva propuesta de horario!</small>
                                            <div class="d-flex gap-2">
                                                <form action="{{ route('citas.responder-cambio', $cita->id) }}" method="POST" class="flex-grow-1">
                                                    @csrf <input type="hidden" name="accion" value="aceptar">
                                                    <button type="submit" class="btn btn-success btn-sm rounded-pill w-100 py-1" style="font-size: 0.7rem;">Aceptar</button>
                                                </form>
                                                <button class="btn btn-danger btn-sm rounded-pill flex-grow-1 py-1" style="font-size: 0.7rem;" 
                                                        data-bs-toggle="modal" data-bs-target="#modalRechazarCambio{{$cita->id}}">Rechazar</button>
                                            </div>
                                        </div>

                                    {{-- SI YO ENVIÉ UNA SOLICITUD --}}
                                    @elseif($solicitudEnviada)
                                        <div class="bg-light border rounded-pill py-2 px-3 w-100 text-center">
                                            <span class="spinner-border spinner-border-sm text-navy me-2" style="width: 0.8rem; height: 0.8rem;"></span>
                                            <small class="fw-bold text-muted">Esperando respuesta...</small>
                                        </div>

                                    {{-- ESTADOS NORMALES --}}
                                    @else
                                        <span class="badge w-100 py-2 rounded-pill {{ $cita->estado == 'confirmada' ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning-emphasis' }}">
                                            {{ ucfirst($cita->estado) }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5 text-muted">No tienes citas registradas.</div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- BLOQUE DE MODALES (FUERA DE LAS TARJETAS) --}}
    @push('modals')
        @foreach($citas as $cita)
            {{-- Modal Solicitar Cambio --}}
            <div class="modal fade" id="modalSolicitarCambio{{$cita->id}}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                        <div class="modal-header bg-navy text-white border-0">
                            <h5 class="modal-title fw-bold"><x-mcr-calendar class="me-2" style="width: 1.2rem;"/> Nueva Propuesta</h5>
                            <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal"></button>
                        </div>
                        <form action="{{ route('citas.solicitar-cambio', $cita->id) }}" method="POST">
                            @csrf
                            <div class="modal-body p-4">
                                <div class="mb-4 text-center">
                                    <label class="small fw-bold text-navy mb-2 d-block">1. Elige la nueva fecha</label>
                                    <input type="date" name="nueva_fecha" class="form-control rounded-pill border bg-light py-2 px-4 mx-auto input-fecha-propuesta" 
                                        style="max-width: 250px;" data-cita-id="{{ $cita->id }}" min="{{ date('Y-m-d') }}" required>
                                </div>
                                <div class="mb-4 text-center seccion-slots-container" style="display:none;">
                                    <label class="small fw-bold text-navy mb-2 d-block">2. Horarios disponibles</label>
                                    <div id="containerSlotsPropuesta{{ $cita->id }}" class="d-flex flex-wrap justify-content-center gap-2 p-3 bg-light rounded-4 border"></div>
                                    <input type="hidden" name="nueva_hora" id="nuevaHoraPropuesta{{ $cita->id }}" required>
                                </div>
                                <div class="mb-2">
                                    <label class="small fw-bold text-navy mb-2 d-block">Motivo del Cambio</label>
                                    <textarea name="motivo" class="form-control rounded-4 border p-3" rows="3" placeholder="Explica por qué necesitas reagendar..." required></textarea>
                                </div>
                            </div>
                            <div class="modal-footer border-0 p-4 pt-0">
                                <button type="submit" id="btnEnviarPropuesta{{ $cita->id }}" class="btn btn-navy rounded-pill w-100 py-2 fw-bold" disabled>Enviar Propuesta</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Modal Rechazar Cambio --}}
            <div class="modal fade" id="modalRechazarCambio{{$cita->id}}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                        <div class="modal-body p-5 text-center">
                            <div class="bg-danger-subtle text-danger rounded-circle d-inline-flex align-items-center justify-content-center mb-4" style="width: 70px; height: 70px;">
                                <x-mcr-times style="width: 2.5rem;" />
                            </div>
                            <h4 class="fw-bold text-navy mb-2">Rechazar Cambio</h4>
                            <p class="text-muted small mb-4">Indica por qué no puedes aceptar el cambio.</p>
                            <form action="{{ route('citas.responder-cambio', $cita->id) }}" method="POST">
                                @csrf <input type="hidden" name="accion" value="rechazar">
                                <textarea name="motivo_rechazo" class="form-control rounded-4 border-0 bg-light p-3 mb-4 shadow-none" rows="3" placeholder="Ej: No estaré disponible..." required></textarea>
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-light w-100 rounded-pill py-2" data-bs-dismiss="modal">Cerrar</button>
                                    <button type="submit" class="btn btn-danger w-100 rounded-pill py-2 fw-bold">Confirmar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @endpush

    @push('scripts')
    <script>
        document.addEventListener('change', function(e) {
            if (e.target.classList.contains('input-fecha-propuesta')) {
                const citaId = e.target.dataset.citaId;
                const container = document.getElementById(`containerSlotsPropuesta${citaId}`);
                const btn = document.getElementById(`btnEnviarPropuesta${citaId}`);
                const section = e.target.closest('.modal-body').querySelector('.seccion-slots-container');

                section.style.display = 'block';
                container.innerHTML = '<span class="spinner-border spinner-border-sm text-navy"></span>';
                btn.disabled = true;

                fetch(`/api/disponibilidad/{{ $cita->doctor_id }}?fecha=${e.target.value}`)
                    .then(res => res.json())
                    .then(data => {
                        container.innerHTML = '';
                        if (data.slots && data.slots.length > 0) {
                            data.slots.forEach(hora => {
                                const b = document.createElement('button');
                                b.type = 'button';
                                b.className = 'btn btn-sm rounded-pill fw-bold btn-propuesta-slot';
                                b.style.border = '1.5px solid #00213D';
                                b.style.color = '#00213D';
                                b.textContent = hora;
                                b.dataset.hora = hora;
                                b.dataset.citaId = citaId;
                                container.appendChild(b);
                            });
                        } else {
                            container.innerHTML = `<span class="text-danger small">${data.mensaje || 'Sin horarios.'}</span>`;
                        }
                    });
            }
        });

        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('btn-propuesta-slot')) {
                const citaId = e.target.dataset.citaId;
                const input = document.getElementById(`nuevaHoraPropuesta${citaId}`);
                const btn = document.getElementById(`btnEnviarPropuesta${citaId}`);

                e.target.parentElement.querySelectorAll('.btn-propuesta-slot').forEach(b => {
                    b.style.backgroundColor = 'transparent';
                    b.style.color = '#00213D';
                });

                e.target.style.backgroundColor = '#00213D';
                e.target.style.color = '#ffffff';
                input.value = e.target.dataset.hora;
                btn.disabled = false;
            }
        });
    </script>
    @endpush

    <style>
        .hover-scale:hover { transform: translateY(-4px); }
        .bg-surface { background-color: #fbfcfd; }
        html body .btn-navy { background-color: #00213D !important; color: #ffffff !important; border: none !important; }
        .modal { z-index: 3000 !important; }
        .modal-backdrop { z-index: 2900 !important; }
    </style>
</x-layout>