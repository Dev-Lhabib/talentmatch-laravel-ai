<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SoumettreCandidatureRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nom_candidat' => ['required', 'string', 'max:255'],
            'cv_text' => ['required', 'string', 'min:50', 'max:50000'],
        ];
    }
}
