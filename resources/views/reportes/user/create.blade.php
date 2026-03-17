<x-layout>

@section('content')
<div class="container d-flex justify-content-center py-5">
    <div class="card shadow-lg" style="max-width: 600px; width: 100%; border-radius: 10px; overflow: hidden;">
        
        <div class="card-header text-white text-center py-4" style="background-color: #1a2a40;">
            <h2 class="mb-0"><i class="fas fa-exclamation-triangle me-2"></i> Reportar</h2>
            <p class="mb-0 opacity-75">¿Tuviste un problema con este profesional?</p>
        </div>

        <div class="card-body p-4">
            <div class="mb-4">
                <p class="mb-1 text-muted">Administrador: <strong>{{ Auth::user()->name }}</strong></p>
                
                @if($usuario->role === 'farmacia')
                    <p class="mb-1"><strong>Farmacia:</strong> {{ $usuario->farmacia->nombre_comercial ?? $usuario->name }}</p>
                    <p class="mb-1"><strong>RFC:</strong> <span class="text-uppercase text-muted">{{ $usuario->farmacia->rfc ?? 'N/A' }}</span></p>
                @elseif($usuario->role === 'doctor')
                    <p class="mb-1"><strong>Doctor:</strong> {{ $usuario->doctor->nombre ?? $usuario->name }}</p>
                    <p class="mb-1"><strong>Especialidad:</strong> {{ $usuario->doctor->especialidad ?? 'General' }}</p>
                @endif
            </div>

            <hr class="my-4">

            <form action="{{ route('reportes.store') }}" method="POST">
                @csrf
                
                <input type="hidden" name="reportado_id" value="{{ $usuario->id }}">

                <div class="mb-4">
                    <label for="razon" class="form-label fw-bold">Motivo del reporte</label>
                    <textarea 
                        name="razon" 
                        id="razon" 
                        rows="6" 
                        class="form-control @error('razon') is-invalid @enderror" 
                        placeholder="Describe lo ocurrido..."
                        required>{{ old('razon') }}</textarea>
                    
                    @error('razon')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn text-white py-2 fw-bold" style="background-color: #1a2a40; border-radius: 5px;">
                        <i class="fas fa-paper-plane me-2"></i> Enviar reporte
                    </button>
                </div>
            </form>
        </div>

        <div class="card-footer bg-white border-0 text-center pb-4">
            <a href="{{ url()->previous() }}" class="text-decoration-none text-muted">
                <i class="fas fa-arrow-left me-1"></i> Volver
            </a>
        </div>
    </div>
</div>
</x-layout>