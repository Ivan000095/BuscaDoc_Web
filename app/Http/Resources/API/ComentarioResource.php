<?php

namespace App\Http\Resources\API;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ComentarioResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'id_autor' => $this->id_autor,
            'id_destinatario' => $this->id_destinatario,
            'tipo' => $this->tipo,
            'calificacion' => $this->calificacion,
            'contenido' => $this->contenido,
            'user_id' => $this->user_id,
            'respuestas' => RespuestaCollection::make($this->whenLoaded('respuestas')),
        ];
    }
}
