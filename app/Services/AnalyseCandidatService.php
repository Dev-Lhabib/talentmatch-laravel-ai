<?php

declare(strict_types=1);

namespace App\Services;

use App\Ai\Agents\AnalyseCandidatAgent;
use App\Models\Candidature;
use Illuminate\Support\Collection;

class AnalyseCandidatService
{
    public function __construct(
        private readonly AnalyseCandidatAgent $agent,
    ) {}

    public function analyser(Candidature $candidature): Collection
    {
        $offre = $candidature->offre;

        $prompt = $this->buildPrompt($offre, $candidature);

        $response = $this->agent->prompt($prompt);

        return collect([
            'competences_extraites' => $response['competences_extraites'],
            'annees_experience' => $response['annees_experience'],
            'niveau_etudes' => $response['niveau_etudes'],
            'langues' => $response['langues'],
            'matching_score' => $response['matching_score'],
            'points_forts' => $response['points_forts'],
            'lacunes' => $response['lacunes'],
            'competences_manquantes' => $response['competences_manquantes'],
            'recommandation' => $response['recommandation'],
            'justification' => $response['justification'],
        ]);
    }

    private function buildPrompt($offre, Candidature $candidature): string
    {
        $competences = $this->formatCompetences($offre->competences);

        return <<<EOT
        ## Offre d'emploi
        Titre : {$offre->titre}
        Description : {$offre->description}
        Compétences requises : {$competences}
        Expérience minimum : {$offre->experience_min} an(s)

        ## CV du candidat
        Nom : {$candidature->nom_candidat}
        {$candidature->cv_text}

        Retourne une analyse structurée conforme au schéma fourni.
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
