<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\StatutCandidatureEnum;
use App\Models\Candidature;
use App\Models\Conversation;
use Illuminate\Support\Collection;

class ConversationService
{
    public function resolve(Candidature $candidature): Conversation
    {
        if ($candidature->status !== StatutCandidatureEnum::Completed) {
            throw new \RuntimeException(
                'L\'analyse doit être terminée pour démarrer une conversation.'
            );
        }

        return Conversation::firstOrCreate(
            ['candidature_id' => $candidature->id],
            [
                'user_id' => auth()->id(),
                'title' => "Discussion sur {$candidature->nom_candidat}",
            ]
        );
    }

    public function getMessages(Conversation $conversation, int $limit = 20): Collection
    {
        return $conversation->messages()
            ->latest()
            ->limit($limit)
            ->get()
            ->reverse()
            ->values();
    }
}
