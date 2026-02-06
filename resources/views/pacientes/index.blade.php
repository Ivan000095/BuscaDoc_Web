<x-layout>
    @if (Auth::user()->role == 'admin')
        <div class="container">
            <div class="row my-4 mx-1">
                <div class="d-flex justify-content-between align-items-center">
                    <h1 class="mb-0">Pacientes</h1>
                    <a href="{{ route('pacientes.create') }}" class="btn btn-primary rounded-5"
                        style="background-color: #00213D!important;">
                        <i class="bi bi-plus"></i> Agregar Paciente
                    </a>
                </div>
            </div>

            <div class="table-responsive">
                <table id="myTable" class="table table-striped table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Tipo de Sangre</th>
                            <th>Contacto de emergencia</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pacientes as $paciente)
                            <tr>
                                <td>{{ $paciente->user->name }}</td>
                                <td>{{ $paciente->user->email }}</td>
                                <td><span class="badge bg-danger">{{ $paciente->tipo_sangre }}</span></td>
                                <td>
                                    <small><strong>Emergencia:</strong> {{ $paciente->contacto_emergencia ?? 'N/A' }}</small>
                                </td>
                                <td class="text-end">
                                    <div class="btn-group shadow-sm">
                                        <a href="{{ route('pacientes.edit', $paciente->id) }}"
                                            class="btn btn-outline-primary btn-sm">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('pacientes.destroy', $paciente->id) }}" method="POST"
                                            onsubmit="return confirm('¿Estás seguro de eliminar este paciente?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        {{-- ESTO ES LO QUE VERÁ EL DOCTOR O CUALQUIER OTRO ROL --}}
        <div class="container mt-5">
            <div class="row justify-content-center">
                <div class="col-md-6 text-center">
                    <div class="card shadow-lg border-0 rounded-4 p-5">
                        <div class="card-body">
                            <i class="bi bi-exclamaion-octagon text-danger display-1"></i>
                            <h2 class="fw-bold mt-4">Acceso Restringido</h2>
                            <p class="text-muted fs-5">
                                Lo sentimos, <strong>{{ Auth::user()->name }}</strong>.
                                Como {{ Auth::user()->role }}, no tienes permisos para gestionar el catálogo de pacientes.
                            </p>
                            <hr class="my-4">
                            <p class="small text-secondary mb-4">Si crees que esto es un error, contacta al administrador
                                del sistema.</p>
                            <a href="{{ route('home') }}" class="btn btn-primary btn-lg rounded-pill px-5">
                                <i class="bi bi-house-door me-2"></i>Volver al Inicio
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

</x-layout>