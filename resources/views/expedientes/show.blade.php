<x-layout>
    <div class="container py-5">
        
        {{-- ENCABEZADO --}}
        <div class="d-flex align-items-center mb-5 pb-3 border-bottom">
            <a href="{{ url()->previous() }}" class="btn btn-light rounded-circle shadow-sm me-3 d-flex align-items-center justify-content-center btn-back-custom" style="width: 45px; height: 45px;">
                <x-mcr-angle-left style="width: 1.2rem; color: var(--brand-navy);" />
            </a>
            <div>
                <h3 class="fw-bold text-navy mb-1">Ficha Médica Detallada</h3>
                <p class="text-muted small mb-0 d-flex align-items-center">
                    <x-mcr-user-alt class="me-1" style="width: 1rem;" /> Dueño de la cuenta: <span class="fw-bold ms-1 text-dark">{{ $expediente->user->name }}</span>
                    <span class="mx-2">•</span>
                    <x-mcr-envelope class="me-1" style="width: 1rem;" /> {{ $expediente->user->email }}
                </p>
            </div>
            
            @if(Auth::id() == $expediente->user_id)
                <div class="ms-auto">
                    <a href="{{ route('expedientes.edit', $expediente->id) }}" class="btn btn-navy rounded-pill px-4 py-2 shadow-sm d-flex align-items-center gap-2 transition-all hover-scale">
                        <x-mcr-pen style="width: 1.1rem;" />
                        <span class="d-none d-md-inline fw-bold">Editar Información</span>
                    </a>
                </div>
            @endif
        </div>

        <div class="row g-4">
            
            {{-- COLUMNA IZQUIERDA: Tarjeta Principal --}}
            {{-- COLUMNA IZQUIERDA: Tarjeta Principal --}}
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 p-0 mb-4 overflow-hidden card-info">
                    <div class="p-4 py-5 text-center text-white position-relative" style="background: linear-gradient(135deg, var(--brand-navy) 0%, var(--brand-navy-light) 100%);">
                        
                        {{-- Efecto de fondo (Más sutil y rotado) --}}
                        <div class="position-absolute" style="top: -10px; right: -15px; opacity: 0.04; transform: rotate(-15deg);">
                            <x-mcr-user-circle style="width: 160px; height: 160px;" />
                        </div>

                        {{-- Avatar o Foto --}}
                        <div class="mb-3 position-relative z-1 d-inline-block">
                            @if($expediente->parentesco == 'Yo mismo' && Auth::user()->foto)
                                <img src="{{ asset('storage/' . Auth::user()->foto) }}" 
                                     alt="{{ $expediente->nombre_completo }}" 
                                     class="rounded-circle shadow-lg" 
                                     style="width: 90px; height: 90px; object-fit: cover; border: 4px solid rgba(255,255,255,0.3);">
                            @else
                                {{-- Contraste mejorado: Fondo blanco con texto navy --}}
                                <div class="bg-white rounded-circle d-flex align-items-center justify-content-center shadow-lg mx-auto" 
                                     style="width: 90px; height: 90px; border: 4px solid rgba(255,255,255,0.3); color: var(--brand-navy);">
                                    <span class="display-5 fw-bold">{{ strtoupper(substr($expediente->nombre_completo, 0, 1)) }}</span>
                                </div>
                            @endif
                        </div>

                        <div class="position-relative z-1">
                            <h5 class="fw-bold mb-2">{{ $expediente->nombre_completo }}</h5>
                            <span class="badge bg-white text-navy rounded-pill px-3 py-2 shadow-sm fw-bold small">
                                {{ $expediente->parentesco == 'Yo mismo' ? 'Expediente Propio' : $expediente->parentesco }}
                            </span>
                        </div>
                    </div>
                    
                    <div class="card-body p-4 bg-white">
                        <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                            <span class="text-muted d-flex align-items-center"><x-mcr-user-circle class="me-2" style="width: 1.1rem;"/> Género</span>
                            <span class="fw-bold text-dark">{{ ucfirst($expediente->genero) }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                            <span class="text-muted d-flex align-items-center"><x-mcr-clock class="me-2" style="width: 1.1rem;"/> Edad</span>
                            @php
                            $nacimiento = \Carbon\Carbon::parse($expediente->fecha_nacimiento);
                            $diferencia = now()->diff($nacimiento);

                            if ($diferencia->y > 0) {
                                $edadTexto = $diferencia->y . ($diferencia->y == 1 ? ' año' : ' años');
                            } elseif ($diferencia->m > 0) {
                                $edadTexto = $diferencia->m . ($diferencia->m == 1 ? ' mes' : ' meses');
                            } elseif ($diferencia->d > 0) {
                                $edadTexto = $diferencia->d . ($diferencia->d == 1 ? ' día' : ' días');
                            } else {
                                $edadTexto = 'Recién nacido';
                            }
                        @endphp
                        <span class="fw-bold text-dark">{{ $edadTexto }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted d-flex align-items-center"><x-mcl-calendar class="me-2" style="width: 1.1rem;"/> Nacimiento</span>
                            <span class="fw-bold text-dark">{{ \Carbon\Carbon::parse($expediente->fecha_nacimiento)->isoFormat('LL') }}</span>
                        </div>
                    </div>
                </div>
                    
                <div class="card border-0 shadow-sm rounded-4 p-4 text-center bg-white card-info d-flex flex-row align-items-center justify-content-between">
                    <div class="text-start">
                        <h6 class="text-muted small fw-bold mb-1 text-uppercase tracking-wider">Grupo Sanguíneo</h6>
                        <p class="small text-muted mb-0">Información vital</p>
                    </div>
                    <div class="d-flex align-items-center justify-content-center bg-danger-subtle rounded-4 p-3">
                        <x-mcl-test-tube class="text-danger me-2" style="width: 2rem; height: 2rem;" />
                        <span class="fs-3 fw-bold text-danger lh-1">{{ $expediente->tipo_sangre ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>

            {{-- COLUMNA DERECHA: Historial Clínico --}}
            <div class="col-lg-8">
                
                {{-- Tarjetas de Antecedentes --}}
                <div class="row g-4 mb-4">
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white card-info">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-warning-subtle p-2 rounded-3 me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <x-mcr-triangle-exclamation class="text-warning" style="width: 1.2rem;" />
                                </div>
                                <h6 class="fw-bold mb-0 text-navy">Alergias Conocidas</h6>
                            </div>
                            <p class="text-muted mb-0 small lh-base">
                                {{ $expediente->alergias ?? 'No se tienen registros de alergias.' }}
                            </p>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white card-info">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-info-subtle p-2 rounded-3 me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <x-mcr-heart class="text-info" style="width: 1.2rem;" />
                                </div>
                                <h6 class="fw-bold mb-0 text-navy">Padecimientos Crónicos</h6>
                            </div>
                            <p class="text-muted mb-0 small lh-base">
                                {{ $expediente->padecimientos_cronicos ?? 'Sin padecimientos crónicos registrados.' }}
                            </p>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white card-info">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-success-subtle p-2 rounded-3 me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <x-mcr-mug class="text-success" style="width: 1.2rem;" />
                                </div>
                                <h6 class="fw-bold mb-0 text-navy">Estilo de Vida y Hábitos</h6>
                            </div>
                            <div class="p-3 bg-surface rounded-4 border">
                                <p class="text-muted mb-0 small lh-base" style="white-space: pre-line;">
                                    {{ $expediente->habitos_salud ?? 'No se han descrito hábitos de salud.' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- TIMELINE: Notas Médicas --}}
                <div class="card border-0 shadow-sm rounded-4 bg-white">
                    <div class="card-header bg-white border-bottom pt-4 pb-3 px-4 d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <div class="bg-navy-subtle p-2 rounded-3 me-3 d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                                <x-mcr-folder-open class="text-navy" style="width: 1.5rem;" />
                            </div>
                            <div>
                                <h5 class="fw-bold mb-0 text-navy">Historial de Notas Médicas</h5>
                                <small class="text-muted">Consultas y diagnósticos previos</small>
                            </div>
                        </div>
                        <span class="badge bg-light text-navy border rounded-pill">{{ $expediente->notas->count() }} notas</span>
                    </div>

                    <div class="card-body p-4 p-md-5">
                        @if($expediente->notas->isEmpty())
                            <div class="text-center py-5">
                                <div class="bg-surface rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                    <x-mcr-file class="text-muted opacity-50" style="width: 2.5rem; height: 2.5rem;" />
                                </div>
                                <h6 class="fw-bold text-navy">El historial está vacío</h6>
                                <p class="text-muted small mb-0">Aún no se han registrado notas médicas después de las consultas.</p>
                            </div>
                        @else
                            <div class="medical-timeline pt-2">
                                @foreach($expediente->notas as $nota)
                                    <div class="timeline-item position-relative ps-4 pb-5">
                                        <div class="timeline-marker"></div>
                                        
                                        <div class="timeline-content p-4 rounded-4 bg-surface border-start border-navy border-4 shadow-sm">
                                            
                                            {{-- Cabecera de la nota --}}
                                            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-start mb-3 border-bottom pb-3">
                                                <div class="mb-3 mb-md-0">
                                                    <span class="badge rounded-pill bg-white text-navy border px-3 py-2 small shadow-sm d-inline-flex align-items-center mb-2">
                                                        <x-mcr-calendar class="me-2 text-navy" style="width: 0.9rem;" />
                                                        {{ $nota->created_at->isoFormat('LL') }}
                                                    </span>
                                                </div>
                                                <div class="text-md-end bg-white px-3 py-2 rounded-3 border shadow-sm">
                                                    <span class="d-block fw-bold text-navy mb-1"><x-mcr-stethoscope class="me-1 text-muted" style="width: 0.9rem;"/> Dr. {{ $nota->doctor->user->name ?? 'No especificado' }}</span>
                                                    <span class="badge bg-light text-secondary border">{{ $nota->doctor->especialidades->first()->nombre ?? 'General' }}</span>
                                                </div>
                                            </div>

                                            {{-- Contenido de la nota --}}
                                            <div class="row g-3">
                                                <div class="col-12">
                                                    <div class="p-3 bg-white rounded-3 border shadow-sm h-100">
                                                        <h6 class="small fw-bold text-navy text-uppercase tracking-wider d-flex align-items-center mb-2">
                                                            <x-mcr-search class="text-primary me-2" style="width: 1rem;" /> Diagnóstico
                                                        </h6>
                                                        <p class="text-muted small mb-0 lh-base">{{ $nota->diagnostico ?? 'Sin diagnóstico' }}</p>
                                                    </div>
                                                </div>

                                                <div class="col-12">
                                                    <div class="p-3 bg-white rounded-3 border shadow-sm h-100">
                                                        <h6 class="small fw-bold text-navy text-uppercase tracking-wider d-flex align-items-center mb-2">
                                                            <x-mcr-pen class="text-info me-2" style="width: 1rem;" /> Nota de seguimiento
                                                        </h6>
                                                        <p class="text-muted small mb-0 lh-base">{{ $nota->nota_seguimiento ?? 'Sin notas adicionales.' }}</p>
                                                    </div>
                                                </div>

                                                @if($nota->tratamiento)
                                                    <div class="col-12">
                                                        <div class="p-3 bg-primary-subtle border-primary-subtle rounded-3 border shadow-sm h-100">
                                                            <h6 class="small fw-bold text-primary text-uppercase tracking-wider d-flex align-items-center mb-2">
                                                                <x-mcr-pills class="text-primary me-2" style="width: 1rem;" /> Tratamiento Sugerido
                                                            </h6>
                                                            <p class="text-dark small mb-0 lh-base fw-medium">{{ $nota->tratamiento }}</p>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>

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
</x-layout>