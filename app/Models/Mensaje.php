<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mensaje extends Model
{
    use HasFactory;

    protected $table = 'mensajes';

    protected $fillable = [
        'id_remitente',
        'id_destinatario',
        'contenido',
        'leido'
    ];

    public function remitente()
    {
        return $this->belongsTo(User::class, 'id_remitente');
    }

    // Relación con el que recibe
    public function destinatario()
    {
        return $this->belongsTo(User::class, 'id_destinatario');
    }
}
