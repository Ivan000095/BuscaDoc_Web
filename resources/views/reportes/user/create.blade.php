<x-layout>

    <head>
        <style>
            .head_card{
                background-color: #1e293b;
            }
            .btn-navy {
                background-color: #0f172a;
                color: white;
                border-radius: 50px;
                padding: 10px 25px;
                font-weight: 500;
                border: none;
                transition: transform 0.2s;
            }

            .btn-navy:hover {
                background-color: #1e293b;
                color: white;
                transform: translateY(-2px);
            }
        </style>
    </head>

@section('title', 'Reportar a ' . $usuario->name)

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="head_card text-white p-4 text-center">
                    <h3 class="mb-1"><i class="bi bi-exclamation-triangle-fill"></i> Reportar</h3>
                    <p class="mb-0 text-white-75">¿Tuviste un problema con este profesional?</p>
                </div>

                <div class="p-4 bg-light border-bottom">
                    <h5 class="mb-2">{{'Administrador : '. $usuario->name }}</h5>
                    
                    @if($usuario->role === 'doctor' && $usuario->doctor)
                        <p class="text-muted mb-0">
                            <strong>Cédula profesional:</strong> {{ $usuario->doctor->cedula ?? 'No registrada' }}
                        </p>
                    @elseif($usuario->role === 'farmacia' && $usuario->farmacia)
                        <p class="text-muted mb-1">
                            <strong>Farmacia:</strong> {{ $usuario->farmacia->nom_farmacia ?? 'Sin nombre' }}
                        </p>
                        <p class="text-muted mb-0">
                            <strong>RFC:</strong> {{ $usuario->farmacia->rfc ?? 'No registrado' }}
                        </p>
                    @else
                        <p class="text-muted mb-0">Rol: {{ ucfirst($usuario->role) }}</p>
                    @endif
                </div>

                <div class="p-4">
                    <form action="{{ route('reportes.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="reportado_id" value="{{ $usuario->id }}">

                        <div class="mb-4">
                            <label for="descripcion" class="form-label fw-bold">
                                Motivo del reporte
                            </label>
                            <textarea 
                                name="descripcion" 
                                id="descripcion" 
                                class="form-control"
                                rows="5"
                                placeholder="Describe lo ocurrido..."
                                maxlength="2000"
                                required
                            >{{ old('descripcion') }}</textarea>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-navy btn-lg">
                                <i class="bi bi-send-check"></i> Enviar reporte
                            </button>
                        </div>
                    </form>
                </div>

                <div class="p-3 bg-light text-center">
                    <a href="{{ url()->previous() }}" class="text-secondary text-decoration-none">
                        <i class="bi bi-arrow-left"></i> Volver
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
</x-layout>