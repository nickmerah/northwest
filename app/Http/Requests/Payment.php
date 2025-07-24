<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class Payment extends FormRequest
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
            'gateway' => ['required', Rule::in(config('payment.gateways'))],
            'feeType' => ['required', 'integer', Rule::in(config('payment.fee_types'))],
            'redirectUrl' => ['required', 'url', 'starts_with:https://'],
        ];
    }
}
