<?php

namespace App\Contexts\User\Infrastructure\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UnfollowUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Cambiar si necesitas lógica de autorización
    }

    public function rules(): array
    {
        return [
            'target_user_id' => 'required|numeric|exists:users,id',
        ];
    }

    public function messages(): array
    {
        return [
            'target_user_id.exists' => 'El usuario objetivo no existe.',
        ];
    }

    protected function prepareForValidation()
    {
        // Mueve el parámetro de la URL al body para validarlo
        $this->merge([
            'target_user_id' => $this->route('target_user_id'),
        ]);
    }
}