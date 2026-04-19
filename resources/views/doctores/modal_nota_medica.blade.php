

<div class="modal fade" id="modalNotaMedica{{$cita->id}}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-navy text-white border-0 py-3">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-file-earmark-medical me-2"></i>
                    Nota Médica: <span> {{$cita->expediente->nombre_completo}}</span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form id="formNotaMedica"  action="{{ route('notas.store', $cita->id) }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label small fw-bold text-navy">DIAGNÓSTICO</label>
                            <textarea name="diagnostico" class="form-control rounded-3" rows="3" placeholder="Escriba el diagnóstico principal..." required></textarea>
                        </div>
                        
                        <div class="col-12">
                            <label class="form-label small fw-bold text-navy">TRATAMIENTO</label>
                            <textarea name="tratamiento" class="form-control rounded-3" rows="3" placeholder="Medicamentos, dosis, duración..." required></textarea>
                        </div>
                        
                        <div class="col-12">
                            <label class="form-label small fw-bold text-navy">NOTA DE SEGUIMIENTO (Opcional)</label>
                            <textarea name="nota_seguimiento" class="form-control rounded-3" rows="2" placeholder="Observaciones para la próxima consulta..."></textarea>
                            <div class="form-text mt-1 text-muted small">Esta nota será visible para ti y otros doctores en futuras consultas.</div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-navy rounded-pill px-4 shadow-sm">
                        Guardar y Finalizar Consulta
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>




<style>
    .bg-navy { background-color: #0f172a; }
    .btn-navy { background-color: #0f172a; color: white; border: none; }
    .btn-navy:hover { background-color: #1e293b; color: white; }
</style>


