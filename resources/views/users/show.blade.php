<?php
use Illuminate\Support\Str;
use app\Utils;

$apiKey = env('API_KEY');

// Coordenadas
$lat = $user->latitud;
$lng = $user->longitud;
$hasLocation = $lat && $lng;

// Helpers de Roles
$isDoctor = $user->role === 'doctor';
$isPharmacy = $user->role === 'farmacia';
$isPatient = $user->role === 'paciente';
$isAdmin = $user->role === 'admin';
?>

<x-layout>

    <head>
        <style>
            :root {
                --brand-navy: #0d2e4e;
                --brand-navy-light: #1a5f7a;
                --bg-surface: #f8fafc;
            }

            body {
                background-color: var(--bg-surface);
            }

            /* --- Contenedores y Tarjetas --- */
            .soft-card {
                background: white;
                border: 1px solid rgba(13, 46, 78, 0.05);
                border-radius: 24px;
                box-shadow: 0 10px 40px rgba(13, 46, 78, 0.04);
                overflow: hidden;
                transition: all 0.3s ease;
            }

            .soft-card:hover {
                box-shadow: 0 15px 50px rgba(13, 46, 78, 0.08);
            }

            /* --- Foto de Perfil --- */
            .profile-photo-container {
                border-radius: 24px;
                overflow: hidden;
                box-shadow: 0 15px 35px rgba(13, 46, 78, 0.1);
                height: 380px;
                background-color: #e2e8f0;
                position: relative;
            }

            .profile-photo {
                width: 100%;
                height: 100%;
                object-fit: cover;
                transition: transform 0.5s ease;
            }

            .profile-photo-container:hover .profile-photo {
                transform: scale(1.03);
            }

            /* Degradado interior para que el texto del badge siempre se lea */
            .profile-photo-container::after {
                content: '';
                position: absolute;
                bottom: 0;
                left: 0;
                width: 100%;
                height: 40%;
                background: linear-gradient(to top, rgba(13, 46, 78, 0.8) 0%, transparent 100%);
                pointer-events: none;
            }

            /* --- Tipografía y Colores --- */
            .text-navy { color: var(--brand-navy) !important; }
            .bg-navy { background-color: var(--brand-navy) !important; }
            .border-navy { border-color: var(--brand-navy) !important; border-width: 1px;}

            .text-label {
                font-weight: 700;
                color: #94a3b8;
                font-size: 0.8rem;
                text-transform: uppercase;
                letter-spacing: 1.5px;
            }

            /* --- Iconos Magicoons --- */
            .icon-md { width: 1.5rem; height: 1.5rem; fill: currentColor; stroke: currentColor; }
            .icon-lg { width: 2rem; height: 2rem; fill: currentColor; stroke: currentColor; }
            .icon-sm { width: 1.2rem; height: 1.2rem; fill: currentColor; stroke: currentColor; }

            /* --- Filas de Información --- */
            .info-row {
                display: flex;
                align-items: center;
                margin-bottom: 1.2rem;
                padding: 10px;
                border-radius: 16px;
                transition: background 0.2s;
            }

            .info-row:hover {
                background: #f1f5f9;
            }

            .info-icon-box {
                color: var(--brand-navy);
                background: rgba(13, 46, 78, 0.05);
                width: 48px;
                height: 48px;
                display: flex;
                align-items: center;
                justify-content: center;
                border-radius: 14px;
                margin-right: 18px;
                flex-shrink: 0;
            }

            /* --- Badges Glassmorphism --- */
            .role-badge {
                position: absolute;
                bottom: 20px;
                left: 20px;
                padding: 10px 20px;
                border-radius: 50px;
                font-weight: 600;
                font-size: 0.95rem;
                backdrop-filter: blur(12px);
                -webkit-backdrop-filter: blur(12px);
                border: 1px solid rgba(255, 255, 255, 0.2);
                color: white;
                display: flex;
                align-items: center;
                gap: 8px;
                z-index: 2;
                box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
            }

            .badge-glass-navy { background-color: rgba(13, 46, 78, 0.65); }
            .badge-glass-light { background-color: rgba(255, 255, 255, 0.2); color: white; }

            /* --- Tarjetas de Sub-datos (Alergias, Costos, etc) --- */
            .data-box {
                padding: 1.25rem;
                border-radius: 20px;
                height: 100%;
                transition: transform 0.3s ease, box-shadow 0.3s ease;
            }
            
            .data-box:hover {
                transform: translateY(-4px);
                box-shadow: 0 12px 25px rgba(0,0,0,0.05);
            }

            .icon-circle {
                width: 45px;
                height: 45px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                background: white;
                box-shadow: 0 4px 10px rgba(0,0,0,0.05);
                flex-shrink: 0;
            }

            /* --- Botón de Regresar --- */
            .btn-back {
                color: var(--brand-navy);
                background: white;
                border: 1.5px solid #e2e8f0;
                font-weight: 600;
                transition: all 0.3s;
                display: inline-flex;
                align-items: center;
            }
            .btn-back:hover {
                background: var(--brand-navy);
                color: white;
                border-color: var(--brand-navy);
            }
        </style>
    </head>

    <div class="container py-5">

        <div class="mb-4">
            <a href="{{ url()->previous() }}" class="btn btn-back rounded-pill px-4 py-2 shadow-sm">
                <x-mcl-angle-left class="icon-sm me-2"/> Regresar
            </a>
        </div>

        <div class="row g-5">

            {{-- COLUMNA IZQUIERDA --}}
            <div class="col-lg-4">

                {{-- 1. FOTO Y BADGE DE ROL --}}
                <div class="profile-photo-container mb-4">
                    <img src="{{ $user->foto ? asset('storage/' . $user->foto) : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=0d2e4e&color=fff&size=500' }}"
                        alt="{{ $user->name }}" class="profile-photo">

                    {{-- Lógica de Badges Unificados (Glassmorphism) --}}
                    @if($isDoctor)
                        <span class="role-badge badge-glass-navy">
                            <x-mcr-stethoscope class="icon-sm"/> Doctor
                        </span>
                    @elseif($isPharmacy)
                        <span class="role-badge badge-glass-navy">
                            <x-mcr-pills class="icon-sm"/> Farmacia
                        </span>
                    @elseif($isAdmin)
                        <span class="role-badge badge-glass-navy">
                            <x-mcr-shield class="icon-sm"/> Admin
                        </span>
                    @else
                        <span class="role-badge badge-glass-light">
                            <x-mcr-user-alt class="icon-sm"/> Paciente
                        </span>
                    @endif
                </div>

                {{-- Datos Generales --}}
                <div class="soft-card p-4">
                    <div class="mb-3">
                        <span class="text-label d-block mb-1">Nombre Completo</span>
                        <span class="fs-4 fw-bold text-navy lh-sm">{{ $user->name }}</span>
                    </div>

                    {{-- Cédula --}}
                    @if($isDoctor && $user->doctor)
                        <hr class="text-muted opacity-25 my-3">
                        <div class="mb-3">
                            <span class="text-label d-block mb-1">Cédula Profesional</span>
                            <span class="text-muted font-monospace bg-light px-2 py-1 rounded">{{ $user->doctor->cedula }}</span>
                        </div>
                    @endif

                    <hr class="text-muted opacity-25 my-3">
                    <div class="mb-0">
                        <span class="text-label d-block mb-1">Miembro desde</span>
                        <div class="d-flex align-items-center text-muted">
                            <x-mcl-calendar class="icon-sm me-2"/>
                            <span>{{ str::title($user->created_at->translatedFormat('F Y')) }}</span>
                        </div>
                    </div>
                </div>

                @if(auth()->id() === $user->id || auth()->user()->role === 'admin')
                     <div class="mt-4 d-grid">
                        <a href="{{ route('users.edit', $user->id) }}" class="btn btn-navy rounded-pill py-3">
                            <x-mcl-pen class="icon-white me-2"/>Editar Perfil
                        </a>
                    </div>
                @endif
            </div>

            <div class="col-lg-8">
                <div class="soft-card p-5 mb-4">
                    <h4 class="mb-4 fw-bold text-navy">Información de Contacto</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-row">
                                <div class="info-icon-box"><x-mcr-envelope class="icon-md"/></div>
                                <div>
                                    <span class="fw-bold d-block text-navy">Correo Electrónico</span>
                                    <span class="text-muted">{{ $user->email }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-row">
                                <div class="info-icon-box"><x-mcr-calendar class="icon-md"/></div>
                                <div>
                                    <span class="fw-bold d-block text-navy">Fecha de Nacimiento</span>
                                    <span class="text-muted">
                                        {{ $user->f_nacimiento ? \Carbon\Carbon::parse($user->f_nacimiento)->format('d/m/Y') : 'No registrada' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- INFO PACIENTES --}}
                @if($isPatient)
                    <div class="soft-card p-5 mb-4">
                        <div class="d-flex align-items-center mb-4">
                            <div class="info-icon-box me-3 mb-0 bg-navy text-white"><x-mcl-id-card class="icon-md"/></div>
                            <h4 class="fw-bold text-navy mb-0">Ficha Médica Básica</h4>
                        </div>

                        {{-- 1. Buscamos el expediente principal entre todos los que tenga el usuario --}}
                        @php
                            $expedientePrincipal = $user->expedientes ? $user->expedientes->where('parentesco', 'Expediente Propio')->first() : null;
                        @endphp

                        {{-- 2. Validamos si encontró su expediente personal --}}
                        @if($expedientePrincipal)
                            <div class="row g-3">
                                {{-- Tipo de Sangre --}}
                                <div class="col-md-4">
                                    <div class="data-box bg-danger-subtle border border-danger-subtle">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="icon-circle text-danger"><x-mcr-test-tube class="icon-md"/></div>
                                            <div>
                                                <small class="text-danger-emphasis fw-bold text-uppercase" style="font-size: 0.70rem;">Tipo de Sangre</small>
                                                <div class="fs-4 fw-bold text-dark lh-1 mt-1">{{ $expedientePrincipal->tipo_sangre ?? '--' }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Alergias --}}
                                <div class="col-md-8">
                                    <div class="data-box bg-warning-subtle border border-warning-subtle">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="icon-circle text-warning"><x-mcr-triangle-exclamation class="icon-md"/></div>
                                            <div>
                                                <small class="text-warning-emphasis fw-bold text-uppercase" style="font-size: 0.70rem;">Alergias</small>
                                                <div class="fw-medium text-dark lh-sm mt-1">{{ $expedientePrincipal->alergias ?? 'Ninguna alergia registrada.' }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Padecimientos Crónicos (Nombre actualizado según migración) --}}
                                <div class="col-md-6">
                                    <div class="data-box bg-info-subtle border border-info-subtle">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="icon-circle text-info"><x-mcr-heart class="icon-md"/></div>
                                            <div>
                                                <small class="text-info-emphasis fw-bold text-uppercase" style="font-size: 0.70rem;">Padecimientos</small>
                                                <div class="text-dark small lh-sm mt-1">{{ $expedientePrincipal->padecimientos_cronicos ?? 'Sin padecimientos registrados.' }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Hábitos de Salud (Nombre actualizado según migración) --}}
                                <div class="col-md-6">
                                    <div class="data-box bg-success-subtle border border-success-subtle">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="icon-circle text-success"><x-mcr-mug class="icon-md"/></div>
                                            <div>
                                                <small class="text-success-emphasis fw-bold text-uppercase" style="font-size: 0.70rem;">Hábitos</small>
                                                <div class="text-dark small lh-sm mt-1">{{ $expedientePrincipal->habitos_salud ?? 'No especificados.' }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="alert alert-light border rounded-4 d-flex align-items-center shadow-sm p-4" role="alert">
                                <x-mcr-exclamation-circle class="icon-lg text-muted me-3"/>
                                <div>
                                    <strong class="d-block text-navy fs-5">Información pendiente</strong>
                                    <span class="text-muted">Aún no has completado tu ficha médica personal.</span>
                                </div>
                            </div>
                        @endif
                    </div>
                @endif

                {{-- INFO FARMACIAS --}}
                @if($isPharmacy)
                    <div class="soft-card p-5 mb-4">
                        <div class="d-flex align-items-center mb-4">
                            <div class="info-icon-box me-3 mb-0 bg-navy text-white"><x-mcr-pills class="icon-md"/></div>
                            <h4 class="fw-bold text-navy mb-0">Info Básica de la Farmacia</h4>
                        </div>

                        @if($user->farmacia)
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="data-box bg-primary-subtle border border-primary-subtle">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="icon-circle text-primary"><x-mcr-building class="icon-md"/></div>
                                            <div>
                                                <small class="text-primary-emphasis fw-bold text-uppercase" style="font-size: 0.70rem;">Establecimiento</small>
                                                <div class="fs-5 fw-bold text-dark lh-sm mt-1">{{ $user->farmacia->nom_farmacia ?? 'Sin nombre' }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="data-box bg-secondary-subtle border border-secondary-subtle">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="icon-circle text-secondary"><x-mcr-credit-card class="icon-md"/></div>
                                            <div>
                                                <small class="text-secondary-emphasis fw-bold text-uppercase" style="font-size: 0.70rem;">RFC</small>
                                                <div class="fs-5 fw-bold text-dark font-monospace lh-1 mt-1">{{ $user->farmacia->rfc ?? '--' }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-5">
                                    <div class="data-box bg-success-subtle border border-success-subtle">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="icon-circle text-success"><x-mcr-phone class="icon-md"/></div>
                                            <div>
                                                <small class="text-success-emphasis fw-bold text-uppercase" style="font-size: 0.70rem;">Teléfono</small>
                                                <div class="fs-5 fw-bold text-dark lh-1 mt-1">{{ $user->farmacia->telefono ?? '--' }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-7">
                                    <div class="data-box bg-info-subtle border border-info-subtle">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="icon-circle text-info"><x-mcr-clock class="icon-md"/></div>
                                            <div>
                                                <small class="text-info-emphasis fw-bold text-uppercase" style="font-size: 0.70rem;">Horario de Atención</small>
                                                <div class="fs-5 fw-bold text-dark lh-1 mt-1">
                                                    @if($user->farmacia->horario_entrada && $user->farmacia->horario_salida)
                                                        {{ \Carbon\Carbon::parse($user->farmacia->horario_entrada)->format('H:i') }} - 
                                                        {{ \Carbon\Carbon::parse($user->farmacia->horario_salida)->format('H:i') }}
                                                    @else
                                                        <span class="text-muted fs-6">No especificado</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="data-box bg-light border">
                                        <div class="d-flex gap-3">
                                            <div class="icon-circle text-dark"><x-mcr-file class="icon-md"/></div>
                                            <div>
                                                <small class="text-muted fw-bold text-uppercase" style="font-size: 0.70rem;">Sobre Nosotros</small>
                                                <p class="text-dark mb-0 mt-1 small lh-sm">
                                                    {{ $user->farmacia->descripcion ?? 'Sin descripción disponible.' }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="alert alert-light border rounded-4 d-flex align-items-center shadow-sm p-4" role="alert">
                                <x-mcr-exclamation-circle class="icon-lg text-muted me-3"/>
                                <div>
                                    <strong class="d-block text-navy fs-5">Información pendiente</strong>
                                    <span class="text-muted">La farmacia aún no ha completado su perfil.</span>
                                </div>
                            </div>
                        @endif
                    </div>
                @endif

                {{-- INFO DOCTOR --}}
                @if($isDoctor && $user->doctor)
                    <div class="soft-card p-5 mb-4">
                        <div class="d-flex align-items-center mb-4">
                            <div class="info-icon-box me-3 mb-0 bg-navy text-white"><x-mcr-stethoscope class="icon-md"/></div>
                            <h4 class="fw-bold text-navy mb-0">Perfil Médico</h4>
                        </div>
                        
                        <div class="mb-4 bg-light p-4 rounded-4 border">
                            <span class="text-label d-block mb-2">Descripción / Biografía</span>
                            <p class="text-dark mb-0 lh-base">{{ $user->doctor->descripcion }}</p>
                        </div>

                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="info-row bg-white border shadow-sm p-3">
                                    <div class="info-icon-box bg-success-subtle text-success"><x-mcr-wallet class="icon-lg"/></div>
                                    <div>
                                        <span class="fw-bold d-block">Costo Estimado De Consulta</span>
                                        <span
                                            class="text-success fs-5 fw-bold">${{ number_format($user->doctor->costo, 2) }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="soft-card p-4 mb-4" x-data="{ diaSeleccionado: {{ now()->dayOfWeek }} }">
                                    <h6 class="text-navy fw-bold mb-3"><i class="bi bi-clock-history me-2"></i>Disponibilidad Semanal</h6>
                                    
                                    {{-- Botones de días --}}
                                    <div class="d-flex flex-wrap gap-2 mb-3">
                                        @foreach(['Dom', 'Lun', 'Mar', 'Mie', 'Jue', 'Vie', 'Sab'] as $num => $nombre)
                                            @php 
                                                // Accedemos a través de la relación del usuario con el doctor
                                                $tieneHorario = $user->doctor->disponibilidades->contains('dia_semana', $num); 
                                            @endphp
                                            <button @click="diaSeleccionado = {{ $num }}" 
                                                type="button"
                                                class="btn btn-sm rounded-pill px-3 fw-bold transition-all"
                                                :class="diaSeleccionado == {{ $num }} ? 'btn-navy shadow' : 'btn-outline-secondary'"
                                                {{ !$tieneHorario ? 'disabled style=opacity:0.3' : '' }}>
                                                {{ $nombre }}
                                            </button>
                                        @endforeach
                                    </div>

                                    {{-- Contenedor de Horarios --}}
                                    <div class="bg-light p-3 rounded-4 border shadow-sm">
                                        @foreach($user->doctor->disponibilidades->groupBy('dia_semana') as $dia => $bloques)
                                            <div x-show="diaSeleccionado == {{ $dia }}" x-transition:enter.duration.400ms>
                                                <div class="row g-2">
                                                    @foreach($bloques as $bloque)
                                                        <div class="col-md-6">
                                                            <div class="d-flex align-items-center bg-white p-2 rounded-3 border">
                                                                <i class="bi bi-alarm text-success me-2"></i>
                                                                <span class="small fw-bold text-navy">
                                                                    {{ \Carbon\Carbon::parse($bloque->hora_inicio)->format('g:i A') }} - 
                                                                    {{ \Carbon\Carbon::parse($bloque->hora_fin)->format('g:i A') }}
                                                                </span>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endforeach
                                        
                                        {{-- Mensaje si no hay horarios el día seleccionado --}}
                                        <div x-show="!{{ $user->doctor->disponibilidades->pluck('dia_semana')->unique()->values()->toJson() }}.includes(diaSeleccionado)">
                                            <p class="text-muted small mb-0 text-center py-2">
                                                <i class="bi bi-info-circle me-2"></i>No hay consultas programadas.
                                            </p>
                                        </div>
                                    
                                    </div>
                                
                                 </div> 
                        </div> 
                    </div>
                @endif

                {{-- MAPA --}}
                @if($isDoctor || $isPharmacy)
                    <div class="soft-card p-1">
                        <div id="map" style="height: 350px; border-radius: 23px 23px 0 0;"></div>
                        <div class="p-3 bg-white text-center rounded-bottom-4">
                            <span class="text-navy fw-bold">
                                <x-mcr-location-pin class="icon-sm me-1 text-danger"/>
                                Ubicación registrada de operaciones
                            </span>
                        </div>
                    </div>
                @endif

            </div>
        </div>
    </div>

    <script async src="https://maps.googleapis.com/maps/api/js?key=<?php echo $apiKey; ?>&callback=initMap"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        function initMap() {
            const position = { lat: <?php echo $lat; ?>, lng: <?php echo $lng; ?> };
            const map = new google.maps.Map(document.getElementById("map"), {
                zoom: 16,
                center: position,
                disableDefaultUI: true,
                mapId: 'DEMO_MAP_ID', // Opcional para un mapa más limpio
            });
            new google.maps.Marker({
                position: position,
                map: map,
                title: "{{ $user->name }}"
            });
        }
    </script>
</x-layout>