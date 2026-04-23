<x-layout>
                    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <style>
    <style>

        :root {
            --buscadoc-navy: #0A3D62;         /* Azul marino principal */
            --buscadoc-navy-light: #1E5F8C;   /* Azul marino claro para degradados */
            --buscadoc-teal: #00A896;         /* Turquesa médico para acentos */
            --buscadoc-teal-light: #E6F6F4;   /* Fondo turquesa muy suave para íconos */
            --buscadoc-bg: #F4F7F9;           /* Fondo general gris/azul muy claro */
            --buscadoc-input: #EAEFF4;        /* Fondo para inputs (menos blanco) */
        }

        .buscadoc-wrapper {
            background-color: var(--buscadoc-bg);
            border-radius: 1.5rem;
        }


        
        .bg-navy-subtle { 
            background-color: rgba(10, 61, 98, 0.1) !important; 
            color: var(--buscadoc-navy);
        }

        .icon-wrapper-teal {
            background-color: var(--buscadoc-teal-light);
            color: var(--buscadoc-teal);
        }

        .card-buscadoc {
            background-color: #ffffff;
            border: none;
            box-shadow: 0 8px 24px rgba(10, 61, 98, 0.06);
        }

        .card-buscadoc-accent {
            border-top: 5px solid var(--buscadoc-teal);
        }

        .input-buscadoc {
            background-color: var(--buscadoc-input) !important;
            border: 1px solid transparent;
            transition: all 0.3s ease;
        }

        .input-buscadoc:focus {
            background-color: #ffffff !important;
            border-color: var(--buscadoc-teal);
            box-shadow: 0 0 0 0.25rem rgba(0, 168, 150, 0.15);
        }

        .btn-buscadoc {
            background-color: var(--buscadoc-teal);
            color: white;
            border: none;
            transition: all 0.3s ease;
        }

        .btn-buscadoc:hover {
            background-color: #008f7f;
            color: white;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 168, 150, 0.3);
        }

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

    <div class="container py-5">
        <div class="buscadoc-wrapper p-4 p-md-5 shadow-sm">
            
            {{-- ENCABEZADO --}}
            <div class="d-flex align-items-center mb-4 pb-3 border-bottom">
                <a href="{{ route('expedientes.index') }}" class="btn btn-white rounded-circle shadow-sm me-3 d-flex align-items-center justify-content-center transition-hover" style="width: 45px; height: 45px; background-color: white;">
                    <x-mcr-angle-left style="width: 1.2rem; color: var(--buscadoc-navy);" />
                </a>
                <div>
                    <h3 class="fw-bold text-navy mb-0">Actualizar Expediente</h3>
                    <p class="text-muted small mb-0 mt-1">Modificando la información de: <span class="fw-bold px-2 py-1 bg-navy-subtle rounded">{{ $expediente->nombre_completo }}</span></p>
                </div>
            </div>

            <form action="{{ route('expedientes.update', $expediente->id) }}" method="POST" class="form-modern">
                @csrf
                @method('PUT')

                <div class="row g-4 mb-4">
                    {{-- COLUMNA IZQUIERDA: Datos Personales --}}
                    <div class="col-lg-5">
                        <div class="card card-buscadoc rounded-4 overflow-hidden h-100">
                            {{-- Cabecera de la tarjeta --}}
                            <div class="p-4 text-white d-flex align-items-center" style="background: linear-gradient(135deg, var(--buscadoc-navy) 0%, var(--buscadoc-navy-light) 100%);">
                                <div class="bg-white bg-opacity-25 p-2 rounded-3 me-3 d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                                    <x-mcr-user-alt style="width: 1.5rem;" />
                                </div>
                                <h5 class="fw-bold mb-0" style="color:black">Datos Personales</h5>
                            </div>
                            
                            <div class="card-body p-4">
                                <div class="mb-4">
                                    <label class="form-label small fw-bold text-secondary text-uppercase tracking-wider">Nombre Completo</label>
                                    <div class="input-group">
                                        <span class="input-group-text input-buscadoc border-0 rounded-start-4 ps-3">
                                            <x-mcr-user-alt class="text-navy" style="width: 1.2rem;" />
                                        </span>
                                        <input type="text" name="nombre_completo" class="form-control input-buscadoc rounded-end-4 py-2 shadow-none" value="{{ $expediente->nombre_completo }}" required>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label small fw-bold text-secondary text-uppercase tracking-wider">Fecha de Nacimiento</label>
                                    <div class="input-group">
                                        <span class="input-group-text input-buscadoc border-0 rounded-start-4 ps-3">
                                            <x-mcr-calendar class="text-navy" style="width: 1.2rem;" />
                                        </span>
                                        <input type="date" name="fecha_nacimiento" class="form-control input-buscadoc rounded-end-4 py-2 shadow-none" value="{{ $expediente->fecha_nacimiento }}" max="{{ date('Y-m-d') }}" required>
                                    </div>
                                </div>

                                <div class="row g-3 mb-4">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold text-secondary text-uppercase tracking-wider">Género</label>
                                        <select name="genero" class="form-select rounded-4 input-buscadoc py-2 shadow-none" required>
                                            <option value="masculino" {{ $expediente->genero == 'masculino' ? 'selected' : '' }}>Masculino</option>
                                            <option value="femenino" {{ $expediente->genero == 'femenino' ? 'selected' : '' }}>Femenino</option>
                                        </select>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold text-secondary text-uppercase tracking-wider">Tipo de Sangre</label>
                                        <select name="tipo_sangre" class="form-select rounded-4 input-buscadoc py-2 shadow-none">
                                            @foreach(['O+', 'O-', 'A+', 'A-', 'B+', 'B-', 'AB+', 'AB-'] as $tipo)
                                                <option value="{{ $tipo }}" {{ $expediente->tipo_sangre == $tipo ? 'selected' : '' }}>{{ $tipo }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                @if(Auth::user()->role == 'doctor')
                                    <input type="hidden" name="parentesco" value="Paciente">
                                @else
                                    <div class="mb-0">
                                        <label class="form-label small fw-bold text-secondary text-uppercase tracking-wider">Parentesco</label>
                                        <select name="parentesco" class="form-select rounded-4 input-buscadoc py-2 shadow-none" required>
                                            @foreach(['Hijo/a', 'Padre/Madre', 'Cónyuge', 'Otro'] as $relacion)
                                                <option value="{{ $relacion }}" {{ $expediente->parentesco == $relacion ? 'selected' : '' }}>{{ $relacion }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- COLUMNA DERECHA: Detalles Médicos --}}
                    <div class="col-lg-7">
                        <div class="card card-buscadoc card-buscadoc-accent rounded-4 p-4 p-md-5 h-100">
                            
                            <div class="d-flex align-items-center mb-4 pb-3 border-bottom">
                                <div class="icon-wrapper-teal p-2 rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                                    <x-mcr-shield style="width: 1.5rem;" />
                                </div>
                                <h5 class="fw-bold mb-0 text-navy">Detalles de Salud</h5>
                            </div>

                            <div class="col-12 mt-3" x-data="{ 
                                seleccionados: [], 
                                mostrarOtro: false, 
                                otroTexto: '',
                                init() {
                                    let datosBD = '{{ $expediente->alergias }}';
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
                                    let datosBD = '{{ $expediente->padecimientos_cronicos }}';
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
                                    <input type="hidden" name="padecimientos_cronicos" :value="resultado">
                                </div>
                            </div>

                            <div class="col-12 mt-4" x-data="{ 
                                seleccionados: [], 
                                mostrarOtro: false, 
                                otroTexto: '',
                                init() {
                                    let datosBD = '{{ $expediente->habitos_salud }}';
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
                                    <input type="hidden" name="habitos_salud" :value="resultado">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- BOTONES DE ACCIÓN --}}
                <div class="d-flex justify-content-end align-items-center p-4 mt-2">
                    <a href="{{ route('expedientes.index') }}" class="btn btn-link text-decoration-none px-4 py-2 me-3 fw-bold text-muted transition-hover">
                        Cancelar
                    </a>
                    <button type="submit" class="btn btn-buscadoc rounded-pill px-5 py-3 fw-bold shadow-sm d-flex align-items-center">
                        <x-mcl-check-circle class="me-2" style="width: 1.2rem; color: white;" /> Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layout>