<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AccountRegister extends FormRequest
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
            'surname' => 'required|string|min:3|max:255',
            'firstname' => 'required|string|min:3|max:255',
            'othernames' => 'nullable|string|max:255',
            'progtype' => 'required|integer',
            'prog' => 'required|integer',
            'cos_id' => 'required|integer',
            'cos_id_two' => 'required|integer',
            'password' => 'required|alpha_num|min:4|max:255',
            'email' => 'required|string|email|max:150|unique:jlogin,log_email',
            'phoneno' => 'required|alpha_num|min:5|max:150',
        ];
    }
}
