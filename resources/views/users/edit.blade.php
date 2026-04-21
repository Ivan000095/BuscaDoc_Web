<?php
use Illuminate\Support\Str;

$isDoctor = $user->role === 'doctor';
$isPharmacy = $user->role === 'farmacia';
$isPatient = $user->role === 'paciente';
?>

<x-layout>
    <head>
        <style>
            body { background-color: #f3f4f6; }
            .soft-card {
                background: white; border: none; border-radius: 24px;
                box-shadow: 0 10px 40px rgba(0, 0, 0, 0.05); overflow: hidden;
            }
            .text-navy { color: #0d2e4e !important; }
            .text-label {
                font-weight: 700; color: #64748b; font-size: 0.85rem;
                text-transform: uppercase; letter-spacing: 1px;
            }
            .form-control, .form-select {
                border-radius: 12px; border: 1px solid #e2e8f0; padding: 0.75rem 1rem;
            }
            .form-control:focus {
                border-color: #0d2e4e; box-shadow: 0 0 0 0.25rem rgba(13, 46, 78, 0.1);
            }
            .profile-preview-container {
                width: 150px; height: 150px; border-radius: 24px;
                overflow: hidden; margin-bottom: 1rem; position: relative;
                background: #e9ecef; border: 4px solid white; box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            }
            .profile-preview { width: 100%; height: 100%; object-fit: cover; }
        </style>
    </head>

    <div class="container py-5">
        <div class="mb-4 d-flex align-items-center justify-content-between">
            <a href="{{ route('users.show', $user->id) }}" class="btn btn-light rounded-pill px-4 shadow-sm text-muted">
                <i class="bi bi-arrow-left me-2"></i>Cancelar y Volver
            </a>
            <h3 class="fw-bold text-navy mb-0">Editar Perfil</h3>
        </div>
        @if ($errors->any())
            <div class="alert alert-danger rounded-4 mb-4">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form action="{{ route('users.update', $user->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="soft-card p-4 text-center mb-4">
                        <span class="text-label d-block mb-3">Foto de Perfil</span>
                        <div class="d-flex justify-content-center">
                            <div class="profile-preview-container">
                                <img src="{{ $user->foto ? asset('storage/' . $user->foto) : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=random' }}" 
                                     class="profile-preview" id="previewImg">
                            </div>
                        </div>
                        <input type="file" name="foto" class="form-control form-control-sm mt-2" accept="image/*" onchange="previewFile(this)">
                        <small class="text-muted d-block mt-2">Formatos: JPG, PNG. Máx 2MB</small>
                    </div>

                    <div class="soft-card p-4">
                        <h5 class="fw-bold text-navy mb-4">Credenciales</h5>

                        <div class="mb-0">
                            <label class="text-label mb-2">Nueva Contraseña</label>
                            <input type="password" name="password" class="form-control" placeholder="Dejar en blanco para mantener">
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="soft-card p-5 mb-4 border-start border-4 border-navy">
                        <h4 class="mb-4 fw-bold text-navy"><i class="bi bi-person-fill me-2"></i>Información General</h4>
                        <div class="row g-3">
                            <div class="col-md-7">
                                <label class="text-label mb-2">Nombre Completo</label>
                                <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                            </div>
                            <div class="col-md-5">
                                <label class="text-label mb-2">Fecha de Nacimiento</label>
                                <div class="input-group">
                                    <span class="input-group-text "><i class="bi bi-calendar-date"></i></span>
                                    <input type="date" name="f_nacimiento" class="form-control" 
                                        value="{{ old('f_nacimiento', isset($user->f_nacimiento) ? \Carbon\Carbon::parse($user->f_nacimiento)->format('Y-m-d') : '') }}" 
                                        required max="{{ date('Y-m-d', strtotime('-18 years')) }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($isPatient)

                           {{-- 1. Buscamos el expediente principal entre todos los que tenga el usuario --}}
                        @php
                            $expedientePrincipal = $user->expedientes ? $user->expedientes->where('parentesco', 'Expediente Propio')->first() : null;
                        @endphp
                    <div class="soft-card p-5 mb-4 border-start border-4 border-navy">
                        <h4 class="mb-4 fw-bold text-navy"><i class="bi bi-person-vcard-fill me-2"></i>Ficha Médica</h4>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="text-label mb-2">Tipo de Sangre</label>
                                <select name="tipo_sangre" class="form-select">
                                    @foreach(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $tipo)
                                        <option value="{{ $tipo }}" {{ ($expedientePrincipal->tipo_sangre == $tipo) ? 'selected' : '' }}>{{ $tipo }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12">
                                <label class="text-label mb-2">Alergias</label>
                                <textarea name="alergias" class="form-control" rows="2">{{ old('alergias', $expedientePrincipal->alergias) }}</textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="text-label mb-2">Padecimientos</label>
                                <textarea name="padecimientos" class="form-control" rows="2">{{ old('padecimientos_cronicos', $expedientePrincipal->padecimientos_cronicos) }}</textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="text-label mb-2">Hábitos</label>
                                <textarea name="habitos" class="form-control" rows="2">{{ old('habitos', $expedientePrincipal->habitos_salud) }}</textarea>
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- Formulario específico para FARMACIAS --}}
                    @if($isPharmacy)
                    <div class="soft-card p-5 mb-4 border-start border-4 border-success">
                        <h4 class="mb-4 fw-bold text-navy"><i class="bi bi-shop-window me-2"></i>Datos de la Farmacia</h4>
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label class="text-label mb-2">Nombre Comercial</label>
                                <input type="text" name="nom_farmacia" class="form-control" value="{{ old('nom_farmacia', optional($user->farmacia)->nom_farmacia) }}">
                            </div>
                            <div class="col-md-4">
                                <label class="text-label mb-2">RFC</label>
                                <input type="text" name="rfc" class="form-control" value="{{ old('rfc', optional($user->farmacia)->rfc) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="text-label mb-2">Horario Entrada</label>
                                <input type="time" name="horario_entrada" class="form-control" value="{{ old('horario_entrada', optional($user->farmacia)->horario_entrada) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="text-label mb-2">Horario Salida</label>
                                <input type="time" name="horario_salida" class="form-control" value="{{ old('horario_salida', optional($user->farmacia)->horario_salida) }}">
                            </div>
                            <div class="col-12">
                                <label class="text-label mb-2">Descripción / Sobre Nosotros</label>
                                <textarea name="descripcion" class="form-control" rows="3">{{ old('descripcion', optional($user->farmacia)->descripcion) }}</textarea>
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- ==================== FORMULARIO DOCTOR (VERSIÓN CORREGIDA) ==================== --}}
            @if($isDoctor)
 <div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            {{-- Formulario principal apuntando a la función update --}}
            <form action="{{ route('users.update', $user->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="fw-bold text-navy">Editar Perfil Profesional</h2>
                    <button type="submit" class="btn btn-primary px-4 rounded-pill">
                        <i class="bi bi-check-lg me-2"></i>Guardar Cambios
                    </button>
                </div>

                {{-- Card de Datos Profesionales --}}
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4">
                        <h5 class="card-title text-navy mb-4 border-bottom pb-2">
                            <i class="bi bi-clipboard2-pulse me-2"></i>Información del Doctor
                        </h5>
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">Cédula Profesional</label>
                                <input type="text" name="cedula" class="form-control @error('cedula') is-invalid @enderror" 
                                    value="{{ old('cedula', $user->doctor?->cedula) }}" required>
                                @error('cedula') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold small">Costo de Consulta ($)</label>
                                <input type="number" name="costo" step="0.01" class="form-control @error('costo') is-invalid @enderror" 
                                    value="{{ old('costo', $user->doctor?->costo) }}" required>
                                @error('costo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-12">
                                <label class="form-label fw-bold small">Especialidad</label>
                                <select name="especialidad_id" class="form-select @error('especialidad_id') is-invalid @enderror">
                                    @foreach($especialidades as $especialidad)
                                        <option value="{{ $especialidad->id }}" 
                                            {{ old('especialidad_id', $user->doctor?->especialidad_id) == $especialidad->id ? 'selected' : '' }}>
                                            {{ $especialidad->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Sección de Disponibilidad con Alpine.js --}}
                @php
                    // Preparamos los datos para Alpine
                    $oldHorarios = old('horarios');
                    if (is_array($oldHorarios)) {
                        $horariosIniciales = $oldHorarios;
                    } elseif ($user->doctor?->disponibilidades->isNotEmpty()) {
                        $horariosIniciales = $user->doctor->disponibilidades->map(fn($d) => [
                            'dia' => (string)$d->dia_semana,
                            'inicio' => substr($d->hora_inicio, 0, 5),
                            'fin' => substr($d->hora_fin, 0, 5)
                        ])->toArray();
                    } else {
                        $horariosIniciales = [['dia' => '1', 'inicio' => '09:00', 'fin' => '14:00']];
                    }
                @endphp

                <div class="card border-0 shadow-sm rounded-4" 
                     x-data="{ 
                        horarios: @js($horariosIniciales),
                        add() { this.horarios.push({dia: '1', inicio: '09:00', fin: '18:00'}) },
                        remove(i) { if(this.horarios.length > 1) this.horarios.splice(i, 1) }
                     }">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="card-title text-navy m-0">
                                <i class="bi bi-calendar3 me-2"></i>Horarios de Atención
                            </h5>
                            <div class="d-flex align-items-center">
                                <label class="me-2 small fw-bold text-muted">Duración de Cita:</label>
                                <select name="duracion_cita" class="form-select form-select-sm" style="width: 140px;">
                                    @foreach([15, 30, 45, 60] as $min)
                                        <option value="{{ $min }}" {{ old('duracion_cita', $user->doctor?->duracion_cita) == $min ? 'selected' : '' }}>
                                            {{ $min }} minutos
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-borderless align-middle">
                                <thead class="table-light">
                                    <tr class="small text-uppercase text-muted">
                                        <th>Día de la semana</th>
                                        <th>Hora Inicio</th>
                                        <th>Hora Fin</th>
                                        <th width="50"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="(h, index) in horarios" :key="index">
                                        <tr>
                                            <td>
                                                <select :name="`horarios[${index}][dia]`" x-model="h.dia" class="form-select">
                                                    <option value="1">Lunes</option>
                                                    <option value="2">Martes</option>
                                                    <option value="3">Miércoles</option>
                                                    <option value="4">Jueves</option>
                                                    <option value="5">Viernes</option>
                                                    <option value="6">Sábado</option>
                                                    <option value="0">Domingo</option>
                                                </select>
                                            </td>
                                            <td>
                                                <input type="time" :name="`horarios[${index}][inicio]`" x-model="h.inicio" class="form-control" required>
                                            </td>
                                            <td>
                                                <input type="time" :name="`horarios[${index}][fin]`" x-model="h.fin" class="form-control" required>
                                            </td>
                                            <td>
                                                <button type="button" @click="remove(index)" class="btn btn-outline-danger btn-sm border-0" x-show="horarios.length > 1">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>

                        <button type="button" @click="add()" class="btn btn-outline-primary btn-sm rounded-pill mt-2">
                            <i class="bi bi-plus-lg me-1"></i> Agregar Bloque
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
            @endif

                    {{-- ==================== FIN FORMULARIO DOCTORES ==================== --}}

                    {{-- Botón de Guardar --}}
                    <div class="mt-4">
                        <button type="submit" class="btn btn-navy rounded-pill py-3 px-5 shadow-sm w-100 fs-5">
                            <i class="bi bi-check-circle me-2"></i>Guardar Cambios
                        </button>
                    </div>

                </div>
            </div>
        </form>
    </div>




<script>
function previewFile(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => document.getElementById("previewImg").src = e.target.result;
        reader.readAsDataURL(input.files[0]);
    }
}

</script>




</x-layout>