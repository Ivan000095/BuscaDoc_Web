<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SolicitudCambio extends Model
{
      protected $table = "solicitudes_cambio";

    protected $fillable = [
        'cita_id', 'solicitante_id', 'solicitado_id', 
        'nueva_fecha', 'nueva_hora', 'motivo', 'estado'
    ];

    public function cita() { return $this->belongsTo(Cita::class); }
    public function solicitante() { return $this->belongsTo(User::class, 'solicitante_id'); }
}