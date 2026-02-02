<x-layout>
    <div class="container py-5">
        
        {{-- Título de la sección --}}
        <div class="row mb-4">
            <div class="col-12 text-center">
                <h2 class="fw-bold" style="color: #00213D;">Nuestros Especialistas</h2>
                <p class="text-muted">Encuentra al doctor ideal para ti</p>
            </div>
        </div>

        {{-- INICIO DEL GRID (FILA) --}}
        <div class="row justify-content-center">

            {{-- BUCLE: Repetimos esto por cada doctor en la base de datos --}}
            @forelse($doctores as $doctor)
                <div class="col-12 col-md-6 col-lg-3 mb-4">
                    <div class="card h-100 border-0 shadow-sm hover-card" style="border-radius: 20px; transition: transform 0.2s;">
                        <div class="card-body text-center d-flex flex-column justify-content-center align-items-center p-4">

                            {{-- 1. FOTO DINÁMICA --}}
                            {{-- Verificamos si el usuario tiene foto, si no, usamos un avatar genérico --}}
                            <img src="{{ $doctor->user->foto ? asset('storage/' . $doctor->user->foto) : 'https://ui-avatars.com/api/?name='.urlencode($doctor->user->name).'&background=random' }}" 
                                 alt="{{ $doctor->user->name }}"
                                 class="rounded-circle mb-3 shadow-sm object-fit-cover" 
                                 style="width: 100px; height: 100px;">

                            {{-- 2. NOMBRE (Viene de la relación ->user) --}}
                            <h5 class="card-title fw-bold text-dark mb-1">
                                Dr. {{ $doctor->user->name }}
                            </h5>

                            {{-- 3. ESPECIALIDAD (Opcional: toma la primera si tiene) --}}
                            <p class="text-muted small mb-3">
                                {{ $doctor->especialidades->first()->nombre ?? 'Médico General' }}
                            </p>

                            {{-- 4. BOTÓN (Enlace al perfil individual) --}}
                            {{-- Usamos users.show porque es el perfil público --}}
                            <a href="{{ route('users.show', $doctor->user->id) }}"
                               class="btn btn-outline-primary btn-sm rounded-pill px-4 stretched-link">
                                Ver Perfil
                            </a>

                        </div>
                    </div>
                </div>
            
            {{-- SI NO HAY DOCTORES --}}
            @empty
                <div class="col-12 text-center py-5">
                    <div class="alert alert-light border shadow-sm rounded-4">
                        <i class="bi bi-info-circle me-2"></i> No hay doctores registrados en este momento.
                    </div>
                </div>
            @endforelse

        </div>
    </div>
    
    {{-- Estilo extra para el efecto hover --}}
    <style>
        .hover-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
        }
    </style>
</x-layout>