<?php

namespace App\Contexts\User\Infrastructure\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SearchUserRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'username' => ['required', 'string', 'max:50'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:99'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
