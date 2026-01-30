<?php

namespace App\Http\Resources\API;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MensajeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'id_remitente' => $this->id_remitente,
            'id_destinatario' => $this->id_destinatario,
            'contenido' => $this->contenido,
            'leido' => $this->leido,
            'user_id' => $this->user_id,
        ];
    }
}
