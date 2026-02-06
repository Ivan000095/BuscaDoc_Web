@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Administración de Farmacias</h1>
        <a href="{{ route('admin.farmacias.create') }}" class="btn btn-primary">
            + Nueva Farmacia
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Farmacia</th>
                    <th>RFC</th>
                    <th>Teléfono</th>
                    <th>Dueño (Usuario)</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($farmacias as $f)
                    <tr>
                        <td>{{ $f->id }}</td>
                        <td>{{ $f->nom_farmacia }}</td>
                        <td>{{ $f->rfc ?? '—' }}</td>
                        <td>{{ $f->telefono ?? '—' }}</td>
                        <td>
                            @if($f->user)
                                {{ $f->user->name }}<br>
                                <small class="text-muted">{{ $f->user->email }}</small>
                            @else
                                <span class="text-danger">Sin dueño</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('admin.farmacias.edit', $f->id) }}" class="btn btn-sm btn-primary">
                                    Editar
                                </a>
                                <form action="{{ route('admin.farmacias.destroy', $f->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('¿Eliminar esta farmacia?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">Eliminar</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">No hay farmacias registradas.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection