<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Expediente extends Model
{
    protected $fillable = [
        'id',
        'user_id',
        'nombre_completo',
        'fecha_nacimiento',
        'genero',
        'parentesco',
        'tipo_sangre',
        'alergias',
        'padecimientos_cronicos',
        'habitos_salud'
    ];

    // El dueño del expediente (ej. el paciente que puede ser un padre de familia 
    // o el doctor que puede guardar la lista de sus pacientes)
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // El historial de notas que los doctores han dejado en este expediente
    public function notas(): HasMany
    {
        return $this->hasMany(NotaMedica::class);
    }

// En ExpedienteController.php



}