<?php
$apiKey = "AIzaSyDzSz-VqueMjM2OEaddCFuNLSl7LsCpqzQ";

$requestData = [
    "origin" => ["location" => ["latLng" => ["latitude" => 16.768256, "longitude" => -93.1357181]]],
    "destination" => ["location" => ["latLng" => ["latitude" => 17.9072, "longitude" => -91.0961]]],
    "travelMode" => "DRIVE"
];

$ch = curl_init('https://routes.googleapis.com/directions/v2:computeRoutes');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestData));
$headers = [
    'Content-Type: application/json',
    'X-Goog-Api-Key: ' . $apiKey,
    'X-Goog-FieldMask: routes.duration,routes.distanceMeters,routes.polyline.encodedPolyline'
];
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
$response = curl_exec($ch);
curl_close($ch);

$data = json_decode($response, true);

$encodedString = "";
if (!empty($data['routes'])) {
    $encodedString = $data['routes'][0]['polyline']['encodedPolyline'];
}
?>

<x-layout>

    <head>
        <style>
            #map {
                height: 250px;
                width: 25%;
            }

            /* Estilos específicos para esta vista */
            .profile-header {
                background: linear-gradient(to bottom right, var(--custom-dark-blue), #004e8d);
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
                color: var(--custom-dark-blue);
                display: flex;
                align-items: center;
                justify-content: center;
                border-radius: 10px;
                margin-right: 15px;
                font-size: 1.2rem;
            }

            /* Mapa Ajustado */
            #map {
                height: 350px;
                /* Un poco más alto para que luzca */
                width: 100%;
                /* Ancho completo del contenedor */
                border-radius: 15px;
            }
        </style>
    </head>
    <!-- <div id="map"></div> -->

    <div class="profile-header text-center">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    {{-- Imagen Circular --}}
                    <img src="{{Storage::url($doctor->image) }}" alt="{{ $doctor->name }}"
                        class="rounded-circle profile-img mb-3">

                    <h1 class="fw-bold display-5">{{ $doctor->name }}</h1>
                    <p class="fs-4 opacity-75"><i class="bi bi-award-fill me-2"></i>{{ $doctor->especialidad }}</p>

                    <div class="d-flex justify-content-center gap-2 mt-3">
                        <span class="badge bg-light text-dark rounded-pill px-3 py-2">
                            <i class="bi bi-translate me-1"></i> {{ $doctor->idioma }}
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
                        <p class="text-end text-muted small">Registrado el: {{ $doctor->fecha }}</p>
                    </div>
                </div>

                {{-- Tarjeta del Mapa (Ruta) --}}
                <div class="card info-card shadow-lg border-0">
                    <div class="card-header bg-white border-0 pt-4 px-4">
                        <h4 class="fw-bold text-primary"><i class="bi bi-map-fill me-2"></i>Ruta de Llegada</h4>
                    </div>
                    <div class="card-body p-4">
                        <div id="map" class="shadow-sm"></div>
                    </div>
                </div>
            </div>

            {{-- COLUMNA DERECHA: Detalles Rápidos y Contacto --}}
            <div class="col-lg-4">

                {{-- Tarjeta de Costos y Horarios --}}
                <div class="card info-card shadow-sm mb-4 bg-primary text-white">
                    <div class="card-body p-4">
                        <h5 class="mb-3"><i class="bi bi-cash-coin me-2"></i>Costo de Consulta</h5>
                        <h2 class="fw-bold mb-4">{{ $doctor->costos }}</h2>

                        <hr class="border-white opacity-25">

                        <h5 class="mb-3"><i class="bi bi-clock me-2"></i>Horario de Atención</h5>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>Entrada:</span>
                            <span class="fw-bold">{{ $doctor->horarioentrada }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span>Salida:</span>
                            <span class="fw-bold">{{ $doctor->horariosalida }}</span>
                        </div>
                    </div>
                </div>

                {{-- Tarjeta de Contacto --}}
                <div class="card info-card shadow-sm">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-4">Información de Contacto</h5>

                        <div class="d-flex align-items-center mb-3">
                            <div class="icon-box">
                                <i class="bi bi-telephone-fill"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block">Teléfono</small>
                                <span class="fw-bold">{{ $doctor->telefono }}</span>
                            </div>
                        </div>

                        <div class="d-flex align-items-center mb-3">
                            <div class="icon-box">
                                <i class="bi bi-geo-alt-fill"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block">Dirección</small>
                                <span>{{ $doctor->direccion }}</span>
                            </div>
                        </div>

                        <div class="d-grid gap-2 mt-4">
                            <a href="tel:{{ $doctor->telefono }}" class="btn btn-outline-primary rounded-pill">
                                Llamar Ahora
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script async
        src="https://maps.googleapis.com/maps/api/js?key=<?php echo $apiKey; ?>&libraries=geometry&callback=initMap"></script>

    <script>
        function initMap() {
            const encodedPolyline = <?php echo json_encode($encodedString); ?>;

            // Coordenadas iniciales para centrar el mapa (Ocosingo aprox)
            const centro = { lat: 16.9072, lng: -92.0961 };

            // 2. Crear el mapa
            const map = new google.maps.Map(document.getElementById("map"), {
                zoom: 8,
                center: centro,
            });

            if (encodedPolyline) {
                // 3. Decodificar la línea mágica
                // La API de geometría convierte ese string raro en coordenadas
                const decodedPath = google.maps.geometry.encoding.decodePath(encodedPolyline);

                // 4. Dibujar la línea azul (Polyline)
                const routeLine = new google.maps.Polyline({
                    path: decodedPath,
                    geodesic: true,
                    strokeColor: "#2196F3", // Color azul Google
                    strokeOpacity: 1.0,
                    strokeWeight: 4,
                });

                routeLine.setMap(map);

                // 5. Ajustar el zoom automáticamente para que se vea toda la ruta
                const bounds = new google.maps.LatLngBounds();
                decodedPath.forEach((point) => {
                    bounds.extend(point);
                });
                map.fitBounds(bounds);
            } else {
                alert("No se pudo obtener la ruta desde el servidor PHP.");
            }
        }
    </script>
</x-layout>