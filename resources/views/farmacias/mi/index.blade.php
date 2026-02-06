@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Mi Farmacia</h1>
        <a href="{{ route('farmacias.mi.editar') }}" class="btn btn-primary">
            <i class="bi bi-pencil me-1"></i>Editar información
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <h2 class="mb-4">{{ $farmacia->nom_farmacia }}</h2>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="text-muted">RFC</label>
                    <p class="fs-5">{{ $farmacia->rfc ?? '—' }}</p>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="text-muted">Teléfono</label>
                    <p class="fs-5">{{ $farmacia->telefono ?? '—' }}</p>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="text-muted">Horario</label>
                    <p class="fs-5">{{ $farmacia->horario ?? '—' }}</p>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="text-muted">Días de operación</label>
                    <p class="fs-5">{{ $farmacia->dias_op ?? '—' }}</p>
                </div>
            </div>

            <div class="mt-4">
                <label class="text-muted">Descripción</label>
                <p class="fs-5">{{ $farmacia->descripcion ?? 'Sin descripción' }}</p>
            </div>
        </div>
    </div>
</div>
@endsection