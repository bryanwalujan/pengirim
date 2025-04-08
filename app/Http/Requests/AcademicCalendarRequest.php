<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AcademicCalendarRequest extends FormRequest
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
        $rules = [
            'title' => 'required|string|max:255',
            'academic_year' => 'required|string|max:255',
            'is_active' => 'boolean',
        ];

        if ($this->isMethod('post')) {
            $rules['file'] = 'required|mimes:pdf|max:2048';
        } else {
            $rules['file'] = 'sometimes|mimes:pdf|max:2048';
        }

        return $rules;
    }
}
