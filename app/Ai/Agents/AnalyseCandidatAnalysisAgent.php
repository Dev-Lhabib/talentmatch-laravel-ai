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

        Tu DOIS retourner UNIQUEMENT un objet JSON avec EXACTEMENT ces clés (ni plus, ni moins) :

        {
            "competences_extraites": ["PHP", "Laravel", ...],
            "annees_experience": 5,
            "niveau_etudes": "bac+5",
            "langues": ["Français", "Anglais"],
            "matching_score": 85,
            "points_forts": ["Maîtrise Laravel", ...],
            "lacunes": ["Pas d'expérience Docker", ...],
            "competences_manquantes": ["Redis", ...],
            "recommandation": "convoquer",
            "justification": "Le candidat correspond parfaitement au profil..."
        }

        Règles par champ :
        - competences_extraites : tableau de strings (liste des compétences du candidat)
        - annees_experience : entier (nombre d'années, entre 0 et 50)
        - niveau_etudes : UNE SEULE valeur parmi "bac", "bac+2", "bac+3", "bac+4", "bac+5", "doctorat", "non_specifie"
        - langues : tableau de strings
        - matching_score : entier entre 0 et 100
        - points_forts : tableau de strings
        - lacunes : tableau de strings
        - competences_manquantes : tableau de strings
        - recommandation : UNE SEULE valeur parmi "convoquer" (fortement recommandé), "attente" (viable mais à vérifier), "rejeter" (ne convient pas)
        - justification : string expliquant la décision

        IMPORTANT : Ne retourne RIEN d'autre que le JSON ci-dessus, sans texte avant ni après.
        N'utilise aucun outil externe.
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
