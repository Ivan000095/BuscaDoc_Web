<x-layout>

    <div class="container">
        <div class="row my-4 mx-1">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="mb-0">Doctores</h1>
                <button style="background-color: #00213D!important;"class="btn btn-primary rounded-5" onclick="execute('{{ route('doctores.create') }}')">
                    <i class=" bi bi-plus"></i>
                    <span class="d-none d-sm-inline">Agregar</span>
                </button>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="table-responsive">
                <table id="myTable" class="table table-striped table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>Nombre</th>
                            <th>Especialidad</th>
                            <th>Cédula</th>
                            <th>Descripción</th>
                            <th>Costo</th>
                            <th>Entrada</th>
                            <th>Salida</th>
                            <th>Fecha Nac.</th>
                            <th>Foto</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @section('js')
        {{-- Asegúrate de tener jQuery y DataTables JS importados en tu layout principal --}}

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
                        { data: 'name', name: 'users.name' },
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
                            className: 'text-end'
                        }
                    ],
                    pageLength: 10,
                    language: {
                        url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
                    }
                });
            });

            function execute(url) {
                window.location.href = url;
            }

            function deleteRecord(url) {
                if (confirm('¿Está seguro de eliminar este doctor y su usuario asociado?')) {
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

            // Alertas de sesión (SweetAlert o nativo)
            @if (session('success'))
                alert("{{ session('success') }}");
            @endif

            @if (session('error'))
                alert("{{ session('error') }}");
            @endif
        </script>
    @endsection

</x-layout>