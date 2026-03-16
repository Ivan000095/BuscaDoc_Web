<?php
$apiKey = env('API_KEY');
$lat = $farmacia->user->latitud ?? 16.9080;
$lng = $farmacia->user->longitud ?? -92.0946;
?>
<x-layout>
<head>
<style>
body { background-color: #f3f4f6; }
.soft-card {
    background: white;
    border: none;
    border-radius: 24px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.05);
    overflow: hidden;
}
.profile-photo-container {
    border-radius: 24px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0,0,0,0.08);
    height: 400px;
    background-color: #e9ecef;
}
.profile-photo {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.text-navy { color: #0f172a; }
.text-label { font-weight: 700; color: #000; }
.info-row {
    display: flex;
    align-items: flex-start;
    margin-bottom: 1.2rem;
}
.info-icon {
    font-size: 1.3rem;
    color: #0f172a;
    margin-right: 15px;
    width: 24px;
    text-align: center;
}
.btn-navy {
    background-color: #0f172a;
    color: white;
    border-radius: 50px;
    padding: 10px 25px;
    font-weight: 500;
    border: none;
    transition: transform 0.2s;
}
.btn-navy:hover {
    background-color: #1e293b;
    color: white;
    transform: translateY(-2px);
}
</style>
</head>

@if(session('success'))
<div id="notification-pill" class="pill-notification">
    <div class="pill-icon"><i class="bi bi-check-lg"></i></div>
    <span>{{ session('success') }}</span>
</div>
@endif

<div class="container py-5">
    <div class="row g-5">
        {{-- Columna Izquierda --}}
        <div class="col-lg-4">
            <div class="profile-photo-container mb-4">
                <img src="{{ $farmacia->user->foto ? asset('storage/' . $farmacia->user->foto) : 'https://ui-avatars.com/api/?name=' . urlencode($farmacia->user->name) }}"
                    alt="{{ $farmacia->nom_farmacia }}" 
                    class="profile-photo">
            </div>

            <div class="soft-card p-4">
                <div class="mb-2">
                    <span class="text-label">Farmacia:</span>
                    <span class="text-muted">{{ $farmacia->nom_farmacia }}</span>
                </div>
                <div class="mb-2">
                    <span class="text-label">Propietario:</span>
                    <span class="text-muted">{{ $farmacia->user->name }}</span>
                </div>
                <div class="mb-2">
                    <span class="text-label">RFC:</span>
                    <span class="text-muted">{{ $farmacia->rfc ?? 'No registrado' }}</span>
                </div>
                <div class="mb-0">
                    <span class="text-label">Teléfono:</span>
                    <span class="text-muted">{{ $farmacia->telefono ?? 'No registrado' }}</span>
                </div>
            </div>

            <br>
            <div class="soft-card p-1">
                <div id="map" style="height: 300px; border-radius: 24px;"></div>
            </div>
        </div>

        {{-- Columna Derecha --}}
        <div class="col-lg-8">
            <div class="soft-card p-5 mb-4">
                <div class="info-row">
                    <div class="info-icon"><i class="bi bi-geo-alt-fill"></i></div>
                    <div>
                        <span class="fw-bold d-block">Ubicación</span>
                        <span class="text-muted">Consultar mapa abajo <i class="bi bi-arrow-down-short"></i></span>
                    </div>
                </div>

                {{-- Horarios --}}
                <div class="info-row">
                    <div class="info-icon"><i class="bi bi-clock"></i></div>
                    <div>
                        <span class="fw-bold d-block">Horario de Atención</span>
                        <span class="text-muted">
                            @if($farmacia->horario_entrada && $farmacia->horario_salida)
                                {{ \Carbon\Carbon::parse($farmacia->horario_entrada)->format('H:i') }} - 
                                {{ \Carbon\Carbon::parse($farmacia->horario_salida)->format('H:i') }}
                            @else
                                No especificado
                            @endif
                        </span>
                    </div>
                </div>

                {{-- Días de operación --}}
                @if($farmacia->dias_op)
                    <div class="info-row">
                        <div class="info-icon"><i class="bi bi-calendar-week"></i></div>
                        <div>
                            <span class="fw-bold d-block">Días de Operación</span>
                            <span class="text-muted">{{ $farmacia->dias_op }}</span>
                        </div>
                    </div>
                @endif

                {{-- Email --}}
                <div class="info-row">
                    <div class="info-icon"><i class="bi bi-envelope-fill"></i></div>
                    <div>
                        <span class="fw-bold d-block">Contacto (Email)</span>
                        <span class="text-muted">{{ $farmacia->user->email }}</span>
                    </div>
                </div>

                {{-- Descripción --}}
                @if($farmacia->descripcion)
                    <div class="info-row mb-0">
                        <div class="info-icon"><i class="bi bi-file-text"></i></div>
                        <div>
                            <span class="fw-bold d-block">Descripción</span>
                            <span class="text-muted">{{ $farmacia->descripcion }}</span>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Botones de Acción --}}
            @if(Auth::check() && Auth::user()->role == 'paciente')
                <div class="d-flex gap-3 mb-5">
                    <a href="{{ route('reportes.user.create', ['reportado_id' => $farmacia->user->id]) }}" 
                       class="btn btn-navy px-4 flex-grow-1">
                        <i class="bi bi-person-fill-exclamation"></i> Reportar
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<script async src="https://maps.googleapis.com/maps/api/js?key={{ $apiKey }}&callback=initMap"></script>
<script>
function initMap() {
    const position = { lat: <?php echo $lat; ?>, lng: <?php echo $lng; ?> };
    const map = new google.maps.Map(document.getElementById("map"), {
        zoom: 16,
        center: position,
        disableDefaultUI: true,
    });
    new google.maps.Marker({
        position: position,
        map: map,
        title: "{{ $farmacia->nom_farmacia }}"
    });
}
</script>
</x-layout>