<x-layout>
    <style>
        .modal { z-index: 2050 !important; }
        .modal-backdrop { z-index: 2040 !important; }
        
        .dataTables_processing {
            box-shadow: none !important;
            border: none !important;
            background: none !important;
            z-index: 10 !important;
        }
    </style>
    @if (Auth::user()->role == 'admin')
        <div class="container py-5">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold text-navy mb-0">Gestión de Doctores</h2>
                    <p class="text-muted small mb-0">Panel de control de especialistas registrados</p>
                </div>

                <div class="d-flex gap-2">
                    <button class="btn btn-navy rounded-pill px-4 shadow-sm" data-bs-toggle="modal"
                        data-bs-target="#modalReportePDF">
                        <x-mcl-chart-pie class="icon-white me-1" />
                        <span class="d-none d-sm-inline">Reporte PDF</span>
                    </button>

                    <button class="btn btn-navy rounded-pill px-4 shadow-sm" onclick="execute('{{ url('/doctorstore') }}')">
                        <x-mcl-plus-circle class="icon-white me-1" />
                        <span class="d-none d-sm-inline">Nuevo Doctor</span>
                    </button>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table id="myTable" class="table table-hover align-middle mb-0" style="width:100%">
                            <thead class="bg-navy text-white">
                                <tr>
                                    <th class="py-3 ps-4">Foto</th>
                                    <th class="py-3">Nombre</th>
                                    <th class="py-3">Especialidad</th>
                                    <th class="py-3">Cédula</th>
                                    <th class="py-3">Costo</th>
                                    <th class="py-3">Estado Horario</th>
                                    <th class="py-3 text-center">Agenda</th>
                                    <th class="py-3 text-end pe-4">Acciones</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="container d-flex flex-column justify-content-center align-items-center min-vh-100" style="margin-top: -50px;">
            <div class="card shadow-lg border-0 rounded-4 p-5 text-center" style="max-width: 500px;">
                <div class="mb-3">
                    <div class="bg-danger-subtle text-danger rounded-circle d-inline-flex p-3">
                        <i class="bi bi-shield-lock-fill display-4"></i>
                    </div>
                </div>
                <h2 class="fw-bold text-navy">Acceso Restringido</h2>
                <p class="text-muted mt-2">
                    Hola <strong>{{ Auth::user()->name }}</strong>, esta sección es exclusiva para administradores.
                </p>
                <div class="mt-4">
                    <a href="{{ route('home') }}" class="btn btn-navy rounded-pill px-5 py-2">
                        <i class="bi bi-arrow-left me-2"></i> Volver al Inicio
                    </a>
                </div>
            </div>
        </div>
    @endif

    @push('modals')
        <div class="modal fade" id="modalReportePDF" tabindex="-1" aria-labelledby="modalReportePDFLabel" aria-hidden="true"
            data-bs-backdrop="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content bg-surface border-0 shadow-soft rounded-4 overflow-hidden">
                    <div class="modal-header bg-navy text-white border-0 px-4 py-3">
                        <h5 class="modal-title fw-bold d-flex align-items-center" id="modalReportePDFLabel">
                            <x-mcl-file class="icon-white me-2" /> Configurar Reporte
                        </h5>
                        <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal"
                            aria-label="Cerrar"></button>
                    </div>

                    <form action="{{ route('doctores.reporte') }}" method="POST" target="_blank">
                        @csrf
                        <div class="modal-body p-4 text-main">
                            <p class="text-muted small mb-4">Selecciona los filtros para personalizar el listado de médicos
                                que aparecerán en tu PDF.</p>
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-navy small mb-1">Registrados desde</label>
                                    <input type="date" name="fecha_inicio"
                                        class="form-control bg-light border-0 rounded-3 px-3 py-2">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-navy small mb-1">Hasta</label>
                                    <input type="date" name="fecha_fin"
                                        class="form-control bg-light border-0 rounded-3 px-3 py-2"
                                        value="{{ date('Y-m-d') }}">
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold text-navy small mb-1">Estado de Citas</label>
                                <select name="citas_activas" class="form-select bg-light border-0 rounded-3 px-3 py-2">
                                    <option value="todos">Todos los doctores</option>
                                    <option value="1">Solo con agenda habilitada</option>
                                    <option value="0">Agenda deshabilitada</option>
                                </select>
                            </div>
                            <div class="mb-2">
                                <label class="form-label fw-bold text-navy small mb-1">Ordenar por</label>
                                <select name="orden" class="form-select bg-light border-0 rounded-3 px-3 py-2">
                                    <option value="recientes">Más recientes</option>
                                    <option value="antiguos">Más antiguos</option>
                                    <option value="costo_alto">Costo más alto</option>
                                    <option value="costo_bajo">Costo más bajo</option>
                                </select>
                            </div>
                        </div>

                        <div class="modal-footer border-top-0 bg-light px-4 py-3">
                            <button type="button" class="btn btn-light rounded-pill px-4 fw-bold"
                                data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-navy rounded-pill px-4 fw-bold d-flex align-items-center">
                                <x-mcl-download class="icon-white me-2" /> Descargar PDF
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endpush

    @push('scripts')
        <script>
            $(document).ready(function () {
                $('#myTable').DataTable({
                    serverSide: true,
                    processing: true,
                    ajax: '{{ route("doctor.data") }}',
                    columns: [
                        { data: 'image', orderable: false, searchable: false, className: 'ps-4' },
                        { data: 'name', name: 'users.name', className: 'fw-bold text-navy' },
                        { data: 'especialidad', orderable: false },
                        { data: 'cedula', name: 'cedula' },
                        { data: 'costos', name: 'costo' },
                        { data: 'horario', orderable: false },
                        { data: 'citas', name: 'citas', className: 'text-center' },
                        { data: 'actions', orderable: false, searchable: false, className: 'text-end pe-4' }
                    ],
                    language: {
                        url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json',
                        processing: `
                            <div class="d-flex flex-column align-items-center justify-content-center py-3" style="background: rgba(255,255,255,0.8); border-radius: 15px;">
                                <div class="spinner-border text-navy mb-2" role="status" style="width: 2.5rem; height: 2.5rem;"></div>
                                <span class="fw-bold text-navy small text-uppercase">Cargando datos...</span>
                            </div>
                        `,
                    },
                    dom: '<"d-flex justify-content-between align-items-center p-3"f>rt<"d-flex justify-content-between align-items-center p-3"ip>'
                });
            });

            function execute(url) { window.location.href = url; }
            function deleteRecord(url) {
                if (confirm('¿Eliminar doctor y usuario asociado?')) {
                    let form = document.createElement('form');
                    form.method = 'POST'; form.action = url;
                    form.innerHTML = `@csrf @method('DELETE')`;
                    document.body.appendChild(form);
                    form.submit();
                }
            }
        </script>
    @endpush
</x-layout>