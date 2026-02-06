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

                                @if($cita->estado == 'pendiente')
                                    <div class="row g-2">
                                        {{-- Botón ACEPTAR --}}
                                        <div class="col-6">
                                            <form action="{{ route('citas.status', $cita->id) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="estado" value="confirmada">
                                                
                                                <button type="submit" class="btn btn-navy rounded-pill btn-sm w-100">
                                                    Aceptar
                                                </button>
                                            </form>
                                        </div>

                                        {{-- Botón RECHAZAR --}}
                                        <div class="col-6">
                                            <form action="{{ route('citas.status', $cita->id) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="estado" value="cancelada">
                                                
                                                <button type="submit" class="btn btn-danger rounded-pill btn-sm w-100 text-white">
                                                    Rechazar
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                @else
                                    <div class="text-center mt-2">
                                        <span class="badge {{ $cita->estado == 'confirmada' ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }} rounded-pill px-3">
                                            {{ ucfirst($cita->estado) }}
                                        </span>
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