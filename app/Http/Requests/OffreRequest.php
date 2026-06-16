<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OffreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'titre' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'min:20'],
            'competences' => ['nullable', 'array'],
            'competences.*' => ['string', 'max:100'],
            'experience_min' => ['nullable', 'integer', 'min:0', 'max:50'],
        ];
    }
}
