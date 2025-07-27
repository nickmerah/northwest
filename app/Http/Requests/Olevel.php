<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class Olevel extends FormRequest
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
            // First Sitting (required)
            'first.examName' => ['required', 'string', Rule::in(['NECO', 'WAEC/WASCE', 'WAEC', 'NABTEB'])],
            'first.examNo' => 'required|string',
            'first.centerNo' => 'required|string',
            'first.examMonth' => 'required|string',
            'first.examYear' => 'required|digits:4',
            'first.sitting' => 'required|in:First,Second',
            'first.subjectName' => 'required|array|min:1',
            'first.subjectName.*' => 'required|string',
            'first.grade' => 'required|array|min:1',
            'first.grade.*' => 'required|string|in:A1,AR,B2,B3,C4,C5,C6,D7,D8,F9,ABS,A,B,C,D,E,F,E8,P',

            // Second Sitting (optional with sometimes)
            'second.examName' => ['sometimes', 'required', 'string', Rule::in(['NECO', 'WAEC/WASCE', 'WAEC', 'NABTEB'])],
            'second.examNo' => 'sometimes|required|string',
            'second.centerNo' => 'sometimes|required|string',
            'second.examMonth' => 'sometimes|required|string',
            'second.examYear' => 'sometimes|required|digits:4',
            'second.sitting' => 'sometimes|required|in:First,Second',
            'second.subjectName' => 'sometimes|required|array|min:1',
            'second.subjectName.*' => 'required_with:second.subject|string',
            'second.grade' => 'sometimes|required|array|min:1',
            'second.grade.*' => 'required_with:second.grade|string|in:A1,AR,B2,B3,C4,C5,C6,D7,D8,F9,ABS,A,B,C,D,E,F,E8,P',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $firstSubjects = $this->input('first.subjectName', []);
            $firstGrades = $this->input('first.grade', []);

            if (count($firstSubjects) !== count($firstGrades)) {
                $validator->errors()->add(
                    'first.grade',
                    'The number of grades must match the number of subjects for first sitting.'
                );
            }

            if ($this->has('second.subjectName') && $this->has('second.grade')) {
                $secondSubjects = $this->input('second.subjectName', []);
                $secondGrades = $this->input('second.grade', []);

                if (count($secondSubjects) !== count($secondGrades)) {
                    $validator->errors()->add(
                        'second.grade',
                        'The number of grades must match the number of subjects for second sitting.'
                    );
                }
            }
        });
    }
}
