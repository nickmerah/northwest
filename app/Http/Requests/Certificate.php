<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class Certificate extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'jamb_result' => 'sometimes|required|file|mimes:pdf|max:100',
            'o_level_result' => 'sometimes|required|file|mimes:pdf|max:100',
            'birth_certificate' => 'sometimes|required|file|mimes:pdf|max:100',
        ];
    }
}
