<x-layout>
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <style>
       
        :root {
            --buscadoc-navy: #0A3D62;         
            --buscadoc-navy-light: #1E5F8C;   
            --buscadoc-teal: #00A896;         
            --buscadoc-teal-light: #E6F6F4;   
            --buscadoc-bg: #F4F7F9;           
            --buscadoc-input: #EAEFF4;       
        }

        .buscadoc-wrapper {
            background-color: var(--buscadoc-bg);
            border-radius: 1.5rem;
        }

        .text-navy { color: var(--buscadoc-navy) !important; }
        .text-teal { color: var(--buscadoc-teal) !important; }
        
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


    </style>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                
                <div class="buscadoc-wrapper p-4 p-md-5 shadow-sm">
                    {{-- Encabezado del Formulario --}}
                    <div class="d-flex align-items-center mb-4 pb-3 border-bottom">
                        <div class="text-white rounded-circle d-flex align-items-center justify-content-center me-3 shadow-sm" style="width: 55px; height: 55px; background: linear-gradient(135deg, var(--buscadoc-navy) 0%, var(--buscadoc-navy-light) 100%);">
                            <x-mcl-folder-open style="width: 1.8rem;" />
                        </div>
                        <div>
                            <h3 class="fw-bold text-navy mb-0">Nuevo Expediente Médico</h3>
                            <p class="text-muted small mb-0 mt-1">Completa la información para mantener un historial preciso.</p>
                        </div>
                    </div>

                    <form action="{{ route('expedientes.store') }}" method="POST" class="card-buscadoc p-4 p-md-5 rounded-4 form-modern">
                        @csrf

                        {{-- Botón Volver --}}
                        <div class="mb-4">
                            <a href="{{ route('expedientes.index') }}" class="btn btn-link text-muted text-decoration-none small fw-bold px-0 d-inline-flex align-items-center transition-hover">
                                <x-mcr-angle-left class="me-1" style="width: 1rem;" /> Volver a mis expedientes
                            </a>
                        </div>

                        {{-- Sección 1: Datos Personales --}}
                        <div class="mb-5">
                            <div class="d-flex align-items-center mb-4 border-bottom pb-3">
                                <span class="bg-navy-subtle fw-bold rounded-circle me-3 d-flex align-items-center justify-content-center fs-6" style="width: 35px; height: 35px;">1</span>
                                <h5 class="fw-bold text-navy mb-0">Información Personal</h5>
                            </div>
                            
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-secondary text-uppercase tracking-wider">Nombre Completo</label>
                                    <div class="input-group">
                                        <span class="input-group-text input-buscadoc border-0 rounded-start-4 ps-3">
                                            <x-mcr-user-alt class="text-navy" style="width: 1.2rem;" />
                                        </span>
                                        <input type="text" name="nombre_completo" class="form-control input-buscadoc border-0 rounded-end-4 py-3 shadow-none" placeholder="Ej. Juan Pérez" required>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-secondary text-uppercase tracking-wider">Fecha de Nacimiento</label>
                                    <div class="input-group">
                                        <span class="input-group-text input-buscadoc border-0 rounded-start-4 ps-3">
                                            <x-mcr-calendar class="text-navy" style="width: 1.2rem;" />
                                        </span>
                                        <input type="date" name="fecha_nacimiento" class="form-control input-buscadoc border-0 rounded-end-4 py-3 shadow-none text-muted" max="{{ date('Y-m-d') }}" required>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label small fw-bold text-secondary text-uppercase tracking-wider">Género</label>
                                    <select name="genero" class="form-select input-buscadoc border-0 rounded-4 py-3 shadow-none text-secondary" required>
                                        <option value="" disabled selected>Seleccionar...</option>
                                        <option value="masculino">Masculino</option>
                                        <option value="femenino">Femenino</option>
                                    </select>
                                </div>
                                
                                @if(Auth::user()->role == 'doctor')
                                    <input type="hidden" name="parentesco" value="Paciente">
                                @else
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold text-secondary text-uppercase tracking-wider">Parentesco</label>
                                        <select name="parentesco" class="form-select input-buscadoc border-0 rounded-4 py-3 shadow-none text-secondary" required>
                                            <option value="" disabled selected>Seleccionar...</option>
                                            
                                            <option value="Hijo/a">Hijo/a</option>
                                            <option value="Padre/Madre">Padre/Madre</option>
                                            <option value="Cónyuge">Cónyuge</option>
                                            <option value="Otro">Otro</option>
                                        </select>
                                    </div>
                                @endif

                                <div class="col-md-4">
                                    <label class="form-label small fw-bold text-secondary text-uppercase tracking-wider">Tipo de Sangre</label>
                                    <select name="tipo_sangre" class="form-select input-buscadoc border-0 rounded-4 py-3 shadow-none text-secondary">
                                        <option value="" selected>Desconocido</option>
                                        <option value="O+">O+</option>
                                        <option value="O-">O-</option>
                                        <option value="A+">A+</option>
                                        <option value="A-">A-</option>
                                        <option value="B+">B+</option>
                                        <option value="AB+">AB+</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- Sección 2: Antecedentes Médicos --}}
                        <div class="mb-4 mt-5">
                                <div class="col-12 mt-3" x-data="{ 
                                    seleccionados: [], mostrarOtro: false, otroTexto: '',
                                    get resultado() {
                                        let list = [...this.seleccionados];
                                        if (this.mostrarOtro && this.otroTexto.trim() !== '') list.push(this.otroTexto.trim());
                                        return list.join(', ');
                                    }
                                }">
                                    <label class="form-label text-navy small fw-bold mb-2">Alergias</label>
                                    <div class="bg-light p-3 rounded-4 border">
                                        <div class="d-flex flex-wrap gap-3">
                                            <div class="form-check custom-checkbox mb-0">
                                                <input class="form-check-input" type="checkbox" value="Ninguna" id="al_ninguna" x-model="seleccionados">
                                                <label class="form-check-label small fw-medium cursor-pointer" for="al_ninguna">Ninguna</label>
                                            </div>
                                            <div class="form-check custom-checkbox mb-0">
                                                <input class="form-check-input" type="checkbox" value="Penicilina" id="al_peni" x-model="seleccionados">
                                                <label class="form-check-label small fw-medium cursor-pointer" for="al_peni">Penicilina</label>
                                            </div>
                                            <div class="form-check custom-checkbox mb-0">
                                                <input class="form-check-input" type="checkbox" value="Polvo / Ácaros" id="al_polvo" x-model="seleccionados">
                                                <label class="form-check-label small fw-medium cursor-pointer" for="al_polvo">Polvo / Ácaros</label>
                                            </div>
                                            <div class="form-check custom-checkbox mb-0">
                                                <input class="form-check-input" type="checkbox" value="Mariscos" id="al_mariscos" x-model="seleccionados">
                                                <label class="form-check-label small fw-medium cursor-pointer" for="al_mariscos">Mariscos</label>
                                            </div>
                                            <div class="form-check custom-checkbox mb-0">
                                                <input class="form-check-input" type="checkbox" id="al_otro" x-model="mostrarOtro" @change="if(!mostrarOtro) otroTexto = ''">
                                                <label class="form-check-label small fw-medium cursor-pointer" for="al_otro">Otra(s)</label>
                                            </div>
                                        </div>
                                        
                                        <div x-show="mostrarOtro" x-transition x-cloak class="mt-3">
                                            <div class="input-wrapper">
                                                <i class="bi bi-pencil-square input-icon"></i>
                                                <input type="text" class="form-control-custom" placeholder="Especifique sus alergias..." x-model="otroTexto" :required="mostrarOtro && role === 'paciente'">
                                            </div>
                                        </div>
                                        <input type="hidden" name="alergias" :value="resultado">
                                    </div>
                                </div>

                                <div class="col-12 mt-2" x-data="{ 
                                    seleccionados: [], mostrarOtro: false, otroTexto: '',
                                    get resultado() {
                                        let list = [...this.seleccionados];
                                        if (this.mostrarOtro && this.otroTexto.trim() !== '') list.push(this.otroTexto.trim());
                                        return list.join(', ');
                                    }
                                }">
                                    <label class="form-label text-navy small fw-bold mb-2">Padecimientos Crónicos</label>
                                    <div class="bg-light p-3 rounded-4 border">
                                        <div class="d-flex flex-wrap gap-3">
                                            <div class="form-check custom-checkbox mb-0">
                                                <input class="form-check-input" type="checkbox" value="Ninguno" id="pad_ninguno" x-model="seleccionados">
                                                <label class="form-check-label small fw-medium cursor-pointer" for="pad_ninguno">Ninguno</label>
                                            </div>
                                            <div class="form-check custom-checkbox mb-0">
                                                <input class="form-check-input" type="checkbox" value="Diabetes" id="pad_diab" x-model="seleccionados">
                                                <label class="form-check-label small fw-medium cursor-pointer" for="pad_diab">Diabetes</label>
                                            </div>
                                            <div class="form-check custom-checkbox mb-0">
                                                <input class="form-check-input" type="checkbox" value="Hipertensión" id="pad_hiper" x-model="seleccionados">
                                                <label class="form-check-label small fw-medium cursor-pointer" for="pad_hiper">Hipertensión</label>
                                            </div>
                                            <div class="form-check custom-checkbox mb-0">
                                                <input class="form-check-input" type="checkbox" value="Asma" id="pad_asma" x-model="seleccionados">
                                                <label class="form-check-label small fw-medium cursor-pointer" for="pad_asma">Asma</label>
                                            </div>
                                            <div class="form-check custom-checkbox mb-0">
                                                <input class="form-check-input" type="checkbox" id="pad_otro" x-model="mostrarOtro" @change="if(!mostrarOtro) otroTexto = ''">
                                                <label class="form-check-label small fw-medium cursor-pointer" for="pad_otro">Otro(s)</label>
                                            </div>
                                        </div>
                                        
                                        <div x-show="mostrarOtro" x-transition x-cloak class="mt-3">
                                            <div class="input-wrapper">
                                                <i class="bi bi-pencil-square input-icon"></i>
                                                <input type="text" class="form-control-custom" placeholder="Especifique sus padecimientos..." x-model="otroTexto" :required="mostrarOtro && role === 'paciente'">
                                            </div>
                                        </div>
                                        <input type="hidden" name="padecimientos_cronicos" :value="resultado">
                                    </div>
                                </div>

                                <div class="col-12 mt-2" x-data="{ 
                                    seleccionados: [], mostrarOtro: false, otroTexto: '',
                                    get resultado() {
                                        let list = [...this.seleccionados];
                                        if (this.mostrarOtro && this.otroTexto.trim() !== '') list.push(this.otroTexto.trim());
                                        return list.join(', ');
                                    }
                                }">
                                    <label class="form-label text-navy small fw-bold mb-2">Hábitos de Salud</label>
                                    <div class="bg-light p-3 rounded-4 border">
                                        <div class="d-flex flex-wrap gap-3">
                                            <div class="form-check custom-checkbox mb-0">
                                                <input class="form-check-input" type="checkbox" value="Ejercicio Regular" id="hab_ejercicio" x-model="seleccionados">
                                                <label class="form-check-label small fw-medium cursor-pointer" for="hab_ejercicio">Ejercicio Regular</label>
                                            </div>
                                            <div class="form-check custom-checkbox mb-0">
                                                <input class="form-check-input" type="checkbox" value="Fumador" id="hab_fuma" x-model="seleccionados">
                                                <label class="form-check-label small fw-medium cursor-pointer" for="hab_fuma">Fumador</label>
                                            </div>
                                            <div class="form-check custom-checkbox mb-0">
                                                <input class="form-check-input" type="checkbox" value="Consumo de Alcohol" id="hab_alcohol" x-model="seleccionados">
                                                <label class="form-check-label small fw-medium cursor-pointer" for="hab_alcohol">Consumo de Alcohol</label>
                                            </div>
                                            <div class="form-check custom-checkbox mb-0">
                                                <input class="form-check-input" type="checkbox" id="hab_otro" x-model="mostrarOtro" @change="if(!mostrarOtro) otroTexto = ''">
                                                <label class="form-check-label small fw-medium cursor-pointer" for="hab_otro">Otro(s)</label>
                                            </div>
                                        </div>
                                        
                                        <div x-show="mostrarOtro" x-transition x-cloak class="mt-3">
                                            <div class="input-wrapper">
                                                <i class="bi bi-pencil-square input-icon"></i>
                                                <input type="text" class="form-control-custom" placeholder="Especifique otros hábitos..." x-model="otroTexto" :required="mostrarOtro && role === 'paciente'">
                                            </div>
                                        </div>
                                        <input type="hidden" name="habitos_salud" :value="resultado">
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Botón Submit --}}
                        <div class="d-flex justify-content-end align-items-center mt-5 pt-4 border-top">
                            <button type="submit" class="btn btn-navy btn-lg rounded-pill px-5 py-3 fw-bold shadow-sm d-flex align-items-center">
                                <x-mcl-bookmark class="me-2" style="width: 1.2rem; color: white;" /> Guardar Expediente
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-layout>