@extends('layouts.app')

<style>
    .hover-scale {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .hover-scale:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1) !important;
    }

    .btn-custom {
        background-color: #00213D;
        border-color: #00213D;
        color: white;
    }

    .btn-custom:hover {
        background-color: #003366;
        color: white;
    }

    .custom-text-dark {
        color: #00213D;
    }
</style>

@section('content')
    <div class="container">
        {{-- Notificaciones --}}
        @if(session('success'))
            <div id="notification-pill" class="pill-notification">
                <div class="pill-icon"><i class="bi bi-check-lg"></i></div>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div id="notification-pill" class="pill-notification error">
                <div class="pill-icon"><i class="bi bi-x-lg"></i></div>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        {{-- ========================================== --}}
        {{-- PANEL ADMINISTRADOR --}}
        {{-- ========================================== --}}
        @if (Auth::user()->role == 'admin')
            <div class="row mb-4">
                <div class="col-12">
                    <h2 class="fw-bold custom-text-dark">Panel de Administración</h2>
                    <p class="text-muted">Bienvenido, {{ Auth::user()->name }} </p>
                </div>
            </div>

            <div class="row justify-content-center">
                <div class="col-12 col-md-6 col-lg-3 mb-4">
                    <div class="card h-100 border-50 shadow-sm hover-card">
                        <div class="card-body text-center d-flex flex-column justify-content-center align-items-center p-4">
                            <img src="{{ asset('images/doctores.jpg') }}" alt="Doctores"
                                class="rounded-circle mb-3 shadow-sm object-fit-cover" style="width: 80px; height: 80px;">
                            <h5 class="card-title fw-bold custom-text-dark">Doctores</h5>
                            <a href="{{ route('doctores.index') }}"
                                class="btn btn-custom btn-sm stretched-link mt-2 rounded-pill px-4">Entrar</a>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-3 mb-4">
                    <div class="card h-100 border-50 shadow-sm hover-card">
                        <div class="card-body text-center d-flex flex-column justify-content-center align-items-center p-4">
                            <img src="{{ asset('images/farmacias.jpeg') }}" alt="Farmacias"
                                class="rounded-circle mb-3 shadow-sm object-fit-cover" style="width: 80px; height: 80px;">
                            <h5 class="card-title fw-bold custom-text-dark">Farmacias</h5>
                            <a href="{{ route('admin.farmacias.index') }}"
                                class="btn btn-custom btn-sm stretched-link mt-2 rounded-pill px-4">Entrar</a>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-3 mb-4">
                    <div class="card h-100 border-50 shadow-sm hover-card">
                        <div class="card-body text-center d-flex flex-column justify-content-center align-items-center p-4">
                            <img src="{{ asset('images/pacientes.jpg') }}" alt="Pacientes"
                                class="rounded-circle mb-3 shadow-sm object-fit-cover" style="width: 80px; height: 80px;">
                            <h5 class="card-title fw-bold custom-text-dark">Pacientes</h5>
                            <a href="{{ route('pacientes.index') }}"
                                class="btn btn-custom btn-sm stretched-link mt-2 rounded-pill px-4">Entrar</a>
                        </div>
                    </div>
                </div>
            </div>

        @elseif (Auth::user()->role == 'doctor')
            <div class="row justify-content-center mb-5">
                <div class="col-lg-8 text-center">
                    <h2 class="fw-bold text-navy mb-2">Panel Médico</h2>
                    <p class="text-muted mb-4">Bienvenido, Dr. {{ Auth::user()->name }}</p>
                </div>
            </div>

            <div class="row g-4">
                {{-- COLUMNA PRINCIPAL (IZQUIERDA) --}}
                <div class="col-lg-8">

                    {{-- 1. TARJETAS DE ACCIÓN RÁPIDA (BOTONES) --}}
                    <h5 class="fw-bold text-navy mb-3">Accos Rápidos</h5>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <a href="{{ route('doctores.citas', Auth::user()->doctor->id) }}" class="text-decoration-none">
                                <div class="card h-100 border-0 shadow-sm rounded-4 p-4 hover-scale text-center">
                                    <div class="bg-navy-subtle text-navy rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center"
                                        style="width: 60px; height: 60px;">
                                        <i class="bi bi-calendar-week fs-3"></i>
                                    </div>
                                    <h6 class="fw-bold text-dark">Gestionar Agenda</h6>
                                </div>
                            </a>
                        </div>

                        <div class="col-md-6">
                            <a href="{{ route('mensajes.index') }}" class="text-decoration-none">
                                <div class="card h-100 border-0 shadow-sm rounded-4 p-4 hover-scale text-center">
                                    <div class="bg-navy-subtle text-navy rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center"
                                        style="width: 60px; height: 60px;">
                                        <i class="bi bi-chat-text-fill fs-3"></i>
                                    </div>
                                    <h6 class="fw-bold text-dark">Mensajes Pacientes</h6>
                                </div>
                            </a>
                        </div>
                    </div>

                    <h5 class="fw-bold text-navy mb-3">Resumen</h5>
                    <div class="row g-4">
                        <div class="col-md-4">
                            <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden">
                                <div
                                    class="card-header bg-white border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-success-subtle text-success rounded-circle d-flex align-items-center justify-content-center me-3"
                                            style="width: 40px; height: 40px;">
                                            <i class="bi bi-person-check-fill"></i>
                                        </div>
                                        <h6 class="fw-bold text-dark mb-0">Siguiente Paciente</h6>
                                    </div>
                                </div>

                                <div class="card-body px-4 pb-4 pt-3">
                                    @if($proximaCitaDoctor)
                                        <div class="p-3 bg-light rounded-3 border-start border-4 border-success">
                                            <div class="d-flex align-items-center mb-2">
                                                {{-- Foto del Paciente --}}
                                                <img src="{{ $proximaCitaDoctor->paciente->user->foto ? asset('storage/' . $proximaCitaDoctor->paciente->user->foto) : 'https://ui-avatars.com/api/?name=' . urlencode($proximaCitaDoctor->paciente->user->name) }}"
                                                    class="rounded-circle me-3 shadow-sm" width="45" height="45"
                                                    style="object-fit: cover;">

                                                <div>
                                                    <span
                                                        class="fw-bold text-dark d-block">{{ $proximaCitaDoctor->paciente->user->name }}</span>
                                                    <small
                                                        class="text-muted">{{ $proximaCitaDoctor->paciente->tipo_sangre ?? 'Paciente' }}</small>
                                                </div>
                                            </div>

                                            <div class="d-flex align-items-center justify-content-between mt-3">
                                                <span class="badge bg-white text-dark border shadow-sm">
                                                    <i class="bi bi-clock me-1 text-navy"></i>
                                                    {{ $proximaCitaDoctor->fecha_hora->format('h:i A') }}
                                                </span>
                                                @if($proximaCitaDoctor->estado == 'pendiente')
                                                    <span class="badge bg-warning text-dark">Por confirmar</span>
                                                @else
                                                    <span class="badge bg-success">Confirmada</span>
                                                @endif
                                            </div>
                                        </div>
                                    @else
                                        <div class="text-center py-4 opacity-50">
                                            <i class="bi bi-calendar-check fs-1 text-muted"></i>
                                            <p class="mb-0 small mt-2">No tienes citas próximas.</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden hover-scale">
                                <a href="{{ route('doctores.show', Auth::user()->doctor->id) }}#pills-reviews" 
                                class="text-decoration-none stretched-link"></a>
                                <div class="card-header bg-white border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-warning-subtle text-warning rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                            <i class="bi bi-star-fill"></i>
                                        </div>
                                        <h6 class="fw-bold text-dark mb-0">Última Opinión</h6>
                                    </div>
                                </div>

                                <div class="card-body px-4 pb-4 pt-3 position-relative">
                                    @if($ultimaReview)
                                        <div class="position-relative z-1 pt-2">
                                            {{-- Comentario --}}
                                            <p class="text-muted fst-italic mb-3 small pe-3">
                                                "{{ Str::limit($ultimaReview->contenido, 80) }}"
                                            </p>
                                            
                                            <div class="d-flex align-items-center justify-content-between border-top pt-3">
                                                <div class="d-flex align-items-center">
                                                    {{-- FOTO --}}
                                                    <img src="{{ $ultimaReview->autor?->foto ? asset('storage/' . $ultimaReview->autor->foto) : 'https://ui-avatars.com/api/?name=' . urlencode($ultimaReview->autor?->name ?? 'Anónimo') }}" 
                                                        class="rounded-circle me-2 shadow-sm" width="30" height="30" style="object-fit: cover;">
                                                    
                                                    {{-- NOMBRE --}}
                                                    <small class="fw-bold text-dark">{{ $ultimaReview->autor?->name ?? 'Anónimo' }}</small>
                                                </div>
                                                
                                                <div class="text-warning small bg-light px-2 py-1 rounded-pill border">
                                                    <span class="fw-bold text-dark me-1">{{ number_format($ultimaReview->calificacion, 1) }}</span>
                                                    <i class="bi bi-star-fill text-warning"></i>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <div class="text-center py-4">
                                            <div class="text-warning opacity-50 mb-2">
                                                <i class="bi bi-star"></i><i class="bi bi-star"></i><i class="bi bi-star"></i>
                                            </div>
                                            <p class="mb-0 small text-muted">Aún no tienes reseñas.</p>
                                        </div>
                                    @endif
                                </div>
                                
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden hover-scale position-relative">
                                <a href="{{ route('doctores.show', Auth::user()->doctor->id) }}#pills-questions" 
                                class="text-decoration-none stretched-link"></a>

                                <div class="card-header bg-white border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-info-subtle text-info rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                            <i class="bi bi-question-lg"></i>
                                        </div>
                                        <h6 class="fw-bold text-dark mb-0">Última pregunta</h6>
                                    </div>
                                </div>

                                <div class="card-body px-4 pb-4 pt-3 position-relative">
                                    @if($ultimaQuestion)
                                        <div class="position-relative z-1 pt-2">
                                            <p class="text-muted fst-italic mb-3 small pe-3">
                                                "{{ Str::limit($ultimaQuestion->contenido, 80) }}"
                                            </p>
                                            <div class="d-flex align-items-center justify-content-between border-top pt-3">
                                                <div class="d-flex align-items-center">
                                                    {{-- CORREGIDO: Usamos $ultimaQuestion en lugar de $ultimaReview --}}
                                                    <img src="{{ $ultimaQuestion->autor?->foto ? asset('storage/' . $ultimaQuestion->autor->foto) : 'https://ui-avatars.com/api/?name=' . urlencode($ultimaQuestion->autor?->name ?? 'Anónimo') }}" 
                                                        class="rounded-circle me-2 shadow-sm" width="30" height="30" style="object-fit: cover;">
                                                    <small class="fw-bold text-dark">{{ $ultimaQuestion->autor?->name ?? 'Anónimo' }}</small>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <div class="text-center py-4">
                                            <p class="mb-0 small text-muted">Aún no tienes preguntas.</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                {{-- COLUMNA LATERAL (PERFIL) --}}
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm rounded-4 p-4 sticky-top" style="top: 20px;">
                        <div class="d-flex align-items-center mb-4">
                            <img src="{{ Auth::user()->foto ? asset('storage/' . Auth::user()->foto) : 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) }}"
                                class="rounded-circle me-3 shadow-sm" width="60" height="60" style="object-fit: cover;">
                            <div>
                                <h6 class="fw-bold mb-0 text-navy">Dr. {{ Auth::user()->name }}</h6>
                                <small class="text-muted">
                                    {{ Auth::user()->doctor->especialidades->pluck('nombre')->join(', ') ?: 'Médico General' }}
                                </small>
                            </div>
                        </div>

                        <div class="border-top pt-3 mb-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted fw-bold">Costo Consulta</small>
                                <span
                                    class="fs-5 fw-bold text-success">${{ number_format(Auth::user()->doctor->costo, 2) }}</span>
                            </div>
                        </div>

                        <div class="bg-light p-3 rounded-3 mb-3">
                            <small class="text-muted d-block fw-bold mb-1">Horario de Atención</small>
                            <div class="d-flex align-items-center">
                                <i class="bi bi-clock me-2 text-navy"></i>
                                <span class="small text-dark">
                                    {{ \Carbon\Carbon::parse(Auth::user()->doctor->horario_entrada)->format('h:i A') }} -
                                    {{ \Carbon\Carbon::parse(Auth::user()->doctor->horario_salida)->format('h:i A') }}
                                </span>
                            </div>
                        </div>

                        <a href="{{ route('doctores.show', Auth::user()->doctor->id) }}"
                            class="btn btn-outline-navy w-100 rounded-pill btn-sm">
                            Ver mi perfil público
                        </a>
                    </div>
                </div>
            </div>

        @elseif (Auth::user()->role == 'farmacia')
            <div class="row justify-content-center mb-5">
                <div class="col-lg-8 text-center">
                    <h2 class="fw-bold text-navy mb-2">Panel de Farmacia</h2>
                    <p class="text-muted mb-4">Administra tu presencia y reputación.</p>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-lg-8">
                    {{-- Estadísticas Rápidas (Estilo Paciente) --}}
                    <div class="card border-0 shadow rounded-4 overflow-hidden mb-4 bg-navy text-white">
                        <div class="card-body p-4 d-flex align-items-center justify-content-between">
                            <div>
                                <h5 class="fw-bold mb-1">Tu Calificación</h5>
                                <div class="d-flex align-items-center mt-2">
                                    <span
                                        class="display-4 fw-bold me-3">{{ number_format(Auth::user()->farmacia->promedio_calificacion, 1) }}</span>
                                    <div class="text-warning fs-5">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <i
                                                class="bi {{ $i <= round(Auth::user()->farmacia->promedio_calificacion) ? 'bi-star-fill' : 'bi-star' }}"></i>
                                        @endfor
                                    </div>
                                </div>
                                <small class="opacity-75">Basado en tus reseñas recibidas</small>
                            </div>
                            <div class="d-none d-md-block opacity-25">
                                <i class="bi bi-trophy-fill" style="font-size: 5rem;"></i>
                            </div>
                        </div>
                    </div>

                    <h5 class="fw-bold text-navy mb-3">Acciones</h5>
                    <div class="row g-3">
                        {{-- Tarjeta 1: Ver Perfil --}}
                        <div class="col-md-6">
                            <a href="{{ route('farmacias.detalle', Auth::user()->farmacia->id) }}" class="text-decoration-none">
                                <div class="card h-100 border-0 shadow-sm rounded-4 p-4 hover-scale text-center">
                                    <div class="bg-navy-subtle text-navy rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center"
                                        style="width: 70px; height: 70px;">
                                        <i class="bi bi-shop fs-2"></i>
                                    </div>
                                    <h5 class="fw-bold text-dark">Ver mi Farmacia</h5>
                                    <small class="text-muted">Cómo te ven los clientes</small>
                                </div>
                            </a>
                        </div>

                        {{-- Tarjeta 2: Reseñas --}}
                        <div class="col-md-6">
                            <div class="card h-100 border-0 shadow-sm rounded-4 p-4 hover-scale text-center">
                                <div class="bg-warning-subtle text-warning rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center"
                                    style="width: 70px; height: 70px;">
                                    <i class="bi bi-chat-quote-fill fs-2"></i>
                                </div>
                                <h5 class="fw-bold text-dark">{{ Auth::user()->farmacia->reviews->count() }} Reseñas</h5>
                                <small class="text-muted">Total de opiniones de clientes</small>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Panel Lateral Farmacia --}}
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm rounded-4 p-4 sticky-top" style="top: 20px;">
                        <div class="d-flex align-items-center mb-4">
                            <img src="{{ Auth::user()->foto ? asset('storage/' . Auth::user()->foto) : 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) }}"
                                class="rounded-circle me-3 shadow-sm" width="60" height="60" style="object-fit: cover;">
                            <div>
                                <h6 class="fw-bold mb-0 text-navy">{{ Auth::user()->farmacia->nom_farmacia }}</h6>
                                <small class="text-muted">Propietario: {{ Auth::user()->name }}</small>
                            </div>
                        </div>

                        <div class="border-bottom pb-3 mb-3">
                            <span class="text-muted small fw-bold d-block mb-1">Horario de Atención</span>
                            <div class="d-flex align-items-center">
                                <i class="bi bi-clock me-2 text-navy"></i>
                                <span class="fw-bold text-dark">
                                    {{ \Carbon\Carbon::parse(Auth::user()->farmacia->horario_entrada)->format('h:i A') }} -
                                    {{ \Carbon\Carbon::parse(Auth::user()->farmacia->horario_salida)->format('h:i A') }}
                                </span>
                            </div>
                        </div>

                        <div class="border-bottom pb-3 mb-3">
                            <span class="text-muted small fw-bold d-block mb-1">Teléfono Público</span>
                            <div class="d-flex align-items-center">
                                <i class="bi bi-telephone-fill me-2 text-navy"></i>
                                <span>{{ Auth::user()->farmacia->telefono }}</span>
                            </div>
                        </div>

                        <div>
                            <span class="text-muted small fw-bold d-block mb-1">RFC</span>
                            <div class="bg-light p-2 rounded border text-center font-monospace small">
                                {{ Auth::user()->farmacia->rfc ?? 'No registrado' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        @elseif (Auth::user()->role == 'paciente')
            <div class="row justify-content-center mb-5">
                <div class="col-lg-8 text-center">
                    <h2 class="fw-bold text-navy mb-2">Bienvenido a BuscaDoc, {{ Auth::user()->name }}</h2>
                    <p class="text-muted mb-4">Encuentra lo que buscas, aquí mismo.</p>

                    <form action="{{ route('global.search') }}" method="GET">
                        <div class="input-group shadow-sm rounded-pill overflow-hidden border-0 p-1 bg-white">
                            <span class="input-group-text bg-white border-0 ps-4"><i class="bi bi-search text-muted"></i></span>
                            <input type="text" name="search" class="form-control border-0 shadow-none ps-2"
                                placeholder="Buscar cardiólogo, pediatra, farmacia..." style="height: 50px;" required>
                            <button class="btn btn-navy rounded-pill px-4 m-1 fw-bold" type="submit">Buscar</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-lg-8">
                    @if($proximaCita)
                        <div class="card border-0 shadow rounded-4 overflow-hidden mb-4">
                            <div class="card-body p-0">
                                <div class="row g-0">
                                    <div
                                        class="col-12 bg-navy text-white p-3 d-flex align-items-center justify-content-between d-md-none">
                                        <span class="fw-bold"><i class="bi bi-calendar-event me-2"></i>Tu próxima cita</span>
                                    </div>

                                    <div
                                        class="col-md-2 bg-light d-flex flex-column align-items-center justify-content-center py-4 border-end">
                                        <span
                                            class="text-uppercase small fw-bold text-muted">{{ $proximaCita->fecha_hora->format('M') }}</span>
                                        <span
                                            class="display-4 fw-bold text-navy lh-1">{{ $proximaCita->fecha_hora->format('d') }}</span>
                                        <span class="small text-muted">{{ $proximaCita->fecha_hora->format('l') }}</span>
                                    </div>

                                    <div class="col-md-7 p-4 d-flex align-items-center">
                                        <div class="d-flex align-items-center">
                                            <img src="{{ $proximaCita->doctor->user->foto ? asset('storage/' . $proximaCita->doctor->user->foto) : 'https://ui-avatars.com/api/?name=' . urlencode($proximaCita->doctor->user->name) }}"
                                                class="rounded-circle shadow-sm me-3" width="65" height="65"
                                                style="object-fit: cover;">
                                            <div>
                                                <small class="text-primary fw-bold text-uppercase"
                                                    style="font-size: 0.7rem; letter-spacing: 1px;">
                                                    Próxima Consulta
                                                </small>
                                                <h4 class="fw-bold text-navy mb-1">Dr. {{ $proximaCita->doctor->user->name }}</h4>
                                                <div class="d-flex align-items-center text-muted">
                                                    <i class="bi bi-clock-fill me-2 text-warning"></i>
                                                    <span
                                                        class="fw-bold text-dark">{{ $proximaCita->fecha_hora->format('h:i A') }}</span>
                                                    <span class="mx-2">•</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div
                                        class="col-md-3 bg-white p-4 d-flex flex-column justify-content-center align-items-center border-start">
                                        @if($proximaCita->estado == 'pendiente')
                                            <span class="badge bg-warning text-dark rounded-pill px-3 mb-3">
                                                Pendiente de confirmar
                                            </span>
                                        @elseif($proximaCita->estado == 'confirmada')
                                            <span class="badge bg-success rounded-pill px-3 mb-3">
                                                <i class="bi bi-check-circle me-1"></i> Confirmada
                                            </span>
                                        @endif
                                        <a href="{{ route('pacientes.citas') }}"
                                            class="btn btn-outline-navy rounded-pill btn-sm px-4">
                                            Ver mis citas
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="card border-0 shadow-sm rounded-4 mb-4 p-4 text-center bg-white">
                            <div class="py-3">
                                <div class="mb-3">
                                    <i class="bi bi-calendar-plus text-muted" style="font-size: 3rem; opacity: 0.3;"></i>
                                </div>
                                <h5 class="fw-bold text-navy">No tienes citas próximas</h5>
                                <p class="text-muted small">¿Te sientes mal o necesitas un chequeo?</p>
                                <a href="{{ route('doctores.vista') }}" class="btn btn-navy rounded-pill px-4 mt-2">
                                    Buscar un Doctor
                                </a>
                            </div>
                        </div>
                    @endif

                    <h5 class="fw-bold text-navy mb-3">¿Qué necesitas hacer?</h5>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <a href="{{ route('doctores.vista') }}" class="text-decoration-none">
                                <div class="card h-100 border-0 shadow-sm rounded-4 p-3 hover-scale text-center">
                                    <div class="bg-navy-subtle text-navy rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center"
                                        style="width: 60px; height: 60px;">
                                        <i class="bi bi-person-lines-fill fs-3"></i>
                                    </div>
                                    <h6 class="fw-bold text-dark">Buscar Doctor</h6>
                                    <small class="text-muted">Agenda tu consulta</small>
                                </div>
                            </a>
                        </div>

                        <div class="col-md-4">
                            <a href="{{ route('farmacias.catalogo') }}" class="text-decoration-none">
                                <div class="card h-100 border-0 shadow-sm rounded-4 p-3 hover-scale text-center">
                                    <div class="bg-navy-subtle text-navy rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center"
                                        style="width: 60px; height: 60px;">
                                        <i class="bi bi-shop fs-3"></i>
                                    </div>
                                    <h6 class="fw-bold text-dark">Farmacias</h6>
                                    <small class="text-muted">Encuentra una farmacia</small>
                                </div>
                            </a>
                        </div>

                        <div class="col-md-4">
                            <a href="{{ route('mensajes.index') }}" class="text-decoration-none">
                                <div class="card h-100 border-0 shadow-sm rounded-4 p-3 hover-scale text-center">
                                    <div class="bg-navy-subtle text-navy rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center"
                                        style="width: 60px; height: 60px;">
                                        <i class="bi bi-chat-dots-fill fs-3"></i>
                                    </div>
                                    <h6 class="fw-bold text-dark">Mis chats</h6>
                                    <small class="text-muted">Enviar mensaje a un doctor</small>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm rounded-4 p-4 sticky-top" style="top: 20px; z-index: 1;">
                        <div class="d-flex align-items-center mb-4">
                            <img src="{{ Auth::user()->foto ? asset('storage/' . Auth::user()->foto) : 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) }}"
                                class="rounded-circle me-3 shadow-sm" width="50" height="50" style="object-fit: cover;">
                            <div>
                                <h6 class="fw-bold mb-0 text-navy">Mi Ficha Médica</h6>
                                <a href="{{ route('users.show', Auth::user()->id) }}"
                                    class="small text-muted text-decoration-none">Ver perfil completo ></a>
                            </div>
                        </div>

                        @if(Auth::user()->patient)
                            <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                                <span class="text-muted small fw-bold">Mi tipo de sangre</span>
                                <span
                                    class="fw-bold text-danger bg-danger-subtle px-3 py-1 rounded-pill">{{ Auth::user()->patient->tipo_sangre ?? '--' }}</span>
                            </div>
                            <div class="mb-3">
                                <span class="text-muted small fw-bold d-block mb-1">Mis alergias</span>
                                <span
                                    class="fw-medium text-dark small bg-light p-2 rounded d-block border">{{ Auth::user()->patient->alergias ?? 'Ninguna registrada' }}</span>
                            </div>
                            <div class="mb-3">
                                <span class="text-muted small fw-bold d-block mb-1">Mi contacto de emergencia</span>
                                <div class="d-flex align-items-center text-navy fw-bold bg-light p-2 rounded border">
                                    <i class="bi bi-telephone-fill me-2"></i>
                                    {{ Auth::user()->patient->contacto_emergencia ?? '--' }}
                                </div>
                            </div>
                        @else
                            <div class="alert alert-warning small border-0 rounded-3">
                                <i class="bi bi-exclamation-circle-fill me-1"></i> Completa tu perfil médico para emergencias.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection