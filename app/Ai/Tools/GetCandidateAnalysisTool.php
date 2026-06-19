<?php

declare(strict_types=1);

namespace App\Ai\Tools;

use App\Models\Candidate;

class GetCandidateAnalysisTool
{
    public function __invoke(int $candidatId): array|string
    {
        $candidate = Candidate::with('applications.analyse', 'applications.offre')
            ->where('id', $candidatId)
            ->where('user_id', auth()->id())
            ->first();

        if (! $candidate) {
            return 'Candidat introuvable ou accès non autorisé.';
        }

        $application = $candidate->applications()
            ->with('analyse', 'offre')
            ->whereHas('analyse')
            ->latest()
            ->first();

        if (! $application) {
            $latestApp = $candidate->applications()->latest()->first();
            $status = $latestApp?->status?->value ?? 'inconnu';

            return 'L"analyse de ce candidat n"est pas encore disponible '
                 ."(statut : {$status}).";
        }

        $a = $application->analyse;

        return [
            'candidat' => $candidate->name,
            'offre' => $application->offre->titre,
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
