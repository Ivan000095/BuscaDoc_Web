<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;

class DoctorStoreRequest extends FormRequest
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
            'name' => ['required', 'string'],
            'especialidad' => ['nullable', 'string'],
            'cedula' => ['nullable', 'string'],
            'costos' => ['required', 'numeric', 'between:-999999.99,999999.99'],
            'horarioentrada' => ['required'],
        ];
    }
}
