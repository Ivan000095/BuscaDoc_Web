<?php
$apiKey = "AIzaSyDzSz-VqueMjM2OEaddCFuNLSl7LsCpqzQ";
// Coordenadas para el mapa
$lat = $doctor->user->latitud ?? 16.9080;
$lng = $doctor->user->longitud ?? -92.0946;
?>

<x-layout>
    <head>
        <style>
            body {
                background-color: #f3f4f6; /* Fondo gris claro como la imagen */
            }

            /* Clase para las tarjetas blancas redondeadas */
            .soft-card {
                background: white;
                border: none;
                border-radius: 24px; /* Bordes muy redondeados */
                box-shadow: 0 10px 40px rgba(0, 0, 0, 0.05); /* Sombra suave y difusa */
                overflow: hidden;
            }

            /* Estilo de la imagen de perfil (Rectangular/Retrato) */
            .profile-photo-container {
                border-radius: 24px;
                overflow: hidden;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
                height: 400px; /* Altura fija para formato retrato */
                background-color: #e9ecef;
            }
            
            .profile-photo {
                width: 100%;
                height: 100%;
                object-fit: cover; /* Asegura que la foto llene el espacio sin deformarse */
            }

            /* Títulos y textos */
            .text-navy { color: #0f172a; } /* Azul oscuro casi negro */
            .text-label { font-weight: 700; color: #000; }

            /* Íconos de la tarjeta derecha */
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

            /* Botones estilo "Navy" */
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

            /* Input de reseña */
            .review-input {
                background-color: #f8fafc;
                border: none;
                border-radius: 50px;
                padding: 15px 25px;
            }
        </style>
    </head>

    <div class="container py-5">
        <div class="row g-5">
            
            {{-- COLUMNA IZQUIERDA: Foto y Datos Básicos --}}
            <div class="col-lg-4">
                
                {{-- 1. FOTO DE PERFIL (Estilo Retrato) --}}
                <div class="profile-photo-container mb-4">
                    <img src="{{ $doctor->user->foto ? asset('storage/' . $doctor->user->foto) : 'https://ui-avatars.com/api/?name='.urlencode($doctor->user->name) }}" 
                        alt="{{ $doctor->user->name }}" 
                        class="profile-photo">
                </div>

                {{-- 2. TARJETA DE DATOS (Nombre, Cédula, Especialidad) --}}
                <div class="soft-card p-4">
                    <div class="mb-2">
                        <span class="text-label">Doctor:</span> 
                        <span class="text-muted">{{ $doctor->user->name }}</span>
                    </div>
                    
                    <div class="mb-2">
                        <span class="text-label">Cédula profesional:</span> 
                        <span class="text-muted">{{ $doctor->cedula }}</span>
                    </div>

                    <div class="mb-0">
                        <span class="text-label">Especialidad:</span>
                        <span class="text-muted">
                             @if($doctor->especialidades->count() > 0)
                                {{ $doctor->especialidades->pluck('nombre')->join(', ') }}
                            @else
                                General
                            @endif
                        </span>
                    </div>
                    
                    {{-- Idiomas extra --}}
                    <div class="mt-2">
                         <span class="badge bg-light text-dark border rounded-pill">{{ $doctor->idiomas ?? 'Español' }}</span>
                    </div>
                </div>
            </div>

            {{-- COLUMNA DERECHA: Información Detallada --}}
            <div class="col-lg-8">
                
                {{-- 3. TARJETA DE INFORMACIÓN (Ubicación, Horarios, Contacto) --}}
                <div class="soft-card p-5 mb-4">
                    
                    {{-- Ubicación (Usando el mapa como referencia ya que no hay dirección escrita) --}}
                    <div class="info-row">
                        <div class="info-icon"><i class="bi bi-geo-alt-fill"></i></div>
                        <div>
                            <span class="fw-bold d-block">Ubicación del Consultorio</span>
                            <span class="text-muted">
                                Consultar mapa abajo <i class="bi bi-arrow-down-short"></i>
                            </span>
                        </div>
                    </div>

                    {{-- Horarios --}}
                    <div class="info-row">
                        <div class="info-icon"><i class="bi bi-clock"></i></div>
                        <div>
                            <span class="fw-bold d-block">Horarios</span>
                            <span class="text-muted">
                                {{ \Carbon\Carbon::parse($doctor->horario_entrada)->format('H:i') }} am - 
                                {{ \Carbon\Carbon::parse($doctor->horario_salida)->format('H:i') }} pm
                            </span>
                        </div>
                    </div>

                    {{-- Contacto (Email en vez de teléfono) --}}
                    <div class="info-row">
                        <div class="info-icon"><i class="bi bi-envelope-fill"></i></div> {{-- Icono de sobre --}}
                        <div>
                            <span class="fw-bold d-block">Contacto (Email)</span>
                            <span class="text-muted">{{ $doctor->user->email }}</span>
                        </div>
                    </div>
                    
                    {{-- Costo (Agregado para aprovechar el espacio) --}}
                    <div class="info-row mb-0">
                        <div class="info-icon"><i class="bi bi-cash-coin"></i></div>
                        <div>
                            <span class="fw-bold d-block">Costo Consulta</span>
                            <span class="text-success fw-bold">${{ number_format($doctor->costo, 2) }}</span>
                        </div>
                    </div>
                </div>

                {{-- 4. BOTONES DE ACCIÓN --}}
                <div class="d-flex gap-3 mb-5">
                    <button class="btn btn-navy px-4 flex-grow-1">Solicitar cita</button>
                    <button class="btn btn-navy px-4 flex-grow-1">Reportar</button>
                </div>

                {{-- 5. TARJETA DE RESEÑAS / PREGUNTAS --}}
                <div class="soft-card p-5 mb-4">
                    <h4 class="mb-4 text-center text-navy fw-normal">Reseñas / Preguntas</h4>
                    
                    <div class="position-relative mb-4">
                        <input type="text" class="form-control review-input" placeholder="Escribe tu pregunta o reseña aquí...">
                        <button class="btn btn-navy btn-sm position-absolute end-0 top-50 translate-middle-y me-2 rounded-pill px-3">
                            Enviar
                        </button>
                    </div>

                    <div class="d-flex align-items-center justify-content-between border-top pt-4">
                        <div class="d-flex align-items-center">
                            <img src="https://ui-avatars.com/api/?name=User&background=random" class="rounded-circle me-3" width="40">
                            <small class="text-muted fst-italic">"Muy profesional, excelente atención."</small>
                        </div>
                        <div class="text-warning">
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                        </div>
                    </div>
                </div>

                {{-- 6. MAPA (Agregado abajo para no romper el diseño superior) --}}
                <div class="soft-card p-1">
                    <div id="map" style="height: 300px; border-radius: 24px;"></div>
                </div>

            </div>
        </div>
    </div>

    {{-- Scripts del Mapa --}}
    <script async src="https://maps.googleapis.com/maps/api/js?key=<?php echo $apiKey; ?>&callback=initMap"></script>
    <script>
        function initMap() {
            const position = { lat: <?php echo $lat; ?>, lng: <?php echo $lng; ?> };
            const map = new google.maps.Map(document.getElementById("map"), {
                zoom: 16,
                center: position,
                disableDefaultUI: true, // Mapa limpio sin botones
            });
            new google.maps.Marker({
                position: position,
                map: map,
                title: "{{ $doctor->user->name }}"
            });
        }
    </script>
</x-layout>