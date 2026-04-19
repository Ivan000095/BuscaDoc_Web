<x-layout>
    @push('styles')
        <style>
            .h-15 {
                height: 15% !important;
            }

            .w-15 {
                width: 15% !important;
            }

            .w-175r {
                width: 1.575rem !important;
            }

            .h-175r {
                height: 1.575rem !important;
            }

            .hover-scale {
                transition: transform 0.2s ease, box-shadow 0.2s ease;
            }

            .hover-scale:hover {
                transform: translateY(-5px);
                box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1) !important;
            }

            .btn-custom {
                background-color: #00213D;
                border-color: #00213D;
                color: white;
            }

            .btn-custom:hover {
                background-color: #003366;
                color: white;
            }

            .custom-text-dark {
                color: #00213D;
            }

            .custom-map-control-button {
                background-color: #fff;
                border: 0;
                border-radius: 2px;
                box-shadow: 0 1px 4px -1px rgba(0, 0, 0, 0.3);
                margin: 10px;
                padding: 0 0.5em;
                font: 400 18px Roboto, Arial, sans-serif;
                overflow: hidden;
                height: 40px;
                cursor: pointer;
            }

            .custom-map-control-button:hover {
                background: rgb(235, 235, 235);
            }

            .btn-geo:hover .icon-normal {
                display: none;
            }

            .btn-geo:hover .icon-hover {
                display: inline-block !important;
            }

            .btn-geo:hover {
                background-color: #0d2e4e !important;
                color: white !important;
                transform: scale(1.1);
                transition: all 0.2s ease-in-out;
            }

            #map {
                min-height: 450px;
                width: 100%;
                border-radius: 15px;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            }

            .hero-guest {
                background: linear-gradient(135deg, #00213D 0%, #0d2e4e 100%);
                border-radius: 24px;
                color: white;
                padding: 4rem 2rem;
            }

            .specialty-icon {
                width: 60px;
                height: 60px;
                display: flex;
                align-items: center;
                justify-content: center;
                border-radius: 50%;
                font-size: 1.8rem;
            }

            .scroll-horizontal {
                display: flex;
                flex-wrap: nowrap;
                overflow-x: auto;
                gap: 1.5rem;
                padding-bottom: 2rem;
                scroll-snap-type: x mandatory;
                -ms-overflow-style: none;
                scrollbar-width: none;
            }

            .scroll-horizontal::-webkit-scrollbar {
                display: none;
            }

            .doctor-card-snap {
                scroll-snap-align: center;
                width: 280px;
                flex-shrink: 0;
            }

            .search-form-card {
                background-color: transparent !important;

                .card-body {
                    border-radius: 50px;
                    background-color: #f8f9fa;
                }
            }

            .search-input-group {
                background-color: white;
                border-radius: 50px;
                overflow: hidden;

                input {
                    font-weight: 500;
                }
            }

            .custom-user-role-dropdown {
                .dropdown-toggle {
                    border-radius: 50px;
                    color: $navy-main;

                    &:after {
                        display: none;
                    }
                }

                .dropdown-menu {
                    margin-top: 15px !important;

                    .dropdown-item {
                        color: $navy-main;

                        &:hover {
                            background-color: rgba($navy-main, 0.05);
                        }
                    }
                }
            }

            .search-button {
                height: 100%; // Para igualar la altura de los inputs

                .icon-white {
                    stroke: white !important;
                }
            }

            .heart-animated {
                pointer-events: none;

                animation: latido-bg 2.5s infinite ease-in-out;
                transform-origin: center;
            }

            @keyframes latido-bg {
                0% {
                    transform: scale(1);
                    opacity: 0.08;
                }

                10% {
                    transform: scale(1.05);
                    opacity: 0.15;
                }

                20% {
                    transform: scale(1);
                    opacity: 0.08;
                }

                30% {
                    transform: scale(1.03);
                    opacity: 0.12;
                }

                40% {
                    transform: scale(1);
                    opacity: 0.08;
                }

                100% {
                    transform: scale(1);
                    opacity: 0.08;
                }
            }

            .scroll-horizontal {
                display: flex;
                flex-wrap: nowrap;
                overflow-x: auto;
                gap: 1.25rem;
                padding: 1rem 0.5rem 1.5rem 0.5rem; 
                scroll-snap-type: x mandatory;
                scroll-behavior: smooth;
                -webkit-overflow-scrolling: touch;
            }

            .scroll-horizontal::-webkit-scrollbar {
                height: 8px;
            }
            .scroll-horizontal::-webkit-scrollbar-track {
                background: rgba(0, 33, 61, 0.05);
                border-radius: 10px;
                margin: 0 10px;
            }
            .scroll-horizontal::-webkit-scrollbar-thumb {
                background: rgba(0, 33, 61, 0.2);
                border-radius: 10px;
            }
            .scroll-horizontal::-webkit-scrollbar-thumb:hover {
                background: rgba(0, 33, 61, 0.5);
            }

            .doctor-card-snap {
                scroll-snap-align: start;
                width: 220px;
                flex-shrink: 0;
            }
        </style>
    @endpush

    <div class="container">
        @if(session('success'))
            <div id="notification-pill" class="pill-notification">
                <div class="pill-icon"><i class="bi bi-check-lg"></i></div>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div id="notification-pill" class="pill-notification error">
                <div class="pill-icon"><i class="bi bi-x-lg"></i></div>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        @guest
            <div class="row mt-4 mb-5">
                <div class="col-12">
                    <div class="hero-guest text-center shadow-lg position-relative overflow-hidden"
                        style="background: linear-gradient(135deg, #00213D 0%, #0d2e4e 100%); border-radius: 24px; color: white; padding: 4rem 2rem;">
                        <i class="bi bi-heart-pulse-fill position-absolute heart-animated"
                            style="font-size: 22rem; right: -40px; top: -60px; color: white; z-index: 0;"></i>

                        <h1 class="fw-bold mb-3 position-relative z-1">Encuentra a tu médico ideal</h1>
                        <p class="lead mb-4 opacity-75 position-relative z-1" style="max-width: 600px; margin: 0 auto;">
                            Reserva citas presenciales, envía mensajes y localiza tu farmacia más cercana en un solo lugar.
                        </p>

                        <div class="row justify-content-center position-relative z-3 mb-5">
                            <div class="col-11 col-lg-10 col-xl-9">
                                <div class="card border-0 shadow-lg rounded-5 search-form-card"
                                    style="background-color: #f8f9fa;">
                                    <div class="card-body p-3 p-md-2">
                                        <form action="{{ route('global.search') }}" method="GET" class="search-form-global"
                                            id="searchForm">
                                            <input type="hidden" name="type" id="searchTypeInput" value="">

                                            <div class="row g-2 align-items-center">
                                                <div class="col-12 col-md">
                                                    <div
                                                        class="input-group input-group-lg search-input-group bg-white rounded-pill overflow-hidden border">
                                                        <span class="input-group-text bg-white border-0 ps-4"><i
                                                                class="bi bi-search text-muted"></i></span>
                                                        <input type="text" name="search"
                                                            class="form-control border-0 shadow-none ps-2"
                                                            placeholder="Nombre, clínica o síntoma...">
                                                    </div>
                                                </div>

                                                <div class="col-12 col-md-auto">
                                                    <div class="dropdown custom-user-role-dropdown w-100"
                                                        id="searchDropdownGroup">
                                                        <button
                                                            class="btn btn-lg bg-white border rounded-pill text-start d-flex align-items-center justify-content-between w-100 px-4"
                                                            style="height: 48px;" type="button" data-bs-toggle="dropdown"
                                                            aria-expanded="false">
                                                            <div class="d-flex align-items-center">
                                                                <div id="selectedRoleIcon"
                                                                    class="d-flex align-items-center justify-content-center me-2 text-muted flex-shrink-0"
                                                                    style="width: 24px;">
                                                                    <i class="bi bi-funnel fs-5"></i>
                                                                </div>
                                                                <span class="dropdown-label text-navy fw-bold"
                                                                    id="selectedRoleLabel">¿Qué buscas?</span>
                                                            </div>
                                                            <i class="bi bi-chevron-down text-muted ms-3 small"></i>
                                                        </button>

                                                        <div
                                                            class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-4 w-100 p-2 mt-2">
                                                            <ul class="list-unstyled mb-0" id="roleSelector">
                                                                <li>
                                                                    <a class="dropdown-item py-2 px-3 rounded-3 d-flex align-items-center mb-1"
                                                                        href="#" data-value="doctor">
                                                                        <div class="me-3 text-navy d-flex align-items-center justify-content-center flex-shrink-0 icon-wrapper"
                                                                            style="width: 30px; height: 30px;">
                                                                            <x-mcr-stethoscope
                                                                                style="width: 100%; height: 100%;" />
                                                                        </div>
                                                                        <div class="text-group">
                                                                            <span
                                                                                class="fw-bold text-navy d-block">Doctores</span>
                                                                            <span class="text-muted small"
                                                                                style="font-size: 0.75rem;">Especialistas
                                                                                médicos</span>
                                                                        </div>
                                                                    </a>
                                                                </li>
                                                                <li>
                                                                    <a class="dropdown-item py-2 px-3 rounded-3 d-flex align-items-center"
                                                                        href="#" data-value="farmacia">
                                                                        <div class="me-3 text-navy d-flex align-items-center justify-content-center flex-shrink-0 icon-wrapper"
                                                                            style="width: 30px; height: 30px;">
                                                                            <x-mcr-pills
                                                                                style="width: 100%; height: 100%;" />
                                                                        </div>
                                                                        <div class="text-group">
                                                                            <span
                                                                                class="fw-bold text-navy d-block">Farmacias</span>
                                                                            <span class="text-muted small"
                                                                                style="font-size: 0.75rem;">Medicamentos e
                                                                                insumos</span>
                                                                        </div>
                                                                    </a>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-12 col-md-auto d-none" id="especialidadGroup">
                                                    <select
                                                        class="form-select form-select-lg rounded-pill border bg-white text-navy fw-bold"
                                                        name="especialidad_id" style="height: 48px; min-width: 200px;">
                                                        <option value="" selected>Todas las especialidades</option>
                                                        @foreach($especialidades ?? [] as $esp)
                                                            <option value="{{ $esp->id }}">{{ $esp->nombre }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="col-12 col-md-auto">
                                                    {{-- Alineación del ícono de buscar --}}
                                                    <button
                                                        class="btn btn-navy btn-lg rounded-pill w-100 px-4 fw-bold search-button d-flex align-items-center justify-content-center"
                                                        type="submit" style="height: 48px;">
                                                        <x-mcl-search class="icon-white me-2 flex-shrink-0"
                                                            style="width: 1.2rem;" /> Buscar
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-center gap-3 position-relative z-1">
                            <a href="/login"
                                class="btn btn-light rounded-pill px-4 py-2 fw-bold text-navy shadow-sm hover-scale">Iniciar
                                Sesión</a>
                            <a href="/register"
                                class="btn btn-outline-light rounded-pill px-4 py-2 fw-bold hover-scale">Crear Cuenta</a>
                        </div>

                    </div>
                </div>
            </div>

            <br><br>

            <div class="d-flex justify-content-center align-items-center mb-4">
                <h2 class="fw-bold text-navy mb-0">Nuestras especialidades</h2>
            </div>

            <div class="d-flex flex-wrap gap-3 mb-5 justify-content-center justify-content-md-start">
                @forelse ($especialidades as $especialidad)
                    <a href="{{ route('specs.show', $especialidad->id) }}" class="text-decoration-none">
                        <div class="bg-white border rounded-pill px-4 py-2 shadow-sm hover-scale d-flex align-items-center">
                            <i class="bi bi-star-fill text-warning me-2" style="font-size: 0.8rem;"></i>
                            <span class="fw-bold custom-text-dark me-2">{{ $especialidad->nombre }}</span>
                            <span class="badge bg-navy-subtle text-navy rounded-circle">
                                {{ $especialidad->doctors->count() }}
                            </span>
                        </div>
                    </a>
                @empty
                    <div class="text-muted small fst-italic">
                        <i class="bi bi-info-circle me-1"></i> Aún no hay especialidades registradas.
                    </div>
                @endforelse
            </div>

            <div id="specialties-container">
                @foreach ($especialidades as $index => $especialidad)
                    <div class="specialty-section {{ $index >= 2 ? 'd-none hidden-specialty' : '' }}">
                        <div class="row mt-5 mb-3">
                            <div class="col-12 d-flex justify-content-between align-items-end">
                                <h4 class="fw-bold custom-text-dark mb-0">{{ $especialidad->nombre }}</h4>
                                <a href="#" class="text-decoration-none text-navy fw-bold small">Ver todos ></a>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="scroll-horizontal px-2">
                                    @foreach ($especialidad->doctors as $doctor)
                                        <div
                                            class="card border-0 shadow-sm rounded-4 hover-scale p-4 d-flex flex-column align-items-center doctor-card-snap">
                                            <div class="position-relative mb-4" style="width: 110px; height: 110px;">
                                                @if($doctor->user->foto)
                                                    <img src="{{ asset('storage/' . $doctor->user->foto) }}"
                                                        alt="Foto de {{ $doctor->user->name }}"
                                                        class="rounded-circle shadow-sm border border-4 border-white w-100 h-100"
                                                        style="object-fit: cover;">
                                                @else
                                                    <div
                                                        class="bg-navy-subtle text-navy rounded-circle d-flex align-items-center justify-content-center shadow-sm border border-4 border-white w-100 h-100">
                                                        <span
                                                            class="fs-1 fw-bold text-uppercase">{{ substr($doctor->user->name, 0, 1) }}</span>
                                                    </div>
                                                @endif
                                                <span
                                                    class="position-absolute bottom-0 end-0 bg-success border border-3 border-white rounded-circle"
                                                    style="width: 22px; height: 22px; margin-bottom: 5px; margin-right: 5px;"></span>
                                            </div>

                                            <h5 class="fw-bold text-dark text-center mb-1 text-truncate w-100">
                                                Dr. {{ $doctor->user->name }}
                                            </h5>

                                            <small class="text-muted mb-4 text-center d-block">
                                                {{ $especialidad->nombre }}
                                            </small>

                                            <a href="{{ route('doctores.show', $doctor->id) }}"
                                                class="btn btn-navy rounded-pill w-100 mt-auto py-2 fw-bold shadow-sm">
                                                Ver Perfil
                                            </a>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                    </div>
                @endforeach
            </div>

            @if(count($especialidades) > 2)
                <div class="row mt-2 mb-5">
                    <div class="col-12 text-center">
                        <button id="toggleSpecialtiesBtn"
                            class="btn btn-outline-navy rounded-pill px-5 py-2 fw-bold shadow-sm transition-all">
                            Ver más especialidades <i class="bi bi-chevron-down ms-2"></i>
                        </button>
                    </div>
                </div>
            @endif

            <div class="row mb-4 mt-5">
                <div class="col-12">
                    <h4 class="fw-bold custom-text-dark">Clínicas y Farmacias en tu zona</h4>
                    <p class="text-muted">Descubre a los profesionales de la salud cerca de ti.</p>
                </div>
            </div>

            <div class="row g-4 mb-5">
                <div class="col-lg-7">
                    <div id="map" class="shadow-sm border"></div>
                </div>
                <div class="col-lg-5 d-flex flex-column" style="height: 450px;">
                    <div class="flex-grow-1" style="overflow-y: auto; overflow-x: hidden; padding-right: 10px;">
                        @forelse ($rutas ?? [] as $usuario)
                            <div class="card border-0 shadow-sm rounded-4 mb-3 hover-scale overflow-hidden"
                                style="cursor: pointer;"
                                onclick="centrar('{{ $usuario->latitud }}', '{{ $usuario->longitud }}')">
                                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center">
                                        @if($usuario->foto)
                                            <img src="{{ asset('storage/' . $usuario->foto) }}" alt="{{ $usuario->name }}"
                                                class="rounded-circle shadow-sm me-3 border border-2 border-white" width="55"
                                                height="55" style="object-fit: cover;">
                                        @else
                                            <div class="bg-navy-subtle text-navy rounded-circle d-flex align-items-center justify-content-center shadow-sm me-3 border border-2 border-white"
                                                style="width: 55px; height: 55px;">
                                                <i
                                                    class="bi {{ $usuario->role == 'doctor' ? 'bi-person-fill' : 'bi-shop' }} fs-4"></i>
                                            </div>
                                        @endif
                                        <div>
                                            <h6 class="fw-bold text-dark mb-0">{{ $usuario->name }}</h6>
                                            <small class="text-muted d-block text-capitalize">{{ $usuario->role }}</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="alert alert-info border-0 shadow-sm rounded-4 text-center p-4">
                                <i class="bi bi-geo-alt fs-1 d-block mb-2"></i>
                                No hay ubicaciones públicas cercanas por el momento.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="row justify-content-center mb-5 pb-5">
                <div class="col-md-8 text-center bg-white p-5 rounded-4 shadow-sm border">
                    <h3 class="fw-bold custom-text-dark mb-3">¿Eres médico o tienes una farmacia?</h3>
                    <p class="text-muted mb-4">Únete a la red de salud más grande. Gestiona tus citas, recibe reseñas y
                        aumenta tu visibilidad.</p>
                    <a href="/register" class="btn btn-navy rounded-pill px-5 py-2 fw-bold">Registrar mi consultorio</a>
                </div>
            </div>

        @endguest

        @auth
            @if (Auth::user()->role == 'admin')
                <div class="row mb-4">
                    <div class="col-12">
                        <h2 class="fw-bold custom-text-dark">Panel de Administración</h2>
                        <p class="text-muted">Bienvenido, {{ Auth::user()->name }} </p>
                    </div>
                </div>

                <div class="row mb-5 justify-content-center">
                    <div class="col-12 col-md-6 col-lg-3 mb-5">
                        <div class="card h-100 border-50 shadow-sm hover-card">
                            <div class="card-body text-center d-flex flex-column justify-content-center align-items-center p-4">
                                <img src="{{ asset('images/doctores.jpg') }}" alt="Doctores"
                                    class="rounded-circle mb-3 shadow-sm" style="width: 80px; height: 80px; object-fit: cover;">
                                <h5 class="card-title fw-bold custom-text-dark">Doctores</h5>
                                <a href="{{ route('doctores.index') }}"
                                    class="btn btn-navy btn-sm stretched-link mt-2 rounded-pill px-4">Entrar</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-3 mb-5">
                        <div class="card h-100 border-50 shadow-sm hover-card">
                            <div class="card-body text-center d-flex flex-column justify-content-center align-items-center p-4">
                                <img src="{{ asset('images/farmacias.jpeg') }}" alt="Farmacias"
                                    class="rounded-circle mb-3 shadow-sm" style="width: 80px; height: 80px; object-fit: cover;">
                                <h5 class="card-title fw-bold custom-text-dark">Farmacias</h5>
                                <a href="{{ route('admin.farmacias.index') }}"
                                    class="btn btn-navy btn-sm stretched-link mt-2 rounded-pill px-4">Entrar</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-3 mb-5">
                        <div class="card h-100 border-50 shadow-sm hover-card">
                            <div class="card-body text-center d-flex flex-column justify-content-center align-items-center p-4">
                                <img src="{{ asset('images/pacientes.jpg') }}" alt="Pacientes"
                                    class="rounded-circle mb-3 shadow-sm" style="width: 80px; height: 80px; object-fit: cover;">
                                <h5 class="card-title fw-bold custom-text-dark">Pacientes</h5>
                                <a href="{{ route('pacientes.index') }}"
                                    class="btn btn-navy btn-sm stretched-link mt-2 rounded-pill px-4">Entrar</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-3 mb-5">
                        <div class="card h-100 border-50 shadow-sm hover-card">
                            <div class="card-body text-center d-flex flex-column justify-content-center align-items-center p-4">
                                <img src="{{ asset('images/reporte.jpg') }}" alt="Reportes"
                                    class="rounded-circle mb-3 shadow-sm" style="width: 80px; height: 80px; object-fit: cover;">
                                <h5 class="card-title fw-bold custom-text-dark">Reportes</h5>
                                <a href="{{ route('admin.reportes.index') }}"
                                    class="btn btn-navy btn-sm stretched-link mt-2 rounded-pill px-4">Entrar</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-3 mb-5">
                        <div class="card h-100 border-50 shadow-sm hover-card">
                            <div class="card-body text-center d-flex flex-column justify-content-center align-items-center p-4">
                                <x-mcr-download
                                    class="rounded-circle mb-2 shadow-sm" style="width: 80px; height: 80px; "/>
                                <h5 class="card-title fw-bold custom-text-dark">Backups</h5>
                                <a href="{{ route('backups.index') }}"
                                    class="btn btn-navy btn-sm stretched-link mt-2 rounded-pill px-4">Entrar</a>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- CHATBOT WIDGET --}}
                <button id="chatToggleBtn"
                    class="btn bg-navy text-white rounded-circle shadow-lg position-fixed d-flex align-items-center justify-content-center hover-scale"
                    style="bottom: 30px; right: 30px; width: 65px; height: 65px; z-index: 1050; transition: transform 0.2s;">
                    <i class="bi bi-robot fs-3"></i>
                </button>

                <div id="chatWidget" class="card shadow-lg position-fixed d-none flex-column fade-in"
                    style="bottom: 110px; right: 30px; width: 380px; border-radius: 20px; z-index: 1050; overflow: hidden; border: 1px solid rgba(0,0,0,0.1);">

                    <div class="card-header d-flex justify-content-between align-items-center p-3 text-white border-bottom-0"
                        style="background-color: #0d2e4e;">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-stars fs-4 me-2 text-warning"></i>
                            <div class="d-flex flex-column">
                                <span class="fw-bold mb-0 lh-1">Gemini AI</span>
                                <small class="opacity-75" style="font-size: 0.75rem;">Asistente BuscaDoc</small>
                            </div>
                        </div>
                        <button id="closeChatBtn" class="btn text-white p-0 m-0 border-0 opacity-75 hover-opacity-100">
                            <i class="bi bi-x-lg fs-5"></i>
                        </button>
                    </div>

                    <div class="card-body p-0 bg-light">
                        <div id="chat-messages" class="p-3" style="height: 400px; overflow-y: auto; overflow-x: hidden;">
                            <div class="d-flex flex-row justify-content-start mb-4">
                                <img src="{{ asset('images/chatbot.png') }}" alt="bot avatar" class="rounded-circle shadow-sm"
                                    style="width: 40px; height: 40px; object-fit: cover;">
                                <div class="p-3 ms-3 bg-white shadow-sm"
                                    style="border-radius: 15px; border-top-left-radius: 0;">
                                    <p class="small mb-0 text-dark">¡Hola, {{ Auth::user()->name }}! Soy Gemini, listo para
                                        integrarme a BuscaDoc. ¿Qué datos de la clínica necesitas consultar hoy?</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer bg-white border-top-0 p-3">
                        <div class="input-group">
                            <input type="text"
                                class="form-control rounded-pill border-secondary border-opacity-25 shadow-none bg-light ps-4"
                                id="chatInput" placeholder="Pregúntale a Gemini..." autocomplete="off">
                            <button id="btnSend"
                                class="btn bg-navy text-white rounded-circle ms-2 shadow-sm d-flex align-items-center justify-content-center"
                                style="width: 45px; height: 45px;">
                                <i class="bi bi-send-fill"></i>
                            </button>
                        </div>
                    </div>
                </div>

            @elseif (Auth::user()->role == 'doctor')
                <div class="row justify-content-center mb-4">
                    <div class="col-lg-8 text-center">
                        <h2 class="fw-bold text-navy mb-1">Panel Médico</h2>
                        <p class="text-muted">Bienvenido, Dr. {{ Auth::user()->name }}. Aquí está tu resumen.</p>
                    </div>
                </div>

                <div class="row g-4">
                    <div class="col-lg-8">
                        <h5 class="fw-bold text-navy mb-3">Accesos Rápidos</h5>
                        <div class="row g-3 mb-5">
                            @if(Auth::user()->doctor->citas == true)
                                <div class="col-md-5">
                                    <a href="{{ route('doctores.citas', Auth::user()->doctor->id) }}" class="text-decoration-none">
                                        <div class="card h-100 border-0 shadow-sm rounded-4 p-4 hover-scale text-center bg-white">
                                            <div class="bg-navy-subtle text-navy rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center"
                                                style="width: 60px; height: 60px;">
                                                <i class="bi bi-calendar-week fs-3"></i>
                                            </div>
                                            <h6 class="fw-bold text-dark mb-0">Gestionar Agenda</h6>
                                        </div>
                                    </a>
                                </div>
                            @endif

                            <div class="col-md-{{ Auth::user()->doctor->citas == true ? '7' : '12' }}">
                                <a href="{{ route('mensajes.index') }}" class="text-decoration-none">
                                    <div class="card h-100 border-0 shadow-sm rounded-4 p-4 hover-scale text-white position-relative overflow-hidden"
                                        style="background: linear-gradient(135deg, #00213D 0%, #0d2e4e 100%);">
                                        {{-- Ícono de fondo marca de agua --}}
                                        <div class="position-absolute"
                                            style="right: -15px; top: -15px; opacity: 0.1; transform: rotate(-15deg);">
                                            <x-mcr-chat-dots style="font-size: 8rem;" /</x></x>>
                                        </div>
                                        <div
                                            class="position-relative z-1 d-flex align-items-center justify-content-between h-100">
                                            <div>
                                                <h5 class="fw-bold mb-1">Centro de Mensajes</h5>
                                                <p class="mb-0 opacity-75 small">Atiende las consultas de tus pacientes</p>
                                            </div>
                                            <div class="bg-white rounded-circle d-flex align-items-center justify-content-center shadow-sm"
                                                style="width: 45px; height: 45px;">
                                                <i class="bi bi-arrow-right text-navy fs-5"></i>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>

                        <h5 class="fw-bold text-navy mb-3">Resumen Reciente</h5>
                        <div class="row g-4">
                            @if(Auth::user()->doctor->citas == true)
                                <div class="col-md-6">
                                    <div class="card h-100 border border-light shadow-sm rounded-4 overflow-hidden">
                                        <div class="card-header bg-white border-0 pt-4 px-4 pb-0 d-flex align-items-center">
                                            <div class="bg-success-subtle text-success rounded-circle d-flex align-items-center justify-content-center me-3"
                                                style="width: 40px; height: 40px;">
                                                <i class="bi bi-person-check-fill"></i>
                                            </div>
                                            <h6 class="fw-bold text-dark mb-0">Siguiente Paciente</h6>
                                        </div>

                                        <div class="card-body px-4 pb-4 pt-3">
                                            @if($proximaCitaDoctor)
                                                <div class="p-3 bg-light rounded-3 border-start border-4 border-success">
                                                    <div class="d-flex align-items-center mb-3">
                                                        <img src="{{ $proximaCitaDoctor->expediente->user->foto ? asset('storage/' . $proximaCitaDoctor->expediente->user->foto) : 'https://ui-avatars.com/api/?name=' . urlencode($proximaCitaDoctor->expediente->user->name) }}"
                                                            class="rounded-circle me-3 shadow-sm" width="45" height="45"
                                                            style="object-fit: cover;">
                                                        <div>
                                                            <span
                                                                class="fw-bold text-dark d-block">{{ $proximaCitaDoctor->expediente->nombre_completo }}</span>
                                                            <small
                                                                class="text-muted">{{ $proximaCitaDoctor->expediente->tipo_sangre ?? 'Paciente' }}</small>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex align-items-center justify-content-between">
                                                        <span class="badge bg-white text-dark border shadow-sm">
                                                            <i class="bi bi-clock me-1 text-navy"></i>
                                                            
                                                            {{ \Carbon\Carbon::parse($proximaCitaDoctor->fecha)->format('d/m/Y') }}, 
                                                             {{ \Carbon\Carbon::parse($proximaCitaDoctor->hora_inicio)->format('g:i A') }}
                                                        </span>
                                                        <span
                                                            class="badge {{ $proximaCitaDoctor->estado == 'pendiente' ? 'bg-warning text-dark' : 'bg-success' }}">
                                                            {{ ucfirst($proximaCitaDoctor->estado) }}
                                                        </span>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="text-center py-4 opacity-50">
                                                    <i class="bi bi-calendar-check fs-1 text-muted"></i>
                                                    <p class="mb-0 small mt-2">No tienes citas próximas.</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <div class="col-md-6">
                                <div class="card h-100 border border-light shadow-sm rounded-4 overflow-hidden hover-scale">
                                    <a href="{{ route('doctores.show', Auth::user()->doctor->id) }}#pills-questions"
                                        class="text-decoration-none stretched-link"></a>
                                    <div class="card-header bg-white border-0 pt-4 px-4 pb-0 d-flex align-items-center">
                                        <div class="bg-info-subtle text-info rounded-circle d-flex align-items-center justify-content-center me-3"
                                            style="width: 40px; height: 40px;">
                                            <i class="bi bi-question-lg"></i>
                                        </div>
                                        <h6 class="fw-bold text-dark mb-0">Última pregunta</h6>
                                    </div>

                                    <div class="card-body px-4 pb-4 pt-3 position-relative">
                                        @if($ultimaQuestion)
                                            <div class="position-relative z-1 pt-2">
                                                <p class="text-muted fst-italic mb-3 small pe-3">
                                                    "{{ Str::limit($ultimaQuestion->contenido, 80) }}"</p>
                                                <div class="d-flex align-items-center border-top pt-3">
                                                    <img src="{{ $ultimaQuestion->autor?->foto ? asset('storage/' . $ultimaQuestion->autor->foto) : 'https://ui-avatars.com/api/?name=' . urlencode($ultimaQuestion->autor?->name ?? 'Anónimo') }}"
                                                        class="rounded-circle me-2 shadow-sm" width="30" height="30"
                                                        style="object-fit: cover;">
                                                    <small
                                                        class="fw-bold text-dark">{{ $ultimaQuestion->autor?->name ?? 'Anónimo' }}</small>
                                                </div>
                                            </div>
                                        @else
                                            <div class="text-center py-4">
                                                <p class="mb-0 small text-muted">Aún no tienes preguntas.</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- COLUMNA LATERAL (PERFIL DOCTOR) --}}
                    <div class="col-lg-4">
                        <div class="card border-0 shadow-sm rounded-4 p-4 sticky-top" style="top: 20px;">
                            <div class="d-flex align-items-center mb-4">
                                <img src="{{ Auth::user()->foto ? asset('storage/' . Auth::user()->foto) : 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) }}"
                                    class="rounded-circle me-3 shadow-sm" width="60" height="60" style="object-fit: cover;">
                                <div>
                                    <h6 class="fw-bold mb-0 text-navy">Dr. {{ Auth::user()->name }}</h6>
                                    <small class="text-muted">
                                        {{ Auth::user()->doctor->especialidades->pluck('nombre')->join(', ') ?: 'Médico General' }}
                                    </small>
                                </div>
                            </div>

                            <div class="border-top pt-3 mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted fw-bold">Costo Promedio De Consulta</small>
                                    <span
                                        class="fs-5 fw-bold text-success">${{ number_format(Auth::user()->doctor->costo, 2) }}</span>
                                </div>
                            </div>

                            <div class="bg-light p-3 rounded-3 mb-4">
                                <small class="text-muted d-block fw-bold mb-1">Horario de Atención</small>
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-clock me-2 text-navy"></i>
                                        {{-- Horario --}}
                                        @php
                                            $hoy = now()->dayOfWeek; // 0 (Dom) a 6 (Sáb)
                                            $horaActual = now()->format('H:i:s');
                                            $disponibilidadHoy = Auth::user()->doctor->disponibilidades->where('dia_semana', $hoy);
                                            $estaAbierto = false;
                                            $rangoHoy = "Cerrado ahora";

                                            foreach($disponibilidadHoy as $bloque) {
                                                if($horaActual >= $bloque->hora_inicio && $horaActual <= $bloque->hora_fin) {
                                                    $estaAbierto = true;
                                                }
                                            }
                                        @endphp

                                        
                                            
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
                            </div>

                            <a href="{{ route('doctores.show', Auth::user()->doctor->id) }}"
                                class="btn btn-outline-navy w-100 rounded-pill fw-bold">
                                Ver mi perfil público
                            </a>
                        </div>
                    </div>
                </div>

            @elseif (Auth::user()->role == 'farmacia')
                <div class="row justify-content-center mb-5">
                    <div class="col-lg-8 text-center">
                        <h2 class="fw-bold text-navy mb-2">Panel de Farmacia</h2>
                        <p class="text-muted mb-4">Administra tu presencia y reputación.</p>
                    </div>
                </div>

                <div class="row g-4">
                    <div class="col-lg-8">
                        {{-- Estadísticas Rápidas --}}
                        <div class="card border-0 shadow rounded-4 overflow-hidden mb-4 bg-navy text-white">
                            <div class="card-body p-4 d-flex align-items-center justify-content-between">
                                <div>
                                    <h5 class="fw-bold mb-1">Tu Calificación</h5>
                                    <div class="d-flex align-items-center mt-2">
                                        <span
                                            class="display-4 fw-bold me-3">{{ number_format(Auth::user()->farmacia->promedio_calificacion, 1) }}</span>
                                        <div class="text-warning fs-5">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <i
                                                    class="bi {{ $i <= round(Auth::user()->farmacia->promedio_calificacion) ? 'bi-star-fill' : 'bi-star' }}"></i>
                                            @endfor
                                        </div>
                                    </div>
                                    <small class="opacity-75">Basado en tus reseñas recibidas</small>
                                </div>
                                <div class="d-none d-md-block opacity-25">
                                    <i class="bi bi-trophy-fill" style="font-size: 5rem;"></i>
                                </div>
                            </div>
                        </div>

                        <h5 class="fw-bold text-navy mb-3">Acciones</h5>
                        <div class="row g-3">
                            {{-- Tarjeta 1: Ver Perfil --}}
                            <div class="col-md-6">
                                <a href="{{ route('farmacias.detalle', Auth::user()->farmacia->id) }}"
                                    class="text-decoration-none">
                                    <div class="card h-100 border-0 shadow-sm rounded-4 p-4 hover-scale text-center">
                                        <div class="bg-navy-subtle text-navy rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center"
                                            style="width: 70px; height: 70px;">
                                            <i class="bi bi-shop fs-2"></i>
                                        </div>
                                        <h5 class="fw-bold text-dark">Ver mi Farmacia</h5>
                                        <small class="text-muted">Cómo te ven los clientes</small>
                                    </div>
                                </a>
                            </div>

                            {{-- Tarjeta 2: Reseñas --}}
                            <div class="col-md-6">
                                <div class="card h-100 border-0 shadow-sm rounded-4 p-4 hover-scale text-center">
                                    <div class="bg-warning-subtle text-warning rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center"
                                        style="width: 70px; height: 70px;">
                                        <i class="bi bi-chat-quote-fill fs-2"></i>
                                    </div>
                                    <h5 class="fw-bold text-dark">{{ Auth::user()->farmacia->reviews->count() }} Reseñas</h5>
                                    <small class="text-muted">Total de opiniones de clientes</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="card border-0 shadow-sm rounded-4 p-4 sticky-top" style="top: 20px;">
                            <div class="d-flex align-items-center mb-4">
                                <img src="{{ Auth::user()->foto ? asset('storage/' . Auth::user()->foto) : 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) }}"
                                    class="rounded-circle me-3 shadow-sm" width="60" height="60" style="object-fit: cover;">
                                <div>
                                    <h6 class="fw-bold mb-0 text-navy">{{ Auth::user()->farmacia->nom_farmacia }}</h6>
                                    <small class="text-muted">Propietario: {{ Auth::user()->name }}</small>
                                </div>
                            </div>

                            <div class="border-bottom pb-3 mb-3">
                                <span class="text-muted small fw-bold d-block mb-1">Horario de Atención</span>
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-clock me-2 text-navy"></i>
                                    <span class="fw-bold text-dark">
                                        {{ \Carbon\Carbon::parse(Auth::user()->farmacia->horario_entrada)->format('h:i A') }} -
                                        {{ \Carbon\Carbon::parse(Auth::user()->farmacia->horario_salida)->format('h:i A') }}
                                    </span>
                                </div>
                            </div>

                            <div class="border-bottom pb-3 mb-3">
                                <span class="text-muted small fw-bold d-block mb-1">Teléfono Público</span>
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-telephone-fill me-2 text-navy"></i>
                                    <span>{{ Auth::user()->farmacia->telefono }}</span>
                                </div>
                            </div>

                            <div>
                                <span class="text-muted small fw-bold d-block mb-1">RFC</span>
                                <div class="bg-light p-2 rounded border text-center font-monospace small">
                                    {{ Auth::user()->farmacia->rfc ?? 'No registrado' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            @elseif (Auth::user()->role == 'paciente')
                <div class="row justify-content-center mb-4">
                    <div class="col-12 col-lg-10 col-xl-10 text-center">
                        <h2 class="fw-bold text-navy mb-2">Bienvenido a BuscaDoc, {{ Auth::user()->name }}</h2>
                        <p class="text-muted mb-4">Encuentra lo que buscas, aquí mismo.</p>

                        {{-- EL BUSCADOR GIGANTE --}}
                        <div class="row justify-content-center position-relative z-3 mb-5">
                            <div class="col-12 col-md-11 col-lg-12">
                                <div class="card border-0 shadow-sm rounded-5 search-form-card" style="background-color: #f8f9fa;">
                                    <div class="card-body p-3 p-md-2">
                                        <form action="{{ route('global.search') }}" method="GET" class="search-form-global" id="searchForm">
                                            <input type="hidden" name="type" id="searchTypeInput" value="">
                                            <div class="row g-2 align-items-center">
                                                <div class="col-12 col-md">
                                                    <div class="input-group input-group-lg search-input-group bg-white rounded-pill overflow-hidden border">
                                                        <span class="input-group-text bg-white border-0 ps-4"><i class="bi bi-search text-muted"></i></span>
                                                        <input type="text" name="search" class="form-control border-0 shadow-none ps-2" placeholder="Nombre, clínica o síntoma...">
                                                    </div>
                                                </div>

                                                <div class="col-12 col-md-auto">
                                                    <div class="dropdown custom-user-role-dropdown w-100" id="searchDropdownGroup">
                                                        <button class="btn btn-lg bg-white border rounded-pill text-start d-flex align-items-center justify-content-between w-100 px-4" style="height: 48px; min-width: 180px;" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                            <div class="d-flex align-items-center">
                                                                <div id="selectedRoleIcon" class="d-flex align-items-center justify-content-center me-2 text-muted flex-shrink-0" style="width: 24px;">
                                                                    <i class="bi bi-funnel fs-5"></i>
                                                                </div>
                                                                <span class="dropdown-label text-navy fw-bold" id="selectedRoleLabel">¿Qué buscas?</span>
                                                            </div>
                                                            <i class="bi bi-chevron-down text-muted ms-3 small"></i>
                                                        </button>

                                                        <div class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-4 w-100 p-2 mt-2">
                                                            <ul class="list-unstyled mb-0" id="roleSelector">
                                                                <li>
                                                                    <a class="dropdown-item py-2 px-3 rounded-3 d-flex align-items-center mb-1" href="#" data-value="doctor">
                                                                        <div class="me-3 text-navy d-flex align-items-center justify-content-center flex-shrink-0 icon-wrapper" style="width: 30px; height: 30px;">
                                                                            <x-mcr-stethoscope style="width: 100%; height: 100%;" />
                                                                        </div>
                                                                        <div class="text-group">
                                                                            <span class="fw-bold text-navy d-block">Doctores</span>
                                                                            <span class="text-muted small" style="font-size: 0.75rem;">Especialistas médicos</span>
                                                                        </div>
                                                                    </a>
                                                                </li>
                                                                <li>
                                                                    <a class="dropdown-item py-2 px-3 rounded-3 d-flex align-items-center" href="#" data-value="farmacia">
                                                                        <div class="me-3 text-navy d-flex align-items-center justify-content-center flex-shrink-0 icon-wrapper" style="width: 30px; height: 30px;">
                                                                            <x-mcr-pills style="width: 100%; height: 100%;" />
                                                                        </div>
                                                                        <div class="text-group">
                                                                            <span class="fw-bold text-navy d-block">Farmacias</span>
                                                                            <span class="text-muted small" style="font-size: 0.75rem;">Medicamentos e insumos</span>
                                                                        </div>
                                                                    </a>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-12 col-md-auto d-none" id="especialidadGroup">
                                                    <select class="form-select form-select-lg rounded-pill border bg-white text-navy fw-bold px-4" name="especialidad_id" style="height: 48px; min-width: 220px; cursor: pointer;">
                                                        <option value="" selected>Todas las especialidades</option>
                                                        @foreach($especialidades ?? [] as $esp)
                                                            <option value="{{ $esp->id }}">{{ $esp->nombre }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="col-12 col-md-auto">
                                                    <button class="btn btn-navy btn-lg rounded-pill w-100 px-5 fw-bold search-button d-flex align-items-center justify-content-center" type="submit" style="height: 48px;">
                                                        <x-mcl-search class="icon-white me-2 flex-shrink-0" style="width: 1.2rem;"/> Buscar
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-4">
                    <div class="col-lg-8">
                        {{-- WIDGET DE MENSAJES PREMIUM (PACIENTE) --}}
                        <a href="{{ route('mensajes.index') }}" class="text-decoration-none mb-4 d-block">
                            <div class="card border-0 shadow-sm rounded-4 p-4 hover-scale text-white position-relative overflow-hidden" 
                                 style="background: linear-gradient(135deg, #0d2e4e 0%, #00213D 100%);">
                                <div class="position-absolute" style="right: -10px; top: -25px; opacity: 0.1; transform: scale(1.5);">
                                    <x-mcf-chat-dots style="width: 8rem; height: 8rem;" />
                                </div>
                                <div class="position-relative z-1 d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                                            <x-mcf-chat-dots class="fs-4" />
                                        </div>
                                        <div>
                                            <h5 class="fw-bold mb-1">Mis Chats</h5>
                                            <p class="mb-0 opacity-75 small">Continúa la conversación con tus médicos</p>
                                        </div>
                                    </div>
                                    <i class="bi bi-arrow-right fs-4 opacity-75"></i>
                                </div>
                            </div>
                        </a>

                        @if($proximaCita)
                            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-5">
                                <div class="card-body p-0">
                                    <div class="row g-0">
                                        <div class="col-12 bg-navy text-white p-3 d-flex align-items-center justify-content-between d-md-none">
                                            <span class="fw-bold"><i class="bi bi-calendar-event me-2"></i>Tu próxima cita</span>
                                        </div>
                                        <div class="col-md-2 bg-light d-flex flex-column align-items-center justify-content-center py-4 border-end">
                                            <span
                                                class="text-uppercase small fw-bold text-muted">{{ $proximaCita->fecha->format('M') }}</span>
                                            <span
                                                class="display-4 fw-bold text-navy lh-1">{{ $proximaCita->fecha->format('d') }}</span>
                                            <span class="small text-muted">{{ $proximaCita->fecha->format('l') }}</span>
                                        </div>
                                        <div class="col-md-7 p-4 d-flex align-items-center">
                                            <div class="d-flex align-items-center">
                                                <img src="{{ $proximaCita->doctor->user->foto ? asset('storage/' . $proximaCita->doctor->user->foto) : 'https://ui-avatars.com/api/?name=' . urlencode($proximaCita->doctor->user->name) }}"
                                                    class="rounded-circle shadow-sm me-3" width="65" height="65" style="object-fit: cover;">
                                                <div>
                                                    <small class="text-primary fw-bold text-uppercase" style="font-size: 0.7rem; letter-spacing: 1px;">Próxima Consulta</small>
                                                    <h5 class="fw-bold text-navy mb-1">Dr. {{ $proximaCita->doctor->user->name }}</h5>
                                                    <div class="d-flex align-items-center text-muted small">
                                                        <i class="bi bi-clock-fill me-1 text-warning"></i>
                                                        <span class="fw-bold text-dark">{{ \Carbon\Carbon::parse($proximaCita->hora_inicio)->format('h:i A') }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 bg-white p-4 d-flex flex-column justify-content-center align-items-center border-start">
                                            <span class="badge {{ $proximaCita->estado == 'pendiente' ? 'bg-warning text-dark' : 'bg-success' }} rounded-pill px-3 mb-3">
                                                {{ $proximaCita->estado == 'confirmada' ? 'Confirmada' : 'Pendiente' }}
                                            </span>
                                            <a href="{{ route('pacientes.citas') }}" class="btn btn-outline-navy rounded-pill btn-sm px-4">Ver mis citas</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="card border-0 shadow-sm rounded-4 mb-5 p-4 text-center bg-white hover-scale">
                                <div class="py-3">
                                    <div class="mb-3"><x-mcr-calendar class="h-15 w-15 text-muted opacity-25" style="font-size: 3rem;" /></div>
                                    <h5 class="fw-bold text-navy">No tienes citas próximas</h5>
                                    <p class="text-muted small">¿Te sientes mal o necesitas un chequeo?</p>
                                </div>
                            </div>
                        @endif

                        {{-- SECCIÓN DE ESPECIALIDADES IMPORTADA DE GUEST --}}
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="fw-bold custom-text-dark mb-0">Nuestras especialidades</h4>
                        </div>

                        <div class="d-flex flex-wrap gap-2 mb-4">
                            @forelse ($especialidades as $especialidad)
                                <a href="{{ route('specs.show', $especialidad->id) }}" class="text-decoration-none">
                                    <div class="bg-white border rounded-pill px-3 py-2 shadow-sm hover-scale d-flex align-items-center">
                                        <i class="bi bi-star-fill text-warning me-2" style="font-size: 0.8rem;"></i>
                                        <span class="fw-bold custom-text-dark me-2 small">{{ $especialidad->nombre }}</span>
                                        <span class="badge bg-navy-subtle text-navy rounded-circle" style="font-size: 0.7rem;">
                                            {{ $especialidad->doctors->count() }}
                                        </span>
                                    </div>
                                </a>
                            @empty
                                <div class="text-muted small fst-italic">Aún no hay especialidades registradas.</div>
                            @endforelse
                        </div>

                        {{-- CONTENEDOR CON ID EXACTO PARA QUE EL JS LO DETECTE --}}
                        <div id="specialties-container">
                            @php $contadorEspecialidades = 0; @endphp
                            
                            @foreach ($especialidades as $especialidad)
                                @if($especialidad->doctors->count() > 0)
                                    {{-- Se ocultan a partir de la 3ra especialidad válida --}}
                                    <div class="specialty-section-patient mb-4 {{ $contadorEspecialidades >= 2 ? 'd-none hidden-specialty' : '' }}">
                                        
                                        <div class="d-flex justify-content-between align-items-end mb-2 px-2">
                                            <h5 class="fw-bold custom-text-dark mb-0">{{ $especialidad->nombre }}</h5>
                                            <a href="{{ route('specs.show', $especialidad->id) }}"
                                                class="text-decoration-none text-navy fw-bold" style="font-size: 0.85rem;">Ver todos ></a>
                                        </div>

                                        {{-- CARRUSEL MODERNO --}}
                                        <div class="scroll-horizontal">
                                            @foreach ($especialidad->doctors as $doctor)
                                                <div class="card border-0 shadow-sm rounded-4 hover-scale p-3 d-flex flex-column align-items-center doctor-card-snap bg-white">
                                                    
                                                    <div class="position-relative mb-3 mt-2" style="width: 85px; height: 85px;">
                                                        @if($doctor->user->foto)
                                                            <img src="{{ asset('storage/' . $doctor->user->foto) }}"
                                                                alt="Foto de {{ $doctor->user->name }}"
                                                                class="rounded-circle shadow-sm border border-3 border-white w-100 h-100"
                                                                style="object-fit: cover;">
                                                        @else
                                                            <div class="bg-navy-subtle text-navy rounded-circle d-flex align-items-center justify-content-center shadow-sm border border-3 border-white w-100 h-100">
                                                                <span class="fs-3 fw-bold text-uppercase">{{ substr($doctor->user->name, 0, 1) }}</span>
                                                            </div>
                                                        @endif
                                                        <span class="position-absolute bottom-0 end-0 bg-success border border-2 border-white rounded-circle"
                                                            style="width: 18px; height: 18px; margin-bottom: 4px; margin-right: 4px;"></span>
                                                    </div>

                                                    <h6 class="fw-bold text-dark text-center mb-1 text-truncate w-100">Dr. {{ $doctor->user->name }}</h6>
                                                    <small class="text-muted mb-4 text-center d-block" style="font-size: 0.75rem;">{{ $especialidad->nombre }}</small>

                                                    <a href="{{ route('doctores.show', $doctor->id) }}"
                                                        class="btn btn-navy rounded-pill w-100 mt-auto py-2 fw-bold shadow-sm"
                                                        style="font-size: 0.85rem;">
                                                        Ver Perfil
                                                    </a>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    @php $contadorEspecialidades++; @endphp
                                @endif
                            @endforeach
                        </div>

                        {{-- BOTÓN "VER MÁS" (Aparece solo si hay más de 2 especialidades válidas) --}}
                        @if($contadorEspecialidades > 2)
                            <div class="row mt-2 mb-5">
                                <div class="col-12 text-center">
                                    <button id="toggleSpecialtiesBtn" class="btn btn-outline-navy rounded-pill px-5 py-2 fw-bold shadow-sm transition-all">
                                        Ver más especialidades <i class="bi bi-chevron-down ms-2"></i>
                                    </button>
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- COLUMNA LATERAL (PERFIL PACIENTE) --}}
                    <div class="col-lg-4">
                        <div class="card border-0 shadow-sm rounded-4 p-4 sticky-top" style="top: 20px; z-index: 1;">
                            <div class="d-flex align-items-center mb-4">
                                <img src="{{ Auth::user()->foto ? asset('storage/' . Auth::user()->foto) : 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) }}"
                                    class="rounded-circle me-3 shadow-sm" width="50" height="50" style="object-fit: cover;">
                                <div>
                                    <h6 class="fw-bold mb-0 text-navy">Mi Ficha Médica</h6>
                                    <a href="{{ route('users.show', Auth::user()->id) }}" class="small text-muted text-decoration-none">Ver perfil completo ></a>
                                </div>
                            </div>

                            @php
                                // Obtenemos el expediente marcado como 'Propio' para este paciente
                                $expedientePropio = Auth::user()->expedientes()->where('parentesco', 'Expediente Propio')->first();
                            @endphp


                            @if($expedientePropio)
                                <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                                    <span class="text-muted small fw-bold">Mi tipo de sangre</span>
                                    <span class="fw-bold text-danger bg-danger-subtle px-3 py-1 rounded-pill">{{ $expedientePropio->tipo_sangre ?? 'No especificado' }}</span>
                                </div>
                                <div class="mb-3">
                                    <span class="text-muted small fw-bold d-block mb-1">Mis alergias</span>
                                    <span class="fw-medium text-dark small bg-light p-2 rounded d-block border">{{ Str::limit($expedientePropio->alergias, 40) }}</span>
                                </div>
                                <div class="mb-3">
                                    <span class="text-muted small fw-bold d-block mb-1">Padecimientos Crónicos</span>
                                    <div class="d-flex align-items-center text-navy fw-bold bg-light p-2 rounded border">
                                        <i class="bi bi-heart-pulse-fill text-info"></i>
                                        {{ Str::limit($expedientePropio->padecimientos_cronicos, 40) }}
                                    </div>
                                </div>
                            @else
                                <div class="alert alert-warning small border-0 rounded-3">
                                    <i class="bi bi-exclamation-circle-fill me-1"></i> Completa tu perfil médico para emergencias.
                                    <a href="{{ route('expedientes.create') }}" class="btn btn-navy btn-sm rounded-pill">Crear Ficha</a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        @endauth
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const chatToggleBtn = document.getElementById('chatToggleBtn');
                const closeChatBtn = document.getElementById('closeChatBtn');
                const chatWidget = document.getElementById('chatWidget');

                if (!chatWidget) return;

                function toggleChat() {
                    if (chatWidget.classList.contains('d-none')) {
                        chatWidget.classList.remove('d-none');
                        chatWidget.classList.add('d-flex');
                        setTimeout(() => document.getElementById('chatInput').focus(), 100);
                    } else {
                        chatWidget.classList.add('d-none');
                        chatWidget.classList.remove('d-flex');
                    }
                }

                chatToggleBtn.addEventListener('click', toggleChat);
                closeChatBtn.addEventListener('click', toggleChat);

                document.addEventListener('keydown', function (event) {
                    if (event.key === "Escape" && !chatWidget.classList.contains('d-none')) {
                        toggleChat();
                    }
                });

                const btnSend = document.getElementById('btnSend');
                const inputArea = document.getElementById('chatInput');
                const chatMessages = document.getElementById('chat-messages');

                inputArea.addEventListener('keypress', function (e) {
                    if (e.key === 'Enter' && !e.shiftKey) {
                        e.preventDefault();
                        btnSend.click();
                    }
                });

                btnSend.addEventListener('click', async function () {
                    const message = inputArea.value.trim();
                    if (!message) return;

                    inputArea.value = '';
                    appendUserMessage(message);

                    try {
                        btnSend.disabled = true;
                        btnSend.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
                        const loadingId = appendLoadingIndicator();

                        const response = await fetch('/chatbot/send', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({ message: message })
                        });

                        removeLoadingIndicator(loadingId);
                        const data = await response.json();

                        if (response.ok) {
                            appendBotMessage(data.reply);
                        } else {
                            appendBotMessage("Hubo un error de conexión con el servidor.");
                        }

                    } catch (error) {
                        console.error("Error:", error);
                        const loader = document.querySelector('.typing-indicator-container');
                        if (loader) loader.remove();
                        appendBotMessage("No se pudo contactar al servidor.");
                    } finally {
                        btnSend.disabled = false;
                        btnSend.innerHTML = '<i class="bi bi-send-fill"></i>';
                        inputArea.focus();
                    }
                });

                function appendUserMessage(text) {
                    const html = `
                                    <div class="d-flex flex-row justify-content-end mb-4 fade-in">
                                        <div class="p-3 me-3 bg-navy text-white shadow-sm" style="border-radius: 15px; border-top-right-radius: 0;">
                                            <p class="small mb-0">${text}</p>
                                        </div>
                                        <img src="{{ Auth::check() && Auth::user()->foto ? asset('storage/' . Auth::user()->foto) : 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name ?? 'U') }}" 
                                            alt="user avatar" class="rounded-circle shadow-sm" style="width: 40px; height: 40px; object-fit: cover;">
                                    </div>
                                `;
                    chatMessages.insertAdjacentHTML('beforeend', html);
                    scrollToBottom();
                }

                function appendBotMessage(text) {
                    const html = `
                                    <div class="d-flex flex-row justify-content-start mb-4 fade-in">
                                        <img src="{{ asset('images/chatbot.png') }}" alt="bot avatar" class="rounded-circle shadow-sm" style="width: 40px; height: 40px; object-fit: cover;">
                                        <div class="p-3 ms-3 bg-white border border-light shadow-sm" style="border-radius: 15px; border-top-left-radius: 0;">
                                            <div class="small mb-0 text-dark chatbot-reply">${text}</div>
                                        </div>
                                    </div>
                                `;
                    chatMessages.insertAdjacentHTML('beforeend', html);
                    scrollToBottom();
                }

                function appendLoadingIndicator() {
                    const id = 'loader-' + Date.now();
                    const html = `
                                    <div id="${id}" class="d-flex flex-row justify-content-start mb-4 fade-in typing-indicator-container">
                                        <img src="{{ asset('images/chatbot.png') }}" alt="bot avatar" class="rounded-circle shadow-sm opacity-75" style="width: 40px; height: 40px; object-fit: cover;">
                                        <div class="p-3 ms-3 bg-white border border-light shadow-sm d-flex align-items-center" style="border-radius: 15px; border-top-left-radius: 0; min-height: 40px;">
                                            <div class="spinner-grow spinner-grow-sm text-secondary" role="status" style="width: 0.8rem; height: 0.8rem;"></div>
                                            <div class="spinner-grow spinner-grow-sm text-secondary mx-1" role="status" style="width: 0.8rem; height: 0.8rem; animation-delay: 0.2s"></div>
                                            <div class="spinner-grow spinner-grow-sm text-secondary" role="status" style="width: 0.8rem; height: 0.8rem; animation-delay: 0.4s"></div>
                                        </div>
                                    </div>
                                `;
                    chatMessages.insertAdjacentHTML('beforeend', html);
                    scrollToBottom();
                    return id;
                }

                function removeLoadingIndicator(id) {
                    const element = document.getElementById(id);
                    if (element) element.remove();
                }

                function scrollToBottom() {
                    chatMessages.scrollTop = chatMessages.scrollHeight;
                }
            });

            const ubicaciones = @json($rutas ?? []);

            let map, infoWindow;
            let markers = [];

            async function initMap() {
                if (!document.getElementById("map")) return;

                const { Map } = await google.maps.importLibrary("maps");
                const { AdvancedMarkerElement } = await google.maps.importLibrary("marker");

                map = new Map(document.getElementById("map"), {
                    center: { lat: 16.9084, lng: -92.0977 },
                    zoom: 13,
                    mapId: "MAPA_BUSCADOC_ID"
                });

                infoWindow = new google.maps.InfoWindow();

                ubicaciones.forEach(usuario => {
                    const lat = parseFloat(usuario.latitud);
                    const lng = parseFloat(usuario.longitud);

                    if (!isNaN(lat) && !isNaN(lng)) {
                        const marker = new AdvancedMarkerElement({
                            map: map,
                            position: { lat: lat, lng: lng },
                            title: usuario.name
                        });

                        marker.addListener('click', () => {
                            const photoUrl = usuario.foto
                                ? '/storage/' + usuario.foto
                                : 'https://ui-avatars.com/api/?name=' + encodeURIComponent(usuario.name);

                            const contentString = `
                                            <div style="text-align: center; padding: 5px; min-width: 120px;">
                                                <img src="${photoUrl}" alt="${usuario.name}" 
                                                     style="width: 50px; height: 50px; object-fit: cover; border-radius: 50%; box-shadow: 0 2px 5px rgba(0,0,0,0.2); margin-bottom: 8px;">
                                                <h6 style="margin: 0; color: #0d2e4e; font-weight: bold; font-family: sans-serif;">${usuario.name}</h6>
                                                <small style="color: #6c757d; text-transform: capitalize; font-family: sans-serif;">${usuario.role}</small>
                                            </div>
                                        `;

                            infoWindow.setContent(contentString);
                            infoWindow.open({
                                anchor: marker,
                                map: map
                            });
                        });
                        markers.push(marker);
                    }
                });
            }

            function handleLocationError(browserHasGeolocation, infoWindow, pos) {
                infoWindow.setPosition(pos);
                infoWindow.setContent(
                    browserHasGeolocation
                        ? "Error: El servicio de ubicación falló o fue denegado."
                        : "Error: Tu navegador no soporta geolocalización."
                );
                infoWindow.open(map);
            }

            function centrar(latitud, longitud) {
                if (map) {
                    map.setCenter({ lat: parseFloat(latitud), lng: parseFloat(longitud) });
                    map.setZoom(18);
                }
            }
            window.initMap = initMap;


            const toggleSpecialtiesBtn = document.getElementById('toggleSpecialtiesBtn');
            const hiddenSpecialties = document.querySelectorAll('.hidden-specialty');
            let specialtiesExpanded = false;

            if (toggleSpecialtiesBtn) {
                toggleSpecialtiesBtn.addEventListener('click', function () {
                    specialtiesExpanded = !specialtiesExpanded;

                    hiddenSpecialties.forEach(section => {
                        if (specialtiesExpanded) {
                            section.classList.remove('d-none');
                            section.classList.add('fade-in');
                        } else {
                            section.classList.add('d-none');
                            section.classList.remove('fade-in');
                        }
                    });

                    if (specialtiesExpanded) {
                        toggleSpecialtiesBtn.innerHTML = 'Ocultar especialidades <i class="bi bi-chevron-up ms-2"></i>';
                    } else {
                        toggleSpecialtiesBtn.innerHTML = 'Ver más especialidades <i class="bi bi-chevron-down ms-2"></i>';
                        document.getElementById('specialties-container').scrollIntoView({ behavior: 'smooth' });
                    }
                });
            }

            document.addEventListener("DOMContentLoaded", function () {
                const roleSelectorLinks = document.querySelectorAll('#roleSelector a');
                const searchTypeInput = document.getElementById('searchTypeInput');
                const selectedRoleLabel = document.getElementById('selectedRoleLabel');
                const selectedRoleIcon = document.getElementById('selectedRoleIcon');
                const especialidadGroup = document.getElementById('especialidadGroup');

                if (roleSelectorLinks.length > 0) {
                    roleSelectorLinks.forEach(link => {
                        link.addEventListener('click', function (e) {
                            e.preventDefault();

                            const target = e.currentTarget;
                            const selectedValue = target.getAttribute('data-value');
                            const labelText = target.querySelector('.fw-bold').innerText;
                            const iconHTML = target.querySelector('.icon-wrapper').innerHTML;

                            searchTypeInput.value = selectedValue;
                            selectedRoleLabel.innerText = labelText;

                            selectedRoleIcon.innerHTML = iconHTML;
                            selectedRoleIcon.classList.remove('text-muted');
                            selectedRoleIcon.classList.add('text-navy');

                            if (selectedValue === 'doctor') {
                                especialidadGroup.classList.remove('d-none');
                                especialidadGroup.classList.add('d-block');
                            } else {
                                especialidadGroup.classList.remove('d-block');
                                especialidadGroup.classList.add('d-none');
                                especialidadGroup.value = '';
                            }
                        });
                    });
                }

                const toggleSpecialtiesBtn = document.getElementById('toggleSpecialtiesBtn');
                const hiddenSpecialties = document.querySelectorAll('.hidden-specialty');
                let specialtiesExpanded = false;

                if (toggleSpecialtiesBtn) {
                    toggleSpecialtiesBtn.addEventListener('click', function () {
                        specialtiesExpanded = !specialtiesExpanded;
                        hiddenSpecialties.forEach(section => {
                            if (specialtiesExpanded) {
                                section.classList.remove('d-none');
                                section.classList.add('fade-in');
                            } else {
                                section.classList.add('d-none');
                                section.classList.remove('fade-in');
                            }
                        });

                        if (specialtiesExpanded) {
                            toggleSpecialtiesBtn.innerHTML = 'Ocultar especialidades <i class="bi bi-chevron-up ms-2"></i>';
                        } else {
                            toggleSpecialtiesBtn.innerHTML = 'Ver más especialidades <i class="bi bi-chevron-down ms-2"></i>';
                            document.getElementById('specialties-container').scrollIntoView({ behavior: 'smooth' });
                        }
                    });
                }
            });

        </script>

        <script async defer src="https://maps.googleapis.com/maps/api/js?key={{ env('API_KEY') }}&callback=initMap&v=beta">
        </script>
    @endpush
</x-layout>