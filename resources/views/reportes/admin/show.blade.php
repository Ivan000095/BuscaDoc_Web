<x-layout>

@section('title', 'Reporte #' . $reporte->id)

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-6">
            <h2><i class="bi bi-flag-fill text-danger"></i> Reporte #{{ $reporte->id }}</h2>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('admin.reportes.index') }}" class="btn btn-outline-secondary">
                ← Volver
            </a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Descripción</h5>
                </div>
                <div class="card-body">
                    @if($reporte->descripcion)
                        <p class="fs-5">{{ $reporte->descripcion }}</p>
                    @else
                        <p class="text-muted fst-italic">Sin descripción.</p>
                    @endif

                    <hr>

                    <div class="row">
                        <div class="col-md-6">
                            <h6><i class="bi bi-person-circle text-primary"></i> Reportador</h6>
                            <p><strong>{{ $reporte->reportador?->name ?? 'Usuario eliminado' }}</strong></p>
                            <small class="text-muted">ID: {{ $reporte->reportador_id }}</small>
                        </div>
                        <div class="col-md-6">
                            <h6><i class="bi bi-exclamation-triangle text-danger"></i> Reportado</h6>
                            <p><strong>{{ $reporte->reportado?->name ?? 'Usuario eliminado' }}</strong></p>
                            <small class="text-muted">ID: {{ $reporte->reportado_id }}</small><br>
                            <small class="text-muted">Rol: {{ ucfirst($reporte->reportado?->role ?? '—') }}</small>
                            
                            @if($reporte->reportado?->role === 'doctor' && $reporte->reportado->doctor)
                                <br>
                                <small>Cédula: {{ $reporte->reportado->doctor->cedula ?? '—' }}</small>
                            @elseif($reporte->reportado?->role === 'farmacia' && $reporte->reportado->farmacia)
                                <br>
                                <small>Farmacia: {{ $reporte->reportado->farmacia->nom_farmacia ?? '—' }}</small><br>
                                <small>RFC: {{ $reporte->reportado->farmacia->rfc ?? '—' }}</small>
                            @endif
                        </div>
                    </div>

                    <div class="mt-3">
                        <small class="text-muted">
                            Enviado el: {{ $reporte->created_at->format('d M Y \a \l\a\s H:i') }}
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Actualizar estado</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.reportes.update', $reporte->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label">Estado actual</label>
                            <select name="estado" class="form-select">
                                <option value="pendiente" {{ $reporte->estado === 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                                <option value="en_proceso" {{ $reporte->estado === 'en_proceso' ? 'selected' : '' }}>En proceso</option>
                                <option value="resuelto" {{ $reporte->estado === 'resuelto' ? 'selected' : '' }}>Resuelto</option>
                                <option value="descartado" {{ $reporte->estado === 'descartado' ? 'selected' : '' }}>Descartado</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            Guardar cambios
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</x-layout>