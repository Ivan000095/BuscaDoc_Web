<?php
$apiKey = "AIzaSyDzSz-VqueMjM2OEaddCFuNLSl7LsCpqzQ";
$lat = $doctor->user->latitud ?? 16.9080;
$lng = $doctor->user->longitud ?? -92.0946;
?>

<x-layout>

    <head>
        <style>
            .profile-header {
                background: linear-gradient(to bottom right, #0d6efd, #004e8d); /* Azul Bootstrap */
                color: white;
                padding-top: 3rem;
                padding-bottom: 3rem;
                border-radius: 0 0 20px 20px;
                margin-bottom: 2rem;
            }

            .profile-img {
                width: 180px;
                height: 180px;
                object-fit: cover;
                border: 5px solid white;
                box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .15);
                background-color: #fff;
            }

            .info-card {
                border: none;
                border-radius: 15px;
                transition: transform 0.3s ease;
            }

            .info-card:hover {
                transform: translateY(-5px);
            }

            .icon-box {
                width: 40px;
                height: 40px;
                background-color: #eef2f6;
                color: #0d6efd;
                display: flex;
                align-items: center;
                justify-content: center;
                border-radius: 10px;
                margin-right: 15px;
                font-size: 1.2rem;
            }

            #map {
                height: 350px;
                width: 100%;
                border-radius: 15px;
            }
        </style>
    </head>

    <div class="profile-header text-center">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <img src="{{ $doctor->user->foto ? asset('storage/' . $doctor->user->foto) : 'https://ui-avatars.com/api/?name='.urlencode($doctor->user->name) }}" 
                        alt="{{ $doctor->user->name }}"
                        class="rounded-circle profile-img mb-3">

                    <h1 class="fw-bold display-5">{{$doctor->user->name }}</h1>
                    
                    {{-- Especialidades: Es una relación de muchos a muchos --}}
                    <p class="fs-4 opacity-75">
                        <i class="bi bi-award-fill me-2"></i>
                        @if($doctor->especialidades->count() > 0)
                            {{ $doctor->especialidades->pluck('nombre')->join(', ') }}
                        @else
                            Sin especialidad registrada
                        @endif
                    </p>

                    <div class="d-flex justify-content-center gap-2 mt-3">
                        <span class="badge bg-light text-dark rounded-pill px-3 py-2">
                            <i class="bi bi-translate me-1"></i> {{ $doctor->idiomas }}
                        </span>
                        <span class="badge bg-warning text-dark rounded-pill px-3 py-2">
                            <i class="bi bi-card-heading me-1"></i> Cédula: {{ $doctor->cedula }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container pb-5">
        <div class="row g-4">

            {{-- COLUMNA IZQUIERDA: Información Principal --}}
            <div class="col-lg-8">
                {{-- Tarjeta de Descripción --}}
                <div class="card info-card shadow-sm mb-4 h-20">
                    <div class="card-body p-4">
                        <h4 class="card-title fw-bold text-primary mb-3">Sobre el Especialista</h4>
                        <p class="card-text text-muted lead" style="font-size: 1.1rem;">
                            {{ $doctor->descripcion }}
                        </p>
                        {{-- Fecha de creación viene de 'created_at' --}}
                        <p class="text-end text-muted small mt-3">
                            Registrado desde: {{ $doctor->created_at->format('d/m/Y') }}
                        </p>
                    </div>
                </div>

                {{-- Tarjeta del Mapa (Ubicación Fija) --}}
                <div class="card info-card shadow-lg border-0">
                    <div class="card-header bg-white border-0 pt-4 px-4">
                        <h4 class="fw-bold text-primary"><i class="bi bi-geo-alt-fill me-2"></i>Ubicación del Consultorio</h4>
                    </div>
                    <div class="card-body p-4">
                        <div id="map" class="shadow-sm"></div>
                        <div class="mt-2 text-muted small text-center">
                            <i class="bi bi-info-circle"></i> Esta es la ubicación registrada del consultorio.
                        </div>
                    </div>
                </div>
            </div>

            {{-- COLUMNA DERECHA: Detalles Rápidos y Contacto --}}
            <div class="col-lg-4">

                {{-- Tarjeta de Costos y Horarios --}}
                <div class="card info-card shadow-sm mb-4 bg-primary text-white">
                    <div class="card-body p-4">
                        <h5 class="mb-3"><i class="bi bi-cash-coin me-2"></i>Costo de Consulta</h5>
                        <h2 class="fw-bold mb-4">${{ number_format($doctor->costo, 2) }}</h2>

                        <hr class="border-white opacity-25">

                        <h5 class="mb-3"><i class="bi bi-clock me-2"></i>Horario de Atención</h5>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>Entrada:</span>
                            {{-- Formateamos la hora para quitar segundos si es necesario --}}
                            <span class="fw-bold">{{ \Carbon\Carbon::parse($doctor->horario_entrada)->format('H:i') }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span>Salida:</span>
                            <span class="fw-bold">{{ \Carbon\Carbon::parse($doctor->horario_salida)->format('H:i') }}</span>
                        </div>
                    </div>
                </div>

                {{-- Tarjeta de Contacto --}}
                <div class="card info-card shadow-sm">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-4">Información de Contacto</h5>

                        {{-- EMAIL (Sustituye a Teléfono porque Teléfono ya no está en BD) --}}
                        <div class="d-flex align-items-center mb-3">
                            <div class="icon-box">
                                <i class="bi bi-envelope-fill"></i>
                            </div>
                            <div class="overflow-hidden">
                                <small class="text-muted d-block">Correo Electrónico</small>
                                <span class="fw-bold text-truncate d-block" title="{{ $doctor->user->email }}">
                                    {{ $doctor->user->email }}
                                </span>
                            </div>
                        </div>

                        {{-- Mensaje sobre dirección (Ya que no hay campo dirección texto, usamos mapa) --}}
                        <div class="d-flex align-items-center mb-3">
                            <div class="icon-box">
                                <i class="bi bi-pin-map-fill"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block">Ubicación</small>
                                <span>Ver en el mapa adjunto</span>
                            </div>
                        </div>

                        <div class="d-grid gap-2 mt-4">
                            <a href="mailto:{{ $doctor->user->email }}" class="btn btn-outline-primary rounded-pill">
                                <i class="bi bi-envelope me-2"></i> Contactar
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- SCRIPTS DEL MAPA --}}
    <script async
        src="https://maps.googleapis.com/maps/api/js?key=<?php echo $apiKey; ?>&callback=initMap">
    </script>

    <script>
        function initMap() {
            // Coordenadas del doctor
            const doctorLocation = { 
                lat: <?php echo $lat; ?>, 
                lng: <?php echo $lng; ?> 
            };

            // 1. Crear el mapa centrado en el doctor
            const map = new google.maps.Map(document.getElementById("map"), {
                zoom: 15, // Zoom más cercano para ver la ubicación exacta
                center: doctorLocation,
            });

            // 2. Agregar el Marcador Rojo
            new google.maps.Marker({
                position: doctorLocation,
                map: map,
                title: "<?php echo $doctor->user->name; ?>",
                animation: google.maps.Animation.DROP // Efecto de caída al cargar
            });
        }
    </script>
</x-layout>