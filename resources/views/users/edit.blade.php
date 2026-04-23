<?php
use Illuminate\Support\Str;

$isDoctor = $user->role === 'doctor';
$isPharmacy = $user->role === 'farmacia';
$isPatient = $user->role === 'paciente';
?>

<x-layout>
    <head>
                            <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <style>
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

                    .register-container {
            min-height: calc(100vh - 100px);
            display: flex;
            align-items: center;
            padding: 3rem 1rem;
        }

        .register-wrapper {
            display: grid;
            grid-template-columns: 1fr 650px;
            gap: 3rem;
            max-width: 1300px;
            width: 100%;
            align-items: start;
            margin: 0 auto;
        }

        /* --- LADO IZQUIERDO (Branding) --- */
        .brand-content {
            padding: 2rem;
            position: sticky;
            top: 2rem;
        }

        .brand-logo {
            background: white;
            padding: 2rem;
            border-radius: 24px;
            display: inline-block;
            box-shadow: 0 10px 40px rgba(13, 46, 78, 0.12);
            margin-bottom: 2rem;
        }

        .brand-logo img {
            max-width: 280px;
            height: auto;
        }

        .brand-title {
            font-size: 2.2rem;
            font-weight: 800;
            color: #0d2e4e;
            margin-bottom: 1.5rem;
            line-height: 1.2;
        }

        .brand-description {
            font-size: 1.15rem;
            color: #64748b;
            line-height: 1.6;
            margin-bottom: 2.5rem;
        }

        .features {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .features li {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 14px 0;
            color: #2c3e50;
            font-size: 1.05rem;
            font-weight: 500;
        }

        .feature-icon {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, #0d2e4e 0%, #1a5f7a 100%);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            flex-shrink: 0;
            box-shadow: 0 4px 12px rgba(13, 46, 78, 0.2);
        }

        /* --- LADO DERECHO (Formulario) --- */
        .register-form-container {
            background: white;
            padding: 3rem;
            border-radius: 24px;
            box-shadow: 0 20px 60px rgba(13, 46, 78, 0.15);
        }

        .register-header {
            text-align: center;
            margin-bottom: 2.5rem;
        }

        .register-header h2 {
            font-size: 2rem;
            font-weight: 700;
            color: #0d2e4e;
            margin: 0;
        }

        .register-header p {
            color: #64748b;
            margin-top: 0.5rem;
            font-size: 0.95rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.6rem;
            color: #0d2e4e;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .input-wrapper {
            position: relative;
        }

        .input-wrapper i.input-icon {
            position: absolute;
            left: 1.25rem;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 1.1rem;
            z-index: 10;
        }

        .form-control-custom,
        .form-select-custom {
            width: 100%;
            padding: 14px 16px 14px 48px;
            border: 2px solid #e2e8f0;
            border-radius: 50px;
            font-size: 1rem;
            transition: all 0.3s;
            background-color: #f8fafc;
            color: #1e293b;
        }

        .form-control-no-icon {
            padding-left: 20px;
        }

        textarea.form-control-custom {
            border-radius: 20px;
            resize: none;
        }

        .form-control-custom:focus,
        .form-select-custom:focus {
            outline: none;
            border-color: #0d2e4e;
            background-color: white;
            box-shadow: 0 0 0 4px rgba(13, 46, 78, 0.08);
        }

        .form-control-custom::placeholder {
            color: #94a3b8;
        }

        /* Estilo para los inputs con error (HTML5 validation) */
        .form-control-custom.is-invalid,
        .form-select-custom.is-invalid {
            border-color: #ef4444 !important;
            background-color: #fef2f2 !important;
            box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.1) !important;
        }

        .btn-primary-custom {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #0d2e4e 0%, #1a5f7a 100%);
            color: white;
            border: none;
            border-radius: 50px;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 1rem;
            box-shadow: 0 4px 15px rgba(13, 46, 78, 0.3);
        }

        .btn-primary-custom:hover:not(:disabled) {
            background: linear-gradient(135deg, #1a5f7a 0%, #0d2e4e 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(13, 46, 78, 0.35);
        }

        .divider {
            display: flex;
            align-items: center;
            margin: 2rem 0;
            color: #94a3b8;
            font-size: 0.85rem;
            font-weight: 600;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: linear-gradient(90deg, transparent, #cbd5e1, transparent);
        }

        .divider span {
            padding: 0 15px;
            color: #0d2e4e;
        }

        .file-upload-wrapper {
            position: relative;
            width: 100%;
            height: 52px;
            margin: 0;
        }

        .file-upload-input {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            cursor: pointer;
            z-index: 2;
        }

        .file-upload-label {
            position: absolute;
            inset: 0;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            background-color: #f8fafc;
            border: 2px dashed #cbd5e1;
            border-radius: 50px;
            color: #64748b;
            font-weight: 600;
            margin: 0 !important;
            /* Mata el margen global de <label> */
            padding: 0 15px !important;
            transition: all 0.3s ease;
            z-index: 1;
        }

        .file-upload-label i {
            display: flex;
            align-items: center;
            line-height: 0;
            transform: translateY(-1px);
            /* 🛠️ Sube el icono ligeramente */
        }

        .file-upload-label span {
            display: inline-block;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 75%;
            line-height: normal;
            transform: translateY(-2px);
            /* 🛠️ Sube el texto para centrarlo perfecto */
        }

        .file-upload-wrapper:hover .file-upload-label {
            background-color: #f1f5f9;
            border-color: #0d2e4e;
            color: #0d2e4e;
        }

        .file-upload-label.has-file {
            background-color: #e0f2fe;
            border-style: solid;
            border-color: #0d2e4e;
            color: #0d2e4e;
        }

        .custom-options {
            position: absolute;
            top: 110%;
            left: 0;
            width: 100%;
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.12);
            z-index: 100;
            overflow: hidden;
            border: 1px solid #e2e8f0;
        }

        .custom-option {
            padding: 14px 20px;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 12px;
            color: #475569;
            font-weight: 500;
        }

        .custom-option:hover {
            background-color: #f8fafc;
            color: #0d2e4e;
        }

        .custom-option.selected {
            background-color: #e0f2fe;
            color: #0d2e4e;
            font-weight: 700;
        }

        .info-box {
            background-color: #f8fafc;
            border-left: 4px solid #0d2e4e;
            border-radius: 12px;
            padding: 1rem;
            display: flex;
            gap: 15px;
            align-items: flex-start;
        }

        .dynamic-section-title {
            color: #0d2e4e;
            font-weight: 800;
            font-size: 0.95rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #f1f5f9;
        }

        .map-container {
            border-radius: 20px;
            overflow: hidden;
            border: 2px solid #e2e8f0;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .register-wrapper {
                grid-template-columns: 1fr;
                gap: 2rem;
                max-width: 700px;
            }

            .brand-content {
                position: static;
                text-align: center;
                padding: 1rem 0;
            }

            .features {
                max-width: 400px;
                margin: 0 auto;
            }
        }

        @media (max-width: 576px) {
            .register-form-container {
                padding: 2rem 1.5rem;
            }
        }

        input[type="time"].form-control-custom {
            padding-left: 10px !important;
            padding-right: 5px !important;
            text-align: center;
            font-size: 0.95rem;
            letter-spacing: -0.5px;
        }

        .flatpickr-calendar {
            border-radius: 16px !important;
            box-shadow: 0 15px 40px rgba(13, 46, 78, 0.15) !important;
            border: none !important;
            padding: 10px !important;
        }

        .flatpickr-time input:hover,
        .flatpickr-time .flatpickr-am-pm:hover,
        .flatpickr-time input:focus,
        .flatpickr-time .flatpickr-am-pm:focus {
            background: #f1f5f9 !important;
            color: #0d2e4e !important;
        }

        .flatpickr-time input,
        .flatpickr-time .flatpickr-am-pm {
            color: #0d2e4e !important;
            font-weight: bold !important;
        }

        .flatpickr-time {
            border-top: none !important;
        }
        .text-navy { color: var(--buscadoc-navy) !important; }
        .text-teal { color: var(--buscadoc-teal) !important; }
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
                        $expedientePrincipal =  $user->expedientes
                        ? $user->expedientes->whereIn('parentesco', ['Propio','propio', 'Yo mismo', 'Expediente propio'])->first() 
                        : null;
                        
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

                            <div class="col-12 mt-3" x-data="{ 
                                seleccionados: [], 
                                mostrarOtro: false, 
                                otroTexto: '',
                                init() {
                                    let datosBD = '{{ $expedientePrincipal->alergias }}';
                                    if (datosBD) {
                                        let lista = datosBD.split(', ');
                                        let opcionesPredefinidas = ['Ninguna', 'Penicilina', 'Polvo / Ácaros', 'Mariscos', 'Medicamentos', 'Polen'];
                                        this.seleccionados = lista.filter(item => opcionesPredefinidas.includes(item));
                                        let otros = lista.filter(item => !opcionesPredefinidas.includes(item));
                                        if (otros.length > 0) {
                                            this.mostrarOtro = true;
                                            this.otroTexto = otros.join(', ');
                                        }
                                    }
                                },
                                get resultado() {
                                    let list = [...this.seleccionados];
                                    if (this.mostrarOtro && this.otroTexto.trim() !== '') list.push(this.otroTexto.trim());
                                    return list.join(', ');
                                }
                            }">
                                <label class="form-label text-navy small fw-bold mb-2">Alergias</label>
                                <div class="bg-light p-3 rounded-4 border">
                                    <div class="d-flex flex-wrap gap-3">
                                        <template x-for="opcion in ['Ninguna', 'Penicilina', 'Polvo / Ácaros', 'Mariscos', 'Medicamentos', 'Polen']">
                                            <div class="form-check custom-checkbox mb-0">
                                                <input class="form-check-input" type="checkbox" :value="opcion" :id="'al_'+opcion" x-model="seleccionados">
                                                <label class="form-check-label small fw-medium cursor-pointer" :for="'al_'+opcion" x-text="opcion"></label>
                                            </div>
                                        </template>
                                        <div class="form-check custom-checkbox mb-0">
                                            <input class="form-check-input" type="checkbox" id="al_otro" x-model="mostrarOtro" @change="if(!mostrarOtro) otroTexto = ''">
                                            <label class="form-check-label small fw-medium cursor-pointer" for="al_otro">Otra(s)</label>
                                        </div>
                                    </div>
                                    <div x-show="mostrarOtro" x-transition x-cloak class="mt-3">
                                        <div class="input-wrapper">
                                            <i class="bi bi-pencil-square input-icon"></i>
                                            <input type="text" class="form-control-custom" placeholder="Especifique..." x-model="otroTexto">
                                        </div>
                                    </div>
                                    <input type="hidden" name="alergias" :value="resultado">
                                </div>
                            </div>

                            <div class="col-12 mt-4" x-data="{ 
                                seleccionados: [], 
                                mostrarOtro: false, 
                                otroTexto: '',
                                init() {
                                    let datosBD = '{{ $expedientePrincipal->padecimientos_cronicos }}';
                                    if (datosBD) {
                                        let lista = datosBD.split(', ');
                                        let opciones = ['Ninguno', 'Diabetes', 'Hipertensión', 'Asma', 'Artritis', 'Enfermedad Cardíaca'];
                                        this.seleccionados = lista.filter(item => opciones.includes(item));
                                        let otros = lista.filter(item => !opciones.includes(item));
                                        if (otros.length > 0) {
                                            this.mostrarOtro = true;
                                            this.otroTexto = otros.join(', ');
                                        }
                                    }
                                },
                                get resultado() {
                                    let list = [...this.seleccionados];
                                    if (this.mostrarOtro && this.otroTexto.trim() !== '') list.push(this.otroTexto.trim());
                                    return list.join(', ');
                                }
                            }">
                                <label class="form-label text-navy small fw-bold mb-2">Padecimientos Crónicos</label>
                                <div class="bg-light p-3 rounded-4 border">
                                    <div class="d-flex flex-wrap gap-3">
                                        <template x-for="opcion in ['Ninguno', 'Diabetes', 'Hipertensión', 'Asma', 'Artritis', 'Enfermedad Cardíaca']">
                                            <div class="form-check custom-checkbox mb-0">
                                                <input class="form-check-input" type="checkbox" :value="opcion" :id="'pad_'+opcion" x-model="seleccionados">
                                                <label class="form-check-label small fw-medium cursor-pointer" :for="'pad_'+opcion" x-text="opcion"></label>
                                            </div>
                                        </template>
                                        <div class="form-check custom-checkbox mb-0">
                                            <input class="form-check-input" type="checkbox" id="pad_otro" x-model="mostrarOtro" @change="if(!mostrarOtro) otroTexto = ''">
                                            <label class="form-check-label small fw-medium cursor-pointer" for="pad_otro">Otro(s)</label>
                                        </div>
                                    </div>
                                    <div x-show="mostrarOtro" x-transition x-cloak class="mt-3">
                                        <div class="input-wrapper">
                                            <i class="bi bi-pencil-square input-icon"></i>
                                            <input type="text" class="form-control-custom" placeholder="Especifique..." x-model="otroTexto">
                                        </div>
                                    </div>
                                    <input type="hidden" name="padecimientos" :value="resultado">
                                </div>
                            </div>

                            <div class="col-12 mt-4" x-data="{ 
                                seleccionados: [], 
                                mostrarOtro: false, 
                                otroTexto: '',
                                init() {
                                    let datosBD = '{{ $expedientePrincipal->habitos_salud }}';
                                    if (datosBD) {
                                        let lista = datosBD.split(', ');
                                        let opciones = ['Ejercicio regular', 'Fumador', 'Consumo de alcohol', 'Dieta equilibrada', 'Sueño regular'];
                                        this.seleccionados = lista.filter(item => opciones.includes(item));
                                        let otros = lista.filter(item => !opciones.includes(item));
                                        if (otros.length > 0) {
                                            this.mostrarOtro = true;
                                            this.otroTexto = otros.join(', ');
                                        }
                                    }
                                },
                                get resultado() {
                                    let list = [...this.seleccionados];
                                    if (this.mostrarOtro && this.otroTexto.trim() !== '') list.push(this.otroTexto.trim());
                                    return list.join(', ');
                                }
                            }">
                                <label class="form-label text-navy small fw-bold mb-2">Hábitos de Salud</label>
                                <div class="bg-light p-3 rounded-4 border">
                                    <div class="d-flex flex-wrap gap-3">
                                        <template x-for="opcion in ['Ejercicio regular', 'Fumador', 'Consumo de alcohol', 'Dieta equilibrada', 'Sueño regular']">
                                            <div class="form-check custom-checkbox mb-0">
                                                <input class="form-check-input" type="checkbox" :value="opcion" :id="'hab_'+opcion" x-model="seleccionados">
                                                <label class="form-check-label small fw-medium cursor-pointer" :for="'hab_'+opcion" x-text="opcion"></label>
                                            </div>
                                        </template>
                                        <div class="form-check custom-checkbox mb-0">
                                            <input class="form-check-input" type="checkbox" id="hab_otro" x-model="mostrarOtro" @change="if(!mostrarOtro) otroTexto = ''">
                                            <label class="form-check-label small fw-medium cursor-pointer" for="hab_otro">Otro(s)</label>
                                        </div>
                                    </div>
                                    <div x-show="mostrarOtro" x-transition x-cloak class="mt-3">
                                        <div class="input-wrapper">
                                            <i class="bi bi-pencil-square input-icon"></i>
                                            <input type="text" class="form-control-custom" placeholder="Especifique..." x-model="otroTexto">
                                        </div>
                                    </div>
                                    <input type="hidden" name="habitos" :value="resultado">
                                </div>
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

                {{-- ==================== FORMULARIO DOCTOR ==================== --}}
                @if($isDoctor)
                    @php
                        $doctor = $user->doctor; // Asumiendo que tienes la relación definida en el modelo User
                    @endphp
                    
                    <div class="soft-card p-5 mb-4 border-start border-4 border-info">
                        <h4 class="mb-4 fw-bold text-navy"><i class="bi bi-medical-ritcher me-2"></i>Información Profesional</h4>
                        
                        <div class="row g-3">
                            {{-- Cédula Profesional --}}
                            <div class="col-md-6">
                                <label class="text-label mb-2">Cédula Profesional</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white"><i class="bi bi-card-checklist text-info"></i></span>
                                    <input type="text" name="cedula" class="form-control" 
                                        value="{{ old('cedula', optional($doctor)->cedula) }}" placeholder="Ej: 12345678">
                                </div>
                            </div>

                            {{-- Idiomas --}}
                            <div class="col-md-6">
                                <label class="text-label mb-2">Idiomas (Separados por comas)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white"><i class="bi bi-translate text-info"></i></span>
                                    <input type="text" name="idiomas" class="form-control" 
                                        value="{{ old('idiomas', optional($doctor)->idiomas) }}" placeholder="Español, Inglés...">
                                </div>
                            </div>

                            {{-- Costo de Consulta --}}
                            <div class="col-md-4">
                                <label class="text-label mb-2">Costo de Consulta ($)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white"><i class="bi bi-cash-stack text-info"></i></span>
                                    <input type="number" step="0.01" name="costo" class="form-control" 
                                        value="{{ old('costo', optional($doctor)->costo ?? 0) }}" required>
                                </div>
                            </div>

                            {{-- Duración de Cita --}}
                            <div class="col-md-4">
                                <label class="text-label mb-2">Duración (minutos)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white"><i class="bi bi-clock-history text-info"></i></span>
                                    <select name="duracion_cita" class="form-select">
                                        @foreach([15, 20, 30, 45, 60, 90] as $min)
                                            <option value="{{ $min }}" {{ (old('duracion_cita', optional($doctor)->duracion_cita) == $min) ? 'selected' : '' }}>
                                                {{ $min }} min
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>


                            {{-- Descripción Profesional --}}
                            <div class="col-12 mt-3">
                                <label class="text-label mb-2">Reseña o Descripción Profesional</label>
                                <textarea name="descripcion_doctor" class="form-control" rows="4" 
                                        placeholder="Cuéntale a tus pacientes sobre tu experiencia...">{{ old('descripcion_doctor', optional($doctor)->descripcion) }}</textarea>
                            </div>
                        </div>
                    </div>

                    
                    {{-- Sección de Disponibilidad Dinámica --}}
                    <div class="soft-card p-5 mb-4 border-start border-4 border-primary" 
                        x-data="{ 
                            horarios: {{ json_encode($doctor && $doctor->disponibilidades ? $doctor->disponibilidades->map(function($d) {
                                return [
                                    'dia' => (string)$d->dia_semana, 
                                    'inicio' => \Carbon\Carbon::parse($d->hora_inicio)->format('H:i'), 
                                    'fin' => \Carbon\Carbon::parse($d->hora_fin)->format('H:i')
                                ];
                            }) : []) }},
                            agregarHorario() {
                                this.horarios.push({ dia: '1', inicio: '09:00', fin: '14:00' });
                            },
                            eliminarHorario(index) {
                                this.horarios.splice(index, 1);
                            }
                        }">
                        
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <h4 class="fw-bold text-navy mb-1"><i class="bi bi-calendar3 me-2"></i>Mis Horarios de Atención</h4>
                                <p class="text-muted small mb-0">Gestiona los turnos en los que aparecerás disponible para citas.</p>
                            </div>
                            <button type="button" @click="agregarHorario()" class="btn btn-outline-primary rounded-pill btn-sm px-3">
                                <i class="bi bi-plus-circle me-1"></i> Agregar Turno
                            </button>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="text-label border-bottom">
                                    <tr>
                                        <th>Día de la Semana</th>
                                        <th>Hora Inicio</th>
                                        <th>Hora Fin</th>
                                        <th class="text-center">Acción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="(horario, index) in horarios" :key="index">
                                        <tr>
                                            <td style="width: 200px;">
                                                <select :name="'disponibilidad['+index+'][dia]'" x-model="horario.dia" class="form-select form-select-sm rounded-3">
                                                    <option value="0">Domingo</option>
                                                    <option value="1">Lunes</option>
                                                    <option value="2">Martes</option>
                                                    <option value="3">Miércoles</option>
                                                    <option value="4">Jueves</option>
                                                    <option value="5">Viernes</option>
                                                    <option value="6">Sábado</option>
                                                </select>
                                            </td>
                                            <td>
                                                <input type="time" :name="'disponibilidad['+index+'][inicio]'" x-model="horario.inicio" class="form-control form-control-sm rounded-3">
                                            </td>
                                            <td>
                                                <input type="time" :name="'disponibilidad['+index+'][fin]'" x-model="horario.fin" class="form-control form-control-sm rounded-3">
                                            </td>
                                            <td class="text-center">
                                                <button type="button" @click="eliminarHorario(index)" class="btn btn-link text-danger p-0">
                                                    <i class="bi bi-trash3-fill fs-5"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                            
                            {{-- Mensaje si no hay horarios --}}
                            <div x-show="horarios.length === 0" class="text-center py-4 bg-light rounded-4 border-dashed">
                                <i class="bi bi-clock-history text-muted fs-2"></i>
                                <p class="text-muted mt-2">No tienes horarios configurados. Haz clic en "Agregar Turno".</p>
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