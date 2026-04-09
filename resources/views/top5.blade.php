<x-layout>
    @push('styles')
        <style>
            .text-navy {
                color: #001f3f;
            }

            .flip-card {
                background-color: transparent;
                height: 350px;
                /* Ajustado para que quepa el texto abajo */
                perspective: 1000px;
                margin-bottom: 20px;
            }

            .flip-card-inner {
                position: relative;
                width: 100%;
                height: 100%;
                text-align: center;
                transition: transform 0.6s;
                transform-style: preserve-3d;
                cursor: pointer;
            }

            .flip-card:hover .flip-card-inner {
                transform: rotateY(180deg);
            }

            .flip-card-front,
            .flip-card-back {
                position: absolute;
                width: 100%;
                height: 100%;
                -webkit-backface-visibility: hidden;
                backface-visibility: hidden;
                border-radius: 40px;
                background-color: #fff;
            }

            .flip-card-front .card-body p {
                font-size: 18px;
            }

            .flip-card-back p {
                font-size: 18px;
            }

            .flip-card-back .bi-start-fill,
            .flip-card-back .bi-star {
                font-size: 1.0rem;
                margin: 0 2 px;
            }

            .flip-card-back {
                background-color: #ffffff;
                transform: rotateY(180deg);
                border: 1px solid #f0f0f0;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
            }

            .btn-navy {
                background-color: #001f3f;
                color: white;
            }
        </style>
    @endpush

    <div class="container py-5">
        <div class="text-center mb-5">
            <form action="{{ route('global.search') }}" method="GET">
                <div class="input-group shadow-sm rounded-pill overflow-hidden border-0 p-1 bg-white">
                    <span class="input-group-text bg-white border-0 ps-4"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-0 shadow-none ps-2"
                        placeholder="Buscar cardiólogo, pediatra, farmacia..." style="height: 50px;" required>
                    <button class="btn btn-navy rounded-pill px-4 m-1 fw-bold" type="submit">Buscar</button>
                </div>
            </form>
            <br>
            <br>
            <h2 class="fw-bold text-navy">Top 6 mejores</h2>
            <p class="text-muted">Los mejores servicios en Ocosingo</p>
        </div>


        <div class="row justify-content-center">
            @foreach($doctores as $doctor)
                <div class="col-12 col-sm-6 col-md-3 col-lg-3 flip-card">
                    <div class="flip-card-inner">
                        <div class="flip-card-front card border-0 shadow-sm overflow-hidden">
                            <div class="bg-light" style="height: 200px;">
                                <img src="{{ $doctor->user->foto ? asset('storage/' . $doctor->user->foto) : 'https://ui-avatars.com/api/?name=' . urlencode($doctor->user->name) }}"
                                    class="w-100 h-100" style="object-fit: cover;">
                            </div>
                            <div class="card-body p-3 text-center">
                                <br>
                                <p class="mb-0 text-navy fw-bold">Doctor: <span
                                        class="fw-normal">{{ $doctor->user->name }}</span></p>
                            </div>
                        </div>

                        <div class="flip-card-back card border-0 shadow-sm p-3 text-center">
                            <p>Calificación</p>
                            <div class="mb-2 text-warning">
                                @for($i = 1; $i <= 5; $i++)
                                    <i
                                        class="bi {{ $i <= round($doctor->promedio_estrellas) ? 'bi-star-fill' : 'bi-star' }}"></i>
                                @endfor
                            </div>
                            <p class="text-navy fw-medium mb-4">
                                {{ $doctor->especialidades->first()->nombre ?? 'Médico general' }}</p>
                            <a href="{{route('doctores.show', $doctor->id)}}"
                                class="btn btn-navy rounded-pill px-4 btn-sm">Más detalles</a>
                        </div>

                    </div>
                </div>
            @endforeach

            @foreach($farmacias as $index => $farmacia)
                <div class="col-12 col-sm-6 col-md-3 col-lg-3 flip-card">
                    <div class="flip-card-inner">
                        <div class="flip-card-front card border-0 shadow-sm overflow-hidden">
                            <div class="bg-light" style="height: 200px;">
                                <img src="{{ $farmacia->user->foto ? asset('storage/' . $farmacia->user->foto) : 'https://ui-avatars.com/api/?name=' . urlencode($farmacia->user->name) }}"
                                    class="w-100 h-100" style="object-fit: cover;">
                            </div>
                            <div class="card-body p-3 text-center">
                                <br>
                                <p class="mb-0 text-dark fw-bold">Farmacia: <span
                                        class="fw-normal">{{ $farmacia->nom_farmacia }}</span></p>
                            </div>
                        </div>

                        <div class="flip-card-back card border-0 shadow-sm p-3 text-center">
                            <p>Calificacion</p>
                            <div class="mb-2 text-warning">
                                @for($i = 1; $i <= 5; $i++)
                                    <i
                                        class="bi {{ $i <= round($farmacia->promedio_estrellas) ? 'bi-star-fill' : 'bi-star' }}"></i>
                                @endfor
                            </div>
                            <a href="{{route('farmacias.detalle', $farmacia->id)}}"
                                class="btn btn-navy rounded-pill px-4 btn-sm">Más detalles</a>
                        </div>

                    </div>
                </div>
            @endforeach
        </div>
</x-layout>