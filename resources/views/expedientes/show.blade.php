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
            @if(Auth::id() == $expediente->user_id)
            <div class="ms-auto">
                <a href="{{ route('expedientes.edit', $expediente->id) }}" class="btn btn-navy rounded-pill px-4 py-2 shadow-sm d-flex align-items-center gap-2 transition-all">
                    <i class="bi bi-pencil-square fs-5"></i>
                    <span class="d-none d-md-inline fw-semibold">Editar Información</span>
                </a>
            </div>
            @endif
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
                            <span class="fw-bold">{{ \Carbon\Carbon::parse($expediente->fecha_nacimiento)->diffForHumans(null, true) }}</span>
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
                                            
                        <div class="col-12 mt-4">
                            <div class="card border-0 shadow-sm rounded-4 bg-white">
                                <div class="card-header bg-white border-0 pt-4 px-4">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-primary bg-opacity-10 p-2 rounded-3 me-3">
                                            <i class="bi bi-journal-medical text-primary fs-5"></i>
                                        </div>
                                        <h6 class="fw-bold mb-0 text-navy">Historial de Notas Médicas</h6>
                                    </div>
                                </div>

                                <div class="card-body p-4">
                                    @if($expediente->notas->isEmpty())
                                        <div class="text-center py-5">
                                            <img src="https://cdn-icons-png.flaticon.com/512/6598/6598519.png" alt="No hay notas" style="width: 80px; opacity: 0.5;">
                                            <p class="text-muted mt-3 mb-0">Aún no se han registrado notas médicas para este paciente.</p>
                                        </div>
                                    @else
                                        <div class="medical-timeline">
                                            @foreach($expediente->notas as $nota)
                                                <div class="timeline-item position-relative ps-4 pb-4">
                                                    <div class="timeline-marker"></div>
                                                    
                                                    <div class="timeline-content p-3 rounded-4 bg-light bg-opacity-50 border-start border-primary border-4 shadow-sm">
                                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                                            <div>
                                                                <div class="d-flex align-items-center gap-2 mb-1">
                                                                    <span class="badge rounded-pill bg-white text-navy border px-3 small shadow-xs">
                                                                        <i class="bi bi-calendar3 me-1 text-primary"></i>
                                                                        {{ $nota->created_at->isoFormat('LL') }}
                                                                    </span>
                                                                </div>

                                                            </div>
                                                            <div class="text-end">
                                                                <small class="d-block fw-bold text-navy">Dr. {{ $nota->doctor->user->name ?? 'No especificado' }}</small>
                                                                <small class="text-muted x-small text-uppercase">{{ $nota->doctor->especialidades->first()->nombre }}</small>
                                                            </div>
                                                        </div>

                                                            <div class="mt-2 p-3 bg-white rounded-3 border-0 shadow-xs">
                                                                <div class="d-flex align-items-center mb-1">
                                                                    <i class="bi bi-capsule text-primary me-2"></i>
                                                                    <strong class="small text-navy text-uppercase">Diagnostico:</strong>
                                                                </div>
                                                                <p class="text-muted small mb-0">{{ $nota->diagnostico ?? 'Sin diagnostico' }}</p>
                                                            </div>



                                                            <div class="mt-2 p-3 bg-white rounded-3 border-0 shadow-xs">
                                                                <div class="d-flex align-items-center mb-1">
                                                                    <i class="bi bi-capsule text-primary me-2"></i>
                                                                    <strong class="small text-navy text-uppercase">Nota de seguimiento:</strong>
                                                                </div>
                                                                <p class="text-muted small mb-0">{{ $nota->nota_seguimiento }}</p>
                                                            </div>

                                                        @if($nota->tratamiento)
                                                            <div class="mt-2 p-3 bg-white rounded-3 border-0 shadow-xs">
                                                                <div class="d-flex align-items-center mb-1">
                                                                    <i class="bi bi-capsule text-primary me-2"></i>
                                                                    <strong class="small text-navy text-uppercase">Tratamiento sugerido:</strong>
                                                                </div>
                                                                <p class="text-muted small mb-0">{{ $nota->tratamiento }}</p>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                </div>
            </div>
        </div>
    </div>

    <style>
        /* Estilos para la línea de tiempo */
        .medical-timeline {
            position: relative;
        }

        .timeline-item:not(:last-child)::before {
            content: "";
            position: absolute;
            left: 0;
            top: 0;
            width: 2px;
            height: 100%;
            background-color: #e2e8f0; /* Color gris suave */
        }

        .timeline-marker {
            position: absolute;
            left: -5px;
            top: 5px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background-color: #0d6efd; /* Tu color primario */
            border: 3px solid #fff;
            box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.1);
            z-index: 1;
        }

        .timeline-content {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            background-color: #f8fafc;
        }

        .timeline-content:hover {
            transform: translateX(5px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.05) !important;
            background-color: #fff;
        }

        .x-small { font-size: 0.75rem; }
        .shadow-xs { box-shadow: 0 2px 4px rgba(0,0,0,0.02); }
        .text-navy { color: #0f172a; }
    </style>

    <style>
        
        .bg-light { background-color: #f8fafc !important; }
        .card { transition: transform 0.2s ease; }
        .card:hover { transform: translateY(-5px); }
    </style>
</x-layout>