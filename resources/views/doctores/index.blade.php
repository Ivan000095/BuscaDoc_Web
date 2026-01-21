<x-layout>

    <?php
// $products = [
//     ['id' => 1, 'name' => 'Coca600ml', 'description' => 'Coca cola de 600 grs', 'price' => 18.00],
//     ['id' => 2, 'name' => 'Coca1lt', 'description' => 'Coca cola de 1 litro', 'price' => 38.00],
// ];
?>
    <div class="container">
        <div class="row my-4 mx-1">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="mb-0">Doctores</h1>
                <button class="btn btn-primary btn-sm" onclick="execute('/doctores/agregar')">
                    <i class="bi bi-plus"></i>
                    <span class="d-none d-sm-inline">Agregar</span>
                </button>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="table-responsive">
                <table id="myTable" class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>name</th>
                            <th>especialidad</th>
                            <th>descripcion</th>
                            <th>fecha</th>
                            <th>image</th>
                            <th>telefono</th>
                            <th>idioma</th>
                            <th>cedula</th>
                            <th>direccion</th>
                            <th>costos</th>
                            <th>horarioentrada</th>
                            <th>horariosalida</th>
                            <th class="text-end text-nowrap w-auto">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- @foreach ($products as $product)
                        <tr>
                            <td>{{ $product['name'] }}</td>
                            <td>{{ $product['description'] }}</td>
                            <td>${{ number_format($product['price'], 2) }}</td>
                            <td class="text-end text-nowrap w-auto">
                                <button class="btn btn-primary btn-sm"
                                    onclick="execute('/products/{{ $product['id'] }}/edit')">
                                    <i class="bi bi-pencil"></i>
                                    <span class="d-none d-sm-inline">Edit</span>
                                </button>
                                <button class="btn btn-danger btn-sm"
                                    onclick="deleteRecord('/products/{{ $product['id'] }}')">
                                    <i class="bi bi-trash"></i>
                                    <span class="d-none d-sm-inline">Delete</span>
                                </button>
                            </td>
                        </tr>
                        @endforeach --}}
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @section('js')
        <script>
            $('#myTable').DataTable({
                serverSide: true,
                processing: true,
                ajax: {
                    url: '{{ route("doctor.data") }}',
                    type: 'GET'
                },
                columns: [
                    { data: 'name' },
                    { data: 'especialidad' },
                    { data: 'descripcion' },
                    { data: 'fecha' },
                    { data: 'image' },
                    { data: 'telefono' },
                    { data: 'idioma' },
                    { data: 'cedula' },
                    { data: 'direccion' },
                    { data: 'costos' },
                    { data: 'horarioentrada' },
                    { data: 'horariosalida' },
                    {
                        data: 'actions',
                        orderable: false,
                        searchable: false
                    }
                ],
                pageLength: 10, // Items por página
                lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
                // language: {
                //     url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json' // Opcional: español
                // }
            });

            function execute(url) {
                window.open(url, '_self');
            }
            function deleteRecord(url) {
                if (confirm('¿Está seguro de eliminar este registro?')) {
                    $('<form>', { 'action': url, 'method': 'POST' })
                        .append($('<input>', { type: 'hidden', name: '_token', value: '{{ csrf_token() }}' }))
                        .append($('<input>', { type: 'hidden', name: '_method', value: 'DELETE' }))
                        .appendTo('body')
                        .submit()
                        .remove();
                }
            }
            // Obtener de la sessión el mensaje de éxito o error
            @if (session('success'))
                alert(`{{ session('success') }}`);
                // Opcional: Recarga la tabla después de agregar/eliminar
                // $('#myTable').DataTable().ajax.reload(null, false); // false para no resetear paginación
            @endif
        </script>
    @endsection

</x-layout>