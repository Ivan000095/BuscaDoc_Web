<x-layout>
    {{-- Solo mostramos si es admin --}}
    @if (Auth::user()->role == 'admin')
        <div class="container py-5">
            {{-- Encabezado --}}
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold text-navy mb-0">Gestión de Pacientes</h2>
                    <p class="text-muted small mb-0">Directorio de usuarios registrados</p>
                </div>
                <a href="{{ route('pacientes.create') }}" class="btn btn-navy rounded-pill px-4 shadow-sm">
                    <i class="bi bi-plus-lg me-1"></i> 
                    <span class="d-none d-sm-inline">Agregar Paciente</span>
                </a>
            </div>

            {{-- Tarjeta de la Tabla --}}
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        {{-- ID 'myTable' para DataTables si lo usas, si no, funciona como tabla normal --}}
                        <table id="myTable" class="table table-hover align-middle mb-0" style="width:100%">
                            <thead class="bg-navy text-white">
                                <tr>
                                    <th class="py-3 ps-4">Nombre</th>
                                    <th class="py-3">Email</th>
                                    <th class="py-3 text-center">Tipo de Sangre</th>
                                    <th class="py-3">Emergencia</th>
                                    <th class="py-3 text-end pe-4">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pacientes as $paciente)
                                    <tr>
                                        {{-- Nombre y Avatar --}}
                                        <td class="ps-4">
                                            <div class="d-flex align-items-center">
                                                {{-- Avatar generado o foto --}}
                                                @if($paciente->user->foto)
                                                    <img src="{{ asset('storage/' . $paciente->user->foto) }}" class="rounded-circle me-2" style="width: 35px; height: 35px; object-fit: cover;">
                                                @else
                                                    <div class="rounded-circle bg-light d-flex align-items-center justify-content-center me-2 text-navy fw-bold" style="width: 35px; height: 35px; font-size: 0.8rem;">
                                                        {{ substr($paciente->user->name, 0, 1) }}
                                                    </div>
                                                @endif
                                                <span class="fw-bold text-dark">{{ $paciente->user->name }}</span>
                                            </div>
                                        </td>
                                        
                                        {{-- Email --}}
                                        <td class="text-muted small">{{ $paciente->user->email }}</td>
                                        
                                        {{-- Sangre (Badge) --}}
                                        <td class="text-center">
                                            @if($paciente->tipo_sangre)
                                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-3">
                                                    {{ $paciente->tipo_sangre }}
                                                </span>
                                            @else
                                                <span class="text-muted small">—</span>
                                            @endif
                                        </td>

                                        {{-- Emergencia --}}
                                        <td>
                                            @if($paciente->contacto_emergencia)
                                                <div class="d-flex align-items-center text-muted small">
                                                    <i class="bi bi-telephone-fill me-2 text-danger"></i>
                                                    {{ $paciente->contacto_emergencia }}
                                                </div>
                                            @else
                                                <span class="text-muted small fst-italic">No registrado</span>
                                            @endif
                                        </td>

                                        {{-- Acciones --}}
                                        <td class="text-end pe-4">
                                            <div class="d-flex justify-content-end gap-2">
                                                <a href="{{ route('pacientes.edit', $paciente->id) }}" class="btn btn-outline-navy btn-sm rounded-pill" title="Editar">
                                                    <i class="bi bi-pencil-fill"></i>
                                                </a>
                                                
                                                <form action="{{ route('pacientes.destroy', $paciente->id) }}" method="POST" 
                                                      onsubmit="return confirm('¿Confirma que desea eliminar a este paciente?');" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm rounded-pill shadow-sm" title="Eliminar">
                                                        <i class="bi bi-trash-fill"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-muted">
                                            <i class="bi bi-people fs-1 d-block mb-2 opacity-50"></i>
                                            No hay pacientes registrados aún.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                
                {{-- Paginación (si aplica) --}}
                @if(method_exists($pacientes, 'links'))
                    <div class="card-footer bg-white border-0 py-3">
                        {{ $pacientes->links() }}
                    </div>
                @endif
            </div>
        </div>
    @else
        {{-- VISTA DE ACCESO DENEGADO (Estilizada igual que antes) --}}
        <div class="container d-flex flex-column justify-content-center align-items-center min-vh-100" style="margin-top: -50px;">
            <div class="card shadow-lg border-0 rounded-4 p-5 text-center" style="max-width: 500px;">
                <div class="mb-3">
                    <div class="bg-danger-subtle text-danger rounded-circle d-inline-flex p-3">
                        <i class="bi bi-shield-lock-fill display-4"></i>
                    </div>
                </div>
                <h2 class="fw-bold text-navy">Acceso Restringido</h2>
                <p class="text-muted mt-2">
                    Hola <strong>{{ Auth::user()->name }}</strong>, no tienes los permisos necesarios para acceder a este módulo.
                </p>
                <div class="mt-4">
                    <a href="{{ route('home') }}" class="btn btn-navy rounded-pill px-5 py-2">
                        <i class="bi bi-arrow-left me-2"></i> Volver al Inicio
                    </a>
                </div>
            </div>
        </div>
    @endif

    {{-- Script para inicializar DataTables solo si hay tabla (Opcional, si quieres búsqueda y paginación automática) --}}
    @section('js')
        @if(Auth::user()->role == 'admin')
            <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
            <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
            <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
            <script>
                $(document).ready(function () {
                    $('#myTable').DataTable({
                        language: {
                            url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json',
                            search: "_INPUT_",
                            searchPlaceholder: "Buscar paciente..."
                        },
                        dom: '<"d-flex justify-content-between align-items-center p-3"f>rt<"d-flex justify-content-between align-items-center p-3"ip>'
                    });
                });
            </script>
        @endif
    @endsection
</x-layout>