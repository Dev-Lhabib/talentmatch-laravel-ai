<?php

declare(strict_types=1);

namespace App\Ai\Tools;

use App\Models\Application;

class GetCandidateAnalysisTool
{
    public function __invoke(int $candidatId): array|string
    {
        $application = Application::with('candidate', 'analyse', 'offre')
            ->where('candidate_id', $candidatId)
            ->whereHas('offre', fn ($q) => $q->where('user_id', auth()->id()))
            ->whereHas('analyse')
            ->latest()
            ->first();

        if (! $application) {
            $latestApp = Application::with('offre')
                ->where('candidate_id', $candidatId)
                ->whereHas('offre', fn ($q) => $q->where('user_id', auth()->id()))
                ->latest()
                ->first();

            $status = $latestApp?->status?->value ?? 'inconnu';

            return 'L"analyse de ce candidat n"est pas encore disponible '
                 ."(statut : {$status}).";
        }

        $candidate = $application->candidate;
        $a = $application->analyse;

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
