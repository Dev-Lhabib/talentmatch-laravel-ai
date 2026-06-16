<?php

declare(strict_types=1);

namespace App\Ai\Tools;

use App\Models\Offre;

class GetJobRequirementsTool
{
    public function __invoke(int $offreId): array|string
    {
        $offre = Offre::with('competences')
            ->where('id', $offreId)
            ->where('user_id', auth()->id())
            ->first();

        if (! $offre) {
            return 'Offre introuvable ou accès non autorisé.';
        }

        return [
            'titre' => $offre->titre,
            'description' => $offre->description,
            'competences_requises' => $offre->competences->pluck('nom')->toArray(),
            'niveau_experience_min' => $offre->experience_min,
        ];
    }
}
