<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class validationDecadeRequest extends FormRequest
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
            'debutDecade' => ['required', 'date'],
            'finDecade'   => ['required', 'date', 'after_or_equal:debutDecade'],
        ];
    }

    public function messages(): array 
    {
        return [
            'debutDecade.required' => 'Veuillez renseigner la date du début.',
            'debutDecade.date' => 'La date de début est invalide.',

            'finDecade.required' => 'Veuillez renseigner la date de fin.',
            'finDecade.date' => 'La date de fin est invalide.',
            'finDecade.after_or_equal' => 'La date de fin doit être supérieure ou égale à la date de début.',
        ];
    }
}
