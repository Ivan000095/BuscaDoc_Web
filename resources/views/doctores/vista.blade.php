<x-layout>
    <div class="container py-5">
        <div class="row mb-4">
            <div class="col-12 text-center">
                <h2 class="fw-bold text-navy">Nuestros Especialistas</h2>
                <p class="text-muted">Encuentra al doctor ideal para ti</p>
            </div>
        </div>
        <div class="row justify-content-center">
            @forelse($doctores as $doctor)
    <div class="col-md-6 col-lg-4 mb-4">
        <div class="card h-100 shadow-sm border-0 rounded-4 overflow-hidden">
            
            {{-- 1. IMAGEN SUPERIOR (Estilo Cover) --}}
            @if($doctor->user->foto)
                <img src="{{ asset('storage/' . $doctor->user->foto) }}" 
                     alt="Dr. {{ $doctor->user->name }}"
                     class="card-img-top" 
                     style="height: 180px; object-fit: cover; object-position: top;">
            @else
                {{-- Si no hay foto, usamos un avatar genérico o un placeholder --}}
                <img src="https://ui-avatars.com/api/?name={{ urlencode($doctor->user->name) }}&background=0d2e4e&color=fff&size=256" 
                     class="card-img-top" 
                     style="height: 180px; object-fit: cover;" 
                     alt="Sin foto">
            @endif

            <div class="card-body d-flex flex-column">

                {{-- 2. CALIFICACIÓN (Copiado de tu lógica de farmacias) --}}
                <div class="mb-3 d-flex align-items-center justify-content-start">
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

                {{-- 3. DATOS PRINCIPALES --}}
                <h5 class="card-title fw-bold text-navy mb-1">Dr. {{ $doctor->user->name }}</h5>
                
                {{-- Especialidad como subtítulo --}}
                <p class="text-primary small fw-bold mb-2">
                    {{ $doctor->especialidades->first()->nombre ?? 'Médico General' }}
                </p>

                <p class="card-text text-muted small mb-3">
                    {{ Str::limit($doctor->descripcion, 100) }}
                </p>

                {{-- 4. INFO EXTRA (Horario y Costo en lugar de Teléfono/RFC) --}}
                <div class="mt-auto">
                    {{-- Horario --}}
                    <div class="d-flex align-items-center mb-2">
                        <i class="bi bi-clock me-2 text-navy"></i>
                        <small class="text-muted">
                            {{ \Carbon\Carbon::parse($doctor->horario_entrada)->format('h:i A') }} - 
                            {{ \Carbon\Carbon::parse($doctor->horario_salida)->format('h:i A') }}
                        </small>
                    </div>

                    {{-- Costo --}}
                    <div class="d-flex align-items-center">
                        <i class="bi bi-cash-coin me-2 text-navy"></i>
                        <small class="fw-bold text-success">
                            ${{ number_format($doctor->costo, 2) }}
                        </small>
                    </div>
                </div>
            </div>

            {{-- 5. FOOTER CON BOTÓN --}}
            <div class="card-footer bg-white border-0 pt-0 pb-4 px-4">
                <a href="{{ route('doctores.show', $doctor->id) }}"
                   class="btn btn-outline-navy w-100 rounded-pill">
                    Ver Perfil
                </a>
            </div>
        </div>
    </div>
            @empty
                <div class="col-12 text-center py-5">
                    <div class="alert alert-light border shadow-sm rounded-4 text-muted">
                        <i class="bi bi-person-slash me-2"></i> No hay doctores disponibles por el momento.
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