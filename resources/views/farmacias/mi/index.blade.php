<x-layout>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-9">
                <div class="mb-4">
                    <a href="{{ route('home') }}" class="btn btn-outline-secondary rounded-pill px-3">
                        ← Regresar
                    </a>
                </div>

                <div class="row g-4">
                    <div class="col-12 col-md-4">
                        <div class="bg-light rounded-4 p-4 text-center shadow-sm" style="height: 280px; display: flex; flex-direction: column; justify-content: space-between;">
                            <div class="d-flex justify-content-center">
                                <span class="display-1 fw-bold text-dark">
                                    {{ strtoupper(substr($farmacia->nom_farmacia, 0, 2)) }}
                                </span>
                            </div>
                            <div>
                                <span class="badge bg-success text-white rounded-pill px-3 py-2">
                                    <i class="bi bi-shop me-1"></i> Farmacia
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-md-8">
                        <div class="card border-0 shadow-sm rounded-4">
                            <div class="card-body">
                                <h4 class="fw-bold mb-4">Información de la Farmacia</h4>

                                <div class="row g-3">
                                    <div class="col-12">
                                        <div class="d-flex align-items-start">
                                            <i class="bi bi-shop text-primary fs-4 mt-1 me-3"></i>
                                            <div>
                                                <h6 class="fw-bold mb-1">Nombre Comercial</h6>
                                                <p class="mb-0">{{ $farmacia->nom_farmacia ?? '—' }}</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="d-flex align-items-start">
                                            <i class="bi bi-telephone text-success fs-4 mt-1 me-3"></i>
                                            <div>
                                                <h6 class="fw-bold mb-1">Teléfono</h6>
                                                <p class="mb-0">{{ $farmacia->telefono ?? '—' }}</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="d-flex align-items-start">
                                            <i class="bi bi-clock text-info fs-4 mt-1 me-3"></i>
                                            <div>
                                                <h6 class="fw-bold mb-1">Horario</h6>
                                                <p class="mb-0">
                                                    @if($farmacia->horario && $farmacia->dias_op)
                                                        {{ $farmacia->horario }}<br>
                                                        <small class="text-muted">{{ $farmacia->dias_op }}</small>
                                                    @else
                                                        <span class="text-muted">No especificado</span>
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="d-flex align-items-start">
                                            <i class="bi bi-file-earmark-text text-secondary fs-4 mt-1 me-3"></i>
                                            <div>
                                                <h6 class="fw-bold mb-1">RFC</h6>
                                                <p class="mb-0">{{ $farmacia->rfc ?? '—' }}</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="d-flex align-items-start">
                                            <i class="bi bi-info-circle text-warning fs-4 mt-1 me-3"></i>
                                            <div>
                                                <h6 class="fw-bold mb-1">Descripción</h6>
                                                <p class="mb-0 text-muted">
                                                    {{ $farmacia->descripcion ?? 'Sin descripción.' }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card border-0 shadow-sm rounded-4 mt-4">
                            <div class="card-body">
                                <h6 class="text-muted small mb-1">REGISTRADA DESDE</h6>
                                <p class="mb-0">
                                    {{ \Carbon\Carbon::parse($farmacia->created_at)->format('F Y') }}
                                </p>
                            </div>
                        </div>

                        <div class="card border-0 shadow-sm rounded-4 mt-4">
                            <div class="card-header bg-light d-flex align-items-center py-3">
                                <i class="bi bi-geo-alt me-2 text-danger"></i>
                                <h5 class="mb-0 fw-bold">Ubicación</h5>
                            </div>
                            <div class="card-body p-0">
                                @if($farmacia->user?->latitud && $farmacia->user?->longitud)
                                    <div id="map-mini" style="height: 250px; width: 100%;"></div>
                                @else
                                    <div class="text-center py-4 text-muted">
                                        <i class="bi bi-map fs-2"></i>
                                        <p class="mt-2 mb-0">Ubicación no registrada</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="mt-4 text-center">
                            <a href="{{ route('farmacias.mi.editar') }}" class="btn btn-dark rounded-pill px-4 py-3 fw-bold w-100 w-md-auto">
                                <i class="bi bi-pencil me-2"></i> Editar Perfil
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @section('js')
        @if($farmacia->user?->latitud && $farmacia->user?->longitud)
            <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDzSz-VqueMjM2OEaddCFuNLSl7LsCpqzQ"></script>
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    const lat = {{ $farmacia->user->latitud }};
                    const lng = {{ $farmacia->user->longitud }};
                    const map = new google.maps.Map(document.getElementById("map-mini"), {
                        zoom: 15,
                        center: { lat, lng },
                        disableDefaultUI: true,
                        styles: [{ featureType: "poi", elementType: "labels", stylers: [{ visibility: "off" }] }]
                    });
                    new google.maps.Marker({
                        position: { lat, lng },
                        map: map,
                        title: "{{ $farmacia->nom_farmacia }}"
                    });
                });
            </script>
        @endif
    @endsection
</x-layout>