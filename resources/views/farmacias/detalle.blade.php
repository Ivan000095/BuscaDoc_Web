


<x-layout>
    <style>
        body {
            background-color: #f3f4f6;
        }

        .soft-card {
            background: white;
            border: none;
            border-radius: 24px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        .profile-photo-container {
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            height: 400px;
            background-color: #e9ecef;
        }

        .profile-photo {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .text-navy {
            color: #0f172a;
        }

        .text-label {
            font-weight: 700;
            color: #000;
        }

        .info-row {
            display: flex;
            align-items: flex-start;
            margin-bottom: 1.2rem;
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

        .review-input {
            background-color: #f8fafc;
            border: none;
            border-radius: 50px;
            padding: 15px 25px;
        }

        .nav-pills .nav-link {
            color: #64748b;
            font-weight: 600;
            border-radius: 50px;
            padding: 8px 20px;
            margin-right: 10px;
        }

        .nav-pills .nav-link.active {
            background-color: #0f172a;
            color: white;
        }

        .rating {
            display: flex;
            flex-direction: row-reverse;
            justify-content: flex-end;
        }

        .rating input {
            display: none;
        }

        .rating label {
            cursor: pointer;
            width: 25px;
            font-size: 25px;
            color: #cbd5e1;
            transition: color 0.2s;
        }

        .rating label:before {
            content: '\2605';
        }

        .rating input:checked~label,
        .rating label:hover,
        .rating label:hover~label {
            color: #fbbf24;
        }

        .avatar-small {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            object-fit: cover;
        }

        .styled-textarea {
            width: 100%;
            background-color: #ffffff;
            border: 2px solid #e2e8f0;
            border-radius: 20px;
            padding: 15px 20px;
            font-size: 0.95rem;
            color: #334155;
            transition: all 0.3s ease;
            resize: none;
            min-height: 100px;
        }

        .styled-textarea:focus {
            background-color: #ffffff;
            border-color: #0f172a;
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.1);
            outline: none;
        }

        .styled-textarea::placeholder {
            color: #94a3b8;
        }

        .textarea-respuesta {
            width: 100%;
            min-height: 20px !important;
        }
    </style>
    <div class="container py-5">
        <div class="row g-4">
            <div class="col-12 col-md-4 d-flex justify-content-center">
                <div class="position-relative" style="max-width: 300px;">
                    @if($farmacia->user?->foto)
                        <img src="{{ asset('storage/' . $farmacia->user->foto) }}"
                            alt="Foto de {{ $farmacia->nom_farmacia }}" class="rounded-4 shadow-sm"
                            style="width: 100%; height: auto; max-height: 350px; object-fit: cover;">
                    @else
                        <div class="bg-light rounded-4 shadow-sm d-flex align-items-center justify-content-center"
                            style="width: 100%; height: 350px; font-size: 4rem; color: #ccc;">
                            <i class="bi bi-shop"></i>
                        </div>
                    @endif
                    <br> <br>
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
                </div>
            </div>

            <div class="col-12 col-md-8">
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body">
                        <h1 class="fw-bold text-navy mb-1">{{ $farmacia->nom_farmacia }}</h1>
                        @if($farmacia->user?->name)
                            <p class="text-muted mb-3">
                                <i class="bi bi-person me-1"></i> Administrador: {{ $farmacia->user->name }}
                            </p>
                        @endif

                        <div class="row g-3">
                            <div class="col-12">
                                <div class="d-flex align-items-start mb-3">
                                    <i class="bi bi-geo-alt text-danger fs-4 mt-1 me-3"></i>
                                    <div>
                                        <h6 class="fw-bold mb-1">Ubicación de la Farmacia</h6>
                                        <p class="text-muted mb-0"> 
                                            Consultar mapa abajo a la izquierda 
                                            <a href="#map-detail">
                                                <i class="b bi-arrow-down-left-circle-fill text-navy"></i>
                                            </a>
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="d-flex align-items-start">
                                    <i class="bi bi-clock text-primary fs-4 mt-1 me-3"></i>
                                    <div>
                                        <h6 class="fw-bold mb-1">Horario</h6>
                                        <p class="mb-0">
                                            
                                            Hora de entrada: 
                                            <small
                                                class="text-muted">{{ $farmacia->horario_entrada ?? 'Sin hora de entrada' }}</small>
                                                <br>
                                            Hora de salida:                                               
                                            <small
                                                class="text-muted">{{ $farmacia->horario_salida ?? 'Sin hora de salida' }}</small>
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
                            <div class="mt-4 pt-3 border-top text-center">
                                <p class="text-muted small mb-1 fw-bold text-uppercase" style="letter-spacing: 1px;">
                                    Calificación</p>

                                <div class="d-flex justify-content-center align-items-center gap-3">
                                    <h1 class="mb-0 fw-bold text-navy display-4">{{ $farmacia->promedio_calificacion }}
                                    </h1>

                                    <div class="text-start">
                                        <div class="text-warning">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i
                                                    class="bi {{ $i <= round($farmacia->promedio_calificacion) ? 'bi-star-fill' : 'bi-star' }}"></i>
                                            @endfor
                                        </div>

                                        <small class="text-muted d-block" style="line-height: 1.2;">
                                            Basado en <br>
                                            <strong>{{ $farmacia->reviews->count() }} opiniones</strong>
                                        </small>
                                    </div>
                                </div>
                            </div>


                        </div>
                    </div>
                </div>
                @if(Auth::user()->role == 'paciente')
                    <div class="d-flex gap-3 mb-5">
                        <button class="btn btn-navy px-4 flex-grow-1">Reportar</button>
                    </div>
                @endif
                

                @if(Auth::user()->role == 'farmacia')    
                <div class="mt-4 d-grid">
                        <a href="{{ route('users.edit', Auth::user()->id) }}" class="btn btn-dark rounded-pill py-3">
                            <i class="bi bi-pencil-square me-2"></i>Editar mi farmacia
                        </a>
                    </div> <br>
                @endif
                <!-- sección de preguntas y reeñas -->
                <div id="seccion-comentarios" class="soft-card p-4 mb-4">
                    <ul class="nav nav-pills mb-4" id="pills-tab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="pills-reviews-tab" data-bs-toggle="pill"
                                data-bs-target="#pills-reviews" type="button" role="tab">
                                <i class="bi bi-star-fill me-1"></i> Reseñas
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="pills-questions-tab" data-bs-toggle="pill"
                                data-bs-target="#pills-questions" type="button" role="tab">
                                <i class="bi bi-question-circle-fill me-1"></i> Preguntas
                            </button>
                        </li>
                    </ul>

                    <!-- cpomentarios -->
                    <div class="tab-content" id="pills-tabContent">
                        <div class="tab-pane fade show active" id="pills-reviews" role="tabpanel">
                            <div class="bg-white border p-4 rounded-4 mb-4 shadow-sm">
                                <h6 class="fw-bold mb-3 text-navy">Deja tu opinión</h6>

                                <form action="{{ route('comentarios.store') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="doctor_id" value="{{ $farmacia->user->id }}">
                                    <input type="hidden" name="tipo" value="resena">

                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="text-muted small">Califica tu experiencia:</span>
                                        <div class="rating">
                                            <div class="rating">
                                                <input type="radio" name="rating" value="5" id="5"><label
                                                    for="5"></label>
                                                <input type="radio" name="rating" value="4" id="4"><label
                                                    for="4"></label>
                                                <input type="radio" name="rating" value="3" id="3"><label
                                                    for="3"></label>
                                                <input type="radio" name="rating" value="2" id="2"><label
                                                    for="2"></label>
                                                <input type="radio" name="rating" value="1" id="1"><label
                                                    for="1"></label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <textarea name="contenido" class="styled-textarea"
                                            placeholder="Cuéntanos, ¿qué tal te parece la atención?..."
                                            required></textarea>
                                    </div>

                                    <div class="text-end">
                                        <button type="submit" class="btn btn-navy px-4 py-2 rounded-pill shadow-sm">
                                            Publicar Reseña <i class="bi bi-send-fill ms-1"></i>
                                        </button>
                                    </div>
                                </form>
                            </div>

                            <!-- lista de reseñas -->
                            <div class="reviews-list">
                                @forelse($farmacia->reviews ?? [] as $review)

                                    <div class="d-flex mb-4 border-bottom pb-3">
                                        <img src="{{ $review->autor->foto ? asset('storage/' . $review->autor->foto) : 'https://ui-avatars.com/api/?name=' . urlencode($review->autor->name) }}"
                                            class="avatar-small me-3 shadow-sm">
                                        <div class="flex-grow-1">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <h6 class="mb-0 fw-bold">{{ $review->autor->name }}</h6>
                                                <small class="text-muted">{{ $review->created_at->diffForHumans() }}</small>
                                            </div>
                                            <div class="text-warning small mb-1">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <i
                                                        class="bi {{ $i <= $review->calificacion ? 'bi-star-fill' : 'bi-star' }}"></i>
                                                @endfor
                                            </div>
                                            <p class="text-muted small mb-0">{{ $review->contenido }}</p>
                                            <br>

                                            @if ($errors->any())
                                                <div class="alert alert-danger">
                                                    <ul>
                                                        @foreach ($errors->all() as $error)
                                                            <li>{{ $error }}</li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            @endif

                                            <!-- FORm de respuestas -->
                                            <form action="{{ route('respuestas.store') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="comentario_id" value="{{ $review->id }}">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <span class="text-muted small">Responde a
                                                        {{ $review->autor->name }}</span>
                                                </div>
                                                <div class="mb-2">
                                                    <textarea name="contenido" class="styled-textarea textarea-respuesta"
                                                        cols="1" placeholder="Responde a este comentario"
                                                        required></textarea>
                                                </div>
                                                <div class="text-end">
                                                    <button type="submit"
                                                        class="btn btn-navy px-4 py-2 rounded-pill shadow-sm">
                                                        Publicar Respuesta <i class="bi bi-send-fill ms-1"></i>
                                                    </button>
                                                </div>
                                                <br>
                                            </form>

                                            <!-- respuestas -->
                                            @forelse($review->respuestas as $respuesta)
                                                <div class="d-flex mb-3 ms-5 mt-2"> {{-- ms-5: Empuja todo a la derecha --}}
                                                    <div class="flex-shrink-0">
                                                        <img src="{{ $respuesta->autor->foto ? asset('storage/' . $respuesta->autor->foto) : 'https://ui-avatars.com/api/?name=' . urlencode($respuesta->autor->name) }}"
                                                            class="avatar-small me-2 rounded-circle shadow-sm"
                                                            style="width: 35px; height: 35px;">
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <div class="bg-light p-3 rounded-4">

                                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                                <div>
                                                                    <span
                                                                        class="fw-bold text-dark small">{{ $respuesta->autor->name }}</span>
                                                                    @if($respuesta->autor->id == $farmacia->user->id)
                                                                        <span class="badge bg-primary text-white ms-1"
                                                                            style="font-size: 0.65rem;">Propietario</span>
                                                                    @endif
                                                                </div>
                                                                <small class="text-muted" style="font-size: 0.7rem;">
                                                                    {{ $respuesta->created_at->diffForHumans() }}
                                                                </small>
                                                            </div>
                                                            <p class="text-dark small mb-0 lh-sm">{{ $respuesta->contenido }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            @empty
                                                {{-- No mostrar nada si no hay respuestas --}}
                                            @endforelse
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-4 text-muted">
                                        <i class="bi bi-stars fs-3 d-block mb-2"></i>
                                        Aún no hay reseñas.
                                    </div>
                                @endforelse
                            </div>
                        </div>

                        <!-- preguntas -->
                        <div class="tab-pane fade" id="pills-questions" role="tabpanel">
                            <div class="questions-list">
                                @forelse($doctor->questions ?? [] as $question)
                                    <div class="mb-4 border-bottom pb-3"> {{-- 1. LA PREGUNTA ORIGINAL --}}
                                        <div class="d-flex mb-3">
                                            <img src="{{ $question->autor->foto ? asset('storage/' . $question->autor->foto) : 'https://ui-avatars.com/api/?name=' . urlencode($question->autor->name) }}"
                                                class="avatar-small me-3 shadow-sm">

                                            <div class="flex-grow-1">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <h6 class="mb-0 fw-bold">{{ $question->autor->name }}</h6>
                                                    <small
                                                        class="text-muted">{{ $question->created_at->diffForHumans() }}</small>
                                                </div>
                                                <p class="text-muted small mb-0 mt-1">{{ $question->contenido }}</p>
                                            </div>
                                        </div>

                                        <div class="ms-5">
                                            <div class="card border-0 bg-light rounded-4 p-3 mb-3">
                                                <form action="{{ route('respuestas.store') }}" method="POST">
                                                    @csrf
                                                    {{-- Vinculamos la respuesta al ID de la pregunta actual --}}
                                                    <input type="hidden" name="comentario_id" value="{{ $question->id }}">

                                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                                        <span class="text-muted small fw-bold">Responder a
                                                            {{ $question->autor->name }}</span>
                                                    </div>

                                                    <div class="d-flex gap-2">
                                                        <textarea name="contenido"
                                                            class="styled-textarea textarea-respuesta" rows="1"
                                                            placeholder="Escribe una respuesta..." style="min-height: 40px;"
                                                            required></textarea>

                                                        <button type="submit"
                                                            class="btn btn-navy btn-sm rounded-circle shadow-sm d-flex align-items-center justify-content-center"
                                                            style="width: 40px; height: 40px; flex-shrink: 0;">
                                                            <i class="bi bi-send-fill"></i>
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>

                                            @forelse($question->respuestas as $respuesta)
                                                <div class="d-flex mb-3 mt-2">
                                                    <div class="flex-shrink-0">
                                                        <img src="{{ $respuesta->autor->foto ? asset('storage/' . $respuesta->autor->foto) : 'https://ui-avatars.com/api/?name=' . urlencode($respuesta->autor->name) }}"
                                                            class="avatar-small me-2 rounded-circle shadow-sm"
                                                            style="width: 35px; height: 35px;">
                                                    </div>

                                                    <div class="flex-grow-1">
                                                        <div class="bg-light p-3 rounded-4">
                                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                                <div>
                                                                    <span
                                                                        class="fw-bold text-dark small">{{ $respuesta->autor->name }}</span>
                                                                    {{-- Badge si es la farmacia --}}
                                                                    @if($respuesta->autor->id == $farmacia->user->id)
                                                                        <span class="badge bg-primary text-white ms-1"
                                                                            style="font-size: 0.65rem;">Propietario</span>
                                                                    @endif
                                                                </div>
                                                                <small class="text-muted" style="font-size: 0.7rem;">
                                                                    {{ $respuesta->created_at->diffForHumans() }}
                                                                </small>
                                                            </div>
                                                            <p class="text-dark small mb-0 lh-sm">{{ $respuesta->contenido }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            @empty
                                                {{-- No hay respuestas aún --}}
                                            @endforelse

                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-4 text-muted">
                                        <i class="bi bi-chat-square-dots fs-3 d-block mb-2"></i>
                                        Aún no hay preguntas. ¡Sé el primero!
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-center mt-5">
                <a href="{{ route('farmacias.catalogo') }}" class="btn btn-outline-secondary rounded-pill px-4">
                    ← Volver al catálogo
                </a>
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