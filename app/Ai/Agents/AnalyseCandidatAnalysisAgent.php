<?php

declare(strict_types=1);

namespace App\Ai\Agents;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\HasProviderOptions;
use Laravel\Ai\Contracts\HasStructuredOutput;
use Laravel\Ai\Enums\Lab;
use Laravel\Ai\Promptable;

class AnalyseCandidatAnalysisAgent implements Agent, HasProviderOptions, HasStructuredOutput
{
    use Promptable;

    public function provider(): Lab
    {
        return Lab::from(config('ai.default', 'groq'));
    }

    public function model(): ?string
    {
        return config('ai.default_model');
    }

    public function providerOptions($provider): array
    {
        $providerName = $provider instanceof Lab ? $provider->value : (string) $provider;

        if ($providerName === Lab::Groq->value) {
            return [
                'response_format' => [
                    'type' => 'json_object',
                ],
            ];
        }

        return [];
    }

    public function instructions(): string
    {
        return <<<'EOT'
        Tu es un expert RH spécialisé dans la présélection de candidats.

        Analyse le CV du candidat par rapport à l'offre d'emploi fournie.
        Évalue l'adéquation entre le profil du candidat et les exigences du poste.
        
        IMPORTANT : Pour le champ 'recommandation', tu DOIS utiliser EXACTEMENT l'une de ces trois valeurs :
        - "convoquer" : si le candidat est fortement recommandé
        - "attente" : si le candidat pourrait être viable mais nécessite plus d'investigation
        - "rejeter" : si le candidat ne convient pas
        
        Retourne UNIQUEMENT une analyse structurée conforme au schéma JSON fourni.
        N'utilise aucun outil externe. Ne pose pas de questions. Ne retourne que le JSON avec les bonnes valeurs d'enum.
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
