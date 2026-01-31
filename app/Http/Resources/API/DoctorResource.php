<?php

namespace App\Http\Resources\API;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DoctorResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            // --- datos de doctor) ---
            'id_doctor' => $this->id,
            'cedula' => $this->cedula,
            'descripcion' => $this->descripcion,
            'precio_consulta' => (float) $this->costo,
            'idiomas' => $this->idiomas,
            'horarios' => [
                'entrada' => $this->horario_entrada,
                'salida' => $this->horario_salida,
            ],

            // --- datos de usuario ---
            'nombre' => $this->user->name,
            'email' => $this->user->email,
            'fecha_nacimiento' => $this->user->f_nacimiento,
            'foto_perfil' => $this->user->foto 
                ? asset('storage/' . $this->user->foto) 
                : null, // Genera la URL completa automáticamente
            'ubicacion' => [
                'latitud' => (float) $this->user->latitud,
                'longitud' => (float) $this->user->longitud,
            ],

            'especialidades' => $this->especialidades->map(function ($esp) {
                return [
                    'id' => $esp->id,
                    'nombre' => $esp->nombre,
                ];
            }),
        ];
    }
}