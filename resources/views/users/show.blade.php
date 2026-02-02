<?php
use Illuminate\Support\Str;
use app\Utils;

$apiKey = "AIzaSyDzSz-VqueMjM2OEaddCFuNLSl7LsCpqzQ";

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
            body { background-color: #f3f4f6; }

            /* Tarjetas */
            .soft-card {
                background: white;
                border: none;
                border-radius: 24px;
                box-shadow: 0 10px 40px rgba(0, 0, 0, 0.05);
                overflow: hidden;
                transition: transform 0.2s;
            }
            
            /* Foto de Perfil */
            .profile-photo-container {
                border-radius: 24px;
                overflow: hidden;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
                height: 350px;
                background-color: #e9ecef;
                position: relative;
            }
            .profile-photo {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }

            /* Textos */
            .text-navy { color: #0f172a; }
            .text-label { font-weight: 700; color: #64748b; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 1px; }
            
            /* Íconos */
            .info-row { display: flex; align-items: flex-start; margin-bottom: 1.2rem; }
            .info-icon {
                font-size: 1.2rem;
                color: #0f172a;
                margin-right: 15px;
                background: #f1f5f9;
                width: 40px;
                height: 40px;
                display: flex;
                align-items: center;
                justify-content: center;
                border-radius: 12px;
            }

            /* Badges de Rol */
            .role-badge {
                position: absolute;
                bottom: 15px;
                left: 15px;
                padding: 8px 16px;
                border-radius: 20px;
                font-weight: bold;
                backdrop-filter: blur(5px);
                color: white;
                box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            }
            .bg-doctor { background-color: rgba(13, 110, 253, 0.9); } /* Azul */
            .bg-patient { background-color: rgba(13, 202, 240, 0.9); } /* Cyan */
            .bg-pharmacy { background-color: rgba(25, 135, 84, 0.9); } /* Verde */
            .bg-admin { background-color: rgba(33, 37, 41, 0.95); }   /* Negro */

        </style>
    </head>

    <div class="container py-5">
        
        <div class="mb-4">
            <a href="{{ url()->previous() }}" class="btn btn-light rounded-pill px-4 shadow-sm text-muted">
                <i class="bi bi-arrow-left me-2"></i>Regresar
            </a>
        </div>

        <div class="row g-5">
            
            {{-- COLUMNA IZQUIERDA --}}
            <div class="col-lg-4">
                
                {{-- 1. FOTO Y BADGE DE ROL --}}
                <div class="profile-photo-container mb-4">
                    <img src="{{ $user->foto ? asset('storage/' . $user->foto) : 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=random' }}" 
                         alt="{{ $user->name }}" 
                         class="profile-photo">
                    
                    {{-- Lógica de Badges --}}
                    @if($isDoctor)
                        <span class="role-badge bg-doctor"><i class="bi bi-heart-pulse-fill me-2"></i>Doctor</span>
                    @elseif($isPharmacy)
                        <span class="role-badge bg-pharmacy"><i class="bi bi-shop me-2"></i>Farmacia</span>
                    @elseif($isAdmin)
                        <span class="role-badge bg-admin"><i class="bi bi-shield-lock-fill me-2"></i>Admin</span>
                    @else
                        <span class="role-badge bg-patient"><i class="bi bi-person-fill me-2"></i>Paciente</span>
                    @endif
                </div>

                {{-- datos generales --}}
                <div class="soft-card p-4">
                    <div class="mb-3">
                        <span class="text-label d-block mb-1">Nombre </span> 
                        <span class="fs-5 fw-bold text-navy">{{ $user->name }}</span>
                    </div>

                    {{-- Cédula --}}
                    @if($isDoctor && $user->doctor)
                        <hr class="opacity-10">
                        <div class="mb-3">
                            <span class="text-label d-block mb-1">Cédula Profesional</span> 
                            <span class="text-muted font-monospace">{{ $user->doctor->cedula }}</span>
                        </div>
                    @endif

                    <hr class="opacity-10">
                    <div class="mb-0">
                        <span class="text-label d-block mb-1">Miembro desde</span> 
                        <span class="text-muted small">{{ str::title($user->created_at->translatedFormat('F Y')) }}</span>
                    </div>
                </div>

                @if(auth()->id() === $user->id || auth()->user()->role === 'admin')
                    <div class="mt-4 d-grid">
                        <a href="{{ route('users.edit', $user->id) }}" class="btn btn-dark rounded-pill py-3">
                            <i class="bi bi-pencil-square me-2"></i>Editar Perfil
                        </a>
                    </div>
                @endif
            </div>

            {{-- parte derecha --}}
            <div class="col-lg-8">
                
                {{-- contacto --}}
                <div class="soft-card p-5 mb-4">
                    <h4 class="mb-4 fw-bold text-navy">Información de Contacto</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-row">
                                <div class="info-icon"><i class="bi bi-envelope-fill"></i></div>
                                <div>
                                    <span class="fw-bold d-block text-navy">Correo Electrónico</span>
                                    <span class="text-muted">{{ $user->email }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-row">
                                <div class="info-icon"><i class="bi bi-calendar-event"></i></div>
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

                {{-- info de los pacientes --}}
                @if($isPatient)
                    <div class="soft-card p-5 mb-4 border-start border-4 border-info">
                        <h4 class="mb-4 fw-bold text-info"><i class="bi bi-person-vcard-fill me-2"></i>Ficha Médica Básica</h4>
                        
                        @if($user->patient)
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <div class="info-row mb-0">
                                        <div class="info-icon text-danger bg-danger-subtle"><i class="bi bi-droplet-fill"></i></div>
                                        <div>
                                            <span class="fw-bold d-block">Tipo de Sangre</span>
                                            <span class="fs-5 fw-bold text-navy">{{ $user->patient->tipo_sangre ?? 'No especificado' }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-row mb-0">
                                        <div class="info-icon text-warning bg-warning-subtle"><i class="bi bi-exclamation-triangle-fill"></i></div>
                                        <div>
                                            <span class="fw-bold d-block">Alergias</span>
                                            <span class="text-muted">{{ $user->patient->alergias ?? 'Ninguna conocida' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="alert alert-info bg-info-subtle border-0 rounded-4">
                                <i class="bi bi-info-circle me-2"></i> El paciente aún no ha llenado su ficha médica.
                            </div>
                        @endif
                    </div>
                @endif

                {{-- información del doctor --}}
                @if($isDoctor && $user->doctor)
                    <div class="soft-card p-5 mb-4 border-start border-4 border-primary">
                        <h4 class="mb-4 fw-bold text-primary"><i class="bi bi-clipboard2-pulse me-2"></i>Perfil Médico</h4>
                        <div class="mb-4">
                            <span class="text-label d-block mb-2">Descripción</span>
                            <p class="text-muted">{{ $user->doctor->descripcion }}</p>
                        </div>
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="info-row mb-0">
                                    <div class="info-icon text-primary bg-primary-subtle"><i class="bi bi-cash-coin"></i></div>
                                    <div>
                                        <span class="fw-bold d-block">Costo Consulta</span>
                                        <span class="text-success fs-5 fw-bold">${{ number_format($user->doctor->costo, 2) }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-row mb-0">
                                    <div class="info-icon text-primary bg-primary-subtle"><i class="bi bi-clock"></i></div>
                                    <div>
                                        <span class="fw-bold d-block">Horario</span>
                                        <span class="text-muted">
                                            {{ \Carbon\Carbon::parse($user->doctor->horario_entrada)->format('H:i') }} - 
                                            {{ \Carbon\Carbon::parse($user->doctor->horario_salida)->format('H:i') }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- mapa --}}
                @if($hasLocation)
                    <div class="soft-card p-1">
                        <div id="map" style="height: 350px; border-radius: 24px;"></div>
                        <div class="p-3 text-center">
                            <small class="text-muted">
                                <i class="bi bi-geo-alt-fill me-1 text-danger"></i>
                                Ubicación registrada
                            </small>
                        </div>
                    </div>
                @endif

            </div>
        </div>
    </div>
    
    {{-- Scripts del Mapa --}}
    @if($hasLocation)
        <script async src="https://maps.googleapis.com/maps/api/js?key=<?php echo $apiKey; ?>&callback=initMap"></script>
        <script>
            function initMap() {
                const position = { lat: <?php echo $lat; ?>, lng: <?php echo $lng; ?> };
                const map = new google.maps.Map(document.getElementById("map"), {
                    zoom: 15,
                    center: position,
                    disableDefaultUI: true
                });
                new google.maps.Marker({
                    position: position,
                    map: map,
                    title: "{{ $user->name }}"
                });
            }
        </script>
    @endif
</x-layout>