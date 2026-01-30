<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Especialidad extends Model
{
    protected $table = 'especialidads'; 

    protected $fillable = ['Nombre'];

    // Relación inversa (opcional pero útil)
    public function doctores()
    {
        return $this->belongsToMany(Doctor::class, 'doctor__especialidads', 'especialidad_id', 'doctor_id');
    }
}