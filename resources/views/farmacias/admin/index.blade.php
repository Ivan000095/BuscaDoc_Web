<x-layout>
    <div class="modal fade" id="modalReporteFarmaciasPDF" tabindex="-1" aria-labelledby="modalReporteFarmaciasPDFLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-surface border-0 shadow-soft rounded-4 overflow-hidden">
                <div class="modal-header bg-navy text-white border-0 px-4 py-3">
                    <h5 class="modal-title fw-bold d-flex align-items-center" id="modalReporteFarmaciasPDFLabel">
                        <x-mcl-file class="icon-white me-2"/> Configurar Reporte
                    </h5>
                    <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                
                <form action="{{ route('admin.farmacias.reporte') }}" method="POST" target="_blank">
                    @csrf
                    <div class="modal-body p-4 text-main">
                        <p class="text-muted small mb-4">Selecciona los filtros para personalizar el listado de farmacias que aparecerán en tu PDF.</p>
                        
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-navy small mb-1">Registradas desde</label>
                                <input type="date" name="fecha_inicio" class="form-control bg-app border-0 shadow-none rounded-3 px-3 py-2 text-main">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-navy small mb-1">Hasta</label>
                                <input type="date" name="fecha_fin" class="form-control bg-app border-0 shadow-none rounded-3 px-3 py-2 text-main" value="{{ date('Y-m-d') }}">
                            </div>
                        </div>

                        <div class="mb-2">
                            <label class="form-label fw-bold text-navy small mb-1">Ordenar por</label>
                            <select name="orden" class="form-select bg-app border-0 shadow-none rounded-3 px-3 py-2 text-main">
                                <option value="recientes">Más recientes primero</option>
                                <option value="antiguos">Más antiguas primero</option>
                                <option value="nombre_asc">Nombre de Farmacia (A-Z)</option>
                                <option value="nombre_desc">Nombre de Farmacia (Z-A)</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="modal-footer border-top-0 bg-app px-4 py-3">
                        <button type="button" class="btn btn-light rounded-pill px-4 fw-bold shadow-sm" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-navy rounded-pill px-4 fw-bold shadow-sm d-flex align-items-center">
                            <x-mcl-download class="icon-white me-2"/> Descargar PDF
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @if(Auth::user() && Auth::user()->role == 'admin')
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold text-navy mb-0">Farmacias</h2>
                <p class="text-muted small mb-0">Catálogo de sucursales registradas</p>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-navy rounded-pill px-4 shadow-soft" data-bs-toggle="modal" data-bs-target="#modalReporteFarmaciasPDF">
                    <x-mcl-chart-pie class="icon-white me-1"/> 
                    <span class="d-none d-sm-inline">Generar reporte en PDF</span>
                </button>

                <button class="btn btn-navy rounded-pill px-4 shadow-sm" 
                    onclick="execute('{{ route('admin.farmacias.create') }}')">
                    <x-mcl-plus-circle class="icon-white me-1" /> Agregar Nueva
                </button>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table id="myTable" class="table table-hover align-middle mb-0" style="width:100%">
                        <thead class="bg-navy text-white">
                            <tr>
                                <th class="py-3 ps-4">Dueño</th>
                                <th class="py-3">Farmacia</th>
                                <th class="py-3">RFC</th>
                                <th class="py-3">Teléfono</th>
                                <th class="py-3">Horario</th>
                                <th class="py-3">Nacimiento</th>
                                <th class="py-3">Foto</th>
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

    @push('scripts')
        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
        <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

        <script>
            $(document).ready(function () {
                $('#myTable').DataTable({
                    serverSide: true,
                    processing: true,
                    ajax: {
                        url: '{{ route("admin.farmacias.index") }}',
                        type: 'GET'
                    },
                    columns: [
                        { data: 'nombre_dueño', name: 'users.name', className: 'ps-4 fw-bold text-navy' }, 
                        { data: 'nom_farmacia', name: 'nom_farmacia' },
                        { data: 'rfc', name: 'rfc' },
                        { data: 'telefono', name: 'telefono' },
                        { data: 'horario', name: 'horario', searchable: false },
                        { data: 'fecha_nacimiento', name: 'users.f_nacimiento' },
                        { 
                            data: 'foto', 
                            name: 'foto', 
                            orderable: false, 
                            searchable: false 
                        },
                        { 
                            data: 'acciones', 
                            orderable: false, 
                            searchable: false, 
                            className: 'text-end pe-4' 
                        }
                    ],
                    pageLength: 10,
                    language: {
                        url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json',
                        search: "_INPUT_",
                        searchPlaceholder: "Buscar farmacia...",
                        processing: `
                            <div class="d-flex flex-column align-items-center justify-content-center" style="color: #0d2e4e;">
                                <div class="spinner-border mb-2" role="status" style="width: 2rem; height: 2rem;"></div>
                                <span class="fw-semibold small">Procesando...</span>
                            </div>
                        `,
                    },
                    dom: '<"d-flex justify-content-between align-items-center p-3"f>rt<"d-flex justify-content-between align-items-center p-3"ip>'
                });
            });

            function execute(url) {
                window.location.href = url;
            }

            function deleteRecord(url) {
                if (confirm('¿Está seguro de eliminar esta farmacia y su usuario asociado? Esta acción no se puede deshacer.')) {
                    let form = document.createElement('form');
                    form.method = 'POST';
                    form.action = url;

                    let csrfToken = document.createElement('input');
                    csrfToken.type = 'hidden';
                    csrfToken.name = '_token';
                    csrfToken.value = '{{ csrf_token() }}';
                    form.appendChild(csrfToken);

                    let methodField = document.createElement('input');
                    methodField.type = 'hidden';
                    methodField.name = '_method';
                    methodField.value = 'DELETE';
                    form.appendChild(methodField);

                    document.body.appendChild(form);
                    form.submit();
                }
            }

            document.addEventListener("DOMContentLoaded", function() {
                const modalPDF = document.getElementById('modalReporteFarmaciasPDF');
                if (modalPDF) {
                    document.body.appendChild(modalPDF);
                }
            });
        </script>
    @endpush
</x-layout>