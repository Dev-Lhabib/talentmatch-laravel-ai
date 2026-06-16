<?php

declare(strict_types=1);

namespace App\Ai\Agents;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\HasStructuredOutput;
use Laravel\Ai\Promptable;

class AnalyseCandidatAgent implements Agent, HasStructuredOutput
{
    use Promptable;

    public function instructions(): string
    {
        return <<<'EOT'
        Tu es un expert RH spécialisé dans la présélection de candidats.
        Analyse le CV du candidat par rapport à l'offre d'emploi fournie.
        Évalue l'adéquation entre le profil du candidat et les exigences du poste.
        Retourne une analyse structurée avec un score de matching, les points forts,
        les lacunes, les compétences manquantes, et une recommandation.
        EOT;
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'competences_extraites' => $schema->array()
                ->items($schema->string())
                ->required(),
            'annees_experience' => $schema->integer()
                ->min(0)
                ->max(50)
                ->required(),
            'niveau_etudes' => $schema->string()
                ->enum(['bac', 'bac+2', 'bac+3', 'bac+4', 'bac+5', 'doctorat', 'non_specifie'])
                ->required(),
            'langues' => $schema->array()
                ->items($schema->string())
                ->required(),
            'matching_score' => $schema->integer()
                ->min(0)
                ->max(100)
                ->required(),
            'points_forts' => $schema->array()
                ->items($schema->string())
                ->required(),
            'lacunes' => $schema->array()
                ->items($schema->string())
                ->required(),
            'competences_manquantes' => $schema->array()
                ->items($schema->string())
                ->required(),
            'recommandation' => $schema->string()
                ->enum(['convoquer', 'attente', 'rejeter'])
                ->required(),
            'justification' => $schema->string()
                ->required(),
        ];
    }
}
