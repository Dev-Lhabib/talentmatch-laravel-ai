<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFeedbackRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => 'required|string|in:bug,suggestion,analyse,autre',
            'offre_id' => 'nullable|exists:offres,id',
            'candidate_id' => 'nullable|exists:candidates,id',
            'sujet' => 'required|string|max:255',
            'message' => 'required|string|min:20|max:5000',
            'priorite' => 'nullable|string|in:low,medium,high',
        ];
    }

    public function attributes(): array
    {
        return [
            'type' => 'type de retour',
            'sujet' => 'sujet',
            'message' => 'message',
            'priorite' => 'priorité',
        ];
    }
}
