<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStorageFileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'path' => ['sometimes', 'string', 'max:1024'], // move/rename when present
            'alias' => ['sometimes', 'nullable', 'string', 'max:191', 'alpha_dash'],
        ];
    }
}
