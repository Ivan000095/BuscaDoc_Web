<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reporte extends Model
{
    protected $fillable = [
        'reportador_id',
        'reportado_id',
        'descripcion',
        'estado',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relación: quién hizo el reporte
    public function reportador()
    {
        return $this->belongsTo(User::class, 'reportador_id');
    }

    // Relación: a quién se reportó
    public function reportado()
    {
        return $this->belongsTo(User::class, 'reportado_id');
    }
}