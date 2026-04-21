<x-layout>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                
                {{-- ENCABEZADO --}}
                <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4 pb-3 border-bottom gap-3">
                    <div class="d-flex align-items-center">
                        <div class="bg-navy-subtle text-navy rounded-circle d-flex align-items-center justify-content-center me-3 shadow-sm" style="width: 50px; height: 50px;">
                            <x-mcr-calendar style="width: 1.6rem;" />
                        </div>
                        <div>
                            <h3 class="fw-bold text-navy mb-0">Mis Citas Médicas</h3>
                            <p class="text-muted small mb-0">Gestiona tu agenda y revisa el historial clínico.</p>
                        </div>
                    </div>
                    
                    @if(Auth::user()->role == 'doctor')
                        <button class="btn btn-navy rounded-pill px-4 shadow-sm fw-bold d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#modalCitaExterna">
                            <x-mcl-plus-circle class="icon-white me-2" style="width: 1.2rem;"/> Agendar Cita
                        </button>
                    @endif
                </div>

                {{-- LÓGICA DE AGRUPACIÓN Y SEPARACIÓN --}}
                @php
                    $hoy = now()->startOfDay();
                    $coleccion = collect($citas);

                    // 1. AGENDA ACTIVA: Citas de hoy o futuro que no han terminado ni sido canceladas
                    $agendaActiva = $coleccion->filter(function($c) use ($hoy) {
                        $fechaCita = \Carbon\Carbon::parse($c->fecha)->startOfDay();
                        return !in_array($c->estado, ['finalizada', 'no asistida', 'cancelada', 'rechazada']) 
                               && $fechaCita->greaterThanOrEqualTo($hoy);
                    })->groupBy(function($c) {
                        return \Carbon\Carbon::parse($c->fecha)->format('Y-m-d');
                    })->sortKeys();

                    // 2. HISTORIAL: Citas concluidas, canceladas, rechazadas o de fechas pasadas
                    $historial = $coleccion->filter(function($c) use ($hoy) {
                        $fechaCita = \Carbon\Carbon::parse($c->fecha)->startOfDay();
                        return in_array($c->estado, ['finalizada', 'no asistida', 'cancelada', 'rechazada']) 
                               || $fechaCita->lessThan($hoy);
                    })->sortByDesc(function($c) {
                        return \Carbon\Carbon::parse($c->fecha->format('Y-m-d') . ' ' . $c->hora_inicio);
                    });
                @endphp

                {{-- ========================================== --}}
                {{-- SECCIÓN 1: AGENDA ACTIVA (AGRUPADA POR DÍA) --}}
                {{-- ========================================== --}}
                @if($agendaActiva->isNotEmpty())
                    @foreach($agendaActiva as $fechaStr => $grupoCitas)
                        
                        @php
                            $fechaObj = \Carbon\Carbon::parse($fechaStr);
                            $hoyStr = now()->format('Y-m-d');
                            $mananaStr = now()->addDay()->format('Y-m-d');

                            if ($fechaStr == $hoyStr) {
                                $etiquetaDia = 'Hoy';
                                $colorEtiqueta = 'text-primary';
                            } elseif ($fechaStr == $mananaStr) {
                                $etiquetaDia = 'Mañana';
                                $colorEtiqueta = 'text-info';
                            } else {
                                $etiquetaDia = ucfirst($fechaObj->translatedFormat('l'));
                                $colorEtiqueta = 'text-navy';
                            }
                        @endphp

                        <div class="d-flex align-items-center mb-3 mt-4">
                            <h5 class="mb-0 fw-bold {{ $colorEtiqueta }} me-2">{{ $etiquetaDia }}</h5>
                            <span class="text-muted small fw-medium">{{ $fechaObj->translatedFormat('d \d\e F, Y') }}</span>
                            <div class="flex-grow-1 border-bottom ms-3" style="opacity: 0.5;"></div>
                        </div>

                        @foreach($grupoCitas as $cita)
                            {{-- VARIABLES DE LA CITA --}}
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

                                $yaPropusoCambio = \App\Models\SolicitudCambio::where('cita_id', $cita->id)
                                    ->where('solicitante_id', $user->id)
                                    ->exists();

                                $inicioCita = \Carbon\Carbon::parse($cita->fecha->format('Y-m-d') . ' ' . $cita->hora_inicio);
                                $duracion = $cita->doctor->duracion_cita ?? 30;
                                $tiempoMinimoParaFinalizar = $inicioCita->copy()->addMinutes($duracion);
                                
                                $ahora = now();

                                $citaIniciada = $ahora->greaterThanOrEqualTo($inicioCita);
                                $puedeFinalizar = $ahora->greaterThanOrEqualTo($tiempoMinimoParaFinalizar);
                                $esHoy = $cita->fecha->isToday();
                                $minutosRestantes = $ahora->diffInMinutes($tiempoMinimoParaFinalizar, false);
                            @endphp

                            {{-- TARJETA DE LA CITA --}}
                            <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden card-cita hover-scale transition-all">
                                <div class="card-body p-0">
                                    <div class="row g-0">
                                        
                                        <div class="col-3 col-md-2 bg-navy text-white d-flex flex-column align-items-center justify-content-center text-center p-2">
                                            <span class="d-block text-uppercase fw-bold opacity-75" style="font-size: 0.7rem; letter-spacing: 1px;">
                                                {{ \Carbon\Carbon::parse($cita->hora_inicio)->format('A') }}
                                            </span>
                                            <span class="d-block display-6 fw-bold lh-1 my-1">{{ \Carbon\Carbon::parse($cita->hora_inicio)->format('h:i') }}</span>
                                            <span class="d-block small opacity-75 fw-medium">Hora</span>
                                        </div>

                                       <div class="col-9 col-md-6 p-4 d-flex align-items-center">
                                            @php
                                                // Si el paciente no tiene foto, le generamos un avatar bonito con sus iniciales
                                                $fotoPaciente = $cita->expediente->user->foto ?? null;
                                                $avatarUrl = $fotoPaciente 
                                                    ? asset('storage/' . $fotoPaciente) 
                                                    : 'https://ui-avatars.com/api/?name=' . urlencode($cita->expediente->nombre_completo) . '&background=f0f4f8&color=00213D&bold=true';
                                            @endphp

                                            <img src="{{ $avatarUrl }}" class="rounded-circle shadow-sm border border-2 border-white me-3" width="65" height="65" style="object-fit: cover;">
                                            
                                            <div class="overflow-hidden">
                                                {{-- Tipo de paciente / Parentesco --}}
                                                <div class="d-flex align-items-center mb-1 text-primary">
                                                    <x-mcr-user-alt class="me-1" style="width: 0.85rem;" />
                                                    <span class="fw-bold text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.5px;">
                                                        {{ $cita->expediente->parentesco == 'Yo mismo' ? 'Paciente de plataforma' : $cita->expediente->parentesco }}
                                                    </span>
                                                </div>
                                                
                                                {{-- Nombre del Paciente --}}
                                                <h5 class="fw-bold text-navy mb-1 text-truncate" title="{{ $cita->expediente->nombre_completo }}">
                                                    {{ $cita->expediente->nombre_completo }}
                                                </h5>
                                                
                                                {{-- Ficha médica rápida (Edad, Género, Sangre) --}}
                                                <div class="d-flex flex-wrap gap-1 mt-1">
                                                    <span class="badge bg-light text-muted border rounded-pill" style="font-size: 0.65rem;">
                                                        {{ \Carbon\Carbon::parse($cita->expediente->fecha_nacimiento)->age }} años
                                                    </span>
                                                    <span class="badge bg-light text-muted border rounded-pill" style="font-size: 0.65rem;">
                                                        {{ ucfirst($cita->expediente->genero) }}
                                                    </span>
                                                    @if($cita->expediente->tipo_sangre)
                                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2" style="font-size: 0.65rem;">
                                                            <x-mcr-test-tube style="width: 0.5rem;" class="me-1"/>{{ $cita->expediente->tipo_sangre }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-12 col-md-4 bg-surface border-start d-flex flex-column align-items-center justify-content-center p-4 gap-2 position-relative">
                                            
                                            @if(in_array($cita->estado, ['cancelada', 'rechazada', 'finalizada', 'no asistida']))
                                                <form action="{{ route('citas.destroy', $cita->id) }}" method="POST" class="position-absolute" style="top: 10px; right: 10px;">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-light rounded-circle shadow-sm border-0 text-danger" onclick="return confirm('¿Eliminar cita del historial?')">
                                                        <x-mcr-times style="width: 0.8rem;" />
                                                    </button>
                                                </form>
                                            @endif

                                            @if($user->role == 'paciente' && $cita->estado == 'pendiente' && !$cita->reprogramada)
                                                <button class="btn btn-outline-navy btn-sm rounded-pill w-100 fw-bold" data-bs-toggle="modal" data-bs-target="#reprogramarLibreModal{{ $cita->id }}">
                                                    <x-mcr-calendar class="me-1" style="width: 0.9rem;"/> Reagendar
                                                </button>
                                            @endif

                                            @if($solicitudPendiente && !$esPasada)
                                                <div class="alert bg-warning-subtle border border-warning border-opacity-25 rounded-4 p-3 mb-0 text-center shadow-sm w-100">
                                                    <div class="d-flex align-items-center justify-content-center mb-2">
                                                        <strong class="text-warning-emphasis">¡Nueva Propuesta!</strong>
                                                    </div>
                                                    <button type="button" class="btn btn-warning btn-sm rounded-pill px-4 fw-bold text-dark w-100 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalDetallesPropuesta{{$cita->id}}">
                                                        Ver Detalles
                                                    </button>
                                                </div>
                                            @elseif($solicitudEnviada)
                                                <div class="alert bg-light border text-muted small p-2 w-100 text-center mb-0">
                                                    <span class="spinner-border spinner-border-sm me-1" style="width: 0.7rem; height: 0.7rem;"></span> Esperando respuesta...
                                                </div>
                                            @elseif($cita->estado == 'pendiente')
                                                @if($user->role == 'doctor')
                                                    <div class="w-100 text-center">
                                                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-3 py-1 mb-2 w-100">Nueva Solicitud</span>
                                                        <div class="d-flex gap-2">
                                                            <form action="{{ route('citas.updateStatus', $cita->id) }}" method="POST" class="flex-fill m-0">
                                                                @csrf @method('PATCH')
                                                                <input type="hidden" name="estado" value="confirmada">
                                                                <button type="submit" class="btn btn-success btn-sm w-100 rounded-pill fw-bold shadow-sm">Aceptar</button>
                                                            </form>
                                                            <form action="{{ route('citas.updateStatus', $cita->id) }}" method="POST" class="flex-fill m-0">
                                                                @csrf @method('PATCH')
                                                                <input type="hidden" name="estado" value="cancelada">
                                                                <button type="submit" class="btn btn-outline-danger btn-sm w-100 rounded-pill fw-bold shadow-sm" onclick="return confirm('¿Rechazar esta cita?')">Rechazar</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                @else
                                                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-3 py-2 w-100 shadow-sm">Pendiente</span>
                                                @endif
                                            @elseif($cita->estado == 'confirmada')
                                                @if($user->role == 'doctor')
                                                    <div class="w-100">
                                                        @if($citaIniciada)
                                                            <div class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-2 mb-3 w-100 shadow-sm d-flex align-items-center justify-content-center">
                                                                <span class="spinner-grow spinner-grow-sm me-2 text-success" role="status"></span>
                                                                Paciente en Consulta
                                                            </div>
                                                            
                                                            <div class="d-flex gap-2">
                                                                @if($puedeFinalizar)
                                                                    <button type="button" class="btn btn-navy btn-sm flex-fill rounded-pill fw-bold py-2 shadow-sm" 
                                                                            data-bs-toggle="modal" data-bs-target="#modalFinalizarCita{{ $cita->id }}">
                                                                        <x-mcr-check-circle class="me-1" style="width: 0.9rem;"/> Finalizar
                                                                    </button>
                                                                @else
                                                                    <div class="alert bg-light border-0 small text-muted mb-0 py-2 px-2 rounded-4 flex-fill text-center d-flex align-items-center justify-content-center" style="font-size: 0.65rem;">
                                                                        <x-mcr-clock style="width: 0.7rem;" class="me-1"/> 
                                                                        Terminar en {{ $minutosRestantes }} min
                                                                    </div>
                                                                @endif

                                                                <form action="{{ route('citas.updateStatus', $cita->id) }}" method="POST" class="m-0 flex-fill">
                                                                    @csrf @method('PATCH')
                                                                    <input type="hidden" name="estado" value="no asistida">
                                                                    <button type="submit" class="btn btn-outline-danger btn-sm w-100 rounded-pill fw-bold py-2 shadow-sm" onclick="return confirm('¿Confirmar inasistencia?')">
                                                                        No Asistió
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        @else
                                                            <div class="badge bg-info-subtle text-info border border-info-subtle rounded-pill px-3 py-2 mb-2 w-100 shadow-sm d-flex align-items-center justify-content-center">
                                                                <x-mcr-calendar class="me-2" style="width: 1rem;"/> Cita Programada
                                                            </div>
                                                            <p class="text-muted mb-0 text-center small lh-sm">Consulta a las<br><span class="fw-bold">{{ \Carbon\Carbon::parse($cita->hora_inicio)->format('h:i A') }}</span></p>
                                                        @endif
                                                    </div>
                                                @else
                                                    <div class="text-center w-100">
                                                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-4 py-2 w-100 shadow-sm">Confirmada</span>
                                                        <p class="text-muted mb-0 mt-2 small fw-medium">Asiste puntualmente a tu cita.</p>
                                                    </div>
                                                @endif
                                            @elseif($cita->estado == 'finalizada')
                                                <div class="w-100 text-center">
                                                    <span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill px-4 py-2 w-100 shadow-sm">
                                                        <x-mcr-check class="me-1" style="width: 1rem;"/> Cita Concluida
                                                    </span>
                                                </div>
                                            @elseif($cita->estado == 'no asistida')
                                                <div class="w-100 text-center">
                                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-4 py-2 w-100 shadow-sm">
                                                        <x-mcr-user-alt class="me-1" style="width: 1rem;"/> Paciente no asistió
                                                    </span>
                                                </div>
                                            @else
                                                <span class="badge bg-light text-muted border rounded-pill px-3 py-2 w-100 shadow-sm">{{ ucfirst($cita->estado) }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endforeach
                @endif

                @if($historial->isNotEmpty())
                    
                    <div class="d-flex align-items-center mb-4 mt-5 pt-3">
                        <div class="bg-light px-3 py-2 rounded-pill border shadow-sm">
                            <h6 class="mb-0 fw-bold text-secondary text-uppercase small" style="letter-spacing: 1px;">
                                Historial de Consultas
                            </h6>
                        </div>
                        <div class="flex-grow-1 border-bottom ms-3" style="opacity: 0.3;"></div>
                    </div>

                    @foreach($historial as $cita)
                        {{-- VARIABLES DE LA CITA --}}
                        @php 
                            $user = Auth::user();
                            $fecha = \Carbon\Carbon::parse($cita->fecha)->format('Y/m/d');
                            $hora = $cita->hora_inicio;
                            $fecha_hora = \Carbon\Carbon::parse($fecha . ' ' . $hora);
                            $esPasada = $fecha_hora->isPast();

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
                            $yaPropusoCambio = \App\Models\SolicitudCambio::where('cita_id', $cita->id)
                                ->where('solicitante_id', $user->id)
                                ->exists();

                            $inicioCita = \Carbon\Carbon::parse($cita->fecha->format('Y-m-d') . ' ' . $cita->hora_inicio);
                            $duracion = $cita->doctor->duracion_cita ?? 30;
                            $tiempoMinimoParaFinalizar = $inicioCita->copy()->addMinutes($duracion);
                            $ahora = now();
                            $citaIniciada = $ahora->greaterThanOrEqualTo($inicioCita);
                            $puedeFinalizar = $ahora->greaterThanOrEqualTo($tiempoMinimoParaFinalizar);
                            $esHoy = $cita->fecha->isToday();
                            $minutosRestantes = $ahora->diffInMinutes($tiempoMinimoParaFinalizar, false);
                        @endphp

                        {{-- TARJETA DE LA CITA PARA HISTORIAL --}}
                        <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden card-cita hover-scale transition-all" style="opacity: 0.85;">
                            <div class="card-body p-0">
                                <div class="row g-0">
                                    
                                    <div class="col-3 col-md-2 bg-secondary text-white d-flex flex-column align-items-center justify-content-center text-center p-2">
                                        <span class="d-block text-uppercase fw-bold opacity-75" style="font-size: 0.7rem; letter-spacing: 1px;">
                                            {{ $cita->fecha->translatedFormat('M') }}
                                        </span>
                                        <span class="d-block display-6 fw-bold lh-1 my-1">{{ $cita->fecha->format('d') }}</span>
                                        <span class="d-block small opacity-75 fw-medium">{{ \Carbon\Carbon::parse($cita->hora_inicio)->format('h:i A') }}</span>
                                    </div>

                                    <div class="col-9 col-md-6 p-4 d-flex align-items-center">
                                        @php
                                            $fotoPaciente = $cita->expediente->user->foto ?? null;
                                            $avatarUrl = $fotoPaciente 
                                                ? asset('storage/' . $fotoPaciente) 
                                                : 'https://ui-avatars.com/api/?name=' . urlencode($cita->expediente->nombre_completo) . '&background=e9ecef&color=6c757d';
                                        @endphp

                                        <img src="{{ $avatarUrl }}" class="rounded-circle shadow-sm border border-2 border-white me-3 grayscale" width="65" height="65" style="object-fit: cover; filter: grayscale(100%); opacity: 0.8;">
                                        
                                        <div class="overflow-hidden">
                                            <div class="d-flex align-items-center mb-1 text-secondary">
                                                <x-mcr-user-alt class="me-1" style="width: 0.85rem;" />
                                                <span class="fw-bold text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.5px;">
                                                    {{ $cita->expediente->parentesco == 'Yo mismo' ? 'Paciente de plataforma' : $cita->expediente->parentesco }}
                                                </span>
                                            </div>
                                            
                                            <h5 class="fw-bold text-secondary mb-1 text-truncate" title="{{ $cita->expediente->nombre_completo }}">
                                                {{ $cita->expediente->nombre_completo }}
                                            </h5>
                                            
                                            <div class="d-flex flex-wrap gap-1 mt-1">
                                                <span class="badge bg-light text-muted border rounded-pill" style="font-size: 0.65rem;">
                                                    {{ \Carbon\Carbon::parse($cita->expediente->fecha_nacimiento)->age }} años
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12 col-md-4 bg-light border-start d-flex flex-column align-items-center justify-content-center p-4 gap-2 position-relative">
                                        
                                        @if(in_array($cita->estado, ['cancelada', 'rechazada', 'finalizada', 'no asistida']))
                                            <form action="{{ route('citas.destroy', $cita->id) }}" method="POST" class="position-absolute" style="top: 10px; right: 10px;">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-white rounded-circle shadow-sm border text-danger" onclick="return confirm('¿Eliminar cita del historial?')">
                                                    <x-mcr-times style="width: 0.8rem;" />
                                                </button>
                                            </form>
                                        @endif

                                        @if($cita->estado == 'finalizada')
                                            <div class="w-100 text-center">
                                                <span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill px-4 py-2 w-100 shadow-sm">
                                                    <x-mcr-check class="me-1" style="width: 1rem;"/> Cita Concluida
                                                </span>
                                            </div>
                                        @elseif($cita->estado == 'no asistida')
                                            <div class="w-100 text-center">
                                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-4 py-2 w-100 shadow-sm">
                                                    <x-mcr-user-alt class="me-1" style="width: 1rem;"/> Paciente no asistió
                                                </span>
                                            </div>
                                        @else
                                            <span class="badge bg-white text-muted border rounded-pill px-3 py-2 w-100 shadow-sm">{{ ucfirst($cita->estado) }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif


                {{-- ESTADO VACÍO GENERAL --}}
                @if($coleccion->isEmpty())
                    <div class="text-center py-5">
                        <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                            <x-mcr-calendar class="text-muted opacity-50" style="width: 2.5rem;" />
                        </div>
                        <h5 class="text-muted fw-bold">No tienes citas registradas</h5>
                        <p class="text-muted small">Tus próximas consultas y tu historial aparecerán aquí.</p>
                    </div>
                @endif

            </div>
        </div>
    </div>

    {{-- MODALES FUERA DEL FLUJO PARA EVITAR PANTALLA GRIS --}}
    @push('modals')
        @foreach($citas as $cita)
            @php 
                $user = Auth::user();
                $propuestaActiva = \App\Models\SolicitudCambio::where('cita_id', $cita->id)
                    ->where('solicitado_id', $user->id)
                    ->where('estado', 'pendiente')->first();

                $pacientesExternos = \App\Models\Expediente::where('user_id', Auth::id())
                    ->where('parentesco', 'Paciente Externo')->get();
            @endphp

            {{-- Modal Detalles Propuesta --}}
            @if($propuestaActiva)
            <div class="modal fade" id="modalDetallesPropuesta{{$cita->id}}" tabindex="-1" aria-hidden="true" data-bs-backdrop="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                        <div class="modal-header bg-navy text-white border-0 py-3">
                            <h5 class="modal-title fw-bold d-flex align-items-center">Detalles del Cambio</h5>
                            <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body p-4">
                            <div class="row g-3 mb-4">
                                <div class="col-6">
                                    <div class="p-3 bg-light rounded-4 border text-center h-100">
                                        <span class="d-block small text-muted text-uppercase fw-bold mb-1">Cita Original</span>
                                        <span class="d-block fw-bold text-navy">{{ \Carbon\Carbon::parse($cita->fecha)->format('d/m/Y') }}</span>
                                        <span class="d-block text-primary">{{ \Carbon\Carbon::parse($cita->hora_inicio)->format('h:i A') }}</span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="p-3 bg-warning-subtle rounded-4 border border-warning border-opacity-25 text-center h-100">
                                        <span class="d-block small text-warning-emphasis text-uppercase fw-bold mb-1">Propuesta</span>
                                        <span class="d-block fw-bold text-navy">{{ \Carbon\Carbon::parse($propuestaActiva->nueva_fecha)->format('d/m/Y') }}</span>
                                        <span class="d-block text-danger">{{ \Carbon\Carbon::parse($propuestaActiva->nueva_hora)->format('h:i A') }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="small fw-bold text-navy text-uppercase mb-2 d-block">Motivo del paciente:</label>
                                <div class="p-3 bg-light rounded-4 border text-muted fst-italic">
                                    "{{ $propuestaActiva->motivo ?? 'Sin motivo especificado por el usuario.' }}"
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-0 p-4 pt-0 d-flex gap-2">
                            <button type="button" class="btn btn-outline-danger flex-fill rounded-pill py-2 fw-bold" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#modalRechazarCambio{{$cita->id}}">
                                Rechazar
                            </button>
                            <form action="{{ route('citas.responder-cambio', $cita->id) }}" method="POST" class="flex-fill m-0">
                                @csrf <input type="hidden" name="accion" value="aceptar">
                                <button type="submit" class="btn btn-success w-100 rounded-pill py-2 fw-bold shadow-sm">
                                    Aceptar Cambio
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @endif
            
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

            {{-- Modal Finalizar Cita (Doctor) --}}
            <div class="modal fade" id="modalFinalizarCita{{ $cita->id }}" tabindex="-1" aria-hidden="true" data-bs-backdrop="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                        <div class="modal-header bg-navy text-white border-0 py-3">
                            <h5 class="modal-title fw-bold d-flex align-items-center">
                                <x-mcr-folder class="me-2" style="width: 1.5rem;"/> 
                                Finalizar Consulta: {{ $cita->expediente->nombre_completo }}
                            </h5>
                            <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal"></button>
                        </div>
                        
                        <form action="{{ route('notas.store', $cita->id) }}" method="POST">
                            @csrf
                            <div class="modal-body p-4">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label small fw-bold text-navy text-uppercase">Diagnóstico Médico <span class="text-danger">*</span></label>
                                        <textarea name="diagnostico" class="form-control rounded-4 border-light-subtle bg-light p-3 shadow-sm" rows="3" placeholder="Ej. Faringitis aguda, requiere reposo..." required></textarea>
                                    </div>
                                    
                                    <div class="col-12">
                                        <label class="form-label small fw-bold text-navy text-uppercase">Tratamiento Sugerido <span class="text-danger">*</span></label>
                                        <textarea name="tratamiento" class="form-control rounded-4 border-light-subtle bg-light p-3 shadow-sm" rows="3" placeholder="Indicar medicamentos, dosis y duración..." required></textarea>
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label small fw-bold text-navy text-uppercase">Notas de Seguimiento (Opcional)</label>
                                        <textarea name="nota_seguimiento" class="form-control rounded-4 border-light-subtle bg-light p-3 shadow-sm" rows="2" placeholder="Notas internas o para la siguiente cita..."></textarea>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="modal-footer border-0 p-4 pt-0">
                                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-navy rounded-pill px-5 fw-bold shadow-sm">
                                    Guardar Nota y Finalizar Cita
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="modalCitaExterna" tabindex="-1" aria-hidden="true" data-bs-backdrop="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="modal-header bg-navy text-white border-0 py-3">
                    <h5 class="modal-title fw-bold d-flex align-items-center">
                        <x-mcr-user-plus-alt class="me-2" style="width: 1.5rem;"/> Agendar Paciente Externo
                    </h5>
                    <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal"></button>
                </div>
                
                <form id="formCitaExterna" action="{{ route('citas.externa') }}" method="POST">
                    @csrf
                    <div class="modal-body p-4 p-md-5 bg-surface">
                        
                        {{-- 1. SELECTOR DE PACIENTE --}}
                        <h6 class="fw-bold text-navy mb-3 border-bottom pb-2">1. Datos del Paciente</h6>
                        
                        <div class="d-flex gap-2 mb-3 bg-light p-1 rounded-pill border w-100 mx-auto" style="max-width: 400px;">
                            <input type="radio" class="btn-check" name="tipo_paciente" id="tipoExistente" value="existente" autocomplete="off" checked onchange="togglePacienteForm()">
                            <label class="btn btn-sm rounded-pill flex-fill fw-bold" for="tipoExistente">Paciente Guardado</label>

                            <input type="radio" class="btn-check" name="tipo_paciente" id="tipoNuevo" value="nuevo" autocomplete="off" onchange="togglePacienteForm()">
                            <label class="btn btn-sm rounded-pill flex-fill fw-bold" for="tipoNuevo">Paciente Nuevo</label>
                        </div>

                        {{-- Formulario Paciente Existente --}}
                        <div id="seccionPacienteExistente" class="mb-4">
                            <select name="expediente_id" class="form-select rounded-pill border-light-subtle shadow-sm py-2 px-4">
                                <option value="">-- Selecciona un paciente registrado --</option>
                                @foreach($pacientesExternos as $paciente)
                                    <option value="{{ $paciente->id }}">{{ $paciente->nombre_completo }}</option>
                                @endforeach
                            </select>
                            @if($pacientesExternos->isEmpty())
                                <small class="text-danger mt-1 d-block"><x-mcr-info-circle style="width:0.8rem;"/> No tienes pacientes guardados. Registra uno nuevo.</small>
                            @endif
                        </div>

                        {{-- Formulario Paciente Nuevo (Oculto por defecto) --}}
                        <div id="seccionPacienteNuevo" class="mb-4" style="display: none;">
                            <div class="row g-3 p-3 border rounded-4 bg-white shadow-sm">
                                <div class="col-md-12">
                                    <label class="small fw-bold text-muted mb-1">Nombre Completo *</label>
                                    <input type="text" name="nombre_completo" class="form-control rounded-pill bg-light border-0 px-3">
                                </div>
                                <div class="col-md-6">
                                    <label class="small fw-bold text-muted mb-1">Fecha de Nacimiento *</label>
                                    <input type="date" name="fecha_nacimiento" class="form-control rounded-pill bg-light border-0 px-3">
                                </div>
                                <div class="col-md-6">
                                    <label class="small fw-bold text-muted mb-1">Género *</label>
                                    <select name="genero" class="form-select rounded-pill bg-light border-0 px-3">
                                        <option value="masculino">Masculino</option>
                                        <option value="femenino">Femenino</option>
                                        <option value="otro">Otro</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- 2. FECHA Y HORA --}}
                        <h6 class="fw-bold text-navy mt-4 mb-3 border-bottom pb-2">2. Fecha y Hora de la Consulta</h6>
                        
                        <div class="row g-4 align-items-start">
                            <div class="col-md-5 border-end-md">
                                <label class="small fw-bold text-muted mb-2 d-block text-center">Selecciona el día</label>
                                <input type="date" name="nueva_fecha" id="fechaCitaExterna" 
                                    class="form-control rounded-pill border bg-white py-2 px-3 shadow-sm mx-auto input-fecha-cambio text-center" 
                                    data-cita-id="Externa" 
                                    data-doctor-id="{{ Auth::user()->doctor->id }}" 
                                    min="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-7">
                                <label class="small fw-bold text-muted mb-2 d-block text-center">Horarios Disponibles</label>
                                <div id="containerSlotsExterna" class="d-flex flex-wrap justify-content-center gap-2 p-3 bg-white rounded-4 border shadow-sm" style="min-height: 60px;">
                                    <span class="text-muted small fst-italic">Selecciona una fecha primero...</span>
                                </div>
                                <input type="hidden" name="nueva_hora" id="nuevaHoraCambioExterna" required>
                            </div>
                        </div>

                        <div class="mt-4">
                            <label class="small fw-bold text-muted mb-1">Motivo de la consulta (Opcional)</label>
                            <input type="text" name="motivo_consulta" class="form-control rounded-pill border px-4" placeholder="Ej. Dolor de cabeza, chequeo de rutina...">
                        </div>

                    </div>
                    <div class="modal-footer border-0 p-4 bg-white rounded-bottom-4">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" id="btnEnviarSolicitudExterna" class="btn btn-navy rounded-pill px-5 fw-bold shadow-sm" disabled>
                            Agendar Cita
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

        @endforeach
    @endpush

    @push('scripts')
    <script>
        // 1. Función para el Modal de Cita Externa
        function togglePacienteForm() {
            const tipoNuevo = document.getElementById('tipoNuevo');
            if(!tipoNuevo) return;
            
            const isNuevo = tipoNuevo.checked;
            document.getElementById('seccionPacienteExistente').style.display = isNuevo ? 'none' : 'block';
            document.getElementById('seccionPacienteNuevo').style.display = isNuevo ? 'block' : 'none';
            
            document.querySelector('[name="expediente_id"]').required = !isNuevo;
            document.querySelector('[name="nombre_completo"]').required = isNuevo;
            document.querySelector('[name="fecha_nacimiento"]').required = isNuevo;
        }

        // 2. Estilos inyectados para los botones de Paciente
        document.addEventListener("DOMContentLoaded", function() {
            const style = document.createElement('style');
            style.innerHTML = `
                .btn-check:checked + .btn { background-color: #00213D; color: white; border-color: #00213D; }
                .btn-check:not(:checked) + .btn { color: #6c757d; }
            `;
            document.head.appendChild(style);
        });

        // 3. Lógica principal para cargar horarios al cambiar la fecha
        document.addEventListener('change', function(e) {
            if (e.target.classList.contains('input-fecha-cambio') || e.target.classList.contains('input-fecha-reprogramar')) {
                const citaId = e.target.dataset.citaId;
                const doctorId = e.target.dataset.doctorId; // CLAVE: Saca el ID del doctor del input
                const isRepro = e.target.classList.contains('input-fecha-reprogramar');
                
                const containerId = isRepro ? `slotsContainer${citaId}` : `containerSlots${citaId}`;
                const btnId = citaId === 'Externa' ? 'btnEnviarSolicitudExterna' : (isRepro ? `btnConfirmar${citaId}` : `btnEnviarSolicitud${citaId}`);
                const sectionId = isRepro ? null : `seccionHorarios${citaId}`;
                
                const container = document.getElementById(containerId);
                const btn = document.getElementById(btnId);
                const section = sectionId ? document.getElementById(sectionId) : null;

                if (!doctorId) {
                    console.error("Falta agregar data-doctor-id en el input de HTML.");
                    return;
                }

                if(section) section.style.display = 'block';

                container.innerHTML = '<span class="spinner-border spinner-border-sm text-navy"></span>';
                if(btn) btn.disabled = true;

                // Fetch a la API con el doctorId correcto
                fetch(`/api/disponibilidad/${doctorId}?fecha=${e.target.value}`)
                    .then(res => res.json())
                    .then(data => {
                        container.innerHTML = '';
                        if (data.slots && data.slots.length > 0) {
                            data.slots.forEach(hora => {
                                container.innerHTML += `<button type="button" class="btn btn-sm rounded-pill fw-bold btn-slot-generic shadow-sm" 
                                    style="border: 1.5px solid #00213D; color: #00213D; background: white;" 
                                    data-hora="${hora}" data-cita-id="${citaId}" data-is-repro="${isRepro}">${hora}</button>`;
                            });
                        } else {
                            container.innerHTML = `<span class="text-muted small fw-medium">${data.mensaje || 'Sin horarios.'}</span>`;
                        }
                    })
                    .catch(err => {
                        container.innerHTML = `<span class="text-danger small">Error de red.</span>`;
                        console.error(err);
                    });
            }
        });

        // 4. Lógica al seleccionar una hora
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('btn-slot-generic')) {
                const citaId = e.target.dataset.citaId;
                const isRepro = e.target.dataset.isRepro === "true";
                const inputId = isRepro ? `horaSeleccionada${citaId}` : `nuevaHoraCambio${citaId}`;
                const btnId = citaId === 'Externa' ? 'btnEnviarSolicitudExterna' : (isRepro ? `btnConfirmar${citaId}` : `btnEnviarSolicitud${citaId}`);
                
                e.target.parentElement.querySelectorAll('.btn-slot-generic').forEach(b => {
                    b.style.backgroundColor = 'white';
                    b.style.color = '#00213D';
                });

                e.target.style.backgroundColor = '#00213D';
                e.target.style.color = '#ffffff';
                
                document.getElementById(inputId).value = e.target.dataset.hora;
                const btn = document.getElementById(btnId);
                if(btn) btn.disabled = false;
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