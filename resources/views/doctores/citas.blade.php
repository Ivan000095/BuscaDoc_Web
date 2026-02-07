@extends('layouts.app')

@section('content')
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold text-navy">Gestión de Citas</h2>
                <p class="text-muted">Administra tu agenda y revisa las fichas médicas.</p>
            </div>
            <div class="bg-white p-2 rounded-pill shadow-sm d-flex align-items-center px-3">
                <i class="bi bi-calendar-check text-navy me-2"></i>
                <span class="fw-bold">{{ now()->format('d M, Y') }}</span>
            </div>
        </div>

        <div class="row g-4">
            @forelse($citas as $cita)
                <div class="col-md-6 col-lg-4">
                    <div class="card border-0 shadow-sm rounded-4 h-100 position-relative overflow-hidden hover-scale">

                        <div class="position-absolute top-0 bottom-0 start-0"
                            style="width: 6px; background-color: {{ $cita->estado == 'pendiente' ? '#ffc107' : ($cita->estado == 'confirmada' ? '#198754' : '#dc3545') }};">
                        </div>

                        <div class="card-body p-4 ps-4">
                            <div class="d-flex align-items-center mb-3">
                                <img src="{{ $cita->paciente->user->foto ? asset('storage/' . $cita->paciente->user->foto) : 'https://ui-avatars.com/api/?name=' . urlencode($cita->paciente->user->name) }}"
                                    class="rounded-circle shadow-sm me-3" width="50" height="50" style="object-fit: cover;">
                                <div>
                                    <h6 class="fw-bold text-navy mb-0">{{ $cita->paciente->user->name }}</h6>
                                    <small class="text-muted">Paciente</small>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="d-flex align-items-center text-muted small mb-2">
                                    <i class="bi bi-clock-fill me-2 text-navy"></i>
                                    {{ $cita->fecha_hora->format('d/m/Y') }} —
                                    <strong class="text-dark ms-1">{{ $cita->fecha_hora->format('h:i A') }}</strong>
                                </div>
                                <div class="bg-light p-3 rounded-3 border-0">
                                    <small class="text-muted fw-bold d-block mb-1">Motivo de consulta:</small>
                                    <p class="mb-0 small text-dark fst-italic">"{{ Str::limit($cita->detalles, 80) }}"</p>
                                </div>
                            </div>

                            <div class="d-grid gap-2">
                                <a href="{{ route('users.show', $cita->paciente->user->id) }}"
                                    class="btn btn-outline-navy rounded-pill btn-sm fw-bold">
                                    <i class="bi bi-person-vcard-fill me-1"></i> Ver Ficha Médica
                                </a>

                                {{-- Lógica de Acciones del Doctor --}}
                                @php
                                    $esPasada = \Carbon\Carbon::parse($cita->fecha_hora)->isPast();
                                @endphp

                                @if($cita->estado == 'pendiente')
                                    {{-- CASO 1: SOLICITUD NUEVA (Aceptar / Rechazar) --}}
                                    <div class="row g-2">
                                        <div class="col-6">
                                            <form action="{{ route('citas.status', $cita->id) }}" method="POST">
                                                @csrf @method('PATCH')
                                                <input type="hidden" name="estado" value="confirmada">
                                                <button type="submit" class="btn btn-navy rounded-pill btn-sm w-100">Aceptar</button>
                                            </form>
                                        </div>
                                        <div class="col-6">
                                            <form action="{{ route('citas.status', $cita->id) }}" method="POST">
                                                @csrf @method('PATCH')
                                                <input type="hidden" name="estado" value="cancelada">
                                                <button type="submit" class="btn btn-danger rounded-pill btn-sm w-100 text-white">Rechazar</button>
                                            </form>
                                        </div>
                                    </div>

                                @elseif($cita->estado == 'confirmada' && $esPasada)
                                    {{-- CASO 2: CITA PASADA (Finalizar / No Asistió) --}}
                                    <div class="text-center mb-2">
                                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill px-3">
                                            <i class="bi bi-clock-history me-1"></i> Tiempo cumplido
                                        </span>
                                    </div>

                                    <div class="row g-2">
                                        <div class="col-12">
                                            <form action="{{ route('citas.status', $cita->id) }}" method="POST">
                                                @csrf @method('PATCH')
                                                <input type="hidden" name="estado" value="finalizada">
                                                <button class="btn btn-navy rounded-pill btn-sm w-100 shadow-sm" title="Marcar como atendida">
                                                    <i class="bi bi-check2-all me-1"></i> Finalizar Consulta
                                                </button>
                                            </form>
                                        </div>
                                        <div class="col-12">
                                            <form action="{{ route('citas.status', $cita->id) }}" method="POST" onsubmit="return confirm('¿Marcar inasistencia?');">
                                                @csrf @method('PATCH')
                                                <input type="hidden" name="estado" value="no asistida">
                                                <button class="btn btn-outline-secondary rounded-pill btn-sm w-100 border-0 bg-light" title="El paciente no llegó">
                                                    <small>El paciente no asistió</small>
                                                </button>
                                            </form>
                                        </div>
                                    </div>

                                @else
                                    {{-- CASO 3: ESTADOS INFORMATIVOS (Confirmada futura, Cancelada, Finalizada, etc.) --}}
                                    <div class="text-center mt-2">
                                        @if($cita->estado == 'confirmada')
                                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3">
                                                <i class="bi bi-calendar-check me-1"></i> Confirmada
                                            </span>
                                        @elseif($cita->estado == 'finalizada')
                                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3">
                                                <i class="bi bi-clipboard-check me-1"></i> Completada
                                            </span>
                                        @elseif($cita->estado == 'no_asistida')
                                            <span class="badge bg-dark-subtle text-dark border border-dark-subtle rounded-pill px-3">
                                                <i class="bi bi-person-slash me-1"></i> Inasistencia
                                            </span>
                                        @else
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-3">
                                                {{ ucfirst($cita->estado) }}
                                            </span>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5">
                    <div class="bg-light rounded-circle d-inline-flex p-4 mb-3 text-muted">
                        <i class="bi bi-calendar-x fs-1"></i>
                    </div>
                    <h5 class="text-muted">No tienes citas programadas.</h5>
                </div>
            @endforelse
        </div>
    </div>

    <style>
        .hover-scale {
            transition: transform 0.2s;
        }

        .hover-scale:hover {
            transform: translateY(-5px);
        }
    </style>
@endsection