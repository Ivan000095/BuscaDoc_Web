<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;

class ComentarioUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'id_autor' => ['required'],
            'id_destinatario' => ['required'],
            'tipo' => ['required', 'in:resena,pregunta'],
            'calificacion' => ['nullable', 'integer'],
            'contenido' => ['nullable', 'string'],
            'user_id' => ['required', 'integer', 'exists:users,id'],
        ];
    }
}
