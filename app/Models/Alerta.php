<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alerta extends Model {
    protected $fillable = [
        'user_id',
        'titulo',
        'mensaje',
        'tipo',
        'referencia_id',
        'leido'
    ];

    // Relación para saber de quién es la alerta
    public function user() {
        return $this->belongsTo(User::class);
    }
}