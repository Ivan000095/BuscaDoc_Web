<x-layout>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <style>
        body {
            background: linear-gradient(135deg, #f0f4f8 0%, #d9e2ec 100%);
            min-height: 100vh;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
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

        .was-validated .form-control-custom:invalid,
        .was-validated .form-select-custom:invalid,
        .form-control-custom.is-invalid,
        .form-select-custom.is-invalid {
            border-color: #ef4444 !important;
            background-color: #fef2f2 !important;
            box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.1) !important;
        }
    </style>

    <div class="register-container">
        <div class="register-wrapper">

            <div class="brand-content">
                <div class="brand-logo">
                    <img src="{{ asset('images/logo_negro.png') }}" alt="BuscaDoc">
                </div>
                <h1 class="brand-title">
                    Únete a la red médica<br>más grande de la región
                </h1>
                <p class="brand-description">
                    Un solo lugar para todas tus necesidades de salud. Regístrate y descubre lo fácil que es cuidar de
                    ti y los tuyos.
                </p>
                <ul class="features">
                    <li>
                        <div class="feature-icon"><i class="bi bi-shield-check"></i></div>
                        <span>Datos 100% seguros y confidenciales</span>
                    </li>
                    <li>
                        <div class="feature-icon"><i class="bi bi-speedometer2"></i></div>
                        <span>Acceso rápido a especialistas</span>
                    </li>
                    <li>
                        <div class="feature-icon"><i class="bi bi-journal-medical"></i></div>
                        <span>Gestión de recetas y expedientes</span>
                    </li>
                </ul>
            </div>

            <div class="register-form-container">
                <div class="register-header">
                    <h2>Crear Cuenta</h2>
                    <p>Completa tus datos para comenzar en BuscaDoc</p>
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger rounded-4 border-0 mb-4 shadow-sm">
                        <ul class="mb-0 small">
                            @foreach ($errors->all() as $error)
                                <li><i class="bi bi-exclamation-circle me-2"></i>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form id="registerForm" method="POST" action="{{ route('register') }}" enctype="multipart/form-data"
                    x-data="{ role: '{{ old('role', 'paciente') }}' }">
                    @csrf

                    <div class="row g-3">
                        <div class="col-md-12 form-group mb-0">
                            <label>Nombre Completo</label>
                            <div class="input-wrapper">
                                <i class="bi bi-person-fill input-icon"></i>
                                <input type="text" name="name"
                                    class="form-control-custom @error('name') is-invalid @enderror"
                                    value="{{ old('name') }}" required minlength="3" pattern="[a-zA-Záéíóú\s]+" placeholder="Ej: Juan Pérez">
                            </div>
                        </div>

                        <div class="col-md-12 form-group mb-0">
                            <label>Correo Electrónico</label>
                            <div class="input-wrapper">
                                <i class="bi bi-envelope-fill input-icon"></i>
                                <input type="email" name="email"
                                    class="form-control-custom @error('email') is-invalid @enderror"
                                    value="{{ old('email') }}" required placeholder="juan@mail.com">
                            </div>
                        </div>

                        <div class="col-md-6 form-group mb-0">
                            <label>Fecha de Nacimiento</label>
                            <input type="date" name="f_nacimiento" id="f_nacimiento"
                                class="form-control-custom form-control-no-icon @error('f_nacimiento') is-invalid @enderror"
                                value="{{ old('f_nacimiento') }}" required
                                :max="role === 'doctor' ? '{{ date('Y-m-d', strtotime('-24 years')) }}' : (role === 'farmacia' ? '{{ date('Y-m-d', strtotime('-21 years')) }}' : '{{ date('Y-m-d', strtotime('-18 years')) }}')"
                                onchange="validarEdadDinamica(this)">

                            <div id="age-error" class="invalid-feedback ms-3">
                                @if(old('role') == 'doctor') Debe ser mayor de 24 años.
                                @elseif(old('role') == 'farmacia') Debe ser mayor de 21 años.
                                @else Debe ser mayor de 18 años.
                                @endif
                            </div>
                        </div>

                        <div class="col-md-6 form-group mb-0">
                            <label>Foto de Perfil</label>
                            <div class="d-flex align-items-center gap-3">
                                <img id="imagePreview" src="#" alt="Tu foto" class="rounded-circle border shadow-sm"
                                    style="width: 52px; height: 52px; object-fit: cover; display: none;">
                                <div class="file-upload-wrapper flex-grow-1">
                                    <input type="file" name="foto" id="foto_input"
                                        class="file-upload-input @error('foto') is-invalid @enderror" accept="image/*"
                                        onchange="previewAndLabel(this)">
                                    <label for="foto_input" class="file-upload-label" id="foto_label_text">
                                        <i class="bi bi-camera me-2"></i><span>Seleccionar...</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="divider"><span>Tipo de Perfil</span></div>

                    <div class="form-group position-relative mb-4" x-data="{ open: false }">
                        <label>¿Cómo usarás la plataforma?</label>
                        <input type="hidden" name="role" x-model="role">

                        <div @click="open = !open" @click.outside="open = false"
                            class="form-select-custom d-flex justify-content-between align-items-center"
                            style="padding-left: 20px; cursor: pointer;">
                            <span class="fw-bold"
                                x-text="role === 'paciente' ? 'Paciente (Busco atención)' : (role === 'doctor' ? 'Doctor (Ofrezco servicios)' : 'Farmacia (Vendo productos)')"></span>
                            <i class="bi bi-chevron-down text-muted transition-all"
                                :style="open && 'transform: rotate(180deg)'"></i>
                        </div>

                        <div x-show="open" x-transition class="custom-options" style="display: none;">
                            <div class="custom-option" :class="role === 'paciente' ? 'selected' : ''"
                                @click="role = 'paciente'; open = false; setTimeout(() => validarEdadDinamica(document.getElementById('f_nacimiento')), 100)">
                                <i class="bi bi-person-badge fs-4" style="color: #0ea5e9;"></i>
                                <span>Paciente (Busco atención médica)</span>
                            </div>
                            <div class="custom-option" :class="role === 'doctor' ? 'selected' : ''"
                                @click="role = 'doctor'; open = false; setTimeout(() => validarEdadDinamica(document.getElementById('f_nacimiento')), 100)">
                                <i class="bi bi-heart-pulse fs-4" style="color: #10b981;"></i>
                                <span>Doctor (Ofrezco servicios)</span>
                            </div>
                            <div class="custom-option" :class="role === 'farmacia' ? 'selected' : ''"
                                @click="role = 'farmacia'; open = false; setTimeout(() => validarEdadDinamica(document.getElementById('f_nacimiento')), 100)">
                                <i class="bi bi-shop fs-4" style="color: #f59e0b;"></i>
                                <span>Farmacia (Vendo productos)</span>
                            </div>
                        </div>
                    </div>

                    <div x-show="role === 'doctor'" x-transition style="display: none;"
                        x-data="{ citasActivas: {{ old('citas', '0') == '1' ? 'true' : 'false' }} }">
                        <h6 class="dynamic-section-title"><i class="bi bi-briefcase-medical me-2"></i>Datos
                            Profesionales</h6>

                        <div class="info-box mb-4">
                            <i class="bi bi-shield-lock-fill fs-3 text-navy"></i>
                            <p class="small mb-0">Al registrarte como <b>Doctor</b>, aceptas nuestro Aviso de
                                Privacidad. Tus datos se utilizarán para garantizar la seguridad de los pacientes.</p>
                        </div>

                        <div class="row g-3">
                            <div class="col-12 form-group mb-0">
                                <textarea name="descripcion_doc" class="form-control-custom form-control-no-icon"
                                    rows="2" placeholder="Breve descripción de usted y su experiencia"
                                    :required="role === 'doctor'">{{ old('descripcion') }}</textarea>
                            </div>

                            <div class="col-md-6 form-group mb-0">
                                <div class="input-wrapper">
                                    <i class="bi bi-card-heading input-icon"></i>
                                    <input type="text" name="cedula" class="form-control-custom"
                                        placeholder="Cédula (7 u 8 dígitos)" pattern="\d{7,8}" maxlength="8"
                                        title="La cédula debe contener 7 u 8 dígitos numéricos"
                                        value="{{ old('cedula') }}" :required="role === 'doctor'">
                                </div>
                            </div>

                            <div class="col-md-6 form-group mb-0">
                                <div class="input-wrapper">
                                    <i class="bi bi-currency-dollar input-icon"></i>
                                    <input type="number" name="costo" class="form-control-custom"
                                        placeholder="Costo por consulta" min="0" step="0.01" value="{{ old('costo') }}"
                                        :required="role === 'doctor'">
                                </div>
                            </div>

                            <div class="col-12 my-3">
                                <div
                                    class="bg-light p-3 rounded-pill border d-flex align-items-center justify-content-between px-4">
                                    <span class="small fw-bold text-navy"><i
                                            class="bi bi-calendar-check me-2"></i>¿Recibir citas en línea?</span>
                                    <div class="form-check form-switch mb-0 fs-5">
                                        <input class="form-check-input" type="checkbox" name="citas" id="citasSwitch"
                                            value="1" x-model="citasActivas">
                                    </div>
                                </div>
                            </div>

                            <div class="col-12"
                                x-data="{ horarios: [{dia: '1', inicio: '09:00', fin: '14:00'}], addHorario() { this.horarios.push({dia: '1', inicio: '09:00', fin: '18:00'}) }, removeHorario(index) { this.horarios.splice(index, 1) } }">

                                <div x-show="citasActivas" x-transition class="mb-3">
                                    <label class="small text-muted fw-bold mb-2">Duración promedio de cita</label>
                                    <select name="duracion_cita" class="form-select-custom form-control-no-icon">
                                        <option value="15">15 minutos</option>
                                        <option value="30" selected>30 minutos</option>
                                        <option value="45">45 minutos</option>
                                        <option value="60">1 hora</option>
                                    </select>
                                </div>

                                <template x-for="(horario, index) in horarios" :key="index">
                                    <div
                                        class="row g-2 mb-3 mt-2 align-items-center bg-light p-3 rounded-4 border mx-0">
                                        <div class="col-md-4">
                                            <label class="small text-muted fw-bold">Día</label>
                                            <select :name="`horarios[${index}][dia]`" x-model="horario.dia"
                                                class="form-control-custom form-control-no-icon py-2"
                                                :required="role === 'doctor' && citasActivas">
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
                                            <label class="small text-muted fw-bold">Entrada</label>
                                            <input type="text" :name="`horarios[${index}][inicio]`"
                                                x-model="horario.inicio"
                                                class="form-control-custom form-control-no-icon py-2 bg-white"
                                                :required="role === 'doctor' && citasActivas"
                                                x-init="flatpickr($el, { enableTime: true, noCalendar: true, dateFormat: 'H:i', time_24hr: true })"
                                                placeholder="09:00">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="small text-muted fw-bold">Salida</label>
                                            <input type="text" :name="`horarios[${index}][fin]`" x-model="horario.fin"
                                                class="form-control-custom form-control-no-icon py-2 bg-white"
                                                :required="role === 'doctor' && citasActivas"
                                                x-init="flatpickr($el, { enableTime: true, noCalendar: true, dateFormat: 'H:i', time_24hr: true })"
                                                placeholder="17:00">
                                        </div>
                                        <div class="col-md-2 text-center mt-4">
                                            <button type="button" @click="removeHorario(index)"
                                                class="btn btn-outline-danger border-0 rounded-circle"
                                                x-show="horarios.length > 1">
                                                <i class="bi bi-trash3-fill"></i>
                                            </button>
                                        </div>
                                    </div>
                                </template>
                                <button type="button" @click="addHorario()"
                                    class="btn btn-sm btn-outline-secondary rounded-pill mt-2 fw-bold">
                                    <i class="bi bi-plus-circle me-1"></i> Añadir horario
                                </button>
                            </div>

                            <div class="col-12 mt-3" x-data="{ 
                                idiomasSeleccionados: [], 
                                mostrarOtro: false,
                                otroIdioma: '',
                                get resultadoIdiomas() {
                                    let list = [...this.idiomasSeleccionados];
                                    if (this.mostrarOtro && this.otroIdioma.trim() !== '') {
                                        list.push(this.otroIdioma.trim());
                                    }
                                    return list.join(', ');
                                }
                            }">
                                <label class="form-label text-navy small fw-bold mb-2">
                                    <i class="bi bi-translate me-1"></i>Idiomas que domina
                                </label>

                                <div class="bg-light p-3 rounded-4 border">
                                    <div class="d-flex flex-wrap gap-4">
                                        <div class="form-check custom-checkbox mb-0">
                                            <input class="form-check-input" type="checkbox" value="Español"
                                                id="idioma_es" x-model="idiomasSeleccionados">
                                            <label class="form-check-label small fw-medium cursor-pointer"
                                                for="idioma_es">Español</label>
                                        </div>
                                        <div class="form-check custom-checkbox mb-0">
                                            <input class="form-check-input" type="checkbox" value="Tzeltal"
                                                id="idioma_tz" x-model="idiomasSeleccionados">
                                            <label class="form-check-label small fw-medium cursor-pointer"
                                                for="idioma_tz">Tzeltal</label>
                                        </div>
                                        <div class="form-check custom-checkbox mb-0">
                                            <input class="form-check-input" type="checkbox" value="Inglés"
                                                id="idioma_en" x-model="idiomasSeleccionados">
                                            <label class="form-check-label small fw-medium cursor-pointer"
                                                for="idioma_en">Inglés</label>
                                        </div>
                                        <div class="form-check custom-checkbox mb-0">
                                            <input class="form-check-input" type="checkbox" id="idioma_otro_check"
                                                x-model="mostrarOtro" @change="if(!mostrarOtro) otroIdioma = ''">
                                            <label class="form-check-label small fw-medium cursor-pointer"
                                                for="idioma_otro_check">Otro(s)</label>
                                        </div>
                                    </div>

                                    <div x-show="mostrarOtro" x-transition style="display: none;" class="mt-3">
                                        <div class="input-wrapper">
                                            <i class="bi bi-pencil-square input-icon"></i>
                                            <input type="text" class="form-control-custom"
                                                placeholder="Ej: Francés, Alemán..." x-model="otroIdioma"
                                                :required="mostrarOtro && role === 'doctor'">
                                        </div>
                                    </div>

                                    <input type="hidden" name="idiomas" :value="resultadoIdiomas">
                                </div>
                            </div>

                            <div class="col-12 mt-3">
                                <label class="form-label text-navy small fw-bold">Especialidades</label>
                                <div class="bg-light p-3 rounded-4 border d-flex flex-wrap gap-3">
                                    @foreach($especialidades ?? [] as $esp)
                                        <div class="form-check custom-checkbox mb-0">
                                            <input class="form-check-input" type="checkbox" name="especialidades[]"
                                                value="{{ $esp->id }}" id="esp_{{ $esp->id }}">
                                            <label class="form-check-label small fw-medium"
                                                for="esp_{{ $esp->id }}">{{ $esp->nombre }}</label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <div x-show="role === 'paciente'" x-transition style="display: none;">
                        <h6 class="dynamic-section-title"><i class="bi bi-clipboard2-pulse me-2"></i>Datos Médicos Básicos</h6>
                        
                        <div class="info-box mb-4" style="border-left-color: #0ea5e9;">
                            <i class="bi bi-lock-fill fs-3 text-info"></i>
                            <p class="small mb-0">Como <b>Paciente</b>, tus datos de salud están protegidos bajo estricta confidencialidad para mejorar tu atención médica en BuscaDoc.</p>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6 form-group mb-0">
                                <label>Género</label>
                                <select name="genero" class="form-select-custom form-control-no-icon" :required="role === 'paciente'">
                                    <option value="masculino">Masculino</option>
                                    <option value="femenino">Femenino</option>
                                </select>
                            </div>

                            <div class="col-md-6 form-group mb-0">
                                <label>Tipo de Sangre</label>
                                <select name="tipo_sangre" class="form-select-custom form-control-no-icon">
                                    <option value="" disabled selected>Seleccione...</option>
                                    @foreach(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $ts)
                                        <option value="{{ $ts }}" {{ old('tipo_sangre') == $ts ? 'selected' : '' }}>{{ $ts }}</option>
                                    @endforeach
                                </select>
                            </div>

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

                    <div x-show="role === 'farmacia'" x-transition style="display: none;">
                        <h6 class="dynamic-section-title"><i class="bi bi-shop-window me-2"></i>Datos del Negocio</h6>

                        <div class="info-box mb-4" style="border-left-color: #f59e0b;">
                            <i class="bi bi-file-earmark-check fs-3 text-warning"></i>
                            <p class="small mb-0">Al registrar tu <b>Farmacia</b>, confirmas que la información es
                                verídica y cumple con las normativas comerciales de salud.</p>
                        </div>

                        <div class="row g-3">
                            <div class="col-12 form-group mb-0">
                                <textarea name="descripcion" class="form-control-custom form-control-no-icon" rows="2"
                                    placeholder="Descripción breve de la farmacia"
                                    :required="role === 'farmacia'">{{ old('descripcion') }}</textarea>
                            </div>

                            <div class="col-12 form-group mb-0">
                                <div class="input-wrapper">
                                    <i class="bi bi-building input-icon"></i>
                                    <input type="text" name="nom_farmacia" class="form-control-custom"
                                        placeholder="Nombre Oficial de la Farmacia" value="{{ old('nom_farmacia') }}"
                                        :required="role === 'farmacia'">
                                </div>
                            </div>

                            <div class="col-md-6 form-group mb-0">
                                <div class="input-wrapper">
                                    <i class="bi bi-upc-scan input-icon"></i>
                                    <input type="text" name="rfc" class="form-control-custom"
                                        placeholder="RFC (12-13 caracteres)"
                                        pattern="[A-Za-zÑñ&]{3,4}\d{6}[A-Za-z0-9]{3}" maxlength="13"
                                        style="text-transform: uppercase;" title="Ingrese un RFC válido"
                                        value="{{ old('rfc') }}" :required="role === 'farmacia'">
                                </div>
                            </div>

                            <div class="col-md-6 form-group mb-0">
                                <div class="input-wrapper">
                                    <i class="bi bi-telephone-fill input-icon"></i>
                                    <input type="tel" name="telefono" class="form-control-custom"
                                        placeholder="Teléfono (10 dígitos)" pattern="\d{10}" maxlength="10"
                                        title="Debe contener exactamente 10 números" value="{{ old('telefono') }}"
                                        :required="role === 'farmacia'">
                                </div>
                            </div>

                            <div class="col-md-6 form-group mb-0">
                                <label>Horario Apertura</label>
                                <input type="text" name="horario_entrada_f"
                                    class="form-control-custom form-control-no-icon bg-white"
                                    value="{{ old('horario_entrada') }}" :required="role === 'farmacia'"
                                    x-init="flatpickr($el, { enableTime: true, noCalendar: true, dateFormat: 'h:i K' })"
                                    placeholder="00:00 AM">
                            </div>

                            <div class="col-md-6 form-group mb-0">
                                <label>Horario Cierre</label>
                                <input type="text" name="horario_salida_f"
                                    class="form-control-custom form-control-no-icon bg-white"
                                    value="{{ old('horario_salida') }}" :required="role === 'farmacia'"
                                    x-init="flatpickr($el, { enableTime: true, noCalendar: true, dateFormat: 'h:i K' })"
                                    placeholder="00:00 PM">
                            </div>
                        </div>
                    </div>

                    <div x-show="role === 'doctor' || role === 'farmacia'" x-transition style="display: none;">
                        <div class="divider"><span>Ubicación Exacta</span></div>

                        <div class="map-container mb-4 position-relative">
                            <div class="bg-light px-4 py-3 border-bottom d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-geo-alt-fill text-danger fs-4 me-3"></i>
                                    <div>
                                        <h6 class="mb-0 fw-bold text-navy" x-text="role === 'farmacia' ? 'Ubicación de la Farmacia' : 'Ubicación del Consultorio'"></h6>
                                        <small class="text-muted">Arrastra el marcador o usa tu ubicación actual.</small>
                                    </div>
                                </div>
                                <button type="button" onclick="obtenerUbicacionActual()" class="btn btn-sm btn-navy rounded-pill px-3 shadow-sm">
                                    <i class="bi bi-crosshair2 me-1"></i> Mi ubicación
                                </button>
                            </div>
                            
                            <input type="hidden" name="latitud" id="latitud" value="{{ old('latitud') }}">
                            <input type="hidden" name="longitud" id="longitud" value="{{ old('longitud') }}">
                            <div id="map" style="height: 350px; width: 100%;"></div>
                        </div>
                    </div>

                    <div class="divider"><span>Seguridad</span></div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6 form-group">
                            <label>Contraseña</label>
                            <div class="input-wrapper">
                                <i class="bi bi-lock-fill input-icon"></i>
                                <input type="password" name="password"
                                    class="form-control-custom @error('password') is-invalid @enderror" required
                                    minlength="8" placeholder="Mínimo 8 caracteres">
                            </div>
                        </div>

                        <div class="col-md-6 form-group">
                            <label>Confirmar Contraseña</label>
                            <div class="input-wrapper">
                                <i class="bi bi-shield-lock-fill input-icon"></i>
                                <input type="password" name="password_confirmation" class="form-control-custom" required
                                    minlength="8" placeholder="••••••••">
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn-primary-custom" id="submitBtn">
                        Crear Cuenta Ahora
                    </button>

                    <div class="text-center mt-4">
                        <span class="text-muted">¿Ya tienes una cuenta?</span>
                        <a href="{{ route('login') }}" class="fw-bold text-decoration-none ms-1"
                            style="color: #0d2e4e;">Iniciar sesión</a>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('registerForm');
            const submitBtn = document.getElementById('submitBtn');

            // 1. MAGIA DE UX PARA INPUTS DINÁMICOS
            form.addEventListener('invalid', function (e) {
                e.preventDefault(); // 🛑 Mata el globito nativo
                
                const input = e.target;
                
                // EL FIX: Usamos una propiedad directa en el input para no perder el span de vista jamás
                if (!input._errorSpan) {
                    const errorSpan = document.createElement('div');
                    errorSpan.className = 'text-danger small fw-bold ps-4 mt-1 mi-error-dinamico';
                    errorSpan.style.display = 'none';
                    
                    if(input.parentElement.classList.contains('input-wrapper')) {
                        input.parentElement.insertAdjacentElement('afterend', errorSpan);
                    } else {
                        input.insertAdjacentElement('afterend', errorSpan);
                    }
                    
                    input._errorSpan = errorSpan; // Lo guardamos directamente en el objeto del input
                }

                // Escribimos el error y pintamos de rojo
                input._errorSpan.textContent = input.validationMessage;
                input._errorSpan.style.display = 'block';
                input.classList.add('is-invalid');

                // Evento para limpiar cuando el usuario escribe
                const limpiarError = function() {
                    if (input.checkValidity()) {
                        input._errorSpan.style.display = 'none';
                        input.classList.remove('is-invalid');
                        input.removeEventListener('input', limpiarError);
                    }
                };
                input.addEventListener('input', limpiarError);

            }, true); // El 'true' atrapa los eventos de los elementos de Alpine.js

            // 2. INTERCEPTAR EL ENVÍO
            form.addEventListener('submit', function (e) {
                if (!form.checkValidity()) {
                    e.preventDefault(); 
                    e.stopPropagation();
                    form.classList.add('was-validated'); 
                    
                    const invalidInputs = form.querySelectorAll(':invalid');
                    if (invalidInputs.length > 0) {
                        invalidInputs[0].focus();
                        invalidInputs[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                    return;
                }

                // 3. ENVIAR FORMULARIO
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Procesando...';
                submitBtn.style.opacity = '0.8';
                submitBtn.style.cursor = 'not-allowed';
            });
        });

        function obtenerUbicacionActual() {
            const btn = event.currentTarget; // Capturamos el botón para dar feedback visual
            const originalContent = btn.innerHTML;
            
            if (navigator.geolocation) {
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        const pos = {
                            lat: position.coords.latitude,
                            lng: position.coords.longitude,
                        };

                        // Centrar mapa y mover marcador
                        map.setCenter(pos);
                        marker.setPosition(pos);
                        
                        // Actualizar los inputs ocultos
                        updateInputs(pos.lat, pos.lng);

                        // Restaurar botón
                        btn.disabled = false;
                        btn.innerHTML = originalContent;
                    },
                    (error) => {
                        btn.disabled = false;
                        btn.innerHTML = originalContent;
                        alert("Error: No se pudo obtener tu ubicación. Asegúrate de dar permisos de GPS.");
                        console.error(error);
                    }
                );
            } else {
                alert("Tu navegador no soporta geolocalización.");
            }
        }

        function previewAndLabel(input) {
            const preview = document.getElementById('imagePreview');
            const labelText = document.querySelector('#foto_label_text span');
            const labelContainer = document.getElementById('foto_label_text');

            if (input.files && input.files[0]) {
                // 1. Usar FileReader para leer la imagen en memoria
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    // 2. Asignar la imagen al src y hacerla visible
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                }
                
                reader.readAsDataURL(input.files[0]);
                
                // 3. Cambiar el texto del botón por el nombre del archivo
                labelText.textContent = input.files[0].name;
                // Agregar la clase para que el CSS lo pinte de azul claro
                labelContainer.classList.add('has-file');
            } else {
                // Reset por si el usuario cancela la selección
                preview.src = '#';
                preview.style.display = 'none';
                labelText.textContent = 'Seleccionar...';
                labelContainer.classList.remove('has-file');
            }
        }
    </script>

    <script>
        let map;
        let marker;

        window.initMap = function () {
            const defLat = 16.9080;
            const defLng = -92.0946;

            const inputLatVal = document.getElementById('latitud').value;
            const inputLngVal = document.getElementById('longitud').value;

            const myLat = inputLatVal ? parseFloat(inputLatVal) : defLat;
            const myLng = inputLngVal ? parseFloat(inputLngVal) : defLng;
            const myLatLng = { lat: myLat, lng: myLng };

            map = new google.maps.Map(document.getElementById("map"), {
                zoom: 15,
                center: myLatLng,
                mapTypeId: google.maps.MapTypeId.ROADMAP
            });

            marker = new google.maps.Marker({
                position: myLatLng,
                map: map,
                draggable: true,
                title: "Ubicación Seleccionada",
                animation: google.maps.Animation.DROP
            });

            marker.addListener("dragend", function (event) {
                updateInputs(event.latLng.lat(), event.latLng.lng());
            });

            map.addListener("click", function (event) {
                marker.setPosition(event.latLng);
                updateInputs(event.latLng.lat(), event.latLng.lng());
            });

            if (!inputLatVal) updateInputs(myLat, myLng);
        }

        function updateInputs(lat, lng) {
            document.getElementById('latitud').value = lat;
            document.getElementById('longitud').value = lng;
        }

        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('registerForm');
            const submitBtn = document.getElementById('submitBtn');

            form.addEventListener('submit', function (e) {
                if (!form.checkValidity()) {
                    e.preventDefault(); 
                    e.stopPropagation();
                    
                    form.classList.add('was-validated'); 
                    
                    const primerError = form.querySelector(':invalid');
                    if(primerError) {
                        primerError.focus();
                        primerError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                    return;
                }

                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Procesando...';
                submitBtn.style.opacity = '0.8';
                submitBtn.style.cursor = 'not-allowed';
            });
        });
    </script>

    <script src="https://maps.googleapis.com/maps/api/js?key={{ env('API_KEY') }}&callback=initMap" async
        defer></script>
</x-layout>