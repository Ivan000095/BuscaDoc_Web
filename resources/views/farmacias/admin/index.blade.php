<x-layout>
    @if(Auth::user() && Auth::user()->role == 'admin')
        
        {{-- Encabezado --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold text-navy mb-0">Farmacias</h2>
                <p class="text-muted small mb-0">Catálogo de sucursales registradas</p>
            </div>
            <button class="btn btn-navy rounded-pill px-4 shadow-sm" 
                onclick="execute('{{ route('admin.farmacias.create') }}')">
                <i class="bi bi-plus-lg me-1"></i> Agregar Nueva
            </button>
        </div>

        {{-- Tabla --}}
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
                        searchPlaceholder: "Buscar farmacia..."
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
        </script>
    @endpush
</x-layout>