<x-layout>
    <div class="container">

        <div class="row mb-5">
            <div class="col-12 text-center">
                <h2 class="fw-bold text-navy">Nuestros Especialistas</h2>
                <p class="text-muted">Encuentra al doctor ideal para ti</p>
            </div>
        </div>

        <div class="row justify-content-center position-relative z-3 mb-5 mt-4">
            <div class="col-11 col-lg-10 col-xl-9">
                <div class="card border-0 shadow-sm rounded-5 search-form-card" style="background-color: #f8f9fa;">
                    <div class="card-body p-3 p-md-2">
                        <form action="{{ route('doctores.vista') }}" method="GET" id="searchDoctorForm">
                            <div class="row g-2 align-items-center">

                                <div class="col-12 col-md">
                                    <div
                                        class="input-group input-group-lg bg-white rounded-pill overflow-hidden border">
                                        <span class="input-group-text bg-white border-0 ps-4">
                                            <x-mcr-search class="text-muted" style="width: 1.2rem;" />
                                        </span>
                                        <input type="text" name="search" class="form-control border-0 shadow-none ps-2"
                                            placeholder="Buscar por nombre de doctor"
                                            value="{{ request('search') }}">
                                    </div>
                                </div>

                                <div class="col-12 col-md-auto">
                                    <select class="form-select form-select-lg rounded-pill border bg-white fw-bold px-4"
                                        name="especialidad" style="height: 48px; min-width: 240px; color: #0d2e4e;">
                                        <option value="">Todas las especialidades</option>
                                        @foreach($especialidades ?? [] as $esp)
                                            <option value="{{ $esp->nombre }}" {{ request('especialidad') == $esp->nombre ? 'selected' : '' }}>
                                                {{ $esp->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-12 col-md-auto">
                                    <button
                                        class="btn btn-lg rounded-pill w-100 px-4 fw-bold d-flex align-items-center justify-content-center shadow-sm"
                                        type="submit"
                                        style="height: 48px; background-color: #0d2e4e; color: white; transition: all 0.3s;">
                                        <x-mcl-search class="icon-white me-2" style="width: 1.2rem;" />
                                            Buscar
                                    </button>
                                </div>

                            </div>

                            <h5 class="card-title fw-bold text-navy mb-1">Dr. {{ $doctor->user->name }}</h5>

                            <p class="text-primary small fw-bold mb-2">
                                {{ $doctor->especialidades->first()->nombre ?? 'Médico General' }}
                            </p>

                            <p class="card-text text-muted small mb-3">
                                {{ Str::limit($doctor->descripcion, 100) }}
                            </p>

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

                                <div class="d-flex align-items-center">
                                    <i class="bi bi-cash-coin me-2 text-navy">Costo promedio de consulta</i>
                                    <small class="fw-bold text-success">
                                        ${{ number_format($doctor->costo, 2) }}
                                    </small>
                                </div>
                            </div>
                        </div>

                        <div class="card-footer bg-white border-0 pt-0 pb-4 px-4">
                            <a href="{{ route('doctores.show', $doctor->id) }}"
                                class="btn btn-navy w-100 rounded-pill">
                                Ver Perfil
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @forelse($doctoresPorEspecialidad as $especialidad => $listaDoctores)
            <div class="row mb-4 mt-2">
                <div class="col-12">
                    <h4 class="fw-bold text-navy border-bottom border-2 border-navy pb-2 d-inline-block">
                        <x-mcf-bookmark class="me-2" />{{ $especialidad }}
                    </h4>
                    <span class="badge bg-light text-muted ms-2 rounded-pill border">
                        {{ $listaDoctores->count() }} {{ $listaDoctores->count() === 1 ? 'doctor' : 'doctores' }}
                    </span>
                </div>
            </div>

            <div class="row justify-content-start mb-5">
                @foreach($listaDoctores as $doctor)
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card h-100 shadow-sm border-0 rounded-4 overflow-hidden hover-card">

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

                                <h5 class="card-title fw-bold text-navy mb-1">Dr. {{ $doctor->user->name }}</h5>

                                <p class="text-primary small fw-bold mb-2">
                                    {{ $especialidad }}
                                </p>

                                <p class="card-text text-muted small mb-3">
                                    {{ Str::limit($doctor->descripcion, 100) }}
                                </p>

                                <div class="mt-auto">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="bi bi-clock me-2 text-navy"></i>
                                        <small class="text-muted">
                                            {{ \Carbon\Carbon::parse($doctor->horario_entrada)->format('h:i A') }} -
                                            {{ \Carbon\Carbon::parse($doctor->horario_salida)->format('h:i A') }}
                                        </small>
                                    </div>

                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-cash-coin me-2 text-navy"></i>
                                        <small class="fw-bold text-success">
                                            ${{ number_format($doctor->costo, 2) }}
                                        </small>
                                    </div>
                                </div>
                            </div>

                            <div class="card-footer bg-white border-0 pt-0 pb-4 px-4">
                                <a href="{{ route('doctores.show', $doctor->id) }}" class="btn btn-navy w-100 rounded-pill">
                                    Ver Perfil
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

        @empty
            <div class="row">
                <div class="col-12 text-center py-5">
                    <div class="alert alert-light border shadow-sm rounded-4 text-muted">
                        <i class="bi bi-person-slash me-2 fs-4 d-block mb-2"></i>
                        No hay doctores disponibles por el momento.
                    </div>
                </div>
            </div>
        @endforelse

    </div>

    <style>
        .hover-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .hover-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(13, 46, 78, 0.1) !important;
        }

        .btn-navy {
            background-color: #0d2e4e;
            color: white;
            transition: all 0.3s ease;
        }

        .btn-navy:hover {
            background-color: #1a5f7a;
            color: white;
            transform: translateY(-2px);
        }
    </style>
</x-layout>