<x-layout>
    <div class="container py-5">
        
        {{-- Encabezado de Resultados --}}
        <div class="mb-5">
            <a href="{{ route('home') }}" class="text-decoration-none text-muted small mb-2 d-inline-block">
                <i class="bi bi-arrow-left"></i> Volver al inicio
            </a>
            <h2 class="fw-bold text-navy">Resultados para: "<span class="text-primary">{{ $query }}</span>"</h2>
            <p class="text-muted">Se encontraron {{ $doctores->count() }} doctores y {{ $farmacias->count() }} farmacias.</p>
        </div>

        @if($doctores->isEmpty() && $farmacias->isEmpty())
            <div class="alert alert-light border shadow-sm rounded-4 text-center py-5">
                <i class="bi bi-search display-1 text-muted opacity-25 mb-3"></i>
                <h4 class="fw-bold text-navy">Sin resultados</h4>
                <p class="text-muted">No encontramos coincidencias. Intenta buscar por otra especialidad o nombre.</p>
            </div>
        @else

            {{-- 1. SECCIÓN DOCTORES --}}
            @if($doctores->isNotEmpty())
                <h4 class="fw-bold text-navy mb-4 border-bottom pb-2">
                    <i class="bi bi-person-lines-fill me-2"></i>Doctores
                </h4>
                <div class="row mb-5">
                    @foreach($doctores as $doctor)
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card h-100 shadow-sm border-0 rounded-4 overflow-hidden">
                                {{-- Foto Rectangular --}}
                                <img src="{{ $doctor->user->foto ? asset('storage/' . $doctor->user->foto) : 'https://ui-avatars.com/api/?name=' . urlencode($doctor->user->name) }}" 
                                     class="card-img-top object-fit-cover" style="height: 180px;">
                                
                                <div class="card-body d-flex flex-column">
                                    <h5 class="fw-bold text-navy mb-1">Dr. {{ $doctor->user->name }}</h5>
                                    <p class="text-primary small fw-bold mb-2">
                                        {{ $doctor->especialidades->first()->nombre ?? 'General' }}
                                    </p>
                                    <p class="text-muted small mb-3">{{ Str::limit($doctor->descripcion, 80) }}</p>
                                    
                                    {{-- Info Extra --}}
                                    <div class="mt-auto small text-muted">
                                        <div class="d-flex align-items-center mb-1">
                                            <i class="bi bi-geo-alt me-2 text-navy"></i> Consultorio Privado
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-cash me-2 text-navy"></i> ${{ number_format($doctor->costo, 2) }}
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer bg-white border-0 pt-0 pb-4 px-4">
                                    <a href="{{ route('doctores.show', $doctor->id) }}" class="btn btn-outline-navy w-100 rounded-pill">Ver Perfil</a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- 2. SECCIÓN FARMACIAS --}}
            @if($farmacias->isNotEmpty())
                <h4 class="fw-bold text-navy mb-4 border-bottom pb-2">
                    <i class="bi bi-shop me-2"></i>Farmacias
                </h4>
                <div class="row">
                    @foreach($farmacias as $f)
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card h-100 shadow-sm border-0 rounded-4 overflow-hidden">
                                {{-- Foto --}}
                                @if($f->user?->foto)
                                    <img src="{{ asset('storage/' . $f->user->foto) }}" class="card-img-top object-fit-cover" style="height: 180px;">
                                @else
                                    <div class="bg-light d-flex align-items-center justify-content-center" style="height: 180px;">
                                        <i class="bi bi-shop fs-1 text-muted opacity-50"></i>
                                    </div>
                                @endif

                                <div class="card-body d-flex flex-column">
                                    <h5 class="fw-bold text-navy mb-1">{{ $f->nom_farmacia }}</h5>
                                    <p class="text-muted small mb-3">{{ Str::limit($f->descripcion, 80) }}</p>
                                    
                                    <div class="mt-auto small text-muted">
                                        <div class="d-flex align-items-center mb-1">
                                            <i class="bi bi-telephone me-2 text-navy"></i> {{ $f->telefono }}
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-clock me-2 text-navy"></i> 
                                            {{ \Carbon\Carbon::parse($f->horario_entrada)->format('h:i A') }} - 
                                            {{ \Carbon\Carbon::parse($f->horario_salida)->format('h:i A') }}
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer bg-white border-0 pt-0 pb-4 px-4">
                                    <a href="{{ route('farmacias.detalle', $f->id) }}" class="btn btn-outline-navy w-100 rounded-pill">Ver Detalles</a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

        @endif
    </div>
</x-layout>