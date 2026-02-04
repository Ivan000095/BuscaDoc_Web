<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comentario extends Model
{
    protected $table = 'comentarios';

    protected $fillable = [
        'id_autor',
        'id_destinatario',
        'tipo',
        'calificacion',
        'contenido',
    ];

    public function autor()
    {
        return $this->belongsTo(User::class, 'id_autor');
    }

    public function respuestas()
    {
        return $this->hasMany(Respuesta::class, 'comentario_id');
    }
}
