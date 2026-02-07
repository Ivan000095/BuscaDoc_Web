@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h3 class="fw-bold text-navy mb-4">Mis Citas Médicas</h3>

            @forelse($citas as $cita)
                <div class="card border-0 shadow-sm rounded-4 mb-3 overflow-hidden">
                    <div class="card-body p-0">
                        <div class="row g-0">
                            <div class="col-3 col-md-2 bg-light d-flex flex-column align-items-center justify-content-center text-center p-2 border-end">
                                <span class="d-block text-uppercase small fw-bold text-muted">{{ $cita->fecha_hora->format('M') }}</span>
                                <span class="d-block display-6 fw-bold text-navy">{{ $cita->fecha_hora->format('d') }}</span>
                                <span class="d-block small text-muted">{{ $cita->fecha_hora->format('D') }}</span>
                            </div>

                            <div class="col-9 col-md-7 p-3 d-flex align-items-center">
                                <img src="{{ $cita->doctor->user->foto ? asset('storage/'.$cita->doctor->user->foto) : 'https://ui-avatars.com/api/?name='.urlencode($cita->doctor->user->name) }}" 
                                    class="rounded-circle shadow-sm me-3" 
                                    width="60" height="60" 
                                    style="object-fit: cover;" 
                                    alt="Dr. {{ $cita->doctor->user->name }}">
                                <div>
                                    <div class="d-flex align-items-center mb-1">
                                        <i class="bi bi-clock-fill text-primary me-2 small"></i>
                                        <span class="fw-bold text-dark small">{{ $cita->fecha_hora->format('h:i A') }}</span>
                                    </div>
                                    <h5 class="fw-bold text-navy mb-0">Dr. {{ $cita->doctor->user->name }}</h5>
                                </div>
                            </div>

                            {{-- Columna Derecha: Estado --}}
                            <div class="col-12 col-md-3 bg-white border-start d-flex flex-column align-items-center justify-content-center p-4 gap-3">
                                
                                @if($cita->estado == 'pendiente')
                                    <div class="text-center">
                                        <span class="badge bg-warning text-dark rounded-pill px-3 py-2">
                                            <i class="bi bi-hourglass-split me-1"></i> Pendiente
                                        </span>
                                        <small class="d-block text-muted mt-2" style="font-size: 0.8rem;">Esperando confirmación</small>
                                    </div>
                                    
                                    <form action="{{ route('citas.status', $cita->id) }}" method="POST" class="w-100"
                                        onsubmit="return confirm('¿Deseas cancelar esta solicitud?');">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="estado" value="cancelada">
                                        <button class="btn btn-outline-danger rounded-pill btn-sm w-100 border-0" style="font-size: 0.8rem;">
                                            Cancelar solicitud
                                        </button>
                                    </form>

                                @elseif($cita->estado == 'confirmada')
                                    <div class="text-center w-100">
                                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-4 py-2 d-inline-flex align-items-center">
                                            <i class="bi bi-check-circle-fill me-2 fs-6"></i> 
                                            <span style="font-size: 0.9rem;">Confirmada</span>
                                        </span>
                                        <small class="d-block text-muted mt-2 fw-medium" style="font-size: 0.85rem;">¡No faltes a tu cita!</small>
                                    </div>

                                    <form action="{{ route('citas.status', $cita->id) }}" method="POST" class="w-100 px-2"
                                        onsubmit="return confirm('¿Seguro que deseas cancelar tu asistencia? Esta acción no se puede deshacer.');">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="estado" value="cancelada">
                                        <button type="submit" class="btn btn-danger rounded-pill w-100 shadow-sm hover-scale py-2 fw-bold d-flex align-items-center justify-content-center">
                                            <i class="bi bi-x-circle-fill me-2"></i> Cancelar Cita
                                        </button>
                                    </form>

                                @elseif($cita->estado == 'cancelada')
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-3 py-2">
                                        <i class="bi bi-x-circle me-1"></i> Cancelada
                                    </span>
                                    <small class="text-muted mt-1" style="font-size: 0.8rem;">Cita anulada</small>
                                @elseif($cita->estado == 'no asistida')
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-3 py-2">
                                        <i class="bi bi-x-circle me-1"></i> No asistida
                                    </span>
                                    <small class="text-muted mt-1" style="font-size: 0.8rem;">El doctor indicó que usted no asistió a la cita</small>
                                @else
                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-2">
                                        <i class="bi bi-check-circle-fill me-1"></i> Completada
                                    </span>
                                    <small class="text-muted mt-1" style="font-size: 0.8rem;">La cita fué llevada a cabo con éxito</small>
                                @endif

                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-5">
                    <img src="https://illustrations.popsy.co/gray/calendar.svg" alt="Empty" style="width: 150px; opacity: 0.5;">
                    <h5 class="text-muted mt-3">Aún no has agendado ninguna cita.</h5>
                    <a href="{{ route('users.index') }}" class="btn btn-navy rounded-pill mt-3">Buscar Doctor</a>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection