<?php

declare(strict_types=1);

namespace App\Services;

use App\Ai\Agents\AnalyseCandidatAnalysisAgent;
use App\Models\Candidate;
use Illuminate\Support\Collection;

class AnalyseCandidatService
{
    public function __construct(
        private readonly AnalyseCandidatAnalysisAgent $agent,
    ) {}

    public function analyser(Candidate $candidate): Collection
    {
        $offre = $candidate->offre;

        $prompt = $this->buildPrompt($offre, $candidate);

        $response = $this->agent->prompt($prompt);

        return collect([
            'competences_extraites' => $response['competences_extraites'] ?? [],
            'annees_experience' => $response['annees_experience'] ?? 0,
            'niveau_etudes' => $response['niveau_etudes'] ?? 'non_specifie',
            'langues' => $response['langues'] ?? [],
            'matching_score' => $response['matching_score'] ?? 0,
            'points_forts' => $response['points_forts'] ?? [],
            'lacunes' => $response['lacunes'] ?? [],
            'competences_manquantes' => $response['competences_manquantes'] ?? [],
            'recommandation' => $response['recommandation'] ?? 'attente',
            'justification' => $response['justification'] ?? 'Analyse non disponible.',
        ]);
    }

    private function buildPrompt($offre, Candidate $candidate): string
    {
        $competences = $this->formatCompetences($offre->competences);

        return <<<EOT
        ## Offre d'emploi
        Titre : {$offre->titre}
        Description : {$offre->description}
        Compétences requises : {$competences}
        Expérience minimum : {$offre->experience_min} an(s)

        ## CV du candidat
        Nom : {$candidate->name}
        {$candidate->cv_text}

        Analyse ce CV par rapport à l'offre et retourne UNIQUEMENT le JSON structuré conforme au schéma. N'utilise aucun outil.
        EOT;
    }

    private function formatCompetences($competences): string
    {
        if ($competences instanceof \Illuminate\Database\Eloquent\Collection) {
            $competences = $competences->pluck('nom')->toArray();
        }

        if (empty($competences)) {
            return 'Non spécifiées — base ton évaluation sur la description du poste et '
                 ."l'expérience générale du candidat.";
        }

        return implode(', ', $competences);
    }
}
