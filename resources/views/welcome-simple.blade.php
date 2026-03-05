<x-layout>
    <div class="container-fluid bg-light min-vh-100 d-flex align-items-center justify-content-center">
        <div class="row justify-content-center w-100">
            <div class="col-md-10 col-lg-8 col-xl-6">
                <div class="card border-0 shadow-lg rounded-5 overflow-hidden fade-in-up">
                    <div class="card-body p-0">
                        <div class="row g-0">
                            <div class="col-md-12 p-5 d-flex flex-column justify-content-center text-center">
                                <div class="mb-4">
                                    <div class="bg-navy-subtle text-navy d-inline-flex align-items-center justify-content-center rounded-circle"
                                        style="width: 80px; height: 80px;">
                                        <i class="bi bi-heart-pulse-fill display-4"></i>
                                    </div>
                                </div>
                                @guest
                                    <h2 class="fw-bold text-navy mb-3">Bienvenido a BuscaDoc</h2>
                                    <p class="text-muted fs-5 mb-4">
                                        La plataforma integral para gestionar tu salud. <br>
                                        Citas, recetas y profesionales en un solo lugar.
                                    </p>
                                    <div class="row g-3 mb-5 justify-content-center">
                                        <div class="col-4">
                                            <div class="p-3 border rounded-4 bg-light h-100 hover-scale">
                                                <i class="bi bi-people-fill fs-3 text-primary mb-2"></i>
                                                <h6 class="fw-bold small">Pacientes</h6>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="p-3 border rounded-4 bg-light h-100 hover-scale">
                                                <i class="bi bi-hospital-fill fs-3 text-info mb-2"></i>
                                                <h6 class="fw-bold small">Doctores</h6>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="p-3 border rounded-4 bg-light h-100 hover-scale">
                                                <i class="bi bi-capsule fs-3 text-success mb-2"></i>
                                                <h6 class="fw-bold small">Farmacias</h6>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-flex gap-3 justify-content-center">
                                        <a href="{{ route('login') }}"
                                            class="btn btn-navy rounded-pill px-5 py-2 fw-bold shadow-sm">
                                            Iniciar Sesión
                                        </a>
                                        <a href="{{ route('register') }}"
                                            class="btn btn-outline-navy rounded-pill px-5 py-2 fw-bold">
                                            Crear Cuenta
                                        </a>
                                    </div>

                                @else
                                    <h2 class="fw-bold text-navy mb-2">¡Hola, {{ Auth::user()->name }}!</h2>
                                    <p class="text-muted mb-4">Nos alegra verte de nuevo.</p>

                                    <div class="py-4">
                                        <div class="spinner-border text-navy" role="status"
                                            style="width: 3rem; height: 3rem;">
                                            <span class="visually-hidden">Cargando...</span>
                                        </div>
                                        <p class="text-muted small mt-3 animate-pulse">Te estamos redirigiendo a tu panel...
                                        </p>
                                    </div>

                                    <a href="{{ route('home') }}" class="btn btn-link text-navy text-decoration-none small">
                                        ¿No has sido redirigido? Haz clic aquí
                                    </a>
                                @endguest

                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <small class="text-muted">© {{ date('Y') }} BuscaDoc. Todos los derechos reservados.</small>
                </div>

            </div>
        </div>
    </div>

    @push('styles')
        <style>
            .text-navy {
                color: #0d2e4e !important;
            }

            .bg-navy-subtle {
                background-color: #e6f0ff;
            }

            .btn-navy {
                background-color: #0d2e4e;
                color: white;
                border: 1px solid #0d2e4e;
                transition: all 0.3s ease;
            }

            .btn-navy:hover {
                background-color: #1a4b7a;
                color: white;
                transform: translateY(-2px);
            }

            .btn-outline-navy {
                color: #0d2e4e;
                border: 2px solid #0d2e4e;
                background: transparent;
                transition: all 0.3s ease;
            }

            .btn-outline-navy:hover {
                background-color: #0d2e4e;
                color: white;
            }

            .fade-in-up {
                animation: fadeInUp 0.8s ease-out;
            }

            @keyframes fadeInUp {
                from {
                    opacity: 0;
                    transform: translateY(20px);
                }

                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            .hover-scale {
                transition: transform 0.2s;
            }

            .hover-scale:hover {
                transform: scale(1.05);
                cursor: default;
            }

            .animate-pulse {
                animation: pulse 2s infinite;
            }

            @keyframes pulse {
                0% {
                    opacity: 1;
                }

                50% {
                    opacity: 0.5;
                }

                100% {
                    opacity: 1;
                }
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            @auth
                setTimeout(function () {
                    window.location.href = "{{ route('home') }}";
                }, 2500);
            @endauth
        </script>
    @endpush
</x-layout>