@extends('layouts.app')
<style>
    .hover-scale {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .hover-scale:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1) !important;
    }

    .soft-card {
        border-radius: 20px;
    }

    .btn-white {
        background: white;
        border-color: #dee2e6;
    }

    .btn-white:hover {
        background: #f8f9fa;
    }
</style>
@section('content')
    <div class="container">
        <!-- inicion sesiada -->
        @if(session('success'))
            <div id="notification-pill" class="pill-notification">
                <div class="pill-icon">
                    <i class="bi bi-check-lg"></i>
                </div>
                <span>{{ session('success') }}</span>
            </div>
        @endif
        <!-- error -->
        @if(session('error'))
            <div id="notification-pill" class="pill-notification error">
                <div class="pill-icon">
                    <i class="bi bi-x-lg"></i>
                </div>
                <span>{{ session('error') }}</span>
            </div>
        @endif

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

                            <img src="{{ asset('images/doctores.jpg') }}" alt="Ivan"
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

                            <img src="{{ asset('images/farmacias.jpeg') }}" alt=""
                                class="rounded-circle mb-3 shadow-sm object-fit-cover" style="width: 80px; height: 80px;">

                            <h5 class="card-title fw-bold custom-text-dark">Farmacias</h5>
                            <a href="{{ route('admin.farmacias.index') }}" class="btn btn-custom btn-sm stretched-link mt-2 rounded-pill px-4">Entrar</a>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-6 col-lg-3 mb-4">
                    <div class="card h-100 border-50 shadow-sm hover-card">
                        <div class="card-body text-center d-flex flex-column justify-content-center align-items-center p-4">

                            <img src="{{ asset('images/pacientes.jpg') }}" alt=""
                                class="rounded-circle mb-3 shadow-sm object-fit-cover" style="width: 80px; height: 80px;">

                            <h5 class="card-title fw-bold custom-text-dark">Pacientes</h5>
                            <a href="{{ route('pacientes.index') }}"
                                class="btn btn-custom btn-sm stretched-link mt-2 rounded-pill px-4">Entrar</a>
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

                                        <a href="{{ route('pacientes.citas') }}" class="btn btn-outline-navy rounded-pill btn-sm px-4">
                                            Ver mis citas
                                        </a>
                                    </div>

                                </div>
                            </div>
                        </div>
                    @else
                        {{-- ESTADO VACÍO (Si no hay citas futuras) --}}
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

                    {{-- acciones rapidas --}}
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
                                    <small class="text-muted">Encuentra una farmacia cera de ti</small>
                                </div>
                            </a>
                        </div>

                        <div class="col-md-4">
                            <a href="{{ route('pacientes.citas') }}" class="text-decoration-none">
                                <div class="card h-100 border-0 shadow-sm rounded-4 p-3 hover-scale text-center">
                                    <div class="bg-navy-subtle text-navy rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center"
                                        style="width: 60px; height: 60px;">
                                        <i class="bi bi-file-earmark-medical-fill fs-3"></i>
                                    </div>
                                    <h6 class="fw-bold text-dark">Mis citas</h6>
                                    <small class="text-muted">Ver mis citas</small>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>

                {{-- perfil resumido --}}
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

@section('styles')
    <style>
        :root {
            --custom-dark-blue: #00213D;
        }

        .btn-custom {
            background-color: var(--custom-dark-blue);
            border-color: var(--custom-dark-blue);
            color: white;
            transition: all 0.3s ease;
        }

        .btn-custom:hover {
            background-color: #003366;
            border-color: #003366;
            color: white;
            transform: scale(1.05);
        }

        .custom-text-dark {
            color: var(--custom-dark-blue);
        }

        .hover-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            background: #ffffff;
        }

        .hover-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 1rem 2rem rgba(0, 33, 61, 0.15) !important;
        }

        .object-fit-cover {
            object-fit: cover;
        }
    </style>
@endsection