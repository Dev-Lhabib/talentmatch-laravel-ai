<?php

declare(strict_types=1);

namespace App\Ai\Tools;

use App\Models\Candidature;

class CompareCandidatesTool
{
    public function __invoke(int $id1, int $id2): array|string
    {
        $candidatures = Candidature::with('analyse', 'offre')
            ->whereIn('id', [$id1, $id2])
            ->where('user_id', auth()->id())
            ->get();

        if ($candidatures->count() !== 2) {
            return 'Une ou plusieurs candidatures sont introuvables ou non autorisées.';
        }

        $c1 = $candidatures->firstWhere('id', $id1);
        $c2 = $candidatures->firstWhere('id', $id2);

        if ($c1->offre_id !== $c2->offre_id) {
            return 'Les deux candidatures doivent appartenir à la même offre d\'emploi.';
        }

        if (! $c1->analyse || ! $c2->analyse) {
            return 'L\'une des deux analyses n\'est pas encore disponible.';
        }

        return [
            'offre' => $c1->offre->titre,
            'candidat_1' => $this->formatAnalyse($c1),
            'candidat_2' => $this->formatAnalyse($c2),
        ];
    }

    private function formatAnalyse(Candidature $c): array
    {
        $a = $c->analyse;

        return [
            'id' => $c->id,
            'nom' => $c->nom_candidat,
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
