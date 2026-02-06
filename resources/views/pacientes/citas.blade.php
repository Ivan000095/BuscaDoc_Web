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
                            <div class="col-12 col-md-3 bg-white border-start d-flex align-items-center justify-content-center p-3">
                                @if($cita->estado == 'pendiente')
                                    <div class="text-center">
                                        <span class="badge bg-warning text-dark rounded-pill mb-2 px-3">Pendiente</span>
                                        <small class="d-block text-muted" style="font-size: 0.75rem;">Esperando confirmación</small>
                                    </div>
                                @elseif($cita->estado == 'confirmada')
                                    <div class="text-center">
                                        <span class="badge bg-success rounded-pill mb-2 px-3">Confirmada</span>
                                        <small class="d-block text-muted" style="font-size: 0.75rem;">¡No faltes!</small>
                                    </div>
                                @else
                                    <span class="badge bg-danger rounded-pill px-3">Cancelada</span>
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