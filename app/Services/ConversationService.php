<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\StatutCandidatureEnum;
use App\Models\Application;
use App\Models\Conversation;
use Illuminate\Support\Collection;

class ConversationService
{
    public function resolve(Application $application): Conversation
    {
        if ($application->status !== StatutCandidatureEnum::Completed) {
            throw new \RuntimeException(
                'L"analyse doit être terminée pour démarrer une conversation.'
            );
        }

        return Conversation::firstOrCreate(
            ['application_id' => $application->id],
            [
                'user_id' => auth()->id(),
                'title' => "Discussion sur {$application->candidate->name}",
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
