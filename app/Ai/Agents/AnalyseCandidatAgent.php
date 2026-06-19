<?php

declare(strict_types=1);

namespace App\Ai\Agents;

use App\Ai\Tools\CompareCandidatesTool;
use App\Ai\Tools\GetCandidateAnalysisTool;
use App\Ai\Tools\GetJobRequirementsTool;
use App\Models\Conversation;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\Conversational;
use Laravel\Ai\Contracts\HasTools;
use Laravel\Ai\Enums\Lab;
use Laravel\Ai\Messages\Message;
use Laravel\Ai\Promptable;

class AnalyseCandidatAgent implements Agent, Conversational, HasTools
{
    use Promptable;

    public function __construct(
        public ?Conversation $conversation = null,
        public ?int $candidatureId = null,
        public ?int $offreId = null,
    ) {}

    public function provider(): Lab
    {
        return Lab::from(config('ai.default', 'groq'));
    }

    public function model(): ?string
    {
        return config('ai.default_model');
    }

    public function instructions(): string
    {
        $context = '';
        if ($this->candidatureId) {
            $context .= "\n\nContexte actuel :\n- ID du candidat discuté : {$this->candidatureId}";
        }
        if ($this->offreId) {
            $context .= "\n- ID de l'offre associée : {$this->offreId}";
        }

        return <<<EOT
        Tu es un expert RH spécialisé dans la présélection de candidats.

        Règle essentielle : Tu DOIS appeler le tool correspondant avant de répondre
        à toute question factuelle sur un candidat, une offre, ou une comparaison.
        Une réponse factuelle sans appel de tool = hallucination = comportement interdit.

        Pour les questions sur un candidat : appelle getCandidateAnalysis avec son ID.
        Pour les questions sur une offre : appelle getJobRequirements avec son ID.
        Pour comparer deux candidats : appelle compareCandidates(id1, id2).
        {$context}
        EOT;
    }

    public function tools(): iterable
    {
        return [
            new GetCandidateAnalysisTool,
            new GetJobRequirementsTool,
            new CompareCandidatesTool,
        ];
    }

    public function messages(): iterable
    {
        if (! $this->conversation) {
            return [];
        }

        return $this->conversation->messages()
            ->latest()
            ->limit(20)
            ->get()
            ->reverse()
            ->map(fn ($m) => new Message($m->role->value, $m->content))
            ->all();
    }
}
