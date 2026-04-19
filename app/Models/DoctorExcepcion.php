<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DoctorExcepcion extends Model
{
    use HasFactory;

    protected $table = 'doctor_excepciones';

    protected $fillable = [
        'doctor_id',
        'fecha',
        'trabaja',
        'hora_inicio',
        'hora_fin',
    ];

    /**
     * Casteo de tipos para facilitar el uso de Carbon.
     */
    protected function casts(): array
    {
        return [
            'fecha' => 'date',
            'trabaja' => 'boolean',
        ];
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }
}