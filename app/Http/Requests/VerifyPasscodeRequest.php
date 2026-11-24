<?php

namespace App\Http\Requests;

use App\Services\TurnstileService;
use Illuminate\Foundation\Http\FormRequest;

class VerifyPasscodeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        $turnstileRequired = app(TurnstileService::class)->isEnabled();

        return [
            'passcode' => ['required', 'string', 'max:255'],
            'cf_turnstile_response' => [
                $turnstileRequired ? 'required' : 'nullable',
                'string',
                'max:2048',
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'cf_turnstile_response.required' => 'Selesaikan verifikasi keamanan Cloudflare terlebih dahulu.',
        ];
    }
}
