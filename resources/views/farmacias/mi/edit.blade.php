@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Editar mi farmacia</h1>
        <a href="{{ route('farmacias.mi') }}" class="btn btn-secondary">
            ← Volver
        </a>
    </div>

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
            <form action="{{ route('farmacias.mi.actualizar') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row mb-3">
                    <div class="col-md-8">
                        <label for="nom_farmacia" class="form-label">Nombre de la farmacia *</label>
                        <input type="text" 
                        class="form-control @error('nom_farmacia') is-invalid @enderror" 
                        id="nom_farmacia" 
                        name="nom_farmacia" 
                        value="{{ old('nom_farmacia', $farmacia->nom_farmacia) }}" 
                        required>
                        @error('nom_farmacia')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="rfc" class="form-label">RFC</label>
                        <input type="text" 
                        class="form-control @error('rfc') is-invalid @enderror" 
                        id="rfc" 
                        name="rfc" 
                        value="{{ old('rfc', $farmacia->rfc) }}">
                        @error('rfc')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="telefono" class="form-label">Teléfono</label>
                        <input type="text" 
                        class="form-control @error('telefono') is-invalid @enderror" 
                        id="telefono" 
                        name="telefono" 
                        value="{{ old('telefono', $farmacia->telefono) }}">
                        @error('telefono')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="horario" class="form-label">Horario de atención</label>
                        <input type="text" 
                        class="form-control @error('horario') is-invalid @enderror" 
                        id="horario" 
                        name="horario" 
                        placeholder="Ej: 8:00 AM - 9:00 PM"
                        value="{{ old('horario', $farmacia->horario) }}">
                        @error('horario')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="dias_op" class="form-label">Días de operación</label>
                        <input type="text" 
                        class="form-control @error('dias_op') is-invalid @enderror" 
                        id="dias_op" 
                        name="dias_op" 
                        placeholder="Ej: Lunes a Sábado"
                        value="{{ old('dias_op', $farmacia->dias_op) }}">
                        @error('dias_op')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="descripcion" class="form-label">Descripción</label>
                    <textarea class="form-control @error('descripcion') is-invalid @enderror" 
                            id="descripcion" 
                            name="descripcion" 
                            rows="3">{{ old('descripcion', $farmacia->descripcion) }}</textarea>
                    @error('descripcion')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Guardar cambios</button>
                    <a href="{{ route('farmacias.mi') }}" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection