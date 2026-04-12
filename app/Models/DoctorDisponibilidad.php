<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DoctorDisponibilidad extends Model
{
    use HasFactory;

    protected $table = 'doctor_disponibilidad';

    protected $fillable = [
        'doctor_id',
        'dia_semana',
        'hora_inicio',
        'hora_fin',
    ];


    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }


    public function getNombreDiaAttribute(): string
    {
        $dias = [
            0 => 'Domingo', 1 => 'Lunes', 2 => 'Martes', 
            3 => 'Miércoles', 4 => 'Jueves', 5 => 'Viernes', 6 => 'Sábado'
        ];
        return $dias[$this->dia_semana];
    }
}