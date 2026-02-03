<x-layout>
    <div class="container py-5">
        <div class="row mb-4">
            <div class="col-12 text-center">
                <h2 class="fw-bold" style="color: #00213D;">Nuestros Especialistas</h2>
                <p class="text-muted">Encuentra al doctor ideal para ti</p>
            </div>
        </div>
        <div class="row justify-content-center">
            @forelse($doctores as $doctor)
                <div class="col-12 col-md-6 col-lg-3 mb-4">
                    <div class="card h-100 border-0 shadow-sm hover-card" style="border-radius: 20px; transition: transform 0.2s;">
                        <div class="card-body text-center d-flex flex-column justify-content-center align-items-center p-4">
                            <img src="{{ $doctor->user->foto ? asset('storage/' . $doctor->user->foto) : 'https://ui-avatars.com/api/?name='.urlencode($doctor->user->name).'&background=random' }}" 
                                 alt="{{ $doctor->user->name }}"
                                 class="rounded-circle mb-3 shadow-sm object-fit-cover" 
                                 style="width: 100px; height: 100px;">
                            <h5 class="card-title fw-bold text-dark mb-1">
                                Dr. {{ $doctor->user->name }}
                            </h5>
                            <p class="text-muted small mb-3">
                                {{ $doctor->especialidades->first()->nombre ?? 'Médico General' }}
                            </p>

                            <div class="mb-3 d-flex align-items-center justify-content-center">
                                @php $promedio = $doctor->promedio_calificacion; @endphp

                                <div class="text-warning small me-2">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <i class="bi {{ $i <= round($promedio) ? 'bi-star-fill' : 'bi-star' }}"></i>
                                    @endfor
                                </div>
                                
                                <span class="fw-bold text-dark small me-1">
                                    {{ $promedio > 0 ? number_format($promedio, 1) : '-' }}
                                </span>

                                <span class="text-muted small" style="font-size: 0.8rem;">
                                    ({{ $doctor->reviews->count() }})
                                </span>
                            </div>

                            <a href="{{ route('doctores.show', $doctor->id) }}"
                               class="btn btn-outline-navy btn-sm rounded-pill px-4 stretched-link "style="color: #00213D;">
                                Ver Perfil
                            </a>

                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5">
                    <div class="alert alert-light border shadow-sm rounded-4">
                        <i class="bi bi-info-circle me-2"></i> No hay doctores registrados en este momento.
                    </div>
                </div>
            @endforelse

        </div>
    </div>
    <style>
        .hover-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
        }
    </style>
</x-layout>