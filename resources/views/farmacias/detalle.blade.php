@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row g-4">
        <!-- Columna izquierda: información principal -->
        <div class="col-lg-8">
            <div class="d-flex align-items-center mb-4">
                <h1 class="mb-0">{{ $farmacia->nom_farmacia }}</h1>
                <span class="badge bg-success ms-3">Abierto hoy</span> <!-- Opcional: lógica de horario -->
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-body">
                    @if($farmacia->descripcion)
                        <p class="lead">{{ $farmacia->descripcion }}</p>
                    @endif

                    <div class="mt-4">
                        <h5><i class="bi bi-geo-alt-fill text-primary me-2"></i>Dirección</h5>
                        <p class="fs-5">
                            {{ $farmacia->direccion ?? 'Dirección no disponible' }}
                        </p>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-6">
                            <h5><i class="bi bi-clock text-primary me-2"></i>Horario</h5>
                            <p>{{ $farmacia->horario ?? 'No especificado' }}</p>
                        </div>
                        <div class="col-md-6">
                            <h5><i class="bi bi-calendar-check text-primary me-2"></i>Días de operación</h5>
                            <p>{{ $farmacia->dias_op ?? 'Lunes a Domingo' }}</p>
                        </div>
                    </div>

                    @if($farmacia->telefono)
                        <div class="mt-4">
                            <h5><i class="bi bi-telephone text-primary me-2"></i>Contacto</h5>
                            <p class="fs-5">
                                <a href="tel:{{ $farmacia->telefono }}" class="text-decoration-none">
                                    {{ $farmacia->telefono }}
                                </a>
                            </p>
                        </div>
                    @endif

                    @if($farmacia->rfc)
                        <div class="mt-4">
                            <h5><i class="bi bi-file-earmark-text text-primary me-2"></i>RFC</h5>
                            <p>{{ $farmacia->rfc }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Columna derecha: acciones y mapa (opcional) -->
        <div class="col-lg-4">
            <div class="sticky-top" style="top: 20px;">
                <div class="card shadow-sm border-0">
                    <div class="card-body text-center">
                        <h5>¿Necesitas ayuda?</h5>
                        <p class="text-muted">Contacta directamente a la farmacia</p>

                        @if($farmacia->telefono)
                            <a href="tel:{{ $farmacia->telefono }}" class="btn btn-success w-100 mb-2">
                                <i class="bi bi-telephone me-2"></i>Llamar ahora
                            </a>
                        @endif

                        <button class="btn btn-outline-primary w-100 mb-2" disabled>
                            <i class="bi bi-whatsapp me-2"></i>Enviar WhatsApp
                        </button>

                        <button class="btn btn-outline-secondary w-100" disabled>
                            <i class="bi bi-map me-2"></i>Ver en mapa
                        </button>
                    </div>
                </div>

                <!-- Imagen o logo (si existe) -->
                @if($farmacia->foto)
                    <div class="card shadow-sm border-0 mt-4">
                        <img src="{{ asset('storage/' . $farmacia->foto) }}" 
                            class="card-img-top" 
                            alt="{{ $farmacia->nom_farmacia }}"
                            style="height: 200px; object-fit: cover;">
                    </div>
                @else
                    <div class="card shadow-sm border-0 mt-4 text-center py-5 bg-light">
                        <i class="bi bi-building fs-1 text-muted"></i>
                        <p class="mt-2 text-muted">Imagen no disponible</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="text-center mt-4">
        <a href="{{ route('farmacias.catalogo') }}" class="btn btn-link">
            ← Volver al catálogo de farmacias
        </a>
    </div>
</div>
@endsection