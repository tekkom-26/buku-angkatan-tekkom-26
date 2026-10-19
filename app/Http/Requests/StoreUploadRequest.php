<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUploadRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * Authorization for the public storage API is handled by the
     * `angkatan.passcode` middleware, not here.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $maxKb = (int) config('angkatan.public_storage.max_upload_size_kb', 10240);

        return [
            'file' => ['required', 'file', 'max:'.$maxKb],
            'path' => ['required', 'string', 'max:1024'],
            'alias' => ['nullable', 'string', 'max:191', 'alpha_dash'],
            'overwrite' => ['nullable', 'boolean'],
        ];
    }
}
