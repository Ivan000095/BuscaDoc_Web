<x-layout>

    <div class="modal fade" id="modalReportePacientesPDF" tabindex="-1" aria-labelledby="modalReportePacientesPDFLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-surface border-0 shadow-soft rounded-4 overflow-hidden">
                <div class="modal-header bg-navy text-white border-0 px-4 py-3">
                    <h5 class="modal-title fw-bold d-flex align-items-center" id="modalReportePacientesPDFLabel">
                        <x-mcl-file class="icon-white me-2"/> Reporte de Pacientes
                    </h5>
                    <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                
                <form action="{{ route('pacientes.reporte') }}" method="POST" target="_blank">
                    @csrf
                    <div class="modal-body p-4 text-main">
                        <p class="text-muted small mb-4">Selecciona los filtros para personalizar tu listado de pacientes.</p>
                        
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-navy small mb-1">Registrados desde</label>
                                <input type="date" name="fecha_inicio" class="form-control bg-app border-0 shadow-none rounded-3 px-3 py-2 text-main">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-navy small mb-1">Hasta</label>
                                <input type="date" name="fecha_fin" class="form-control bg-app border-0 shadow-none rounded-3 px-3 py-2 text-main" value="{{ date('Y-m-d') }}">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold text-navy small mb-1">Tipo de Sangre</label>
                            <select name="tipo_sangre" class="form-select bg-app border-0 shadow-none rounded-3 px-3 py-2 text-main">
                                <option value="todos">Cualquier tipo de sangre</option>
                                <option value="A+">A+</option>
                                <option value="A-">A-</option>
                                <option value="B+">B+</option>
                                <option value="B-">B-</option>
                                <option value="AB+">AB+</option>
                                <option value="AB-">AB-</option>
                                <option value="O+">O+</option>
                                <option value="O-">O-</option>
                            </select>
                        </div>

                        <div class="mb-2">
                            <label class="form-label fw-bold text-navy small mb-1">Ordenar por</label>
                            <select name="orden" class="form-select bg-app border-0 shadow-none rounded-3 px-3 py-2 text-main">
                                <option value="recientes">Más recientes primero</option>
                                <option value="antiguos">Más antiguos primero</option>
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

    @if (Auth::user()->role == 'admin')
        <div class="container py-5">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold text-navy mb-0">Gestión de Pacientes</h2>
                    <p class="text-muted small mb-0">Directorio de usuarios registrados</p>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-navy rounded-pill px-4 shadow-soft" data-bs-toggle="modal" data-bs-target="#modalReportePacientesPDF">
                        <x-mcl-chart-pie class="icon-white me-1"/> 
                        <span class="d-none d-sm-inline">Generar reporte en PDF</span>
                    </button>
                    <a href="{{ route('pacientes.create') }}" class="btn btn-navy rounded-pill px-4 shadow-sm">
                        <x-mcl-plus-circle class="icon-white me-1" /> 
                        <span class="d-none d-sm-inline">Agregar Paciente</span>
                    </a>
                </div>
                
            </div>

            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-body p-0">
                    <div class="table-responsive">
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
                                        <td class="ps-4">
                                            <div class="d-flex align-items-center">
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
                                        
                                        <td class="text-muted small">{{ $paciente->user->email }}</td>
                                        
                                        <td class="text-center">
                                            @if($paciente->tipo_sangre)
                                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-3">
                                                    {{ $paciente->tipo_sangre }}
                                                </span>
                                            @else
                                                <span class="text-muted small">—</span>
                                            @endif
                                        </td>

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
                
                @if(method_exists($pacientes, 'links'))
                    <div class="card-footer bg-white border-0 py-3">
                        {{ $pacientes->links() }}
                    </div>
                @endif
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

    @push('scripts')
        @if(Auth::user()->role == 'admin')
            <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
            <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
            <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
            <script>
                $(document).ready(function () {
                    $('#myTable').DataTable({
                        processing: true,
                        language: {
                            url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json',
                            search: "_INPUT_",
                            searchPlaceholder: "Buscar paciente...",
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

                document.addEventListener("DOMContentLoaded", function() {
                    const modalPDF = document.getElementById('modalReportePacientesPDF');
                    if (modalPDF) {
                        document.body.appendChild(modalPDF);
                    }
                });
            </script>
        @endif
    @endpush
</x-layout>