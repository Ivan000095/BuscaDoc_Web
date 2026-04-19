<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotaMedica extends Model
{

    protected $table = "notas_medicas";

    protected $fillable = [
        'expediente_id',
        'doctor_id',
        'cita_id',
        'diagnostico',
        'tratamiento',
        'nota_seguimiento'
    ];

    public function expediente(): BelongsTo
    {
        return $this->belongsTo(Expediente::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }
}