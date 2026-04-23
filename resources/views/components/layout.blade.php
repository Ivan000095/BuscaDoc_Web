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

        .icon-white {
                stroke: white !important;
            }

        .btn-navy:hover .icon-white {
            stroke: #0d2e4e !important;
        }

        .btn-navy {
            background-color: #0d2e4e;
            color: white;
            border-radius: 50px;
            padding: 12px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-outline-navy {
            color: var(--brand-navy); 
            border: 1.5px solid var(--brand-navy);
            background-color: transparent; 
            font-weight: 600; 
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn-outline-navy:hover {
            background-color: var(--brand-navy);
            color: var(--bg-surface, #ffffff);
        }

        .btn-outline-danger-custom {
            color: #dc3545;
            border: 1.5px solid #dc3545;
            background-color: transparent;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn-outline-danger-custom:hover {
            background-color: #dc3545;
            color: #ffffff;
        }

        .btn-outline-navy svg,
        .btn-outline-danger-custom svg {
            width: 1.2rem;
            height: 1.2rem;
            fill: currentColor; 
            stroke: currentColor;
            stroke-width: 1.1px;
        }

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

        .dataTables_wrapper .page-item.active .page-link {
            background-color: #0d2e4e !important;
            border-color: #0d2e4e !important;
            color: #ffffff !important;
            box-shadow: 0 4px 8px rgba(13, 46, 78, 0.2);
        }

        .dataTables_wrapper .page-link {
            color: #0d2e4e; 
            border-radius: 8px !important;
            margin: 0 3px;
            border: 1px solid #dee2e6;
            transition: all 0.3s ease;
        }

        .dataTables_wrapper .page-link:hover {
            background-color: #f0f4f8;
            color: #0d2e4e;
            border-color: #cdd4dc;
        }

        .dataTables_wrapper .page-item:first-child .page-link, 
        .dataTables_wrapper .page-item:last-child .page-link {
            border-radius: 8px !important;
        }

        .dataTables_wrapper .page-item.disabled .page-link {
            color: #94a3b8;
            background-color: #f8fafc;
            border-color: #e2e8f0;
        }

        .dataTables_wrapper .dataTables_processing > div:not(.d-flex) {
            display: none !important;
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
                        <a class="nav-link px-3 rounded-pill" href="{{ url('/home') }}">Inicio</a>
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

                   @if(in_array(Auth::user()->role, ['paciente', 'doctor']))
                    <li class="nav-item dropdown me-2">
                        <a id="notificationsDropdown" class="nav-link btn rounded-pill px-3 py-1 d-inline-flex align-items-center justify-content-center position-relative" 
                        href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                        style="background-color: rgba(251, 252, 253, 0.1); color: white;"
                        onclick="marcarLeidas()"> <i class="bi bi-bell-fill fs-5"></i>

                            @php $unreadCount = Auth::user()->alertas()->where('leido', false)->count(); @endphp
                            
                            @if($unreadCount > 0)
                                <span id="badge-notificaciones" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" 
                                    style="font-size: 0.65rem; border: 2px solid var(--bg-surface);">
                                    {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                                </span>
                            @endif
                        </a>

                        <div class="dropdown-menu dropdown-menu-end shadow-lg border-0 mt-2" aria-labelledby="notificationsDropdown" style="width: 300px; max-height: 400px; overflow-y: auto;">
                            <div class="dropdown-header d-flex justify-content-between align-items-center">
                                <span class="fw-bold text-dark">Notificaciones</span>
                            </div>
                            <hr class="dropdown-divider my-0">

                            @forelse(Auth::user()->alertas()->where('leido', false)->latest()->take(5)->get() as $alerta)
                                <a class="dropdown-item py-3 border-bottom d-flex align-items-start gap-2" href="#">
                                    <div class="bg-primary rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                                        <i class="bi {{ $alerta->tipo == 'mensaje' ? 'bi-chat-dots' : 'bi-calendar-check' }} text-white"></i>
                                    </div>
                                    <div style="white-space: normal;">
                                        <p class="mb-0 fw-bold small text-dark">{{ $alerta->titulo }}</p>
                                        <p class="mb-0 small text-muted text-truncate" style="max-width: 200px;">{{ $alerta->mensaje }}</p>
                                        <small class="text-primary font-monospace" style="font-size: 0.7rem;">{{ $alerta->created_at->diffForHumans() }}</small>
                                    </div>
                                </a>
                            @empty
                                <div class="text-center py-4">
                                    <i class="bi bi-bell-slash text-muted fs-2"></i>
                                    <p class="text-muted small mt-2">No tienes notificaciones nuevas</p>
                                </div>
                            @endforelse
                        </div>
                    </li>
                @endif
                        <li class="nav-item dropdown w-100">
                            <a id="navbarDropdown" class="nav-link dropdown-toggle btn user-dropdown-toggle rounded-pill px-3 py-1 d-inline-flex align-items-center justify-content-center w-100 text-nowrap" href="#"
                                role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <img src="{{ Auth::user()->foto ? asset('storage/' . Auth::user()->foto) : 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&background=random' }}"
                                    alt="Perfil" class="rounded-circle me-2 shadow-sm"
                                    style="width: 28px; height: 28px; object-fit: cover; border: 2px solid var(--bg-surface);">
                                <span class="fw-bold">{{ explode(' ', trim(Auth::user()->name))[0] }}</span>
                                <span class="ms-2 px-2 py-1 rounded-pill" 
                                    style="font-size: 0.7rem; font-weight: 600; background-color: rgba(251, 252, 253, 0.2); color: var(--bg-surface);">
                                    @php
                                        $roleNames = [
                                            'admin' => 'Administrador',
                                            'doctor' => 'Doctor',
                                            'farmacia' => 'Farmacia',
                                            'paciente' => 'Paciente'
                                        ];
                                    @endphp
                                    {{ $roleNames[Auth::user()->role] ?? ucfirst(Auth::user()->role) }}
                                </span>
                            </a>

                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item py-2 d-flex align-items-center" href="{{ route('users.show', Auth::user()->id) }}">
                                    <i class="bi bi-person-circle text-muted fs-5 me-2"></i> {{ __('Mi perfil') }}
                                </a>
                                
                                @if(Auth::check() && Auth::user()->role === 'paciente')
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

    @auth
        <script type="module">
            import { initializeApp } from "https://www.gstatic.com/firebasejs/10.8.1/firebase-app.js";
            import { getMessaging, getToken, onMessage } from "https://www.gstatic.com/firebasejs/10.8.1/firebase-messaging.js";

            const firebaseConfig = {
                apiKey: "AIzaSyCjY3XJoaq7uGe8TdaQFw_c2YLJZSQUqpY",
                authDomain: "buscadoc-b204b.firebaseapp.com",
                projectId: "buscadoc-b204b",
                storageBucket: "buscadoc-b204b.firebasestorage.app",
                messagingSenderId: "754493965978",
                appId: "1:754493965978:web:769a90bb14471891594123"
            };

            const app = initializeApp(firebaseConfig);
            const messaging = getMessaging(app);

            Notification.requestPermission().then((permission) => {
                if (permission === 'granted') {
                getToken(messaging, { vapidKey: 'TU_CLAVE_VAPID_AQUI' }).then((currentToken) => {
                    if (currentToken) {
                    fetch('/api/usuarios/fcm-token', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ fcm_token: currentToken })
                    }).then(response => console.log('Token web guardado en BD'));

                    }
                }).catch((err) => {
                    console.log('Error obteniendo el token web', err);
                });
                }
            });

            onMessage(messaging, (payload) => {
                console.log('Mensaje recibido en primer plano: ', payload);
                alert(payload.notification.title + ": " + payload.notification.body);
            });
        </script>

        <script>
        function marcarLeidas() {
            let badge = document.getElementById('badge-notificaciones');
            
            if (badge && badge.style.display !== 'none') {
                badge.style.display = 'none';

                fetch('/alertas/leer-todas', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                }).catch(error => console.log('Error al marcar leídas:', error));
            }
        }
        </script>
    @endauth

    @stack('scripts')
    @stack('modals')
</body>
</html>