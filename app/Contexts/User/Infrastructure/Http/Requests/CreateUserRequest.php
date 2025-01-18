<?php

namespace App\Contexts\User\Infrastructure\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'username' => ['required', 'string', 'max:50'],
            'display_name' => ['nullable', 'string', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'username.required' => 'El username es requerido',
            'username.unique' => 'Este username ya está en uso',
            'username.max' => 'El username no puede exceder los 50 caracteres',
            'display_name.max' => 'El nombre no puede exceder los 100 caracteres'
        ];
    }
}
