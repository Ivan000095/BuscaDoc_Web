<x-layout>
    <style>
        body {
            background: linear-gradient(135deg, #f0f4f8 0%, #d9e2ec 100%);
            min-height: 100vh;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
        }

        .login-container {
            min-height: calc(100vh - 100px);
            display: flex;
            align-items: center;
            padding: 3rem 1rem;
        }

        .login-wrapper {
            display: grid;
            grid-template-columns: 1fr 450px;
            gap: 4rem;
            max-width: 1200px;
            width: 100%;
            align-items: center;
            margin: 0 auto;
        }

        /* Left Side - Branding */
        .brand-content {
            padding: 2rem;
        }

        .brand-logo {
            background: white;
            padding: 2rem;
            border-radius: 24px;
            display: inline-block;
            box-shadow: 0 10px 40px rgba(13, 46, 78, 0.12);
            margin-bottom: 2rem;
        }

        .brand-logo img {
            max-width: 280px;
            height: auto;
        }

        .brand-title {
            font-size: 2.2rem;
            font-weight: 800;
            color: #0d2e4e;
            margin-bottom: 1.5rem;
            line-height: 1.2;
        }

        .brand-description {
            font-size: 1.15rem;
            color: #64748b;
            line-height: 1.6;
            margin-bottom: 2.5rem;
        }

        .features {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .features li {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 14px 0;
            color: #2c3e50;
            font-size: 1.05rem;
            font-weight: 500;
        }

        .feature-icon {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, #0d2e4e 0%, #1a5f7a 100%);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            flex-shrink: 0;
            box-shadow: 0 4px 12px rgba(13, 46, 78, 0.2);
        }

        .feature-icon i {
            font-size: 1.3rem;
        }

        /* Right Side - Login Form */
        .login-form-container {
            background: white;
            padding: 3rem;
            border-radius: 24px;
            box-shadow: 0 20px 60px rgba(13, 46, 78, 0.15);
        }

        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .login-header h2 {
            font-size: 2rem;
            font-weight: 700;
            color: #0d2e4e;
            margin: 0;
        }

        .login-header p {
            color: #64748b;
            margin-top: 0.5rem;
            font-size: 0.95rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.6rem;
            color: #0d2e4e;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .input-wrapper {
            position: relative;
        }

        .input-wrapper i {
            position: absolute;
            left: 1.25rem;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 1.1rem;
        }

        .form-control-custom {
            width: 100%;
            padding: 14px 16px 14px 48px;
            border: 2px solid #e2e8f0;
            border-radius: 50px;
            font-size: 1rem;
            transition: all 0.3s;
            background-color: #f8fafc;
        }

        .form-control-custom:focus {
            outline: none;
            border-color: #0d2e4e;
            background-color: white;
            box-shadow: 0 0 0 4px rgba(13, 46, 78, 0.08);
        }

        .form-control-custom::placeholder {
            color: #94a3b8;
        }

        .btn-primary-custom {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #0d2e4e 0%, #1a5f7a 100%);
            color: white;
            border: none;
            border-radius: 50px;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 0.5rem;
            box-shadow: 0 4px 15px rgba(13, 46, 78, 0.3);
        }

        .btn-primary-custom:hover {
            background: linear-gradient(135deg, #1a5f7a 0%, #0d2e4e 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(13, 46, 78, 0.35);
        }

        .btn-create {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #0d2e4e 0%, #1a5f7a 100%);
            color: white;
            border: none;
            border-radius: 50px;
            font-size: 1.05rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 1rem;
            text-decoration: none;
            display: block;
            text-align: center;
            box-shadow: 0 4px 15px rgba(13, 46, 78, 0.3);
        }

        .btn-create:hover {
            background: linear-gradient(135deg, #1a5f7a 0%, #0d2e4e 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(13, 46, 78, 0.35);
            color: white;
        }

        .error-message {
            background-color: #fef2f2;
            border-left: 4px solid #ef4444;
            color: #dc2626;
            padding: 12px 16px;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .divider {
            display: flex;
            align-items: center;
            margin: 1.75rem 0;
            color: #94a3b8;
            font-size: 0.875rem;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: linear-gradient(90deg, transparent, #cbd5e1, transparent);
        }

        .divider span {
            padding: 0 12px;
        }

        /* Responsive */
        @media (max-width: 968px) {
            .login-wrapper {
                grid-template-columns: 1fr;
                gap: 2.5rem;
                max-width: 500px;
            }

            .brand-content {
                text-align: center;
                padding: 0;
            }

            .brand-title {
                font-size: 1.8rem;
            }

            .features {
                max-width: 400px;
                margin: 0 auto;
            }
        }

        @media (max-width: 480px) {
            .login-form-container {
                padding: 2rem 1.5rem;
            }

            .brand-title {
                font-size: 1.6rem;
            }
        }
    </style>

    <div class="login-container">
        <div class="login-wrapper">
            <!-- Left Side - Branding -->
            <div class="brand-content">
                <div class="brand-logo">
                    <img src="{{ asset('images/logo_negro.png') }}" alt="BuscaDoc">
                </div>
                <h1 class="brand-title">
                    Conecta con los mejores<br>profesionales de la salud
                </h1>
                <p class="brand-description">
                    Encuentra doctores, farmacias y agenda citas de manera rápida y segura.
                </p>
                <ul class="features">
                    <li>
                        <div class="feature-icon">
                            <i class="bi bi-heart-pulse-fill"></i>
                        </div>
                        <span>Doctores certificados cerca de ti</span>
                    </li>
                    <li>
                        <div class="feature-icon">
                            <i class="bi bi-shop-window"></i>
                        </div>
                        <span>Farmacias disponibles 24/7</span>
                    </li>
                    <li>
                        <div class="feature-icon">
                            <i class="bi bi-calendar-check-fill"></i>
                        </div>
                        <span>Agenda citas en minutos</span>
                    </li>
                </ul>
            </div>

            <!-- Right Side - Login Form -->
            <div class="login-form-container">
                <div class="login-header">
                    <h2>Iniciar sesión</h2>
                    <p>Ingresa tus credenciales para continuar</p>
                </div>

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    @if ($errors->has('email') || $errors->has('password'))
                        <div class="error-message">
                            <i class="bi bi-exclamation-circle-fill"></i>
                            <span>Credenciales incorrectas. Intenta de nuevo.</span>
                        </div>
                    @endif

                    <div class="form-group">
                        <label for="email">Correo electrónico</label>
                        <div class="input-wrapper">
                            <input 
                                type="email" 
                                id="email" 
                                name="email" 
                                class="form-control-custom" 
                                placeholder="tu@email.com"
                                value="{{ old('email') }}" 
                                required 
                                autocomplete="email" 
                                autofocus
                            >
                            <i class="bi bi-envelope-fill"></i>
                        </div>
                        @error('email')
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password">Contraseña</label>
                        <div class="input-wrapper">
                            <input 
                                type="password" 
                                id="password" 
                                name="password" 
                                class="form-control-custom" 
                                placeholder="••••••••"
                                required 
                                autocomplete="current-password"
                            >
                            <i class="bi bi-lock-fill"></i>
                        </div>
                        @error('password')
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <button type="submit" class="btn-primary-custom">
                        Iniciar sesión
                    </button>

                    <div class="divider">
                        <span>¿No tienes cuenta?</span>
                    </div>

                    <a href="{{ route('register') }}" class="btn-create">
                        Crear cuenta nueva
                    </a>
                </form>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');
            const submitBtn = document.querySelector('.btn-primary-custom');

            form.addEventListener('submit', function() {
                submitBtn.disabled = true;
                
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Conectando...';
                submitBtn.style.opacity = '0.8';
                submitBtn.style.cursor = 'not-allowed';
            });
        });
    </script>
</x-layout>