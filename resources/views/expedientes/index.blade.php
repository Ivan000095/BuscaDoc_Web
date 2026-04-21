<x-layout>
    <div class="container py-5">
        
        {{-- ENCABEZADO --}}
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-5 gap-3">
            <div>
                <h2 class="fw-bold text-navy mb-1 d-flex align-items-center">
                    <x-mcl-folder-open class="text-navy me-3" style="width: 2rem; height: 2rem;" />
                    Mis Expedientes
                </h2>
                <p class="text-muted mb-0">Gestiona tu información médica y la de tus familiares</p>
            </div>
            <a href="{{ route('expedientes.create') }}" class="btn btn-navy btn-lg rounded-pill px-4 shadow-sm d-flex align-items-center">
                <x-mcl-plus-circle class="icon-white-2 me-2" style="width: 1.2rem; height: 1.2rem;" /> Nuevo Expediente
            </a>
        </div>

        {{-- GRID DE EXPEDIENTES --}}
        <div class="row g-4">
            @forelse($expedientes as $expediente)
                <div class="col-md-6 col-lg-4">
                    <div class="card border-0 shadow-sm rounded-4 h-100 transition-hover card-expediente">
                        
                        {{-- Borde superior de color dependiendo si es "Yo mismo" u otro familiar --}}
                        <div class="card-header border-0 bg-transparent pt-4 pb-0 px-4">
                            @if($expediente->parentesco == 'Yo mismo')
                                <div class="badge bg-navy-subtle text-navy rounded-pill px-3 py-2 mb-3 fw-bold border border-navy-subtle">
                                    <x-mcl-user-alt class="me-1" style="width: 1rem;" /> Expediente Propio
                                </div>
                            @else
                                <div class="badge bg-light text-secondary rounded-pill px-3 py-2 mb-3 fw-bold border">
                                    <x-mcl-users-alt class="me-1" style="width: 1rem;" /> {{ $expediente->parentesco }}
                                </div>
                            @endif
                        </div>

                        <div class="card-body px-4 pb-4 pt-1 d-flex flex-column">
                            <div class="d-flex align-items-center mb-4">
                                @if($expediente->parentesco == 'Yo mismo' && Auth::user()->foto)
                                    <img src="{{ asset('storage/' . Auth::user()->foto) }}" 
                                        alt="{{ $expediente->nombre_completo }}" 
                                        class="rounded-circle shadow-sm flex-shrink-0 me-3" 
                                        style="width: 60px; height: 60px; object-fit: cover; border: 2px solid white;">
                                @else
                                    <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 me-3 {{ $expediente->parentesco == 'Yo mismo' ? 'bg-navy text-white shadow-sm' : 'bg-light text-secondary border' }}"
                                        style="width: 60px; height: 60px; font-size: 1.2rem; font-weight: bold;">
                                        {{ strtoupper(substr($expediente->nombre_completo, 0, 1)) }}
                                    </div>
                                @endif

                                <div class="overflow-hidden">
                                    <h5 class="fw-bold mb-1 text-navy text-truncate" title="{{ $expediente->nombre_completo }}">
                                        {{ $expediente->nombre_completo }}
                                    </h5>
                                    <span class="text-muted small">
                                        {{ \Carbon\Carbon::parse($expediente->fecha_nacimiento)->age }} años 
                                        <span class="mx-1">•</span> 
                                        {{ ucfirst($expediente->genero) }}
                                    </span>
                                </div>
                            </div>

                            {{-- Datos Médicos Rápidos --}}
                            <div class="bg-surface rounded-4 p-3 mb-4 flex-grow-1 border">
                                <div class="d-flex align-items-center justify-content-between mb-2 pb-2 border-bottom border-light">
                                    <div class="d-flex align-items-center text-muted small">
                                        <x-mcl-test-tube class="me-2" style="width: 1rem;" /> Tipo de Sangre
                                    </div>
                                    <span class="badge {{ $expediente->tipo_sangre ? 'bg-danger-subtle text-danger' : 'bg-secondary-subtle text-secondary' }} rounded-pill px-2">
                                        {{ $expediente->tipo_sangre ?? 'No registrado' }}
                                    </span>
                                </div>

                                <div class="d-flex flex-column">
                                    <div class="d-flex align-items-center text-muted small mb-1">
                                        <x-mcl-triangle-exclamation class="me-2" style="width: 1rem;" /> Alergias Principales
                                    </div>
                                    <span class="small fw-medium text-dark text-truncate" title="{{ $expediente->alergias ?? 'Ninguna' }}">
                                        {{ $expediente->alergias ?? 'Ninguna registrada' }}
                                    </span>
                                </div>
                            </div>

                            {{-- Botones de Acción --}}
                            <div class="d-flex gap-2 mt-auto">
                                <a href="{{ route('expedientes.show', $expediente->id) }}" class="btn btn-navy rounded-pill flex-grow-1 fw-bold d-flex align-items-center justify-content-center transition-all">
                                    <x-mcl-eye class="icon-white-w me-2" style="width: 1.1rem;" /> Ver Ficha
                                </a>

                                <a href="{{ route('expedientes.edit', $expediente->id) }}" class="btn btn-light border rounded-circle text-secondary transition-all d-flex align-items-center justify-content-center btn-icon" style="width: 40px; height: 40px;" title="Editar">
                                    <x-mcl-pen style="width: 1.1rem;" />
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5">
                    <div class="card border-0 shadow-sm rounded-5 py-5 bg-white">
                        <div class="card-body">
                            <div class="mb-4">
                                <div class="bg-navy-subtle rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 100px; height: 100px;">
                                    <x-mcl-folder-open class="text-navy opacity-75" style="width: 3.5rem; height: 3.5rem;" />
                                </div>
                            </div>
                            <h4 class="fw-bold text-navy mb-2">No tienes expedientes registrados</h4>
                            <p class="text-muted mb-4">Comienza creando tu propia ficha médica para poder agendar citas.</p>
                            <a href="{{ route('expedientes.create') }}" class="btn btn-navy btn-lg rounded-pill px-5 shadow-sm">
                                <x-mcl-plus-circle class="text-white me-2" style="width: 1.2rem;" /> Crear mi expediente
                            </a>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</x-layout>