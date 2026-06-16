<?php

declare(strict_types=1);

namespace App\Ai\Tools;

use App\Models\Candidature;

class GetCandidateAnalysisTool
{
    public function __invoke(int $candidatId): array|string
    {
        $candidature = Candidature::with('analyse', 'offre')
            ->where('id', $candidatId)
            ->where('user_id', auth()->id())
            ->first();

        if (! $candidature) {
            return 'Candidature introuvable ou accès non autorisé.';
        }

        if (! $candidature->analyse) {
            return 'L\'analyse de cette candidature n\'est pas encore disponible '
                 ."(statut : {$candidature->status->value}).";
        }

        $a = $candidature->analyse;

        return [
            'candidat' => $candidature->nom_candidat,
            'offre' => $candidature->offre->titre,
            'matching_score' => $a->matching_score,
            'recommandation' => $a->recommandation->value,
            'justification' => $a->justification,
            'points_forts' => $a->points_forts,
            'lacunes' => $a->lacunes,
            'competences_manquantes' => $a->competences_manquantes,
            'competences_extraites' => $a->competences_extraites,
            'annees_experience' => $a->annees_experience,
            'niveau_etudes' => $a->niveau_etudes,
            'langues' => $a->langues,
        ];
    }
}
