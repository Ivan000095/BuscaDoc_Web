@extends('layouts.app')

@section('content')
    <div class="container">
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

        @if (Auth::user()->role == 'admin')
            <div class="row mb-4">
                <div class="col-12">
                    <h2 class="fw-bold custom-text-dark">Panel de Administración</h2>
                    <p class="text-muted">Bienvenido, {{ Auth::user()->name }} </p>
                </div>
            </div>

            <div class="row justify-content-center">

                <div class="col-12 col-md-6 col-lg-3 mb-4">
                    <div class="card h-100 border-50 shadow-sm hover-card">
                        <div class="card-body text-center d-flex flex-column justify-content-center align-items-center p-4">

                            <img src="{{ asset('images/doctores.jpg') }}" alt="Ivan"
                                class="rounded-circle mb-3 shadow-sm object-fit-cover" style="width: 80px; height: 80px;">

                            <h5 class="card-title fw-bold custom-text-dark">Doctores</h5>
                            <a href="{{ route('doctores.index') }}"
                                class="btn btn-custom btn-sm stretched-link mt-2 rounded-pill px-4">Entrar</a>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-6 col-lg-3 mb-4">
                    <div class="card h-100 border-50 shadow-sm hover-card">
                        <div class="card-body text-center d-flex flex-column justify-content-center align-items-center p-4">

                            <img src="{{ asset('images/farmacias.jpeg') }}" alt=""
                                class="rounded-circle mb-3 shadow-sm object-fit-cover" style="width: 80px; height: 80px;">

                            <h5 class="card-title fw-bold custom-text-dark">Farmacias</h5>
                            <a href="#" class="btn btn-custom btn-sm stretched-link mt-2 rounded-pill px-4">Entrar</a>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-6 col-lg-3 mb-4">
                    <div class="card h-100 border-50 shadow-sm hover-card">
                        <div class="card-body text-center d-flex flex-column justify-content-center align-items-center p-4">

                            <img src="{{ asset('images/pacientes.jpg') }}" alt=""
                                class="rounded-circle mb-3 shadow-sm object-fit-cover" style="width: 80px; height: 80px;">

                            <h5 class="card-title fw-bold custom-text-dark">Pacientes</h5>
                            <a href="{{ route('pacientes.index') }}"
                                class="btn btn-custom btn-sm stretched-link mt-2 rounded-pill px-4">Entrar</a>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <h1>no sos admin pue verga</h1>
        @endif
    </div>
@endsection

@section('styles')
    <style>
        :root {
            --custom-dark-blue: #00213D;
        }

        .btn-custom {
            background-color: var(--custom-dark-blue);
            border-color: var(--custom-dark-blue);
            color: white;
            transition: all 0.3s ease;
        }

        .btn-custom:hover {
            background-color: #003366;
            border-color: #003366;
            color: white;
            transform: scale(1.05);
        }

        .custom-text-dark {
            color: var(--custom-dark-blue);
        }

        .hover-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            background: #ffffff;
        }

        .hover-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 1rem 2rem rgba(0, 33, 61, 0.15) !important;
        }

        .object-fit-cover {
            object-fit: cover;
        }
    </style>
@endsection