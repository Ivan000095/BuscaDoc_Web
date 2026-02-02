<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    <style>
        :root {
            --custom-dark-blue: #00213D;
        }

        .bg-custom-dark {
            background-color: var(--custom-dark-blue) !important;
        }

        .custom-icon-color {
            color: #00213D;
        }

        .navbar-brand img {
            max-height: 40px;
            width: auto;
        }

        .pill-notification {
            position: fixed;
            top: -100px;
            /* Empieza escondida arriba */
            left: 50%;
            transform: translateX(-50%);
            background: white;
            color: #333;
            padding: 12px 30px;
            border-radius: 50px;
            /* Forma de píldora */
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            /* Sombra elegante */
            display: flex;
            align-items: center;
            gap: 12px;
            z-index: 9999;
            /* Siempre encima de todo */
            font-family: system-ui, -apple-system, sans-serif;
            font-weight: 600;
            opacity: 0;
            transition: all 0.6s cubic-bezier(0.68, -0.55, 0.265, 1.55);
            /* Efecto Rebote */
        }

        .pill-notification.show {
            top: 30px;
            opacity: 1;
        }

        .pill-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 24px;
            height: 24px;
            background: #198754;
            color: white;
            border-radius: 50%;
            font-size: 14px;
        }

        .pill-notification.error .pill-icon {
            background: #dc3545;
            /* Rojo */
        }
    </style>

    @yield('styles')
    @stack('styles')
</head>

<body>
    <!-- inicion sesiada -->
    @if(session('success'))
        <div id="notification-pill" class="pill-notification">
            <div class="pill-icon">
                <i class="bi bi-check-lg"></i>
            </div>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    <!-- error -->
    @if(session('error'))
        <div id="notification-pill" class="pill-notification error">
            <div class="pill-icon">
                <i class="bi bi-x-lg"></i>
            </div>
            <span>{{ session('error') }}</span>
        </div>
    @endif
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-dark bg-custom-dark shadow-lg rounded-pill mt-4 mx-auto"
            style="max-width: 1100px; width: 95%;">

            <div class="container px-4">
                <a class="navbar-brand d-flex align-items-center" href="{{ url('/home') }}">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" style="height: 30px;" class="me-2">
                </a>

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                    aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav mx-auto mb-2 mb-lg-0 text-center">
                        <li class="nav-item">
                            <a class="nav-link px-3" href="{{ route('home') }}">Inicio</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link px-3" href="{{ route('doctores.index') }}">Doctores</a>
                        </li>
                    </ul>

                    <ul class="navbar-nav ms-0">
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="btn btn-outline-light rounded-pill px-4 btn-sm me-2"
                                        href="{{ route('login') }}">{{ __('Login') }}</a>
                                </li>
                            @endif
                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="btn btn-light rounded-pill px-4 btn-sm text-dark"
                                        href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown"
                                    class="nav-link dropdown-toggle btn btn-light text-dark rounded-pill px-2 py-1" href="#"
                                    role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre
                                    style="background-color: white; color: black !important;">
                                    <img src="{{ Auth::user()->foto ? asset('storage/' . Auth::user()->foto) : 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&background=random' }}"
                                        alt="{{ Auth::user()->name }}" class="rounded-circle"
                                        style="width: 32px; height: 32px; object-fit: cover;">
                                    {{ Auth::user()->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-end rounded-4 shadow border-0 mt-2"
                                    aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('users.show', Auth::user()->id) }}">
                                        <i class="bi bi-person-circle custom-icon-color"></i>{{ __(' ver mi perfil') }}
                                    </a>
                                    <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault();
                                                             document.getElementById('logout-form').submit();">
                                        <i class="bi bi-box-arrow-left custom-icon-color"></i>{{ __(' Logout') }}
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

        <main class="py-4">
            @yield('content')
        </main>
    </div>
    @stack('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const pill = document.getElementById('notification-pill');

            if (pill) {
                setTimeout(() => {
                    pill.classList.add('show');
                }, 100);

                setTimeout(() => {
                    pill.classList.remove('show');
                }, 4000);
            }
        });
    </script>
</body>

</html>