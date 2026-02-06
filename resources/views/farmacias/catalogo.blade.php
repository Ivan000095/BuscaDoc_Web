@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="text-center mb-5">
        <h1>Farmacias cercanas</h1>
        <p class="lead text-muted">Encuentra la farmacia más cercana a ti en Ocosingo, Chiapas</p>
    </div>

    @if($farmacias->isEmpty())
        <div class="text-center py-5">
            <div class="alert alert-info">
                <strong>¡Aún no hay farmacias registradas!</strong><br>
                Pronto estarán disponibles.
            </div>
        </div>
    @else
        <div class="row g-4">
            @foreach($farmacias as $farmacia)
                <div class="col-md-3 col-sm-6">
                    <div class="card h-100 shadow-sm border-0 overflow-hidden">
                        <!-- Imagen (placeholder por ahora) -->
                        <div class="ratio ratio-16x9 bg-light d-flex align-items-center justify-content-center">
                            @if($farmacia->foto)
                                <img src="{{ asset('storage/' . $farmacia->foto) }}" 
                                    class="img-fluid" 
                                    alt="{{ $farmacia->nom_farmacia }}"
                                    style="object-fit: cover; height: 180px;">
                            @else
                                <div class="text-center text-muted">
                                    <i class="bi bi-building fs-2"></i>
                                    <p class="mt-2 small">Farmacia</p>
                                </div>
                            @endif
                        </div>

                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title fw-bold">{{ $farmacia->nom_farmacia }}</h5>
                            <p class="text-muted mb-2">
                                <i class="bi bi-geo-alt-fill me-1"></i>
                                {{ $farmacia->direccion ?? 'Dirección no disponible' }}
                            </p>
                            <p class="text-muted small mb-3">
                                <i class="bi bi-clock me-1"></i>
                                {{ $farmacia->horario ?? 'Horario no definido' }}
                            </p>

                            <div class="mt-auto">
                                <a href="{{ route('farmacias.detalle', $farmacia->id) }}" 
                                class="btn btn-outline-primary btn-sm w-100">
                                    Ver detalles
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection