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
                border-color: #0d2e4e; box-shadow: 0 0 0 0.25 row rgba(13, 46, 78, 0.1);
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
                        <div class="mb-3">
                            <label class="text-label mb-2">Correo Electrónico</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                        </div>
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

                    {{-- Formulario específico para PACIENTES --}}
                    @if($isPatient)
                    <div class="soft-card p-5 mb-4 border-start border-4 border-navy">
                        <h4 class="mb-4 fw-bold text-navy"><i class="bi bi-person-vcard-fill me-2"></i>Ficha Médica</h4>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="text-label mb-2">Tipo de Sangre</label>
                                <select name="tipo_sangre" class="form-select">
                                    @foreach(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $tipo)
                                        <option value="{{ $tipo }}" {{ (optional($user->patient)->tipo_sangre == $tipo) ? 'selected' : '' }}>{{ $tipo }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-8">
                                <label class="text-label mb-2">Contacto de Emergencia</label>
                                <input type="text" name="contacto_emergencia" class="form-control" value="{{ old('contacto_emergencia', optional($user->patient)->contacto_emergencia) }}">
                            </div>
                            <div class="col-12">
                                <label class="text-label mb-2">Alergias</label>
                                <textarea name="alergias" class="form-control" rows="2">{{ old('alergias', optional($user->patient)->alergias) }}</textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="text-label mb-2">Padecimientos</label>
                                <textarea name="padecimientos" class="form-control" rows="2">{{ old('padecimientos', optional($user->patient)->padecimientos) }}</textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="text-label mb-2">Hábitos</label>
                                <textarea name="habitos" class="form-control" rows="2">{{ old('habitos', optional($user->patient)->habitos) }}</textarea>
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

                    {{-- Formulario específico para DOCTORES --}}
                    @if($isDoctor)
                    <div class="soft-card p-5 mb-4 border-start border-4 border-navy">
                        <h4 class="mb-4 fw-bold text-navy"><i class="bi bi-clipboard2-pulse me-2"></i>Perfil Profesional</h4>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="text-label mb-2">Cédula Profesional</label>
                                <input type="text" name="cedula" class="form-control" value="{{ old('cedula', optional($user->doctor)->cedula) }}">
                            </div>

                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold text-muted">Costo Consulta</label>
                                        <div class="input-group">
                                            <span class="input-group-text fw-bold text-success rounded-start-pill">$</span>
                                            <input type="number" name="costo" step="0.01" min="0" class="form-control rounded-end-pill" 
                                                value="{{ old('costo', $doctor->costo ?? '') }}" required placeholder="0.00">
                                            <div class="invalid-feedback">Ingrese un monto válido.</div>
                                        </div>
                                    </div>
                            {{-- <div class="col-md-6">
                                <label class="text-label mb-2">Costo Consulta ($)</label>
                                <input type="number" step="0.01" name="costo" class="form-control" value="{{ old('costo', optional($user->doctor)->costo) }}">
                            </div> --}}
                           
                                {{-- CONFIGURACIÓN DE CITAS Y HORARIOS --}}
                                @if($isDoctor)
                                    @php
                                        // 1. Verificación de seguridad: ¿Tiene citas pendientes o confirmadas?
                                        $tieneCitasPendientes = isset($user->doctor) && $user->doctor->citas()
                                            ->whereIn('estado', ['pendiente', 'confirmada'])
                                            ->exists();

                                        // 2. Cargar horarios existentes o uno por defecto
                                        $horariosExistentes = isset($user->doctor) && $user->doctor->disponibilidades->count() > 0 
                                            ? $user->doctor->disponibilidades->map(fn($h) => [
                                                'dia' => (string)$h->dia_semana, 
                                                'inicio' => substr($h->hora_inicio, 0, 5), 
                                                'fin' => substr($h->hora_fin, 0, 5)
                                            ])->toJson() 
                                            : '[{dia: "1", inicio: "09:00", fin: "18:00"}]';

                                        // 3. Estado inicial del switch de citas
                                        $citasActivadasOriginal = (isset($user->doctor) && $user->doctor->citas) ? 'true' : 'false';
                                    @endphp

                                    <div class="col-12 mt-4" x-data="{ 
                                        citasActivas: {{ $citasActivadasOriginal }},
                                        bloqueado: {{ $tieneCitasPendientes ? 'true' : 'false' }},
                                        horarios: {{ $horariosExistentes }},
                                        
                                        addHorario() { this.horarios.push({dia: '1', inicio: '09:00', fin: '18:00'}) },
                                        removeHorario(index) { this.horarios.splice(index, 1) },
                                        
                                        intentarToggle() {
                                            if (this.bloqueado && !this.citasActivas) {
                                                alert('No puedes deshabilitar las citas porque tienes consultas pendientes en Ocosingo. Debes gestionarlas primero.');
                                                this.citasActivas = true;
                                            }
                                        }
                                    }">
                                        
                                        {{-- SWITCH DE HABILITAR CITAS --}}
                                        <div class="col-12 mb-3">
                                            <div class="bg-light p-3 rounded-pill border d-flex align-items-center justify-content-between px-4"
                                                :class="bloqueado ? 'opacity-75' : ''">
                                                
                                                <div class="d-flex flex-column">
                                                    <span class="small fw-bold text-navy">¿Habilitar recepción de citas en línea?</span>
                                                    <template x-if="bloqueado">
                                                        <span class="text-danger" style="font-size: 0.7rem;">
                                                            <i class="bi bi-lock-fill"></i> Bloqueado: tienes citas pendientes
                                                        </span>
                                                    </template>
                                                </div>

                                                <div class="form-check form-switch mb-0">
                                                    <input class="form-check-input" type="checkbox" name="citas" id="citasSwitch" 
                                                        value="1" 
                                                        x-model="citasActivas"
                                                        @change="intentarToggle()"
                                                        :disabled="bloqueado">
                                                    <label class="form-check-label small text-muted" for="citasSwitch" x-text="citasActivas ? 'Activo' : 'Inactivo'"></label>
                                                </div>
                                            </div>
                                            {{-- Respaldo de datos si el switch está deshabilitado --}}
                                            <template x-if="bloqueado">
                                                <input type="hidden" name="citas" value="1">
                                            </template>
                                        </div>

                                        {{-- CONFIGURACIÓN DE AGENDA --}}
                                        <div class="col-12 mt-4">
                                            <h6 class="text-navy fw-bold small mb-3">
                                                <i class="bi bi-calendar3 me-2"></i>CONFIGURACIÓN DE AGENDA SEMANAL
                                            </h6>

                                            {{-- DURACIÓN DE CITA (Solo visible si citasActivas es true) --}}
                                            <div class="bg-light p-3 rounded-4 border mb-3" x-show="citasActivas" x-transition>
                                                <label class="small text-muted fw-bold mb-2 d-block">Duración promedio de cada cita</label>
                                                <select name="duracion_cita" class="form-select rounded-pill bg-white border-0 shadow-sm">
                                                    @foreach([15, 20, 30, 45, 60] as $min)
                                                        <option value="{{ $min }}" 
                                                            {{ (optional($user->doctor)->duracion_cita == $min) ? 'selected' : ($min == 30 ? 'selected' : '') }}>
                                                            {{ $min < 60 ? "$min minutos" : "1 hora" }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            {{-- LISTADO DINÁMICO DE HORARIOS --}}
                                            <template x-for="(horario, index) in horarios" :key="index">
                                                <div class="row g-2 mb-3 align-items-end bg-white p-3 rounded-4 border shadow-sm mx-0">
                                                    <div class="col-md-4">
                                                        <label class="small text-muted fw-bold ps-2">Día</label>
                                                        <select :name="`horarios[${index}][dia]`" x-model="horario.dia" class="form-select rounded-pill border-0 bg-light">
                                                            <option value="1">Lunes</option>
                                                            <option value="2">Martes</option>
                                                            <option value="3">Miércoles</option>
                                                            <option value="4">Jueves</option>
                                                            <option value="5">Viernes</option>
                                                            <option value="6">Sábado</option>
                                                            <option value="0">Domingo</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="small text-muted fw-bold ps-2">Entrada</label>
                                                        <input type="time" :name="`horarios[${index}][inicio]`" x-model="horario.inicio" class="form-control rounded-pill border-0 bg-light">
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="small text-muted fw-bold ps-2">Salida</label>
                                                        <input type="time" :name="`horarios[${index}][fin]`" x-model="horario.fin" class="form-control rounded-pill border-0 bg-light">
                                                    </div>
                                                    <div class="col-md-2 text-center">
                                                        <button type="button" @click="removeHorario(index)" class="btn btn-outline-danger border-0 rounded-circle" x-show="horarios.length > 1">
                                                            <i class="bi bi-trash3-fill"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </template>

                                            <button type="button" @click="addHorario()" class="btn btn-sm btn-outline-navy rounded-pill mt-2">
                                                <i class="bi bi-plus-circle me-1"></i> Añadir otro bloque de horario
                                            </button>
                                        </div>
                                    </div>
                                @endif
                            <div class="col-12">
                                <label class="text-label mb-2">Descripción Profesional</label>
                                <textarea name="descripcion" class="form-control" rows="3">{{ old('descripcion', optional($user->doctor)->descripcion) }}</textarea>
                            </div>
                        </div>
                    </div>
                    @endif

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
        // Previsualización de imagen
        function previewFile(input) {
            var file = input.files[0];
            if (file) {
                var reader = new FileReader();
                reader.onload = function() {
                    document.getElementById("previewImg").src = reader.result;
                }
                reader.readAsDataURL(file);
            }
        }
    </script>
</x-layout>