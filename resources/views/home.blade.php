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

            .bg-primary-subtle {
                background-color: #e0f2fe !important;
            }

            .bg-success-subtle {
                background-color: #dcfce7 !important;
            }

            .bg-info-subtle {
                background-color: #e0f7fa !important;
            }

            .bg-danger-subtle {
                background-color: #fee2e2 !important;
            }

            .bg-warning-subtle {
                background-color: #fef3c7 !important;
            }

            .bg-secondary-subtle {
                background-color: #f1f5f9 !important;
            }

            .hover-scale:hover {
                transform: translateY(-5px);
                box-shadow: 0 15px 30px rgba(13, 46, 78, 0.1) !important;
            }

            .transition-all {
                transition: all 0.3s ease;
            }

            .rounded-5 {
                border-radius: 1.5rem !important;
            }

            .bg-navy-subtle {
                background-color: #eef2f6 !important;
                color: #00213D !important;
            }

            .btn-apk {
                background-color: #3DDC84;
                color: #00213D !important;
                border: none;
            }

            .btn-apk:hover {
                background-color: #32b56c;
                transform: scale(1.05);
            }

            @media (max-width: 767px) {
                .hero-guest {
                    padding: 3rem 1rem !important;
                }

                .hero-guest h1 {
                    font-size: 2rem !important;
                }

                .search-form-card {
                    border-radius: 24px !important;
                    background-color: #ffffff !important;
                    /* Más limpio en móvil */
                }

                /* Darle un poco más de altura a los inputs en móvil para que sea fácil tocarlos */
                .search-input-group,
                #searchDropdownGroup button,
                .search-button {
                    height: 56px !important;
                }
            }

            custom-user-role-dropdown .dropdown-menu {
                background-color: #ffffff !important;
                z-index: 1050 !important;
                box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2) !important;
            }

            .custom-user-role-dropdown .dropdown-item:hover {
                background-color: #f8fafc !important;
            }

            .heart-animated {
                opacity: 0.05;
                transition: transform 0.3s ease;
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

                                            <div class="row g-3 align-items-center">
                                                <div class="col-12 col-md">
                                                    <div
                                                        class="input-group input-group-lg search-input-group bg-white rounded-pill overflow-hidden border">
                                                        <span class="input-group-text bg-white border-0 ps-4">
                                                            <i class="bi bi-search text-muted"></i>
                                                        </span>
                                                        <input type="text" name="search"
                                                            class="form-control border-0 shadow-none ps-2"
                                                            placeholder="Nombre del Doctor o Farmacia">
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
                                                            class="dropdown-menu dropdown-menu-end bg-white shadow-sm border-0 rounded-4 w-100 p-2 mt-2">
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

                        <div class="d-flex flex-wrap justify-content-center gap-3 position-relative z-1">
                            <a href="/login"
                                class="btn btn-light rounded-pill px-4 py-2 fw-bold text-navy shadow-sm hover-scale">Iniciar
                                Sesión</a>
                            <a href="/register"
                                class="btn btn-outline-light rounded-pill px-4 py-2 fw-bold hover-scale">Crear Cuenta</a>

                            {{--<a href="{{ asset('descargas/buscadoc.apk') }}"
                                class="btn btn-apk rounded-pill px-4 py-2 fw-bold shadow-sm hover-scale d-flex align-items-center">
                                <i class="bi bi-android2 me-2 fs-5"></i> Descargar App --}}
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div x-data="{ mostrarBanner: true }" x-show="mostrarBanner" x-transition.opacity
                class="alert bg-navy text-white border-0 shadow-sm rounded-4 p-3 mb-4 d-flex align-items-center justify-content-between" id="banner-apk-descarga">
                
                <div class="d-flex align-items-center flex-grow-1">
                    <i class="bi bi-phone-vibrate fs-2 me-3 d-none d-md-block text-white opacity-75"></i>
                    <div class="text-start">
                        <h6 class="fw-bold mb-1">¿Eres un Doctor o un Paciente?</h6>
                        <p class="mb-0 small opacity-75 lh-sm">¡Lleva BuscaDoc en tu bolsillo! Citas más rápidas y notificaciones en tiempo real.</p>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-2 gap-md-3">
                    <a href="{{ asset('descargas/buscadoc_v1.apk') }}" class="btn btn-sm rounded-pill fw-bold text-nowrap px-4 py-2 hover-scale" style="background-color: #3DDC84; color: #00213D;">
                        <i class="bi bi-android2 me-1"></i> Instalar
                    </a>
                    <button type="button" onclick="document.getElementById('banner-apk-descarga').remove()" class="btn-close btn-close-white" style="cursor: pointer;" aria-label="Cerrar"></button>
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
                <div class="row mb-5">
                    <div class="col-12">
                        <div class="d-flex align-items-center">
                            <div class="bg-navy p-3 rounded-4 shadow-sm me-3">
                                <x-mcr-shield class="icon-white" style="width: 2rem; height: 2rem;" />
                            </div>
                            <div>
                                <h2 class="fw-bold text-navy mb-0">Panel de Control Maestro</h2>
                                <p class="text-muted mb-0">Gestión global de la plataforma BuscaDoc | Bienvenido, <span
                                        class="fw-bold text-dark">{{ Auth::user()->name }}</span></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-4 mb-5 justify-content-center">
                    <div class="col-6 col-md-4 col-lg-2">
                        <a href="{{ route('doctores.index') }}" class="text-decoration-none">
                            <div
                                class="card h-100 border-0 shadow-sm rounded-5 hover-scale transition-all py-4 text-center bg-white">
                                <div class="bg-navy-subtle text-primary rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center shadow-sm"
                                    style="width: 65px; height: 65px;">
                                    <x-mcr-stethoscope style="width: 2rem;" />
                                </div>
                                <h6 class="fw-bold text-navy mb-0">Doctores</h6>
                            </div>
                        </a>
                    </div>

                    {{-- FARMACIAS --}}
                    <div class="col-6 col-md-4 col-lg-2">
                        <a href="{{ route('admin.farmacias.index') }}" class="text-decoration-none">
                            <div
                                class="card h-100 border-0 shadow-sm rounded-5 hover-scale transition-all py-4 text-center bg-white">
                                <div class="bg-navy-subtle text-success rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center shadow-sm"
                                    style="width: 65px; height: 65px;">
                                    <x-mcr-pills style="width: 2rem;" />
                                </div>
                                <h6 class="fw-bold text-navy mb-0">Farmacias</h6>
                            </div>
                        </a>
                    </div>

                    {{-- PACIENTES --}}
                    <div class="col-6 col-md-4 col-lg-2">
                        <a href="{{ route('pacientes.index') }}" class="text-decoration-none">
                            <div
                                class="card h-100 border-0 shadow-sm rounded-5 hover-scale transition-all py-4 text-center bg-white">
                                <div class="bg-navy-subtle text-info rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center shadow-sm"
                                    style="width: 65px; height: 65px;">
                                    <x-mcr-users-alt style="width: 2rem;" />
                                </div>
                                <h6 class="fw-bold text-navy mb-0">Pacientes</h6>
                            </div>
                        </a>
                    </div>

                    {{-- REPORTES --}}
                    <div class="col-6 col-md-4 col-lg-2">
                        <a href="{{ route('admin.reportes.index') }}" class="text-decoration-none">
                            <div
                                class="card h-100 border-0 shadow-sm rounded-5 hover-scale transition-all py-4 text-center bg-white">
                                <div class="bg-navy-subtle text-danger rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center shadow-sm"
                                    style="width: 65px; height: 65px;">
                                    <x-mcr-flag style="width: 2rem;" />
                                </div>
                                <h6 class="fw-bold text-navy mb-0">Reportes</h6>
                            </div>
                        </a>
                    </div>

                    {{-- BACKUPS --}}
                    <div class="col-6 col-md-4 col-lg-2">
                        <a href="{{ route('backups.index') }}" class="text-decoration-none">
                            <div
                                class="card h-100 border-0 shadow-sm rounded-5 hover-scale transition-all py-4 text-center bg-white">
                                <div class="bg-navy-subtle text-secondary rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center shadow-sm"
                                    style="width: 65px; height: 65px;">
                                    <x-mcr-folder-upload style="width: 2rem;" />
                                </div>
                                <h6 class="fw-bold text-navy mb-0">Backups</h6>
                            </div>
                        </a>
                    </div>
                </div>

                {{-- 3. CHATBOT GEMINI AI (Refactorizado) --}}
                {{-- <button id="chatToggleBtn"
                    class="btn bg-navy text-white rounded-circle shadow-lg position-fixed d-flex align-items-center justify-content-center hover-scale border-0"
                    style="bottom: 30px; right: 30px; width: 65px; height: 65px; z-index: 1050; transition: transform 0.2s;">
                    <x-mcl-zap class="text-warning" style="width: 1.8rem;" />
                </button>

                <div id="chatWidget" class="card shadow-lg position-fixed d-none flex-column fade-in border-0"
                    style="bottom: 110px; right: 30px; width: 380px; border-radius: 25px; z-index: 1050; overflow: hidden;">

                    <div class="card-header border-0 p-4 text-white d-flex justify-content-between align-items-center"
                        style="background: linear-gradient(135deg, #00213D 0%, #0d2e4e 100%);">
                        <div class="d-flex align-items-center">
                            <div class="bg-white bg-opacity-25 p-2 rounded-circle me-3">
                                <x-mcl-zap class="text-warning" style="width: 1.2rem;" />
                            </div>
                            <div>
                                <h6 class="fw-bold mb-0">Gemini AI Engine</h6>
                                <small class="opacity-75">Soporte Administrativo</small>
                            </div>
                        </div>
                        <button id="closeChatBtn"
                            class="btn btn-link text-white p-0 shadow-none border-0 opacity-50 hover-opacity-100">
                            <x-mcl-times style="width: 1.2rem;" />
                        </button>
                    </div>

                    <div class="card-body bg-light p-0">
                        <div id="chat-messages" class="p-4" style="height: 400px; overflow-y: auto;">
                            <div class="d-flex flex-row justify-content-start mb-4">
                                <div class="bg-white p-3 rounded-4 shadow-sm border"
                                    style="border-top-left-radius: 0 !important; max-width: 85%;">
                                    <p class="small mb-0 text-dark">
                                        Hola **{{ Auth::user()->name }}**, tengo acceso a la base de datos de BuscaDoc.
                                        ¿Necesitas un resumen de las citas de hoy o el estado de los reportes?
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer bg-white border-0 p-3">
                        <div class="input-group bg-light rounded-pill p-1 border shadow-sm">
                            <input type="text" class="form-control border-0 bg-transparent shadow-none ps-3" id="chatInput"
                                placeholder="Pregunta algo..." autocomplete="off">
                            <button id="btnSend"
                                class="btn bg-navy text-white rounded-circle d-flex align-items-center justify-content-center p-0"
                                style="width: 40px; height: 40px;">
                                <x-mcl-send style="width: 1.2rem;" />
                            </button>
                        </div>
                    </div>
                </div>--}}
            @elseif (Auth::user()->role == 'doctor')
                <div class="row justify-content-center mb-5">
                    <div class="col-lg-10 text-center">
                        <div class="bg-navy-subtle d-inline-flex p-3 rounded-circle mb-3 shadow-sm">
                            <x-mcr-user-circle style="width: 2.5rem; height: 2.5rem;" />
                        </div>
                        <h2 class="fw-bold text-navy mb-1">Panel Médico de Control</h2>
                        <p class="text-muted fs-5">Bienvenido de nuevo, <span class="fw-bold text-dark">Dr.
                                {{ Auth::user()->name }}</span>.</p>
                    </div>
                </div>

                <div class="row g-4">
                    {{-- COLUMNA PRINCIPAL (ACCIONES) --}}
                    <div class="col-lg-8">
                        <h5 class="fw-bold text-navy mb-4 d-flex align-items-center">
                            <x-mcr-arrow-export class="me-2" style="width: 1.2rem;" /> Accesos Rápidos
                        </h5>

                        <div class="row g-3 mb-5">
                            {{-- CARD: AGENDA --}}
                            @if(Auth::user()->doctor->citas)
                                <div class="col-md-4">
                                    <a href="{{ route('doctores.citas', Auth::user()->doctor->id) }}" class="text-decoration-none">
                                        <div
                                            class="card h-100 border-0 shadow-sm rounded-4 p-4 hover-scale text-center bg-white transition-all">
                                            <div class="bg-success-subtle text-success rounded-4 mx-auto mb-3 d-flex align-items-center justify-content-center shadow-sm"
                                                style="width: 55px; height: 55px;">
                                                <x-mcr-calendar style="width: 1.8rem;" />
                                            </div>
                                            <h6 class="fw-bold text-navy mb-1">Agenda</h6>
                                            <p class="text-muted small mb-0">Gestionar citas</p>
                                        </div>
                                    </a>
                                </div>
                            @endif

                            {{-- CARD: MENSAJES --}}
                            <div class="col-md-4">
                                <a href="{{ route('mensajes.index') }}" class="text-decoration-none">
                                    <div
                                        class="card h-100 border-0 shadow-sm rounded-4 p-4 hover-scale text-center bg-white transition-all">
                                        <div class="bg-primary-subtle text-primary rounded-4 mx-auto mb-3 d-flex align-items-center justify-content-center shadow-sm"
                                            style="width: 55px; height: 55px;">
                                            <x-mcr-chat-dots style="width: 1.8rem;" />
                                        </div>
                                        <h6 class="fw-bold text-navy mb-1">Mensajes</h6>
                                        <p class="text-muted small mb-0">Atender pacientes</p>
                                    </div>
                                </a>
                            </div>

                            {{-- CARD: EXPEDIENTES --}}
                            <div class="col-md-4">
                                <a href="{{ route('expedientes.index') }}" class="text-decoration-none">
                                    <div
                                        class="card h-100 border-0 shadow-sm rounded-4 p-4 hover-scale text-center bg-white transition-all">
                                        <div class="bg-info-subtle text-info rounded-4 mx-auto mb-3 d-flex align-items-center justify-content-center shadow-sm"
                                            style="width: 55px; height: 55px;">
                                            <x-mcr-folder style="width: 1.8rem;" />
                                        </div>
                                        <h6 class="fw-bold text-navy mb-1">Expedientes</h6>
                                        <p class="text-muted small mb-0">Historial médico</p>
                                    </div>
                                </a>
                            </div>
                        </div>

                        {{-- RESUMEN RECIENTE --}}
                        <h5 class="fw-bold text-navy mb-4 d-flex align-items-center">
                            <x-mcr-activity-circle class="me-2 text-primary" style="width: 1.2rem;" /> Actividad Reciente
                        </h5>
                        <div class="row g-4">
                            {{-- Siguiente Paciente --}}
                            @if(Auth::user()->doctor->citas)
                                <div class="col-md-6">
                                    <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden">
                                        <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                                            <h6 class="fw-bold text-navy mb-0"><x-mcr-user-circle class="me-2 text-success"
                                                    style="width: 1rem;" />Siguiente Cita</h6>
                                        </div>
                                        <div class="card-body p-4">
                                            @if($proximaCitaDoctor)
                                                <div class="p-3 bg-light rounded-4 border-start border-4 border-success shadow-sm">
                                                    <div class="d-flex align-items-center mb-3">
                                                        <img src="{{ $proximaCitaDoctor->expediente->user->foto ? asset('storage/' . $proximaCitaDoctor->expediente->user->foto) : 'https://ui-avatars.com/api/?name=' . urlencode($proximaCitaDoctor->expediente->user->name) }}"
                                                            class="rounded-circle me-3 border border-2 border-white shadow-sm"
                                                            width="45" height="45" style="object-fit: cover;">
                                                        <div>
                                                            <span class="fw-bold text-dark d-block">
                                                                <h4>{{ Str::limit($proximaCitaDoctor->expediente->nombre_completo, 20) }}
                                                                </h4>
                                                            </span>
                                                            <i class="bi bi-clock-fill me-2 text-navy"> Cita programada para el:</i>
                                                            {{ $proximaCitaDoctor->fecha->format('d/m/Y') }} —
                                                            <strong
                                                                class="text-dark ms-1">{{ $proximaCitaDoctor->hora_inicio }}</strong>
                                                        </div>
                                                    </div>
                                                    <div class="text-end">
                                                        <a href="{{ route('doctores.citas') }}"
                                                            class="btn btn-link btn-sm text-navy p-0 fw-bold text-decoration-none">Ver
                                                            agenda <i class="bi bi-arrow-right"></i></a>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="text-center py-3 opacity-50">
                                                    <x-mcr-calendar class="mb-2" style="width: 2rem;" />
                                                    <p class="mb-0 small">Sin citas próximas.</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endif

                            {{-- Última Pregunta --}}
                            <div class="col-md-6">
                                <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden">
                                    <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                                        <h6 class="fw-bold text-navy mb-0"><x-mcr-info-circle class="me-2 text-info"
                                                style="width: 1rem;" />Duda Reciente</h6>
                                    </div>
                                    <div class="card-body p-4">
                                        @if($ultimaQuestion)
                                            <div class="bg-light rounded-4 p-3 border shadow-sm">
                                                <p class="text-muted small fst-italic mb-3">
                                                    "{{ Str::limit($ultimaQuestion->contenido, 60) }}"</p>
                                                <div class="d-flex align-items-center justify-content-between">
                                                    <div class="d-flex align-items-center">
                                                        <img src="{{ $ultimaQuestion->autor?->foto ? asset('storage/' . $ultimaQuestion->autor->foto) : 'https://ui-avatars.com/api/?name=' . urlencode($ultimaQuestion->autor?->name ?? 'A') }}"
                                                            class="rounded-circle me-2 shadow-sm" width="25" height="25"
                                                            style="object-fit: cover;">
                                                        <small
                                                            class="fw-bold text-dark">{{ explode(' ', $ultimaQuestion->autor?->name)[0] }}</small>
                                                    </div>
                                                    <a href="{{ route('doctores.show', Auth::user()->doctor->id) }}#pills-questions"
                                                        class="btn btn-navy btn-sm rounded-pill px-3"
                                                        style="font-size: 0.7rem;">Responder</a>
                                                </div>
                                            </div>
                                        @else
                                            <div class="text-center py-3 opacity-50">
                                                <x-mcr-chat-dots class="mb-2" style="width: 2rem;" />
                                                <p class="mb-0 small">Sin preguntas nuevas.</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- COLUMNA LATERAL (PERFIL Y ESTADO) --}}
                    <div class="col-lg-4">
                        <div class="card border-0 shadow-sm rounded-4 p-4 sticky-top" style="top: 20px;">
                            <div class="text-center mb-4">
                                <div class="position-relative d-inline-block mb-3">
                                    <img src="{{ Auth::user()->foto ? asset('storage/' . Auth::user()->foto) : 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) }}"
                                        class="rounded-circle border border-4 border-white shadow-sm" width="100" height="100"
                                        style="object-fit: cover;">
                                    <span
                                        class="position-absolute bottom-0 end-0 bg-success border border-2 border-white rounded-circle"
                                        style="width: 18px; height: 18px;" title="En línea"></span>
                                </div>
                                <h5 class="fw-bold text-navy mb-0">Dr. {{ Auth::user()->name }}</h5>
                                <p class="text-primary small fw-bold mb-0">
                                    {{ Auth::user()->doctor->especialidades->first()->nombre ?? 'Médico Especialista' }}
                                </p>
                            </div>

                            <div class="bg-navy text-white rounded-4 p-3 mb-4 shadow-sm text-center">
                                <small class="opacity-75 d-block text-uppercase mb-1"
                                    style="font-size: 0.65rem; letter-spacing: 1px;">Ingresos por Consulta</small>
                                <h3 class="fw-bold mb-0">${{ number_format(Auth::user()->doctor->costo, 2) }}</h3>
                            </div>

                            <div class="list-group list-group-flush mb-4">
                                <div class="list-group-item bg-transparent px-0 border-light">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-muted small fw-bold text-uppercase" style="font-size: 0.7rem;">Estado
                                            de Atención</span>
                                        @php
                                            $hoy = now()->dayOfWeek;
                                            $horaActual = now()->format('H:i:s');
                                            $disponibilidadHoy = Auth::user()->doctor->disponibilidades->where('dia_semana', $hoy);
                                            $estaAbierto = false;
                                            foreach ($disponibilidadHoy as $bloque) {
                                                if ($horaActual >= $bloque->hora_inicio && $horaActual <= $bloque->hora_fin)
                                                    $estaAbierto = true;
                                            }
                                        @endphp
                                        @if($disponibilidadHoy->isEmpty())
                                            <span class="badge bg-secondary-subtle text-secondary rounded-pill">Sin consultas</span>
                                        @else
                                            <span
                                                class="badge {{ $estaAbierto ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }} rounded-pill px-3">
                                                {{ $estaAbierto ? 'Abierto' : 'Cerrado' }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="d-grid gap-2">
                                <a href="{{ route('doctores.show', Auth::user()->doctor->id) }}"
                                    class="btn btn-navy rounded-pill fw-bold shadow-sm py-2 transition-hover">
                                    <x-mcr-eye class="me-2" style="width: 1.1rem;" /> Perfil Público
                                </a>
                                <a href="{{ route('users.edit', Auth::user()->doctor->id) }}"
                                    class="btn btn-outline-navy rounded-pill fw-bold py-2 transition-hover">
                                    <x-mcr-settings class="me-2" style="width: 1.1rem;" /> Editar Info
                                </a>
                            </div>
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

                        <div
                            class="alert bg-navy text-white border-0 rounded-4 p-3 mb-4 d-flex flex-column flex-md-row align-items-center justify-content-between shadow-sm">
                            <div class="d-flex align-items-center mb-2 mb-md-0">
                                <div class="text-start">
                                    <h6 class="fw-bold mb-0">¡Lleva BuscaDoc en tu bolsillo!</h6>
                                    <small class="opacity-75">Citas más rápidas y notificaciones en tiempo real con nuestra
                                        app.</small>
                                </div>
                            </div>
                            <a href="{{ asset('descargas/buscadoc_v1.apk') }}"
                                class="btn btn-apk btn-sm rounded-pill px-4 fw-bold">Instalar APK</a>
                        </div>

                        <h2 class="fw-bold text-navy mb-2">Bienvenido a BuscaDoc, {{ Auth::user()->name }}</h2>
                        <p class="text-muted mb-4">Encuentra lo que buscas, aquí mismo.</p>

                        <div class="row justify-content-center position-relative z-3 mb-5">
                            <div class="col-12 col-md-11 col-lg-12">
                                <div class="card border-0 shadow-sm rounded-5 search-form-card"
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
                                                            style="height: 48px; min-width: 180px;" type="button"
                                                            data-bs-toggle="dropdown" aria-expanded="false">
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
                                                                            <x-mcr-pills style="width: 100%; height: 100%;" />
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
                                                        class="form-select form-select-lg rounded-pill border bg-white text-navy fw-bold px-4"
                                                        name="especialidad_id"
                                                        style="height: 48px; min-width: 220px; cursor: pointer;">
                                                        <option value="" selected>Todas las especialidades</option>
                                                        @foreach($especialidades ?? [] as $esp)
                                                            <option value="{{ $esp->id }}">{{ $esp->nombre }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="col-12 col-md-auto">
                                                    <button
                                                        class="btn btn-navy btn-lg rounded-pill w-100 px-5 fw-bold search-button d-flex align-items-center justify-content-center"
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
                    </div>
                </div>

                <div class="row g-4">
                    <div class="col-lg-8">

                        <a href="{{ route('mensajes.index') }}" class="text-decoration-none mb-4 d-block">
                            <div class="card border-0 shadow-sm rounded-4 p-4 hover-scale text-white position-relative overflow-hidden"
                                style="background: linear-gradient(135deg, #0d2e4e 0%, #00213D 100%);">
                                <div class="position-absolute"
                                    style="right: -10px; top: -25px; opacity: 0.1; transform: scale(1.5);">
                                    <x-mcf-chat-dots style="width: 8rem; height: 8rem;" />
                                </div>
                                <div class="position-relative z-1 d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center me-3"
                                            style="width: 50px; height: 50px;">
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

                        <a href="{{ route('expedientes.index') }}" class="text-decoration-none mb-4 d-block">
                            <div class="card border-0 shadow-sm rounded-4 p-4 hover-scale text-white position-relative overflow-hidden"
                                style="background: linear-gradient(135deg, #0f766e 0%, #064e3b 100%);">
                                <div class="position-absolute"
                                    style="right: -10px; top: -25px; opacity: 0.1; transform: scale(1.5);">
                                    <x-mcf-folder-open style="width: 8rem; height: 8rem;" />
                                </div>
                                <div class="position-relative z-1 d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center me-3"
                                            style="width: 50px; height: 50px;">
                                            <x-mcf-folder-open class="fs-4" />
                                        </div>
                                        <div>
                                            <h5 class="fw-bold mb-1">Mis Expedientes</h5>
                                            <p class="mb-0 opacity-75 small">Gestiona los expedientes de tus familiares y tuyos
                                            </p>
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
                                        <div
                                            class="col-12 bg-navy text-white p-3 d-flex align-items-center justify-content-between d-md-none">
                                            <span class="fw-bold"><i class="bi bi-calendar-event me-2"></i>Tu próxima cita</span>
                                        </div>
                                        <div
                                            class="col-md-2 bg-light d-flex flex-column align-items-center justify-content-center py-4 border-end">
                                            <span
                                                class="text-uppercase small fw-bold text-muted">{{ $proximaCita->fecha->format('M') }}</span>
                                            <span
                                                class="display-4 fw-bold text-navy lh-1">{{ $proximaCita->fecha->format('d') }}</span>
                                            <span class="small text-muted">{{ $proximaCita->fecha->format('l') }}</span>
                                        </div>
                                        <div class="col-md-7 p-4 d-flex align-items-center">
                                            <div class="d-flex align-items-center">
                                                <img src="{{ $proximaCita->doctor->user->foto ? asset('storage/' . $proximaCita->doctor->user->foto) : 'https://ui-avatars.com/api/?name=' . urlencode($proximaCita->doctor->user->name) }}"
                                                    class="rounded-circle shadow-sm me-3" width="65" height="65"
                                                    style="object-fit: cover;">
                                                <div>
                                                    <small class="text-primary fw-bold text-uppercase"
                                                        style="font-size: 0.7rem; letter-spacing: 1px;">Próxima Consulta</small>
                                                    <h5 class="fw-bold text-navy mb-1">Dr. {{ $proximaCita->doctor->user->name }}
                                                    </h5>
                                                    <div class="d-flex align-items-center text-muted small">
                                                        <i class="bi bi-clock-fill me-1 text-warning"></i>
                                                        <span
                                                            class="fw-bold text-dark">{{ \Carbon\Carbon::parse($proximaCita->hora_inicio)->format('h:i A') }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div
                                            class="col-md-3 bg-white p-4 d-flex flex-column justify-content-center align-items-center border-start">
                                            <span
                                                class="badge {{ $proximaCita->estado == 'pendiente' ? 'bg-warning text-dark' : 'bg-success' }} rounded-pill px-3 mb-3">
                                                {{ $proximaCita->estado == 'confirmada' ? 'Confirmada' : 'Pendiente' }}
                                            </span>
                                            <a href="{{ route('pacientes.citas') }}"
                                                class="btn btn-outline-navy rounded-pill btn-sm px-4">Ver mis citas</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="card border-0 shadow-sm rounded-4 mb-5 p-4 text-center bg-white hover-scale">
                                <div class="py-3">
                                    <div class="mb-3"><x-mcr-calendar class="h-15 w-15 text-muted opacity-25"
                                            style="font-size: 3rem;" /></div>
                                    <h5 class="fw-bold text-navy">No tienes citas próximas</h5>
                                    <p class="text-muted small">¿Te sientes mal o necesitas un chequeo?</p>
                                </div>
                            </div>
                        @endif

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="fw-bold custom-text-dark mb-0">Nuestras especialidades</h4>
                        </div>

                        <div class="d-flex flex-wrap gap-2 mb-4">
                            @forelse ($especialidades as $especialidad)
                                <a href="{{ route('specs.show', $especialidad->id) }}" class="text-decoration-none">
                                    <div
                                        class="bg-white border rounded-pill px-3 py-2 shadow-sm hover-scale d-flex align-items-center">
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

                        <div id="specialties-container">
                            @php $contadorEspecialidades = 0; @endphp

                            @foreach ($especialidades as $especialidad)
                                @if($especialidad->doctors->count() > 0)
                                    {{-- Se ocultan a partir de la 3ra especialidad válida --}}
                                    <div
                                        class="specialty-section-patient mb-4 {{ $contadorEspecialidades >= 2 ? 'd-none hidden-specialty' : '' }}">

                                        <div class="d-flex justify-content-between align-items-end mb-2 px-2">
                                            <h5 class="fw-bold custom-text-dark mb-0">{{ $especialidad->nombre }}</h5>
                                            <a href="{{ route('specs.show', $especialidad->id) }}"
                                                class="text-decoration-none text-navy fw-bold" style="font-size: 0.85rem;">Ver todos
                                                ></a>
                                        </div>

                                        {{-- CARRUSEL MODERNO --}}
                                        <div class="scroll-horizontal">
                                            @foreach ($especialidad->doctors as $doctor)
                                                <div
                                                    class="card border-0 shadow-sm rounded-4 hover-scale p-3 d-flex flex-column align-items-center doctor-card-snap bg-white">

                                                    <div class="position-relative mb-3 mt-2" style="width: 85px; height: 85px;">
                                                        @if($doctor->user->foto)
                                                            <img src="{{ asset('storage/' . $doctor->user->foto) }}"
                                                                alt="Foto de {{ $doctor->user->name }}"
                                                                class="rounded-circle shadow-sm border border-3 border-white w-100 h-100"
                                                                style="object-fit: cover;">
                                                        @else
                                                            <div
                                                                class="bg-navy-subtle text-navy rounded-circle d-flex align-items-center justify-content-center shadow-sm border border-3 border-white w-100 h-100">
                                                                <span
                                                                    class="fs-3 fw-bold text-uppercase">{{ substr($doctor->user->name, 0, 1) }}</span>
                                                            </div>
                                                        @endif
                                                        <span
                                                            class="position-absolute bottom-0 end-0 bg-success border border-2 border-white rounded-circle"
                                                            style="width: 18px; height: 18px; margin-bottom: 4px; margin-right: 4px;"></span>
                                                    </div>

                                                    <h6 class="fw-bold text-dark text-center mb-1 text-truncate w-100">Dr.
                                                        {{ $doctor->user->name }}
                                                    </h6>
                                                    <small class="text-muted mb-4 text-center d-block"
                                                        style="font-size: 0.75rem;">{{ $especialidad->nombre }}</small>

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

                        @if($contadorEspecialidades > 2)
                            <div class="row mt-2 mb-5">
                                <div class="col-12 text-center">
                                    <button id="toggleSpecialtiesBtn"
                                        class="btn btn-outline-navy rounded-pill px-5 py-2 fw-bold shadow-sm transition-all">
                                        Ver más especialidades <i class="bi bi-chevron-down ms-2"></i>
                                    </button>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="col-lg-4">
                        <div class="card border-0 shadow-sm rounded-4 p-4 sticky-top" style="top: 20px; z-index: 1;">
                            <div class="d-flex align-items-center mb-4">
                                <img src="{{ Auth::user()->foto ? asset('storage/' . Auth::user()->foto) : 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) }}"
                                    class="rounded-circle me-3 shadow-sm" width="50" height="50" style="object-fit: cover;">
                                <div>
                                    <h6 class="fw-bold mb-0 text-navy">Mi Ficha Médica</h6>
                                    <a href="{{ route('users.show', Auth::user()->id) }}"
                                        class="small text-muted text-decoration-none">Ver perfil completo ></a>
                                </div>
                            </div>

                            @php
                                $expedientePropio = Auth::user()->expedientes ? Auth::user()->expedientes->where('parentesco', 'Propio')->first() : null;
                            @endphp

                            @if($expedientePropio)
                                <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                                    <span class="text-muted small fw-bold">Mi tipo de sangre</span>
                                    <span
                                        class="fw-bold text-danger bg-danger-subtle px-3 py-1 rounded-pill">{{ $expedientePropio->tipo_sangre ?? '--' }}</span>
                                </div>

                                <div class="mb-3">
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="bg-warning bg-opacity-10 p-2 rounded-3 me-3">
                                            <i class="bi bi-exclamation-triangle-fill text-warning"></i>
                                        </div>
                                        <div>
                                            <p class="text-muted small mb-0">Alergias</p>
                                            <p class="fw-bold text-navy mb-0 small">
                                                {{ Str::limit($expedientePropio->alergias ?? 'Ninguna', 40) }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-0">
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="bg-info bg-opacity-10 p-2 rounded-3 me-3">
                                            <i class="bi bi-heart-pulse-fill text-info"></i>
                                        </div>
                                        <div>
                                            <p class="text-muted small mb-0">Padecimientos Crónicos</p>
                                            <p class="fw-bold text-navy mb-0 small">
                                                {{ Str::limit($expedientePropio->padecimientos_cronicos ?? 'Ninguno', 40) }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="alert alert-warning small border-0 rounded-3">
                                    <i class="bi bi-exclamation-circle-fill me-1"></i> Completa tu perfil médico para emergencias.
                                    <a href="{{ route('expedientes.create') }}"
                                        class="btn btn-navy btn-sm rounded-pill mt-2 w-100">Crear
                                        Ficha</a>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="row g-4 mb-5 mt-2">
                        <div class="col-lg-7">
                            <div id="map" class="shadow-sm border rounded-4" style="min-height: 450px;"></div>
                        </div>
                        <div class="col-lg-5 d-flex flex-column" style="height: 450px;">
                            <div class="flex-grow-1 pe-2" style="overflow-y: auto; overflow-x: hidden;">
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