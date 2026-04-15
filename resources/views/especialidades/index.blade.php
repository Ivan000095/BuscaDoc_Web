<x-layout>
    <div class="container py-5">
        <a href="{{ url()->previous() == url()->current() ? route('home') : url()->previous() }}" class="btn btn-link text-navy text-decoration-none mb-4 ps-0 hover-scale">
            <x-mcr-arrow-left-circle class="me-2" style="width: 1.2rem;"/> Volver atrás
        </a>

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-5">
            <div class="card-body p-5 bg-navy text-white text-center position-relative">
                <div class="bg-white text-navy rounded-circle mx-auto d-flex align-items-center justify-content-center mb-3 shadow" style="width: 80px; height: 80px;">
                    {{-- Icono representativo de Magicoons --}}
                    <x-mcf-stethoscope class="w-50 h-50" />
                </div>
                
                <h1 class="fw-bold mb-3">{{ $especialidad->nombre }}</h1>
                
                <p class="lead opacity-75 mx-auto mb-0" style="max-width: 700px;">
                    {{ $especialidad->descripcion ?? 'Nuestros especialistas están altamente capacitados para brindarte la mejor atención y cuidado en esta área de la salud.' }}
                </p>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold text-dark mb-0">Especialistas disponibles</h4>
            <span class="badge bg-navy-subtle text-navy rounded-pill px-3 py-2 fs-6">
                {{ $especialidad->doctors->count() }} médicos
            </span>
        </div>

        <div class="row g-4 mb-5">
            @forelse ($especialidad->doctors as $doctor)
                <div class="col-md-6 col-lg-4">
                    <div class="card border-0 shadow-sm rounded-4 h-100 hover-scale text-center p-4">
                        
                        <div class="position-relative mx-auto mb-3" style="width: 110px; height: 110px;">
                            <img src="{{ $doctor->user->foto ? asset('storage/' . $doctor->user->foto) : 'https://ui-avatars.com/api/?name=' . urlencode($doctor->user->name) }}"
                                 class="rounded-circle shadow-sm border border-3 border-white w-100 h-100" style="object-fit: cover;">
                            <span class="position-absolute bottom-0 end-0 bg-success border border-2 border-white rounded-circle"
                                  style="width: 22px; height: 22px; margin-bottom: 5px; margin-right: 5px;"></span>
                        </div>

                        <h5 class="fw-bold text-dark mb-1">Dr. {{ $doctor->user->name }}</h5>
                        
                        <div class="d-flex justify-content-center align-items-center text-muted small mb-4">
                            <x-mcr-wallet class="text-success me-1" style="width: 1.1rem;" />
                            <span>Consulta: ${{ number_format($doctor->costo, 2) }}</span>
                        </div>

                        <a href="{{ route('doctores.show', $doctor->id) }}" class="btn btn-outline-navy rounded-pill w-100 mt-auto py-2 fw-bold">
                            Ver Perfil Completo
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5">
                    <div class="mb-3 opacity-25 text-navy">
                        <x-mcr-search style="width: 5rem;" />
                    </div>
                    <h5 class="text-muted fw-bold">No hay médicos registrados aún.</h5>
                    <p class="text-muted small">Próximamente se unirán especialistas en esta área.</p>
                </div>
            @endforelse
        </div>

    </div>
</x-layout>