<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Paciente extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'tipo_sangre',
        'alergias',
        'cirugias',        // Nuevo campo
        'padecimientos',   // Nuevo campo
        'habitos',         // Nuevo campo
        'contacto_emergencia', // Nuevo campo
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}