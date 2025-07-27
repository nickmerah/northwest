<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class Jamb extends FormRequest
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
            'jambNo' => 'required|string|size:14',
            'subjectName' => 'required|array|size:4',
            'subjectName.*' => 'required|string',
            'jambScore' => 'required|array|size:4',
            'jambScore.*' => 'required|numeric|min:0|max:100',
        ];
    }
}
