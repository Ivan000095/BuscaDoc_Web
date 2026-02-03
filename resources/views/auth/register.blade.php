@extends('layouts.app')

@section('content')
<style>
    body { background-color: #f9fafb; }

    .brand-title { font-size: 3.5rem; font-weight: 800; line-height: 0.9; color: #000; }
    .brand-subtitle { font-size: 2.5rem; font-weight: 800; color: #000; }

    .form-control-pill, .form-select-pill {
        border-radius: 50px;
        background-color: #f1f5f9;
        border: 1px solid transparent;
        padding: 12px 25px;
        font-size: 0.95rem;
        width: 100%;
    }

    .form-select-pill {
        padding-left: 55px !important;
        background-position: right 1rem center;
    }

    .form-control-pill:focus, .form-select-pill:focus {
        background-color: #fff;
        border-color: #0d2e4e;
        box-shadow: 0 0 0 4px rgba(13, 46, 78, 0.1);
        outline: none;
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

    .register-card {
        border: none;
        border-radius: 20px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.08);
    }
</style>

<div class="container py-4">
    <div class="row align-items-center justify-content-center" style="min-height: 85vh;">
        
        <div class="col-lg-5 text-center text-lg-start mb-5 mb-lg-0 pe-lg-5">
            <div class="mb-4">
                <img src="{{ asset('images/logo_negro.png') }}" width="500px">
            </div>
            <p class="fs-5 text-muted">
                Crea una cuenta hoy mismo para agendar citas, gestionar tus recetas o administrar tu consultorio. ¡Es fápido y rácil!
            </p>
            <div class="mt-4 d-none d-lg-block">
                <img src="{{ asset('images/register-illustration.svg') }}" alt="" class="img-fluid" style="max-height: 200px; opacity: 0.8;">
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card register-card p-4 bg-white">
                <div class="card-body">
                    <h3 class="text-center fw-bold mb-2">Crear Cuenta</h3>
                    <p class="text-center text-muted small mb-4">Ingresa tus datos para comenzar</p>

                    <form method="POST" action="{{ route('register') }}">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label text-muted small ps-3 fw-bold">Nombre Completo</label>
                            <input id="name" type="text" class="form-control form-control-pill @error('name') is-invalid @enderror" 
                                   name="name" value="{{ old('name') }}" required autocomplete="name" autofocus
                                   placeholder="Ej: Juan Pérez">
                            @error('name')
                                <span class="invalid-feedback ps-3"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-muted small ps-3 fw-bold">Correo Electrónico</label>
                            <input id="email" type="email" class="form-control form-control-pill @error('email') is-invalid @enderror" 
                                   name="email" value="{{ old('email') }}" required autocomplete="email"
                                   placeholder="juan@ejemplo.com">
                            @error('email')
                                <span class="invalid-feedback ps-3"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-muted small ps-3 fw-bold">¿Cómo usarás la plataforma?</label>
                            
                            <div class="position-relative">
                                <span class="position-absolute text-muted" 
                                    style="top: 50%; transform: translateY(-50%); left: 20px; z-index: 10; pointer-events: none;">
                                    <i class="bi bi-person-badge fs-5"></i>
                                </span>
                                <select name="role" 
                                        class="form-select form-select-pill @error('role') is-invalid @enderror" 
                                        style="padding-left: 50px !important; padding-top: 12px; padding-bottom: 12px;" 
                                        required>
                                    <option value="paciente">Paciente (Busco atención médica)</option>
                                    <option value="doctor">Doctor (Quiero ofrecer consultas)</option>
                                    <option value="farmacia">Farmacia (Administro una farmacia)</option>
                                </select>

                            </div>
                            
                            @error('role')
                                <span class="text-danger small ps-3 mt-1 d-block"><strong>{{ $message }}</strong></span>
                            @enderror
</div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted small ps-3 fw-bold">Contraseña</label>
                                <input id="password" type="password" class="form-control form-control-pill @error('password') is-invalid @enderror" 
                                       name="password" required autocomplete="new-password" placeholder="••••••••">
                                @error('password')
                                    <span class="invalid-feedback ps-3"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted small ps-3 fw-bold">Confirmar</label>
                                <input id="password-confirm" type="password" class="form-control form-control-pill" 
                                       name="password_confirmation" required autocomplete="new-password" placeholder="••••••••">
                            </div>
                        </div>

                        <div class="d-grid mb-4 mt-2">
                            <button type="submit" class="btn btn-navy shadow-sm py-2">
                                {{ __('Registrarme') }}
                            </button>
                        </div>

                        <div class="text-center small">
                            ¿Ya tienes una cuenta? 
                            <a href="{{ route('login') }}" class="fw-bold text-decoration-none" style="color: #0d2e4e;">
                                Iniciar sesión
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection