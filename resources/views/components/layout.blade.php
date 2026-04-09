<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'BuscaDoc') }}</title>

    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito:400,600,700" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.4/css/dataTables.bootstrap5.css">

    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    <style>
        :root {
            --bg-app: #f4f7f9;
            --bg-surface: #fbfcfd;
            --text-main: #2c3e50;
            --text-muted: #64748b;
            --brand-navy: #00213D;
            --brand-navy-hover: #1a3c61;
            --brand-navy-subtle: #eef2f6; 
            --soft-light: #f1f5f9;
        }

        body {
            min-height: 100vh !important;
            background-color: var(--bg-app);
            color: var(--text-main);
            font-family: 'Nunito', sans-serif;
        }

        .bg-surface { background-color: var(--bg-surface) !important; }
        .bg-app { background-color: var(--bg-app) !important; }
        .bg-navy { background-color: var(--brand-navy) !important; }
        .text-navy { color: var(--brand-navy) !important; }
        .bg-navy-subtle { background-color: var(--brand-navy-subtle) !important; }
        .text-main { color: var(--text-main) !important; }
        .bg-white { background-color: rgb(245, 246, 253)!important;}

        .shadow-soft {
            box-shadow: 0 10px 40px -10px rgba(17, 42, 70, 0.08) !important;
        }

        .custom-navbar {
            background-color: var(--brand-navy)!important;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.05);
            transition: all 0.3s ease;
            position: relative;
        }
        
        .navbar-brand img {
            max-height: 38px;
            width: auto;
            transition: transform 0.3s ease;
        }
        .navbar-brand:hover img { transform: scale(1.05); }

        .nav-link {
            color: rgba(251, 252, 253, 0.85) !important;
            font-weight: 600;
            transition: all 0.2s ease;
            position: relative;
        }
        
        .nav-link:hover {
            color: var(--bg-surface) !important;
            transform: translateY(-1px);
        }

        .nav-link::after {
            content: ''; position: absolute; width: 0; height: 2px;
            bottom: 0; left: 50%; background-color: var(--bg-surface);
            transition: all 0.3s ease; transform: translateX(-50%); opacity: 0.7;
        }
        .nav-link:hover::after { width: 70%; }

        .btn-outline-light {
            color: var(--bg-surface);
            border: 1.5px solid rgba(251, 252, 253, 0.5);
            background-color: transparent; font-weight: 600; transition: all 0.3s ease;
        }
        .btn-outline-light:hover {
            background-color: var(--bg-surface); color: var(--brand-navy) !important;
            border-color: var(--bg-surface); transform: translateY(-2px);
        }

        .btn-light {
            background-color: var(--bg-surface); color: var(--brand-navy) !important;
            border: none; font-weight: 600; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }
        .btn-light:hover {
            background-color: #ffffff; transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
        }

        .user-dropdown-toggle {
            background-color: rgba(251, 252, 253, 0.1) !important;
            color: var(--bg-surface) !important;
            border: 1px solid rgba(251, 252, 253, 0.2) !important;
        }
        .user-dropdown-toggle:hover { background-color: rgba(251, 252, 253, 0.2) !important; }

        .dropdown-menu {
            background-color: var(--bg-surface); border-radius: 16px; margin-top: 12px !important;
            border: 1px solid var(--brand-navy-subtle); box-shadow: 0 10px 30px rgba(17, 42, 70, 0.1); padding: 8px;
        }
        .dropdown-item {
            color: var(--text-main); border-radius: 10px; font-weight: 600; transition: all 0.2s; margin-bottom: 2px;
        }
        .dropdown-item:hover {
            background-color: var(--brand-navy-subtle); color: var(--brand-navy); transform: translateX(4px);
        }

        .pill-notification {
            position: fixed; top: -100px; left: 50%; transform: translateX(-50%);
            background: var(--bg-surface); color: var(--text-main); padding: 12px 24px;
            border-radius: 50px; box-shadow: 0 10px 30px rgba(17, 42, 70, 0.15);
            display: flex; align-items: center; gap: 12px; z-index: 9999;
            font-weight: 600; opacity: 0; border: 1px solid var(--brand-navy-subtle);
            transition: all 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        }
        .pill-notification.show { top: 30px; opacity: 1; }
        .pill-icon {
            display: flex; align-items: center; justify-content: center;
            width: 28px; height: 28px; background: #2b8a3e; color: #f8f9fa;
            border-radius: 50%; font-size: 14px;
        }
        .pill-notification.error .pill-icon { background: #c92a2a; }

        footer { border-top: 1px solid var(--brand-navy-subtle) !important; }
        .footer-social-btn {
            background-color: rgba(251, 252, 253, 0.1); color: var(--bg-surface);
            border: 1px solid rgba(251, 252, 253, 0.2); transition: all 0.3s;
        }
        .footer-social-btn:hover { background-color: var(--bg-surface); color: var(--brand-navy); transform: translateY(-3px); }

        .fade-in { animation: fadeIn 0.6s ease-out forwards; }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(15px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @media (min-width: 768px) {
            .custom-navbar {
                border-radius: 100px !important; padding: 10px 20px;
                box-shadow: 0 10px 30px rgba(17, 42, 70, 0.15);
            }
            /* TRUCO PARA CENTRADO PERFECTO EN PANTALLAS GRANDES */
            .navbar-center-absolute {
                position: absolute;
                left: 50%;
                transform: translateX(-50%);
            }
        }

        @media (max-width: 767.98px) {
            .custom-navbar { border-radius: 24px !important; padding: 15px !important; }
            .navbar-collapse {
                background-color: var(--brand-navy); border-radius: 16px; padding: 15px; margin-top: 15px;
                border: 1px solid rgba(255,255,255,0.05);
            }
            .nav-item { margin-bottom: 8px; }
            .nav-link::after { display: none; }
            .navbar-nav .btn, .navbar-nav .dropdown-toggle { width: 100%; justify-content: center; }
            
            .dropdown-menu { background-color: rgba(255, 255, 255, 0.05); border: none !important; box-shadow: none; padding: 0; }
            .dropdown-item { color: var(--bg-surface) !important; text-align: center; }
            .dropdown-item i { color: var(--bg-surface) !important; }
            .dropdown-item:hover { background-color: rgba(255, 255, 255, 0.1); transform: none; }
            .dropdown-divider { border-color: rgba(255, 255, 255, 0.1); }
        }
    </style>

    @stack('styles')
</head>

<body class="d-flex flex-column min-vh-100">
    
    @if(session('success'))
        <div id="notification-pill" class="pill-notification">
            <div class="pill-icon"><i class="bi bi-check-lg"></i></div>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div id="notification-pill" class="pill-notification error">
            <div class="pill-icon"><i class="bi bi-x-lg"></i></div>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <nav class="navbar navbar-expand-md custom-navbar mt-4 mx-auto" style="width: 95%; max-width: 1400px; z-index: 1000;">
        <div class="container-fluid px-3 position-relative">
            <a class="navbar-brand d-flex align-items-center" href="{{ url('/home') }}">
                <img src="{{ asset('images/logo.png') }}" alt="Logo BuscaDoc" class="me-2">
            </a>

            <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon" style="filter: brightness(0) invert(1);"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0 text-center gap-3 navbar-center-absolute">
                    <li class="nav-item">
                        <a class="nav-link px-3 rounded-pill" href="{{ route('top5') }}">Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-3 rounded-pill" href="{{ route('doctores.vista') }}">Doctores</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-3 rounded-pill" href="{{ route('farmacias.catalogo') }}">Farmacias</a>
                    </li>
                </ul>

                <ul class="navbar-nav ms-auto text-center text-md-start align-items-center gap-2">
                    @guest
                        @if (Route::has('login'))
                            <li class="nav-item">
                                <a class="btn btn-outline-light rounded-pill px-4 py-2 btn-sm w-100 text-nowrap"
                                    href="{{ route('login') }}">{{ __('Iniciar sesión') }}</a>
                            </li>
                        @endif
                        @if (Route::has('register'))
                            <li class="nav-item mt-2 mt-md-0">
                                <a class="btn btn-light rounded-pill px-4 py-2 btn-sm w-100 text-nowrap"
                                    href="{{ route('register') }}">{{ __('Crear cuenta') }}</a>
                            </li>
                        @endif
                    @else
                        <li class="nav-item dropdown w-100">
                            <a id="navbarDropdown" class="nav-link dropdown-toggle btn user-dropdown-toggle rounded-pill px-3 py-1 d-inline-flex align-items-center justify-content-center w-100 text-nowrap" href="#"
                                role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <img src="{{ Auth::user()->foto ? asset('storage/' . Auth::user()->foto) : 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&background=random' }}"
                                    alt="Perfil" class="rounded-circle me-2 shadow-sm"
                                    style="width: 28px; height: 28px; object-fit: cover; border: 2px solid var(--bg-surface);">
                                <span class="fw-bold">{{ explode(' ', trim(Auth::user()->name))[0] }}</span>
                            </a>

                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item py-2 d-flex align-items-center" href="{{ route('users.show', Auth::user()->id) }}">
                                    <i class="bi bi-person-circle text-muted fs-5 me-2"></i> {{ __('Mi perfil') }}
                                </a>
                                
                                @if(Auth::check())
                                    <a class="dropdown-item py-2 d-flex align-items-center" href="{{ route('reportes.mis') }}">
                                        <i class="bi bi-flag text-muted fs-5 me-2"></i> {{ __('Mis reportes') }}
                                    </a>
                                @endif
                                
                                @php
                                    $cita = Auth::user()->role == 'paciente' ? '/mis-citas' : (Auth::user()->role == 'doctor' ? '/mis-citas-doc' : null);
                                @endphp
                                
                                @if(Auth::user()->role == 'paciente' || Auth::user()->role == 'doctor' && Auth::user()->doctor->citas == true)
                                    <a class="dropdown-item py-2 d-flex align-items-center" href="{{ $cita }}">
                                        <i class="bi bi-calendar-date text-muted fs-5 me-2"></i> {{ __('Mis citas') }}
                                    </a>
                                @endif
                                
                                <hr class="dropdown-divider mx-2">
                                
                                <a class="dropdown-item py-2 text-danger d-flex align-items-center" href="{{ route('logout') }}" 
                                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="bi bi-box-arrow-right fs-5 me-2"></i> {{ __('Cerrar sesión') }}
                                </a>

                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </div>
                        </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>

    <main class="flex-grow-1 py-5 fade-in">
        {{ $slot }}
    </main>

    <footer class="bg-surface mt-auto">
        <div class="bg-navy py-4" style="border-top-left-radius: 24px; border-top-right-radius: 24px;">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-4 text-center text-md-start mb-3 mb-md-0">
                        <small style="color: rgba(251, 252, 253, 0.7);">
                            &copy; {{ date('Y') }} <strong class="text-white">BuscaDoc</strong>. Todos los derechos reservados.
                        </small>
                    </div>

                    <div class="col-md-4 text-center mb-3 mb-md-0">
                        <div class="d-inline-flex gap-3">
                            <a href="https://www.facebook.com/share/16S5rNJ3i7/" target="_blank"
                                class="btn footer-social-btn rounded-circle shadow-sm d-flex align-items-center justify-content-center"
                                style="width: 38px; height: 38px;">
                                <i class="bi bi-facebook fs-6"></i>
                            </a>
                            <a href="https://www.instagram.com/servifinder1?igsh=MTZhcHdiYXkwdzlnbA==" target="_blank"
                                class="btn footer-social-btn rounded-circle shadow-sm d-flex align-items-center justify-content-center"
                                style="width: 38px; height: 38px;">
                                <i class="bi bi-instagram fs-6"></i>
                            </a>
                        </div>
                    </div>

                    <div class="col-md-4 text-center text-md-end">
                        <img src="{{ asset('images/logo-uts.png') }}" alt="Logo UTS" class="img-fluid" style="max-height: 40px; opacity: 0.9; transition: opacity 0.3s;" onmouseover="this.style.opacity=1" onmouseout="this.style.opacity=0.9">
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdn.datatables.net/2.3.4/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.3.4/js/dataTables.bootstrap5.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const pill = document.getElementById('notification-pill');
            if (pill) {
                setTimeout(() => { pill.classList.add('show'); }, 150);
                setTimeout(() => { pill.classList.remove('show'); }, 4000);
            }
        });
    </script>

    @stack('scripts')
</body>
</html>