<x-layout>
    @if (Auth::user()->role == 'admin')
        <div class="container py-5">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold text-navy mb-0">Gestión de Doctores</h2>
                    <p class="text-muted small mb-0">Directorio de especialistas médicos</p>
                </div>
                <button class="btn btn-navy rounded-pill px-4 shadow-sm" onclick="execute('{{ route('doctores.agregar') }}')">
                    <i class="bi bi-plus-lg me-1"></i> 
                    <span class="d-none d-sm-inline">Agregar Nuevo</span>
                </button>
            </div>

            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table id="myTable" class="table table-hover align-middle mb-0" style="width:100%">
                            <thead class="bg-navy text-white">
                                <tr>
                                    <th class="py-3 ps-4">Nombre</th>
                                    <th class="py-3">Especialidad</th>
                                    <th class="py-3">Cédula</th>
                                    <th class="py-3">Descripción</th>
                                    <th class="py-3">Costo</th>
                                    <th class="py-3">Entrada</th>
                                    <th class="py-3">Salida</th>
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
                        url: '{{ route("doctor.data") }}',
                        type: 'GET'
                    },
                    columns: [
                        { data: 'name', name: 'users.name', className: 'ps-4 fw-bold text-navy' },
                        { data: 'especialidad', name: 'especialidad', orderable: false },
                        { data: 'cedula', name: 'cedula' },
                        { data: 'descripcion', name: 'descripcion' },
                        { data: 'costos', name: 'costo' },
                        { data: 'horarioentrada', name: 'horario_entrada' },
                        { data: 'horariosalida', name: 'horario_salida' },
                        { data: 'fecha', name: 'users.f_nacimiento' },
                        { 
                            data: 'image', 
                            name: 'image', 
                            orderable: false, 
                            searchable: false 
                        },
                        { 
                            data: 'actions', 
                            orderable: false, 
                            searchable: false, 
                            className: 'text-end pe-4' 
                        }
                    ],
                    pageLength: 10,
                    // Traducción y diseño de controles
                    language: {
                        url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json',
                        search: "_INPUT_",
                        searchPlaceholder: "Buscar doctor..."
                    },
                    dom: '<"d-flex justify-content-between align-items-center p-3"f>rt<"d-flex justify-content-between align-items-center p-3"ip>'
                });
            });

            // Función para redirigir
            function execute(url) {
                window.location.href = url;
            }

            // Función para eliminar con confirmación y CSRF
            function deleteRecord(url) {
                if (confirm('¿Está seguro de eliminar este doctor y su usuario asociado? Esta acción no se puede deshacer.')) {
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
            
            // NOTA: Se eliminaron las alertas session() porque el Layout principal
            // ya maneja las notificaciones flotantes (pill-notification).
        </script>
    @endpush
</x-layout>