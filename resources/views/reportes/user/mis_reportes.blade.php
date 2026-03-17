<x-layout> 

@section('title', 'Mis reportes')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-flag"></i> Mis reportes</h2>
        <a href="{{ url()->previous() }}" class="btn btn-outline-secondary btn-sm">
            ← Volver
        </a>
    </div>

    @if($reportes->isEmpty())
        <div class="alert alert-info text-center py-4">
            <i class="bi bi-file-earmark-text fs-1 mb-2"></i>
            <p class="mb-0">Aún no has enviado ningún reporte.</p>
            <small>Puedes reportar a un doctor o farmacia desde su perfil.</small>
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Reportado</th>
                        <th>Descripción</th>
                        <th>Estado</th>
                        <th>Fecha</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($reportes as $r)
                        <tr>
                            <td>
                                @if($r->reportado)
                                    <strong>{{ $r->reportado->name }}</strong><br>
                                    <small class="text-muted">{{ ucfirst($r->reportado->role) }}</small>
                                @else
                                    <em>Usuario eliminado</em>
                                @endif
                            </td>
                            <td>
                                {{ Str::limit($r->razon, 80) }}
                            </td>
                            <td>
                            @php
                            $estados = [
                            'pendiente' => ['text' => 'Pendiente', 'class' => 'bg-warning text-dark'],
                            'en_proceso' => ['text' => 'En proceso', 'class' => 'bg-info text-white'],
                            'resuelto' => ['text' => 'Resuelto', 'class' => 'bg-success text-white'],
                            'descartado' => ['text' => 'Descartado', 'class' => 'bg-secondary text-white'],
                            ];
                            $estado = $estados[$r->estado] ?? ['text' => ucfirst($r->estado), 'class' => 'bg-light text-dark'];
                            @endphp
                            <span class="badge {{ $estado['class'] }}">{{ $estado['text'] }}</span>
                            </td>
                            <td>{{ $r->created_at->format('d M Y') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{ $reportes->links() }}
    @endif
</div>
</x-layout>