@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h2>Editar Farmacia: {{ $farmacia->nom_farmacia }}</h2>

    <a href="{{ route('admin.farmacias.index') }}" class="btn btn-secondary mb-3">← Volver</a>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <form action="{{ route('admin.farmacias.update', $farmacia->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="user_id" class="form-label">Dueño (Usuario) *</label>
                    <select name="user_id" id="user_id" class="form-control @error('user_id') is-invalid @enderror" required>
                        <option value="">-- Selecciona un usuario --</option>
                        @foreach($usuarios as $u)
                            <option value="{{ $u->id }}" {{ ($farmacia->user_id == $u->id) ? 'selected' : '' }}>
                                {{ $u->name }} ({{ $u->email }})
                            </option>
                        @endforeach
                    </select>
                    @error('user_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="nom_farmacia" class="form-label">Nombre de la Farmacia *</label>
                    <input type="text" 
                           class="form-control @error('nom_farmacia') is-invalid @enderror" 
                           name="nom_farmacia" 
                           value="{{ old('nom_farmacia', $farmacia->nom_farmacia) }}" 
                           required>
                    @error('nom_farmacia')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="rfc" class="form-label">RFC</label>
                        <input type="text" class="form-control" name="rfc" value="{{ old('rfc', $farmacia->rfc) }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="telefono" class="form-label">Teléfono</label>
                        <input type="text" class="form-control" name="telefono" value="{{ old('telefono', $farmacia->telefono) }}">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="horario" class="form-label">Horario</label>
                        <input type="text" class="form-control" name="horario" value="{{ old('horario', $farmacia->horario) }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="dias_op" class="form-label">Días de operación</label>
                        <input type="text" class="form-control" name="dias_op" value="{{ old('dias_op', $farmacia->dias_op) }}">
                    </div>
                </div>

                <div class="mb-3">
                    <label for="descripcion" class="form-label">Descripción</label>
                    <textarea class="form-control" name="descripcion" rows="3">{{ old('descripcion', $farmacia->descripcion) }}</textarea>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Actualizar Farmacia</button>
                    <a href="{{ route('admin.farmacias.index') }}" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection