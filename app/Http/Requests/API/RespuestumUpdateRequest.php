<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;

class RespuestumUpdateRequest extends FormRequest
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
            'comentario_id' => ['required', 'integer', 'exists:comentarios.id,id'],
            'id_respondedor' => ['required'],
            'contenido' => ['required', 'string'],
            'user_id' => ['required', 'integer', 'exists:users,id'],
        ];
    }
}
