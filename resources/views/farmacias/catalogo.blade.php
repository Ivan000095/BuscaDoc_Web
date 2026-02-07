<?php
use Carbon\Carbon;
?>
<x-layout>
    <div class="container py-5">
        <div class="row mb-4">
            <div class="col-12 text-center">
                <h2 class="fw-bold text-navy">Farmacias</h2>
                <p class="text-muted">Encuentra la farmacia más cercana con horarios y servicios disponibles.</p>
            </div>
        </div>

        @if($farmacias->isEmpty())
            <div class="row justify-content-center">
                <div class="col-md-8 text-center py-5">
                    <i class="bi bi-shop fs-1 text-secondary"></i>
                    <h3 class="mt-3">No hay farmacias registradas aún</h3>
                    <p class="text-muted">Pronto podrás encontrar farmacias cerca de ti.</p>
                </div>
            </div>
        @else
            <div class="row g-4 justify-content-center">
                @foreach($farmacias as $f)
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100 shadow-sm border-0 rounded-4 overflow-hidden">
                            {{-- IMAGEN --}}
                            @if($f->user?->foto)
                                <img src="{{ asset('storage/' . $f->user->foto) }}" alt="Dueño: {{ $f->user->name }}"
                                    class="card-img-top" style="height: 180px; object-fit: cover;">
                            @else
                                <div class="bg-light d-flex align-items-center justify-content-center" style="height: 180px;">
                                    <i class="bi bi-shop fs-1 text-muted"></i>
                                </div>
                            @endif

                            <div class="card-body d-flex flex-column">

                                {{-- TÍTULO --}}
                                <h5 class="card-title fw-bold text-center">{{ $f->nom_farmacia }}</h5>

                                {{-- DUEÑO (Agregado de la rama reseñas) --}}
                                <p class="text-muted small mb-2 text-center">
                                    <i class="bi bi-person-circle me-1"></i> {{ $f->user?->name ?? '—' }}
                                </p>

                                {{-- ESTRELLAS / CALIFICACIÓN (Lógica dinámica de la rama reseñas) --}}
                                <div class="mb-3 d-flex align-items-center justify-content-center">
                                    @php $promedio = $f->promedio_calificacion; @endphp

                                    <div class="text-warning small me-2">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <i class="bi {{ $i <= round($promedio) ? 'bi-star-fill' : 'bi-star' }}"></i>
                                        @endfor
                                    </div>

                                    <span class="fw-bold text-dark small me-1">
                                        {{ $promedio > 0 ? number_format($promedio, 1) : '-' }}
                                    </span>

                                    <span class="text-muted small" style="font-size: 0.8rem;">
                                        ({{ $f->reviews->count() }})
                                    </span>
                                </div>

                                {{-- DESCRIPCIÓN --}}
                                <p class="card-text text-center">{{ Str::limit($f->descripcion, 100) }}</p>

                                {{-- DATOS EXTRA (Horario, Teléfono, RFC) --}}
                                <div class="mt-auto">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="bi bi-clock me-2 text-navy"></i>
                                        <small> {{ Carbon::parse($f->horario_entrada)->format('h:i A') }} -
                                            {{ Carbon::parse($f->horario_salida)->format('h:i A') }} </small>
                                    </div>
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="bi bi-telephone me-2 text-navy"></i>
                                        <small>{{ $f->telefono }}</small>
                                    </div>
                                    @if($f->rfc)
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-file-earmark-text me-2 text-navy"></i>
                                            <small>RFC: {{ $f->rfc }}</small>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="card-footer bg-white border-0 pt-0">
                                <a href="{{ route('farmacias.detalle', $f->id) }}"
                                    class="btn btn-outline-navy w-100 rounded-pill">
                                    Ver detalles
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</x-layout>