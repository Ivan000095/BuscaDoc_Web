<x-layout>
    <style>
        body {
            background-color: #f9fafb;
        }

        .brand-title {
            font-size: 4rem;
            font-weight: 800;
            line-height: 0.9;
            color: #000;
        }

        .brand-subtitle {
            font-size: 3rem;
            font-weight: 800;
            color: #000;
        }

        .form-control-pill {
            border-radius: 50px;
            background-color: #f1f5f9;
            border: 1px solid transparent;
            padding: 12px 25px;
            font-size: 0.95rem;
        }

        .form-control-pill:focus {
            background-color: #fff;
            border-color: #0d2e4e;
            box-shadow: 0 0 0 4px rgba(13, 46, 78, 0.1);
        }

        .btn-navy {
            background-color: #0d2e4e;
            color: white;
            border-radius: 50px;
            padding: 12px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-navy:hover {
            background-color: #16436d;
            color: white;
            transform: translateY(-2px);
        }

        .login-card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
        }

        .separator {
            display: flex;
            align-items: center;
            text-align: center;
            margin: 20px 0;
            color: #aaa;
        }

        .separator::before,
        .separator::after {
            content: '';
            flex: 1;
            border-bottom: 1px dashed #ccc;
        }

        .separator::before {
            margin-right: .25em;
        }

        .separator::after {
            margin-left: .25em;
        }
    </style>

    <div class="container py-5">
        <div class="row align-items-center justify-content-center" style="min-height: 80vh;">
            <div class="col-md-6 text-center text-md-start mb-5 mb-md-0">
                <div class="mb-4">
                    <img src="{{ asset('images/logo_negro.png') }}" alt="">
                </div>
                <p class="fs-4 text-muted">
                    "Un lugar para todas tus necesidades"
                </p>
            </div>

            <div class="col-md-5 col-lg-4">
                <div class="card login-card p-4 bg-white">
                    <div class="card-body">
                        <h3 class="text-center fw-bold mb-4">Iniciar sesión</h3>

                        <form method="POST" action="{{ route('login') }}">
                            @csrf

                            <div class="mb-3">
                                <label for="email" class="form-label text-muted small ps-3">Correo electrónico</label>
                                <input id="email" type="email"
                                    class="form-control form-control-pill @error('email') is-invalid @enderror"
                                    name="email" value="{{ old('email') }}" required autocomplete="email" autofocus
                                    placeholder="ejemplo@correo.com">

                                @error('email')
                                    <span class="invalid-feedback ps-3" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label text-muted small ps-3">Contraseña</label>
                                <input id="password" type="password"
                                    class="form-control form-control-pill @error('password') is-invalid @enderror"
                                    name="password" required autocomplete="current-password" placeholder="••••••••">

                                @error('password')
                                    <span class="invalid-feedback ps-3" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="mb-3 ps-3 form-check">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                <label class="form-check-label small text-muted" for="remember">
                                    Recordarme
                                </label>
                            </div>

                            <div class="d-grid mb-4">
                                <button type="submit" class="btn btn-navy shadow-sm">
                                    {{ __('Iniciar sesión') }}
                                </button>
                            </div>

                            <div class="separator small">o</div>
                            <div class="d-flex justify-content-center gap-3 mb-4">
                                <a href="{{ route('google.login') }}"
                                    class="btn btn-outline-light border text-dark rounded-pill d-flex align-items-center gap-2 shadow-sm">
                                    <i class="bi bi-google text-navy"></i> <span class="small">Google</span>
                                </a>
                            </div>

                            <div class="text-center small">
                                ¿Aún no tienes cuenta?
                                <a href="{{ route('register') }}" class="fw-bold text-decoration-none"
                                    style="color: #0d2e4e;">
                                    Registrarme
                                </a>
                            </div>

                            @if (Route::has('password.request'))
                                <div class="text-center mt-2">
                                    <a class="small text-muted text-decoration-none" href="{{ route('password.request') }}">
                                        ¿Olvidaste tu contraseña?
                                    </a>
                                </div>
                            @endif

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layout>