<?php

namespace App\Contexts\Tweet\Infrastructure\Http\Requests;

use App\Contexts\Tweet\Domain\ValueObjects\TweetContent;
use Illuminate\Foundation\Http\FormRequest as Request;

class CreateTweetRequest extends Request
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
     *
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        //podria validar alguna palabra clave
        return [
            'content' => [
                'required',
                'string',
                'max:280',
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'content.required' => 'El contenido del tweet es obligatorio',
            'content.string' => 'El contenido del tweet debe ser texto',
            'content.max' => 'El contenido del tweet no puede exceder los :max caracteres',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('content')) {
            $this->merge([
                'content' => trim($this->input('content')),
            ]);
        }
    }
}