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
                    <form action="{{ route('backups.create') }}" method="POST" id="formBackup">
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
                                @foreach ($backups as $backup)
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
                                            <a href="{{ route('backups.download', $backup['file_name']) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                                <i class="bi bi-download"></i>
                                            </a>
                                            <button onclick="deleteBackup('{{ route('backups.destroy', $backup['file_name']) }}')" class="btn btn-sm btn-outline-danger rounded-pill px-3 ms-1">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
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
                $('#backupsTable').DataTable({
                    language: {
                        url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json',
                        search: "_INPUT_",
                        searchPlaceholder: "Buscar respaldo...",
                        emptyTable: `
                            <div class="d-flex flex-column align-items-center justify-content-center text-muted py-5">
                                <i class="bi bi-inbox display-4 mb-3" style="font-size: 3rem;"></i>
                                <h5 class="fw-bold mb-1">Sin respaldos</h5>
                                <p class="mb-0">Aún no hay copias de seguridad generadas.</p>
                            </div>
                        `
                    },
                    order: [[2, 'desc']], 
                    dom: '<"d-flex justify-content-between align-items-center p-3"f>rt<"d-flex justify-content-between align-items-center p-3"ip>'
                });
            });

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