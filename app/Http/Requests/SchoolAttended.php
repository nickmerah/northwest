<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SchoolAttended extends FormRequest
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
            'schoolName' => 'required|string|max:255',
            'ndMatno' => 'required|string|max:50',
            'courseofstudy' => 'required|string|max:255',
            'grade' => 'required|string|max:25', //certObtained
            'fromDate' => 'required|digits:4',
            'toDate' => 'required|digits:4',
        ];
    }
}
