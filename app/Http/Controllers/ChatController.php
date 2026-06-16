<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Ai\Agents\AnalyseCandidatAgent;
use App\Enums\StatutCandidatureEnum;
use App\Http\Requests\ChatMessageRequest;
use App\Models\Candidature;
use App\Models\Offre;
use App\Services\ConversationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ChatController extends Controller
{
    public function __construct(
        private readonly ConversationService $conversationService,
    ) {}

    public function show(Offre $offre, Candidature $candidature): View
    {
        $this->authorizeAccess($offre, $candidature);

        $conversation = $this->conversationService->resolve($candidature);
        $messages = $conversation->messages()->orderBy('created_at')->get();

        return view('chat.show', compact('offre', 'candidature', 'conversation', 'messages'));
    }

    public function store(
        ChatMessageRequest $request,
        Offre $offre,
        Candidature $candidature,
    ): RedirectResponse {
        $this->authorizeAccess($offre, $candidature);

        $conversation = $this->conversationService->resolve($candidature);

        $conversation->messages()->create([
            'role' => 'user',
            'content' => $request->validated('message'),
        ]);

        $agent = new AnalyseCandidatAgent(conversation: $conversation);
        $response = $agent->prompt($request->validated('message'));

        $conversation->messages()->create([
            'role' => 'assistant',
            'content' => $response->text(),
        ]);

        return redirect()->route('chat.show', [$offre, $candidature])
            ->with('success', 'Réponse reçue.');
    }

    private function authorizeAccess(Offre $offre, Candidature $candidature): void
    {
        abort_if($offre->user_id !== auth()->id(), 403);
        abort_if($candidature->offre_id !== $offre->id, 403);
        abort_if(
            $candidature->status !== StatutCandidatureEnum::Completed,
            422,
            "L'analyse de cette candidature n'est pas encore terminée."
        );
    }
}
