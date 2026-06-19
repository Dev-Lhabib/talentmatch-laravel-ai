<?php

declare(strict_types=1);

namespace App\Ai\Tools;

use App\Models\Application;

class CompareCandidatesTool
{
    public function __invoke(int $id1, int $id2): array|string
    {
        $apps = Application::with('candidate', 'analyse', 'offre')
            ->whereIn('id', [$id1, $id2])
            ->whereHas('analyse')
            ->whereHas('offre', fn ($q) => $q->where('user_id', auth()->id()))
            ->get();

        $app1 = $apps->firstWhere('id', $id1);
        $app2 = $apps->firstWhere('id', $id2);

        if (! $app1 || ! $app2) {
            return 'Un ou plusieurs candidats sont introuvables ou non autorisés.';
        }

        if ($app1->offre_id !== $app2->offre_id) {
            return 'Les deux candidats doivent appartenir à la même offre d"emploi.';
        }

        return [
            'offre' => $app1->offre->titre,
            'candidat_1' => $this->formatAnalyse($app1),
            'candidat_2' => $this->formatAnalyse($app2),
        ];
    }

    private function formatAnalyse(Application $app): array
    {
        $a = $app->analyse;

        return [
            'id' => $app->candidate->id,
            'nom' => $app->candidate->name,
            'matching_score' => $a->matching_score,
            'recommandation' => $a->recommandation->value,
            'points_forts' => $a->points_forts,
            'lacunes' => $a->lacunes,
            'competences_manquantes' => $a->competences_manquantes,
            'annees_experience' => $a->annees_experience,
            'justification' => $a->justification,
        ];
    }
}
