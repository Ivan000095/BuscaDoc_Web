<?php

namespace App\Http\Resources\API;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RespuestumResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'comentario_id' => $this->comentario_id,
            'id_respondedor' => $this->id_respondedor,
            'contenido' => $this->contenido,
            'user_id' => $this->user_id,
        ];
    }
}
