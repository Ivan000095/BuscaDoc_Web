<x-layout>
    <div class="container py-5">
        <div class="row my-4">
            <div class="col-12 text-center">
                <h1 class="display-5 fw-bold text-primary">Farmacias en BuscaDoc</h1>
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
            <div class="row g-4">
                @foreach($farmacias as $f)
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100 shadow-sm border-0 rounded-4 overflow-hidden">
                            @if($f->user?->foto)
                                <img src="{{ asset('storage/' . $f->user->foto) }}" 
                                    alt="Dueño: {{ $f->user->name }}"
                                    class="card-img-top" style="height: 180px; object-fit: cover;">
                            @else
                                <div class="bg-light d-flex align-items-center justify-content-center" style="height: 180px;">
                                    <i class="bi bi-shop fs-1 text-muted"></i>
                                </div>
                            @endif

                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title fw-bold">{{ $f->nom_farmacia }}</h5>
                                <p class="text-muted small mb-2">
                                    <i class="bi bi-person-circle me-1"></i> {{ $f->user?->name ?? '—' }}
                                </p>
                                <p class="card-text">{{ Str::limit($f->descripcion, 100) }}</p>

                                <div class="mt-auto">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="bi bi-clock me-2 text-primary"></i>
                                        <small>{{ $f->horario }} | {{ $f->dias_op }}</small>
                                    </div>
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="bi bi-telephone me-2 text-success"></i>
                                        <small>{{ $f->telefono }}</small>
                                    </div>
                                    @if($f->rfc)
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-file-earmark-text me-2 text-info"></i>
                                            <small>RFC: {{ $f->rfc }}</small>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="card-footer bg-white border-0 pt-0">
                                <a href="{{ route('farmacias.detalle', $f->id) }}" 
                                class="btn btn-outline-primary w-100 rounded-pill">
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