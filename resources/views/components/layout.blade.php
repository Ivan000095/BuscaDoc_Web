<!doctype html>
<html lang="en">

<head>
    <title>BuscaDoc</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.4/css/dataTables.bootstrap5.css">

    <style>
        :root {
            --custom-dark-blue: #00213D;
        }
        
        .bg-custom-dark {
            background-color: var(--custom-dark-blue) !important;
        }

        /* Ajuste opcional: Para que el dropdown no se vea cuadrado pegado a la barra redonda */
        .dropdown-menu {
            border-radius: 15px;
            margin-top: 10px !important; 
            border: none;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
    </style>

    @yield('css')
    @stack('styles')
</head>

<body class="bg-light"> {{-- Agregué bg-light al body para que resalte la barra oscura --}}
    
    <header>
    </header>

    <main>
        {{-- 
            CAMBIOS REALIZADOS AQUÍ:
            1. rounded-pill: Hace los bordes redondos tipo pastilla.
            2. shadow-lg: Sombra más fuerte para efecto flotante.
            3. mt-3 mx-auto: Margen arriba y centrado horizontal.
            4. style="width: 95%; max-width: 1100px;": Controla el tamaño para que no toque los bordes.
        --}}
        <nav class="navbar navbar-expand-sm navbar-dark bg-custom-dark shadow-lg rounded-pill mt-2 mx-auto" 
             style="width: 95%;">
            
            <div class="container px-4"> {{-- px-4 evita que el logo quede muy pegado a la curva --}}
                
                <img class="rounded me-3" width="60px" src="{{ asset('images/logo.png') }}" alt="">
                
                <button class="navbar-toggler d-lg-none" type="button" data-bs-toggle="collapse"
                    data-bs-target="#collapsibleNavId" aria-controls="collapsibleNavId" aria-expanded="false"
                    aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                
                <div class="collapse navbar-collapse" id="collapsibleNavId">
                    <ul class="navbar-nav me-auto mt-2 mt-lg-0">
                        {{-- Espacio para items del menú izquierda --}}
                    </ul>
                    
                    <div class="d-flex my-2 my-lg-0">
                        <ul class="navbar-nav me-auto mt-2 mt-lg-0">
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle text-white d-flex align-items-center gap-2" href="#" id="dropdownId" data-bs-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false">
                                    
                                    <img class="rounded-circle border border-light" width="30px" height="30px"
                                        src="{{ asset('images/avtar.avif') }}" alt="" style="object-fit: cover;">
                                    
                                    <span>{{ Auth::check() ? Auth::user()->name : 'Invitado' }}</span>
                                </a>
                                
                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownId">
                                    <a class="dropdown-item" href="#">
                                        <i class="bi bi-person-circle me-2"></i>
                                        Editar Perfil
                                    </a>
                                    <hr class="dropdown-divider">
                                    <a class="dropdown-item text-danger" href="{{ route('logout') }}"
                                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        <i class="bi bi-box-arrow-left me-2"></i>
                                        Cerrar sesión
                                    </a>
                                    {{-- Formulario oculto necesario para el logout en Laravel --}}
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>

        {{-- Contenedor principal para el contenido --}}
        <div class="container mt-4">
            {{ $slot }}
        </div>

    </main>
    
    <footer>
    </footer>
    
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
        integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous">
        </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"
        integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+" crossorigin="anonymous">
        </script>

    <script src="https://cdn.datatables.net/2.3.4/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.3.4/js/dataTables.bootstrap5.js"></script>
    
    @yield('js')
    @stack('scripts')
</body>

</html>