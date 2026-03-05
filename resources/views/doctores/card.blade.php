<?php
$apiKey = env('API_KEY');
// Coordenadas para el mapa
$lat = $doctor->user->latitud ?? 16.9080;
$lng = $doctor->user->longitud ?? -92.0946;
?>

<x-layout>

    <head>
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
    </head>
    @if(session('success'))
        <div id="notification-pill" class="pill-notification">
            <div class="pill-icon"><i class="bi bi-check-lg"></i></div>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    <div class="container py-5">
        <div class="row g-5">

            <div class="col-lg-4">
                <div class="profile-photo-container mb-4">
                    <img src="{{ $doctor->user->foto ? asset('storage/' . $doctor->user->foto) : 'https://ui-avatars.com/api/?name=' . urlencode($doctor->user->name) }}"
                        alt="{{ $doctor->user->name }}" class="profile-photo">
                </div>

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
                    <div class="mt-2">
                        <span
                            class="badge bg-light text-dark border rounded-pill">{{ $doctor->idiomas ?? 'Español' }}</span>
                    </div>

                    <div class="mt-4 pt-3 border-top text-center">
                        <p class="text-muted small mb-1 fw-bold text-uppercase" style="letter-spacing: 1px;">
                            Calificación</p>

                        <div class="d-flex justify-content-center align-items-center gap-3">
                            <h1 class="mb-0 fw-bold text-navy display-4">{{ $doctor->promedio_calificacion }}</h1>

                            <div class="text-start">
                                <div class="text-warning">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i
                                            class="bi {{ $i <= round($doctor->promedio_calificacion) ? 'bi-star-fill' : 'bi-star' }}"></i>
                                    @endfor
                                </div>

                                <small class="text-muted d-block" style="line-height: 1.2;">
                                    Basado en <br>
                                    <strong>{{ $doctor->reviews->count() }} opiniones</strong>
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
                <br>
                <div class="soft-card p-1">
                    <div id="map" style="height: 300px; border-radius: 24px;"></div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="soft-card p-5 mb-4">
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
                    <div class="info-row">
                        <div class="info-icon"><i class="bi bi-envelope-fill"></i></div> {{-- Icono de sobre --}}
                        <div>
                            <span class="fw-bold d-block">Contacto (Email)</span>
                            <span class="text-muted">{{ $doctor->user->email }}</span>
                        </div>
                    </div>
                    <div class="info-row mb-0">
                        <div class="info-icon"><i class="bi bi-cash-coin"></i></div>
                        <div>
                            <span class="fw-bold d-block">Costo Consulta</span>
                            <span class="text-success fw-bold">${{ number_format($doctor->costo, 2) }}</span>
                        </div>
                    </div>
                </div>

                @if(Auth::user()->role == 'paciente')
                    <div class="d-flex gap-3 mb-5">
                        <button type="button" class="btn btn-navy px-4 flex-grow-1" data-bs-toggle="modal"
                            data-bs-target="#agendarCitaModal">
                            <i class="bi bi-calendar-event-fill"></i> Agendar Cita
                        </button>
                        <a type="button" class="btn btn-navy px-4 flex-grow-1"
                            href="{{ route('mensajes.show', parameters: $doctor->user->id) }}">
                            <i class="bi bi-chat-dots-fill"></i> Enviar mensaje
                        </a>
                        <a type="button" class="btn btn-navy px-4 flex-grow-1"
                            href="{{ route('reportes.user.create', ['reportado_id' =>  $doctor->user->id]) }}">
                            <i class="bi bi-person-fill-exclamation"></i> Reportar
                        </a>
                    </div>
                @endif

                @php
                    $puedoResenar = false;
                    if (Auth::check() && Auth::user()->role == 'paciente' && Auth::user()->patient) {
                        $puedoResenar = \App\Models\Cita::where('paciente_id', Auth::user()->patient->id)
                            ->where('doctor_id', $doctor->id)
                            ->where('estado', 'finalizada')
                            ->exists();
                    }
                @endphp

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

                    <div class="tab-content" id="pills-tabContent">
                        <div class="tab-pane fade show active" id="pills-reviews" role="tabpanel">
                            @if($puedoResenar)
                                <div class="bg-white border p-4 rounded-4 mb-4 shadow-sm">
                                    <h6 class="fw-bold mb-3 text-navy">Deja tu opinión</h6>

                                    <form action="{{ route('comentarios.store') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="doctor_id" value="{{ $doctor->user->id }}">
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
                                                placeholder="Cuéntanos, ¿qué tal te pareció la atención?..."
                                                required></textarea>
                                        </div>

                                        <div class="text-end">
                                            <button type="submit" class="btn btn-navy px-4 py-2 rounded-pill shadow-sm">
                                                Publicar Reseña <i class="bi bi-send-fill ms-1"></i>
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            @elseif(Auth::check() && Auth::user()->role == 'paciente')
                                <div class="alert alert-light border-0 shadow-sm rounded-4 mb-4 text-center">
                                    <i class="bi bi-info-circle-fill text-muted me-2"></i>
                                    <span class="text-muted small">Solo puedes escribir una reseña después de haber
                                        completado una cita con este doctor.</span>
                                </div>
                            @endif

                            <div class="reviews-list">
                                @forelse($doctor->reviews ?? [] as $review)
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

                                            @if(Auth::check() && Auth::user()->role == 'doctor' && Auth::user()->doctor->id == $doctor->id)
                                                <br>
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
                                                </form>
                                            @endif

                                            @forelse($review->respuestas as $respuesta)
                                                <div class="d-flex mb-3 ms-5 mt-3">
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
                                                                    @if($respuesta->autor->id == $doctor->user->id)
                                                                        <span class="badge bg-primary text-white ms-1"
                                                                            style="font-size: 0.65rem;">Propietario</span>
                                                                    @endif
                                                                </div>
                                                                <small class="text-muted"
                                                                    style="font-size: 0.7rem;">{{ $respuesta->created_at->diffForHumans() }}</small>
                                                            </div>
                                                            <p class="text-dark small mb-0 lh-sm">{{ $respuesta->contenido }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            @empty
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

                        <div class="tab-pane fade" id="pills-questions" role="tabpanel">

                            @if(Auth::check() && Auth::user()->role == 'paciente')
                                <div class="bg-white border p-4 rounded-4 mb-4 shadow-sm">
                                    <h6 class="fw-bold mb-3 text-navy">
                                        <i class="bi bi-question-circle me-2"></i>Haz una pregunta
                                    </h6>
                                    <form action="{{ route('comentarios.store') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="doctor_id" value="{{ $doctor->user->id }}">
                                        {{-- OJO: Cambiamos el tipo a 'pregunta' --}}
                                        <input type="hidden" name="tipo" value="pregunta">

                                        <div class="mb-3">
                                            <textarea name="contenido" class="styled-textarea"
                                                placeholder="Escribe tu duda o consulta aquí..." required
                                                style="min-height: 80px;"></textarea>
                                        </div>
                                        <div class="text-end">
                                            <button type="submit" class="btn btn-navy px-4 py-2 rounded-pill shadow-sm">
                                                Enviar Pregunta <i class="bi bi-send-fill ms-1"></i>
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            @endif

                            <div class="questions-list">
                                @forelse($doctor->questions ?? [] as $question)
                                    <div class="mb-4 border-bottom pb-3">
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
                                            {{-- FORMULARIO RESPUESTA PREGUNTA (SOLO DOCTOR) --}}
                                            @if(Auth::check() && Auth::user()->role == 'doctor' && Auth::user()->doctor->id == $doctor->id)
                                                <div class="card border-0 bg-light rounded-4 p-3 mb-3">
                                                    <form action="{{ route('respuestas.store') }}" method="POST">
                                                        @csrf
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
                                            @endif

                                            {{-- RESPUESTAS DE LA PREGUNTA --}}
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
                                                                    @if($respuesta->autor->id == $doctor->user->id)
                                                                        <span class="badge bg-primary text-white ms-1"
                                                                            style="font-size: 0.65rem;">Propietario</span>
                                                                    @endif
                                                                </div>
                                                                <small class="text-muted"
                                                                    style="font-size: 0.7rem;">{{ $respuesta->created_at->diffForHumans() }}</small>
                                                            </div>
                                                            <p class="text-dark small mb-0 lh-sm">{{ $respuesta->contenido }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            @empty
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
        </div>
    </div>
    @include('citas.agendar')

    <script async src="https://maps.googleapis.com/maps/api/js?key=<?php echo $apiKey; ?>&callback=initMap"></script>
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
                title: "{{ $doctor->user->name }}"
            });
        }
    </script>
</x-layout>