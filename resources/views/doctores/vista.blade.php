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

                        @if($doctor->user->foto)
                            <img src="{{ asset('storage/' . $doctor->user->foto) }}" alt="Dr. {{ $doctor->user->name }}"
                                class="card-img-top" style="height: 180px; object-fit: cover; object-position: top;">
                        @else
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($doctor->user->name) }}&background=0d2e4e&color=fff&size=256"
                                class="card-img-top" style="height: 180px; object-fit: cover;" alt="Sin foto">
                        @endif

                        <div class="card-body d-flex flex-column">
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
                            @php
                                $hoy = now()->dayOfWeek; // 0 (Dom) a 6 (Sáb)
                                $horaActual = now()->format('H:i:s');
                                $disponibilidadHoy = $doctor->disponibilidades->where('dia_semana', $hoy);
                                $estaAbierto = false;
                                $rangoHoy = "Cerrado ahora";

                                foreach($disponibilidadHoy as $bloque) {
                                    if($horaActual >= $bloque->hora_inicio && $horaActual <= $bloque->hora_fin) {
                                        $estaAbierto = true;
                                    }
                                }
                            @endphp

                            <div class="d-flex align-items-center mb-2">
                                <i class="bi bi-clock me-2 text-navy"></i>
                                @if($disponibilidadHoy->isEmpty())
                                    <span class="badge bg-secondary rounded-pill">Sin consultas hoy</span>
                                @else
                                    <span class="badge {{ $estaAbierto ? 'bg-success' : 'bg-danger' }} rounded-pill me-2">
                                        {{ $estaAbierto ? 'Abierto ahora' : 'Cerrado ahora' }}
                                    </span>
                                    <small class="text-muted">
                                        {{ \Carbon\Carbon::parse($disponibilidadHoy->first()->hora_inicio)->format('g:i A') }} - 
                                        {{ \Carbon\Carbon::parse($disponibilidadHoy->last()->hora_fin)->format('g:i A') }}
                                    </small>
                                @endif
                            </div>

                                {{-- Costo --}}
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-cash-coin me-2 text-navy">Costo promedio de consulta</i>
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
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1) !important;
        }
    </style>
</x-layout>