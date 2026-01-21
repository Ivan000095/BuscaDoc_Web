<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'especialidad',
        'name',
        'descripcion',
        'fecha',
        'image',
        'telefono',
        'idioma',
        'cedula',
        'direccion',
        'costos',
        'horarioentrada',
        'horariosalida',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'fecha' => 'date',
            'costos' => 'decimal:2',
        ];
    }
    protected $casts = [
        'fecha' => 'date', // Esto convierte el texto a Objeto Carbon automágicamente
    ];
}
