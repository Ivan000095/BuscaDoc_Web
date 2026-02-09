<x-layout>

@section('title', 'Gestión de Reportes')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-6">
            <h2><i class="bi bi-exclamation-triangle"></i> Reportes</h2>
            <p class="text-muted">Lista de reportes enviados por usuarios.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Reportador</th>
                            <th>Reportado</th>
                            <th>Rol</th>
                            <th>Estado</th>
                            <th>Fecha</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reportes as $r)
                            <tr>
                                <td>#{{ $r->id }}</td>
                                <td>{{ $r->reportador?->name ?? 'Eliminado' }}</td>
                                <td>{{ $r->reportado?->name ?? 'Eliminado' }}</td>
                                <td>
                                    @php
                                        $rol = $r->reportado?->role ?? 'otro';
                                        $rolClass = match($rol) {
                                            'doctor' => 'primary',
                                            'farmacia' => 'success',
                                            'paciente' => 'info',
                                            default => 'secondary',
                                        };
                                    @endphp
                                    <span class="badge bg-{{ $rolClass }} text-white">
                                        {{ ucfirst($rol) }}
                                    </span>
                                </td>
                                <td>
                                    @php
                                        $estado = $r->estado ?? 'pendiente';
                                        $estadoClass = match($estado) {
                                            'pendiente' => 'warning text-dark',
                                            'en_proceso' => 'info text-white',
                                            'resuelto' => 'success text-white',
                                            'descartado' => 'secondary text-white',
                                            default => 'light text-dark',
                                        };
                                    @endphp
                                    <span class="badge bg-{{ $estadoClass }}">
                                        {{ ucfirst($estado) }}
                                    </span>
                                </td>
                                <td>{{ $r->created_at->format('d M Y') }}</td>
                                <td>
                                    <a href="{{ route('admin.reportes.show', $r->id) }}" 
                                    class="btn btn-sm btn-outline-navy">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{ $reportes->links() }}
        </div>
    </div>
</div>
</x-layout>