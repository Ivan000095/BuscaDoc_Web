<x-layout>
    <div class="container py-5">
        <div class="d-flex align-items-center mb-4">
            <a href="{{ url()->previous() }}" class="btn btn-white btn-sm rounded-pill shadow-sm me-3">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <h4 class="fw-bold text-navy mb-0">Ficha Médica Detallada</h4>
                <p class="text-muted small mb-0">Dueño de la cuenta: {{ $expediente->user->name }}</p>
                <p class="text-muted small mb-0">Correo de la cuenta: {{ $expediente->user->email }}</p>
            
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 p-4 mb-4" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); color: white;">
                    <div class="text-center mb-3">
                        <div class="bg-white bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                            <i class="bi bi-person-badge-fill fs-1 text-info"></i>
                        </div>
                    </div>
                    <div class="text-center">
                        <h5 class="fw-bold mb-1">{{ $expediente->nombre_completo }}</h5>
                        @if ($expediente->parentesco == 'Propio')
                        <span class="badge bg-info rounded-pill px-3">Expediente {{ ucfirst($expediente->parentesco) }}</span>
                        @else 
                        <span class="badge bg-info rounded-pill px-3">{{ ucfirst($expediente->parentesco) }}</span>
                        @endif
                    </div>
                    
                    <hr class="my-4 opacity-25">
                    
                    <div class="small">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="opacity-75">Género:</span>
                            <span class="fw-bold">{{ ucfirst($expediente->genero) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="opacity-75">Edad:</span>
                            <span class="fw-bold">{{ \Carbon\Carbon::parse($expediente->fecha_nacimiento)->age }} años</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="opacity-75">Nacimiento:</span>
                            <span class="fw-bold">{{ \Carbon\Carbon::parse($expediente->fecha_nacimiento)->isoFormat('LL') }}</span>
                        </div>
                    </div>
                </div>
                    
                <div class="card border-0 shadow-sm rounded-4 p-3 text-center bg-white">
                    <h6 class="text-muted small fw-bold mb-2 text-uppercase">Grupo Sanguíneo</h6>
                    <div class="d-flex align-items-center justify-content-center">
                        <i class="bi bi-droplet-fill text-danger fs-3 me-2"></i>
                        <span class="display-6 fw-bold text-navy">{{ $expediente->tipo_sangre ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="row g-4">
                    
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-danger bg-opacity-10 p-2 rounded-3 me-3">
                                    <i class="bi bi-exclamation-triangle-fill text-danger"></i>
                                </div>
                                <h6 class="fw-bold mb-0 text-navy">Alergias</h6>
                            </div>
                            <p class="text-muted mb-0">
                                {{ $expediente->alergias ?? 'No se tienen registros de alergias.' }}
                            </p>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-warning bg-opacity-10 p-2 rounded-3 me-3">
                                    <i class="bi bi-activity text-warning"></i>
                                </div>
                                <h6 class="fw-bold mb-0 text-navy">Padecimientos Crónicos</h6>
                            </div>
                            <p class="text-muted mb-0">
                                {{ $expediente->padecimientos_cronicos ?? 'Sin padecimientos crónicos registrados.' }}
                            </p>
                        </div>
                    </div>

                    <div class="col-12" >
                        <div class="card border-0  shadow-sm rounded-4 p-4 bg-white">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-success bg-opacity-10 p-2 rounded-3 me-3">
                                    <i class="bi bi-heart-pulse-fill text-success"></i>
                                </div>
                                <h6 class="fw-bold mb-0 text-navy">Estilo de Vida y Hábitos</h6>
                            </div>
                            <div class="p-3 bg-light rounded-4">
                                <p class="text-muted mb-0" style="white-space: pre-line;">
                                    {{ $expediente->habitos_salud ?? 'No se han descrito hábitos de salud.' }}
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-12" > 
                        
                        <div class="card border-0 shadow-sm  rounded-4 p-4 bg-white">
                            <h6 class="fw-bold mb-3 text-navy">Última Actualización</h6>
                            <div class="d-flex align-items-center text-muted small">
                                <i class="bi bi-calendar-check me-2"></i>
                                <span>Este expediente fue actualizado por última vez el {{ $expediente->updated_at->isoFormat('LL') }}</span>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <style>
        .text-navy { color: #0f172a; }
        .bg-light { background-color: #f8fafc !important; }
        .card { transition: transform 0.2s ease; }
        .card:hover { transform: translateY(-5px); }
    </style>
</x-layout>