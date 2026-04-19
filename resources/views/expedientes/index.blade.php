<x-layout>
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold text-navy mb-0">Mis Expedientes</h3>
                <p class="text-muted small">Gestiona tu información médica y la de tus familiares</p>
            </div>
            <a href="{{ route('expedientes.create') }}" class="btn btn-navy rounded-pill px-4">
                <i class="bi bi-plus-lg me-2"></i>Nuevo Expediente
            </a>
        </div>

        <div class="row g-3">
            @forelse($expedientes as $expediente)
                <div class="col-md-6 col-lg-4">
                    <div class="card border-0 shadow-sm rounded-4 h-100 transition-hover">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center mb-3">
                                <div class="avatar-circle bg-primary-subtle text-primary me-3">
                                    {{ strtoupper(substr($expediente->nombre_completo, 0, 1)) }}
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-0 text-navy">{{ $expediente->nombre_completo }}</h6>
                                    <span class="badge bg-light text-muted border rounded-pill small">
                                        {{ $expediente->parentesco }}
                                    </span>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="text-muted small">Género:</span>
                                    <span class="small fw-medium">{{ ucfirst($expediente->genero) }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="text-muted small">Edad:</span>


                                    <span class="small fw-medium">{{ \Carbon\Carbon::parse($expediente->fecha_nacimiento)->diffForHumans(null, true) }}</span>
                                    
                                    
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted small">Tipo de Sangre:</span>
                                    <span class="badge bg-danger-subtle text-danger rounded-pill">{{ $expediente->tipo_sangre ?? 'N/A' }}</span>
                                </div>
                            </div>

                            <div class="pt-3 border-top d-flex gap-2">
                                <a href="{{ route('expedientes.show', $expediente->id) }}" class="btn btn-outline-primary btn-sm rounded-pill flex-grow-1">
                                    Ver Ficha
                                </a>

                                <a href="{{ route('expedientes.edit', $expediente->id) }}" class="btn btn-light btn-sm rounded-circle text-muted">
                                   <i class="bi bi-pencil"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5">
                    <div class="mb-4">
                    <i class="bi bi-folder2-open text-primary-subtle" style="font-size: 5rem;"></i>
                    </div>
                    <h5 class="text-muted mt-4">No tienes expedientes registrados</h5>
                    <p class="small text-muted">Comienza agregando tu propio expediente o el de un familiar.</p>
                </div>
            @endforelse
        </div>
    </div>

    <style>
        .avatar-circle {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1.2rem;
        }
        .transition-hover:hover {
            transform: translateY(-5px);
            transition: all 0.3s ease;
            box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
        }
    </style>
</x-layout>