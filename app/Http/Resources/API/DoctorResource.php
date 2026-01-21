<?php

namespace App\Http\Resources\API;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DoctorResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'especialidad' => $this->especialidad,
            'name' => $this->name,
            'descripcion' => $this->descripcion,
            'fecha' => $this->fecha,
            'image' => $this->image,
            'telefono' => $this->telefono,
            'idioma' => $this->idioma,
            'cedula' => $this->cedula,
            'direccion' => $this->direccion,
            'costos' => $this->costos,
            'horarioentrada' => $this->horarioentrada,
            'horariosalida' => $this->horariosalida,
        ];
    }
}
