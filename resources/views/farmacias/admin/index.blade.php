<x-layout>
    @if (Auth::user()->role == 'admin')
    <div class="container">
        <div class="row my-4 mx-1">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="mb-0">Farmacias</h1>
                <button style="background-color: #00213D!important;" class="btn btn-primary rounded-5" onclick="execute('{{ route('admin.farmacias.create') }}')">
                    <i class="bi bi-plus"></i>
                    <span class="d-none d-sm-inline">Agregar</span>
                </button>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="table-responsive">
                <table id="myTable" class="table table-striped table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>Dueño</th>
                            <th>Farmacia</th>
                            <th>RFC</th>
                            <th>Teléfono</th>
                            <th>Horario</th>
                            <th>Días</th>
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
    @else
        <h1 class="justify-content-center">No tendría por qué estar aquí...</h1>
    @endif

    @section('js')
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
                        { data: 'nombre_dueño', name: 'users.name' },
                        { data: 'nom_farmacia', name: 'nom_farmacia' },
                        { data: 'rfc', name: 'rfc' },
                        { data: 'telefono', name: 'telefono' },
                        { data: 'horario', name: 'horario' },
                        { data: 'dias_op', name: 'dias_op' },
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
                if (confirm('¿Está seguro de eliminar esta farmacia y su usuario asociado?')) {
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

            @if (session('success'))
                alert("{{ session('success') }}");
            @endif

            @if (session('error'))
                alert("{{ session('error') }}");
            @endif
        </script>
    @endsection
</x-layout>