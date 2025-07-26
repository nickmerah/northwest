<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use App\Rules\LgaBelongsToState;
use Illuminate\Foundation\Http\FormRequest;

class Profile extends FormRequest
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
        $stateId = (int) $this->input('stateofOrigin');

        return [
            'othernames' => 'nullable|string|max:255',
            'gender' => ['required', 'string', Rule::in(['Male', 'Female'])],
            'maritalStatus' => 'required|string|max:255',
            'birthDate' => 'required|date',
            'contactAddress' => 'required|string|max:255',
            'studentHomeAddress' => 'required|string|max:255',
            'homeTown' => 'required|string|max:150',
            'stateofOrigin' => [
                'required',
                'integer',
                Rule::exists('state', 'state_id'),
            ],
            'lga' => [
                'required',
                'integer',
                Rule::exists('lga', 'lga_id'),
                new LgaBelongsToState($stateId),
            ],
            'nextofKin' => 'required|string|max:255',
            'nextofKinAddress' => 'required|string|max:255',
            'nextofKinEmail' => 'required|email|max:150',
            'nextofKinPhoneNo' => 'required|digits_between:11,13',
            'nextofKinRelationship' => 'required|string|max:50',
            'updateWithPassport' => ['required', Rule::in(['true', 'false'])],
            'profilePicture' => [
                'sometimes',
                function ($attribute, $value, $fail) {
                    if ($this->input('updateWithPassport') === 'true') {
                        if (!$value || !$this->hasFile('profilePicture')) {
                            return $fail('The profile picture is required when updateWithPassport is true.');
                        }

                        if (!$value->isValid() || !in_array($value->extension(), ['jpeg', 'jpg'])) {
                            return $fail('The profile picture must be a valid JPEG image.');
                        }

                        if ($value->getSize() > 100 * 1024) {
                            return $fail('The profile picture must not exceed 100KB.');
                        }
                    }
                },
            ],
        ];
    }
}
