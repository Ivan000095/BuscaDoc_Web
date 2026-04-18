<x-layout>
    @if (Auth::user()->role == 'admin')
        <div class="container py-5">
            
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show shadow-sm rounded-4 border-0" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show shadow-sm rounded-4 border-0" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold text-navy mb-0">Gestión de Respaldos</h2>
                    <p class="text-muted small mb-0">Administración de copias de seguridad de la base de datos</p>
                </div>

                <div class="d-flex gap-2">
                    <form action="{{ route('admin.backups.create') }}" method="POST" id="formBackup">
                        @csrf
                        <button type="submit" class="btn btn-navy rounded-pill px-4 shadow-sm" id="btnCrearRespaldo">
                            <i class="bi bi-database-add me-1 text-white"></i>
                            <span class="d-none d-sm-inline" id="btnText">Generar Respaldo Manual</span>
                        </button>
                    </form>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table id="backupsTable" class="table table-hover align-middle mb-0" style="width:100%">
                            <thead class="bg-navy text-white">
                                <tr>
                                    <th class="py-3 ps-4">Nombre del Archivo</th>
                                    <th class="py-3">Tamaño</th>
                                    <th class="py-3">Fecha de Creación</th>
                                    <th class="py-3 text-end pe-4">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($backups as $backup)
                                    <tr>
                                        <td class="ps-4 fw-bold text-navy">
                                            <i class="bi bi-file-earmark-zip text-secondary me-2"></i>
                                            {{ $backup['file_name'] }}
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark border">{{ $backup['file_size'] }}</span>
                                        </td>
                                        <td class="text-muted">{{ $backup['last_modified'] }}</td>
                                        <td class="text-end pe-4">
                                            <a href="{{ route('admin.backups.download', $backup['file_name']) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                                <i class="bi bi-download"></i> Descargar
                                            </a>
                                            <button onclick="deleteBackup('{{ route('admin.backups.destroy', $backup['file_name']) }}')" class="btn btn-sm btn-outline-danger rounded-pill px-3 ms-1">
                                                <i class="bi bi-trash"></i> Eliminar
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-5 text-muted">
                                            <i class="bi bi-inbox display-4 d-block mb-2"></i>
                                            Aún no hay respaldos generados.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="container d-flex flex-column justify-content-center align-items-center min-vh-100"
            style="margin-top: -50px;">
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
                // Instancia simple de DataTable (sin ajax) para la tabla de archivos
                $('#backupsTable').DataTable({
                    language: {
                        url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json',
                        search: "_INPUT_",
                        searchPlaceholder: "Buscar respaldo..."
                    },
                    order: [[2, 'desc']], // Ordenar por fecha descendente por defecto
                    dom: '<"d-flex justify-content-between align-items-center p-3"f>rt<"d-flex justify-content-between align-items-center p-3"ip>'
                });

                // Efecto de carga en el botón de respaldo manual
                $('#formBackup').on('submit', function() {
                    let btn = $('#btnCrearRespaldo');
                    btn.prop('disabled', true);
                    btn.html('<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Generando...');
                });
            });

            // Función para eliminar el respaldo
            function deleteBackup(url) {
                if (confirm('¿Estás seguro de eliminar este archivo de respaldo? Esta acción no se puede deshacer.')) {
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
        </script>
    @endpush
</x-layout>