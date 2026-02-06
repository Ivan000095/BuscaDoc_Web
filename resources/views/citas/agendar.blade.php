<div class="modal fade" id="agendarCitaModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-navy">Agendar con {{ $doctor->user->name }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                
                <form action="{{ route('citas.store', $doctor->id) }}" method="POST">
                    @csrf
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="small text-muted fw-bold">Fecha</label>
                            {{-- name="fecha" para que pase la validación --}}
                            <input type="date" name="fecha" class="form-control form-control-pill" 
                                   min="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-6">
                            <label class="small text-muted fw-bold">Hora</label>
                            <input type="time" name="hora" class="form-control form-control-pill" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="small text-muted fw-bold">Detalles de su cita</label>
                        <textarea name="motivo" class="form-control rounded-3" rows="3" 
                                  placeholder="Describe brevemente tus síntomas..." required></textarea>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-navy rounded-pill">Confirmar Cita</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>