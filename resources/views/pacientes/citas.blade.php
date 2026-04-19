<x-layout>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <h3 class="fw-bold text-navy mb-4">Mis Citas Médicas</h3>

                @forelse($citas as $cita)
                    <div class="card border-0 shadow-sm rounded-4 mb-3 overflow-hidden">
                        <div class="card-body p-0">
                            <div class="row g-0">
                                <div
                                    class="col-3 col-md-2 bg-light d-flex flex-column align-items-center justify-content-center text-center p-2 border-end">
                                    <span
                                        class="d-block text-uppercase small fw-bold text-muted">{{ $cita->fecha->format('M') }}</span>
                                    <span
                                        class="d-block display-6 fw-bold text-navy">{{ $cita->fecha->format('d') }}</span>
                                    <span class="d-block small text-muted">{{ $cita->fecha->format('D') }}</span>
                                </div>

                                <div class="col-9 col-md-7 p-3 d-flex align-items-center">
                                    <img src="{{ $cita->doctor->user->foto ? asset('storage/' . $cita->doctor->user->foto) : 'https://ui-avatars.com/api/?name=' . urlencode($cita->doctor->user->name) }}"
                                        class="rounded-circle shadow-sm me-3" width="60" height="60"
                                        style="object-fit: cover;" alt="Dr. {{ $cita->doctor->user->name }}">
                                    <div>
                                        <div class="d-flex align-items-center mb-1">
                                            <i class="bi bi-clock-fill text-primary me-2 small"></i>
                                            <span
                                                class="fw-bold text-dark small">{{ \Carbon\Carbon::parse($cita->hora_inicio)->format('h:i A') }} </span>
                                        </div>
                                        <h5 class="fw-bold text-navy mb-0">Dr. {{ $cita->doctor->user->name }}</h5>
                                    </div>
                                </div>
                                        @php 
                                            $user = Auth::user();

                                            // Buscamos si esta cita tiene una solicitud pendiente donde el usuario actual es el solicitado
                                            $solicitudPendiente = \App\Models\SolicitudCambio::where('cita_id', $cita->id)
                                                                ->where('solicitado_id', Auth::id())
                                                                ->where('estado', 'pendiente')
                                                                ->first();
                                        @endphp
                                {{-- Columna Derecha: Estado --}}
                                <div
                                    class="col-12 col-md-3 bg-white border-start d-flex flex-column align-items-center justify-content-center p-4 gap-3">

                                    @if(in_array($cita->estado, ['cancelada', 'rechazada', 'finalizada']))
                                        <form action="{{ route('citas.destroy', $cita->id) }}" method="POST" 
                                            class="position-absolute" style="top: 10px; right: 10px; z-index: 10;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-light rounded-circle shadow-sm border-0" 
                                                    onclick="return confirm('¿Deseas eliminar esta cita de tu vista?')"
                                                    style="width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;">
                                                <i class="bi bi-x-lg text-danger" style="font-size: 0.8rem;"></i>
                                            </button>
                                        </form>
                                    @endif


                                    @if($cita->estado == 'pendiente' && !$cita->reprogramada)
                                        <button class="btn btn-outline-primary btn-sm rounded-pill px-3 shadow-sm" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#reprogramarLibreModal{{ $cita->id }}">
                                            <i class="bi bi-calendar-event me-1"></i> Reagendar (1 vez)
                                        </button>
                                    @elseif($cita->reprogramada && $cita->estado == 'pendiente')
                                        <span class="badge bg-light text-muted border rounded-pill">
                                            <i class="bi bi-info-circle me-1"></i> Cambio ya realizado
                                        </span>
                                    @endif



                                        @if($solicitudPendiente && $cita->estado != 'finalizada')
                                            <div class="alert alert-warning border-0 rounded-4 small p-2 mt-2">
                                                <strong>¡Solicitud de cambio!</strong><br>
                                                Propuesta: {{ $solicitudPendiente->nueva_fecha }} a las {{ $solicitudPendiente->nueva_hora }} <br>
                                                Motivo: {{ $solicitudPendiente->motivo }}
                                                <div class="d-flex gap-2 mt-2">
                                                    <form action="{{ route('citas.responder-cambio', $cita->id) }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="accion" value="aceptar">
                                                        <button type="submit" class="btn btn-xs btn-success rounded-pill">Aceptar</button>
                                                    </form>
                                                    
                                                    <button type="button" 
                                                    class="btn btn-xs btn-danger rounded-pill " data-bs-toggle="modal"
                                                    data-bs-target="#modalRechazarCambio{{$cita->id}}" data-bs-config='{"backdrop":true, "keyboard":true}'>
                                                         Rechazar  
                                                    </button>
                                                </div>
                                            </div>
                                        @endif
                                    @if($cita->estado == 'pendiente')
                                        <div class="text-center">
                                            <span class="badge bg-warning text-dark rounded-pill px-3 py-2">
                                                <i class="bi bi-hourglass-split me-1"></i> Pendiente
                                            </span>
                                            <small class="d-block text-muted mt-2" style="font-size: 0.8rem;">Esperando
                                                confirmación</small>
                                        </div>

                                        <form action="{{ route('citas.status', $cita->id) }}" method="POST" class="w-100"
                                            onsubmit="return confirm('¿Deseas cancelar esta solicitud?');">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="estado" value="cancelada">
                                            <button class="btn btn-outline-danger rounded-pill btn-sm w-100 border-0"
                                                style="font-size: 0.8rem;">
                                                Cancelar solicitud
                                            </button>
                                        </form>
                                    

                                        






                                    @elseif($cita->estado == 'confirmada')
                                        <div class="text-center w-100">
                                            <span
                                                class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-4 py-2 d-inline-flex align-items-center">
                                                <i class="bi bi-check-circle-fill me-2 fs-6"></i>
                                                <span style="font-size: 0.9rem;">Confirmada</span>
                                            </span>

                                                @php
                                                    $fecha = \Carbon\Carbon::parse($cita->fecha)->format('Y/m/d');
                                                    $hora = $cita->hora_inicio;
                                                    $fecha_hora = \Carbon\Carbon::parse($fecha . ' ' . $hora);
                                                    $esPasada = \Carbon\Carbon::parse($fecha_hora)->isPast();
                                                @endphp




                                            @if(!$esPasada)
                                            <small class="d-block text-muted mt-2 fw-medium" style="font-size: 0.85rem;">¡No
                                                faltes a tu cita!</small>
                                            @else
                                            <small class="d-block text-muted mt-2 fw-medium" style="font-size: 0.85rem;">
                                                ¡En espera de la accion del doctor!</small>
                                            @endif
                                        </div>


                                         @php
                                            // Buscamos si esta cita tiene una solicitud pendiente donde el usuario actual es el solicitante
                                            $solicitudEnviada = \App\Models\SolicitudCambio::where('cita_id', $cita->id)
                                                                ->where('solicitante_id', Auth::id())
                                                                ->where('estado', 'pendiente')
                                                                ->first();

                                            // Buscamos si el usuario actual fue el solicitante de un cambio que fue rechazado
                                            $ultimoRechazo = \App\Models\SolicitudCambio::where('cita_id', $cita->id)
                                                                ->where('solicitante_id', Auth::id())
                                                                ->where('estado', 'rechazada')
                                                                ->latest()
                                                                ->first();

                                            // Buscamos si esta cita tiene una solicitud confirmada donde el usuario actual es el solicitado
                                            $solicitudConfirmada = \App\Models\SolicitudCambio::where('cita_id', $cita->id)
                                                                ->where('solicitado_id', Auth::id())
                                                                ->where('estado', 'aceptada')
                                                                ->first();

                                            
                                            // Buscamos si esta cita tiene una solicitud confirmada donde el usuario actual es el solicitado
                                            $solicitudAceptada = \App\Models\SolicitudCambio::where('cita_id', $cita->id)
                                                                ->where('solicitante_id', Auth::id())
                                                                ->where('estado', 'aceptada')
                                                                ->first();                    



                                        @endphp

                                        @if($cita->estado == 'confirmada' && !$solicitudEnviada && !$esPasada && !$solicitudPendiente)


                                            <button type="button" 
                                                    class="btn btn-sm btn-outline-primary rounded-pill px-3 mt-2" data-bs-toggle="modal"
                                                    data-bs-target="#modalSolicitarCambio{{$cita->id}}" data-bs-config='{"backdrop":true, "keyboard":true}'>
                                                <i class="bi bi-calendar-event me-1"></i> Solicitar Cambio  
                                            </button>
                                        @elseif($solicitudEnviada)
                                        <div class="alert alert-warning border-0 rounded-4 small p-2 mt-2"> 
                                            <strong>¡Solicitud Enviada!</strong><br>
                                        </div>
                                        @endif




                                        @if($ultimoRechazo && !$solicitudEnviada && !$solicitudConfirmada && !$solicitudAceptada)
                                            <div class="alert alert-danger border-0 shadow-sm rounded-4 small p-2 mt-2">
                                                <center><i class="bi bi-exclamation-triangle-fill fs-4 me-3"></i></center>
                                                <div class="d-flex align-items-center">
                                                    
                                                    <div>
                                                        <h6 class="fw-bold mb-1">Tu solicitud de cambio fue rechazada</h6>
                                                        
                                                            <strong>Motivo:</strong> {{ $ultimoRechazo->motivo }}
                                                        
                                                    </div>
                                                </div>
                                                @if(!$solicitudPendiente)
                                                <div class="text-end mt-2">
                                                    <button class="btn btn-sm btn-outline-danger rounded-pill" 
                                                            data-bs-toggle="modal"
                                                    data-bs-target="#modalSolicitarCambio{{ $cita->id }}" data-bs-config='{"backdrop":true, "keyboard":true}'>
                                                        Intentar otro horario
                                                    </button>
                                                </div>
                                                @endif
                                            </div>
                                        @endif

                                        @if($solicitudAceptada && !$solicitudEnviada && !$solicitudConfirmada && $cita->estado != 'finalizada')
                                        <span
                                            class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-1 py-2">
                                            Tu solicitud fue aceptada con exito!
                                        </span>
                                        @endif

                                        <form action="{{ route('citas.status', $cita->id) }}" method="POST" class="w-100 px-2"
                                            onsubmit="return confirm('¿Seguro que deseas cancelar tu asistencia? Esta acción no se puede deshacer.');">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="estado" value="cancelada">
                                            <button class="btn btn-outline-danger rounded-pill btn-sm w-100 border-0"
                                                style="font-size: 0.8rem;">
                                                <i class="bi bi-x-circle-fill me-2"></i> Cancelar Cita
                                            </button>
                                        </form>

                                    @elseif($cita->estado == 'cancelada')

                                        <span
                                            class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-3 py-2">
                                            <i class="bi bi-x-circle me-1"></i> Cancelada
                                        </span>
                                        <small class="text-muted mt-1" style="font-size: 0.8rem;">Cita anulada</small>
                                    @elseif($cita->estado == 'no asistida')
                                        <span
                                            class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-3 py-2">
                                            <i class="bi bi-x-circle me-1"></i> No asistida
                                        </span>
                                        <small class="text-muted mt-1" style="font-size: 0.8rem;">El doctor indicó que usted no
                                            asistió a la cita</small>
                                    @else
                                        <span
                                            class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-2">
                                            <i class="bi bi-check-circle-fill me-1"></i> Completada
                                        </span>
                                        <small class="text-muted mt-1" style="font-size: 0.8rem;">La cita fué llevada a cabo con
                                            éxito</small>
                                    @endif

                                </div>
                            </div>
                        </div>
                    </div>

                    @push('modals')
                     @include('users.modal_reagendar')
                     @endpush


                    <div class="modal fade" id="reprogramarLibreModal{{ $cita->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content border-0 shadow-lg rounded-4">
                                <div class="modal-header border-0 pb-0">
                                    <h5 class="fw-bold text-navy">Reagendar Cita</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                
                                <form action="{{ route('citas.reprogramarLibre', $cita->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-body">
                                        <div class="alert alert-info border-0 rounded-4 small mb-4">
                                            <i class="bi bi-info-circle-fill me-2"></i>
                                            Esta es tu **única oportunidad** para cambiar la fecha de esta cita sin previa autorización del médico.
                                        </div>

                                        <div class="mb-4">
                                            <label class="small fw-bold text-navy mb-2">1. Selecciona la nueva fecha</label>
                                            <input type="date" 
                                                name="nueva_fecha" 
                                                id="fechaReprogramar{{ $cita->id }}" 
                                                class="form-control rounded-pill border-0 bg-light input-fecha-reprogramar" 
                                                data-cita-id="{{ $cita->id }}"
                                                data-doctor-id="{{ $cita->doctor_id }}"
                                                min="{{ date('Y-m-d') }}" 
                                                required>
                                        </div>

                                        <div class="mb-3">
                                            <label class="small fw-bold text-navy mb-2">2. Horarios disponibles</label>
                                            <div id="slotsContainer{{ $cita->id }}" class="d-flex flex-wrap gap-2">
                                                <span class="text-muted small italic">Selecciona una fecha para ver horarios...</span>
                                            </div>
                                            <input type="hidden" name="nueva_hora" id="horaSeleccionada{{ $cita->id }}" required>
                                        </div>
                                    </div>

                                    <div class="modal-footer border-0">
                                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
                                        <button type="submit" id="btnConfirmar{{ $cita->id }}" class="btn btn-navy rounded-pill px-4" disabled>
                                            Confirmar Cambio
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>



                    <script>
                    document.addEventListener('change', function(e) {
                        if (e.target.classList.contains('input-fecha-reprogramar')) {
                            const citaId = e.target.dataset.citaId;
                            const doctorId = e.target.dataset.doctorId;
                            const fecha = e.target.value;
                            const container = document.getElementById(`slotsContainer${citaId}`);
                            const btnConfirmar = document.getElementById(`btnConfirmar${citaId}`);
                            const inputHora = document.getElementById(`horaSeleccionada${citaId}`);

                            // Limpiar contenedor y resetear hora
                            container.innerHTML = '<div class="spinner-border spinner-border-sm text-primary" role="status"></div> <span class="small ms-2">Buscando...</span>';
                            inputHora.value = '';
                            btnConfirmar.disabled = true;

                            fetch(`/api/disponibilidad/{{ $cita->doctor->id }}?fecha=${fecha}`)
                                .then(response => response.json())
                                .then(data => {
                                    container.innerHTML = '';
                                    if (data.slots && data.slots.length > 0) {
                                        data.slots.forEach(hora => {
                                            const btn = document.createElement('button');
                                            btn.type = 'button';
                                            btn.className = 'btn btn-outline-primary btn-sm rounded-pill btn-slot-repro';
                                            btn.textContent = hora;
                                            btn.dataset.hora = hora;
                                            btn.dataset.citaId = citaId;
                                            container.appendChild(btn);
                                        });
                                    } else {
                                        container.innerHTML = `<span class="text-danger small"><i class="bi bi-x-circle me-1"></i> ${data.mensaje || 'No hay horarios disponibles.'}</span>`;
                                    }
                                })
                                .catch(error => {
                                    container.innerHTML = '<span class="text-danger small">Error al cargar horarios.</span>';
                                });
                        }
                    });

                    // Manejar el clic en los botones de hora (slots)
                    document.addEventListener('click', function(e) {
                        if (e.target.classList.contains('btn-slot-repro')) {
                            const citaId = e.target.dataset.citaId;
                            const hora = e.target.dataset.hora;
                            const container = document.getElementById(`slotsContainer${citaId}`);
                            const inputHora = document.getElementById(`horaSeleccionada${citaId}`);
                            const btnConfirmar = document.getElementById(`btnConfirmar${citaId}`);

                            // Desmarcar otros botones en este modal
                            container.querySelectorAll('.btn-slot-repro').forEach(btn => {
                                btn.classList.replace('btn-primary', 'btn-outline-primary');
                            });

                            // Marcar el seleccionado
                            e.target.classList.replace('btn-outline-primary', 'btn-primary');
                            inputHora.value = hora;
                            btnConfirmar.disabled = false;
                        }
                    });
                    </script>






                @empty
                    <div class="text-center py-5">
                        <img src="https://illustrations.popsy.co/gray/calendar.svg" alt="Empty"
                            style="width: 150px; opacity: 0.5;">
                        <h5 class="text-muted mt-3">Aún no has agendado ninguna cita.</h5>
                        <a href="{{ route('users.index') }}" class="btn btn-navy rounded-pill mt-3">Buscar Doctor</a>
                    </div>
                @endforelse
            </div>
        </div>
    </div>





 









</x-layout>