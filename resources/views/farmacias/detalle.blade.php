<x-layout>
    <div class="container py-5">
        <div class="row g-4">
            <!-- Imagen grande (izquierda) -->
            <div class="col-12 col-md-4 d-flex justify-content-center">
                <div class="position-relative" style="max-width: 300px;">
                    @if($farmacia->user?->foto)
                        <img src="{{ asset('storage/' . $farmacia->user->foto) }}"
                             alt="Foto de {{ $farmacia->nom_farmacia }}"
                             class="rounded-4 shadow-sm"
                             style="width: 100%; height: auto; max-height: 350px; object-fit: cover;">
                    @else
                        <div class="bg-light rounded-4 shadow-sm d-flex align-items-center justify-content-center"
                             style="width: 100%; height: 350px; font-size: 4rem; color: #ccc;">
                            <i class="bi bi-shop"></i>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Información principal (derecha) -->
            <div class="col-12 col-md-8">
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body">
                        <h1 class="fw-bold text-primary mb-1">{{ $farmacia->nom_farmacia }}</h1>
                        @if($farmacia->user?->name)
                            <p class="text-muted mb-3">
                                <i class="bi bi-person me-1"></i> Dueño: {{ $farmacia->user->name }}
                            </p>
                        @endif

                        <!-- Datos estructurados -->
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="d-flex align-items-start mb-3">
                                    <i class="bi bi-geo-alt text-danger fs-4 mt-1 me-3"></i>
                                    <div>
                                        <h6 class="fw-bold mb-1">Ubicación de la Farmacia</h6>
                                        <p class="text-muted mb-0">
                                            Consultar mapa abajo ↓
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="d-flex align-items-start">
                                    <i class="bi bi-clock text-primary fs-4 mt-1 me-3"></i>
                                    <div>
                                        <h6 class="fw-bold mb-1">Horarios</h6>
                                        <p class="mb-0">
                                            {{ $farmacia->horario ?? 'No especificado' }}
                                            <br>
                                            <small class="text-muted">{{ $farmacia->dias_op ?? 'Todos los días' }}</small>
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="d-flex align-items-start">
                                    <i class="bi bi-envelope text-success fs-4 mt-1 me-3"></i>
                                    <div>
                                        <h6 class="fw-bold mb-1">Contacto (Email)</h6>
                                        <p class="mb-0">
                                            {{ $farmacia->user?->email ?? 'No disponible' }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="d-flex align-items-start">
                                    <i class="bi bi-telephone text-info fs-4 mt-1 me-3"></i>
                                    <div>
                                        <h6 class="fw-bold mb-1">Teléfono</h6>
                                        <p class="mb-0">
                                            {{ $farmacia->telefono ?? 'No disponible' }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            @if($farmacia->rfc)
                                <div class="col-md-6">
                                    <div class="d-flex align-items-start">
                                        <i class="bi bi-file-earmark-text text-secondary fs-4 mt-1 me-3"></i>
                                        <div>
                                            <h6 class="fw-bold mb-1">RFC</h6>
                                            <p class="mb-0">{{ $farmacia->rfc }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                
        <!-- Mini mapa (debajo de todo) -->
        <div class="row mt-5">
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="card-header bg-light d-flex align-items-center py-3">
                        <i class="bi bi-geo-alt me-2 text-danger"></i>
                        <h5 class="mb-0 fw-bold">Ubicación en Mapa</h5>
                    </div>
                    <div class="card-body p-0">
                        @if($farmacia->user?->latitud && $farmacia->user?->longitud)
                            <div id="map-detail" style="height: 300px; width: 100%;"></div>
                        @else
                            <div class="text-center py-5 text-muted">
                                <i class="bi bi-map fs-2"></i>
                                <p class="mt-2 mb-0">Ubicación no disponible</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Volver -->
        <div class="text-center mt-5">
            <a href="{{ route('farmacias.catalogo') }}" class="btn btn-outline-secondary rounded-pill px-4">
                ← Volver al catálogo
            </a>
        </div>
    </div>

    @section('js')
        @if($farmacia->user?->latitud && $farmacia->user?->longitud)
            <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDzSz-VqueMjM2OEaddCFuNLSl7LsCpqzQ"></script>
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    const lat = {{ $farmacia->user->latitud }};
                    const lng = {{ $farmacia->user->longitud }};
                    const map = new google.maps.Map(document.getElementById("map-detail"), {
                        zoom: 15,
                        center: { lat, lng },
                        disableDefaultUI: true,
                        styles: [{ featureType: "poi", elementType: "labels", stylers: [{ visibility: "off" }] }]
                    });
                    new google.maps.Marker({
                        position: { lat, lng },
                        map: map,
                        title: "{{ $farmacia->nom_farmacia }}",
                        icon: {
                            url: "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' width='30' height='30' fill='%23d32f2f'%3E%3Cpath d='M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z'/%3E%3C/svg%3E",
                            scaledSize: new google.maps.Size(30, 30)
                        }
                    });
                });
            </script>
        @endif
    @endsection
</x-layout>