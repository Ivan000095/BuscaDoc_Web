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
                                <input type="date" name="f_nacimiento" class="form-control" value="{{ old('f_nacimiento', $user->f_nacimiento) }}" max="{{ date('Y-m-d', strtotime('-18 years')) }}">
                            </div>
                        </div>
                    </div>

                    {{-- Formulario específico para PACIENTES --}}
                    @if($isPatient)
                    <div class="soft-card p-5 mb-4 border-start border-4 border-info">
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
                    <div class="soft-card p-5 mb-4 border-start border-4 border-primary">
                        <h4 class="mb-4 fw-bold text-primary"><i class="bi bi-clipboard2-pulse me-2"></i>Perfil Profesional</h4>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="text-label mb-2">Cédula Profesional</label>
                                <input type="text" name="cedula" class="form-control" value="{{ old('cedula', optional($user->doctor)->cedula) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="text-label mb-2">Costo Consulta ($)</label>
                                <input type="number" step="0.01" name="costo" class="form-control" value="{{ old('costo', optional($user->doctor)->costo) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="text-label mb-2">Horario Entrada</label>
                                <input type="time" name="horario_entrada" class="form-control" value="{{ old('horario_entrada', optional($user->doctor)->horario_entrada) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="text-label mb-2">Horario Salida</label>
                                <input type="time" name="horario_salida" class="form-control" value="{{ old('horario_salida', optional($user->doctor)->horario_salida) }}">
                            </div>
                            <div class="col-12">
                                <label class="text-label mb-2">Descripción Profesional</label>
                                <textarea name="descripcion" class="form-control" rows="3">{{ old('descripcion', optional($user->doctor)->descripcion) }}</textarea>
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- Botón de Guardar --}}
                    <div class="mt-4">
                        <button type="submit" class="btn btn-dark rounded-pill py-3 px-5 shadow-sm w-100 fs-5">
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