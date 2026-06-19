<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Ai\Agents\AnalyseCandidatAgent;
use App\Enums\StatutCandidatureEnum;
use App\Http\Requests\ChatMessageRequest;
use App\Models\Candidate;
use App\Models\Offre;
use App\Services\ConversationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ChatController extends Controller
{
    public function __construct(
        private readonly ConversationService $conversationService,
    ) {}

    public function show(Request $request, Offre $offre, Candidate $candidate): View
    {
        $this->authorizeAccess($offre, $candidate);

        $conversation = $this->conversationService->resolve($candidate);
        $messages = $conversation->messages()->orderBy('created_at')->get();

        $compareId = null;
        $compareMessage = null;
        if ($request->has('compare')) {
            $compareId = (int) $request->input('compare');
            $compareCandidate = Candidate::where('id', $compareId)
                ->where('offre_id', $offre->id)
                ->where('status', StatutCandidatureEnum::Completed)
                ->first();

            if (! $compareCandidate || $compareCandidate->id === $candidate->id) {
                $compareId = null;
            } else {
                $compareMessage = sprintf(
                    'Compare le candidat %s (id: %d) avec le candidat %s (id: %d) sur la même offre.',
                    $candidate->name,
                    $candidate->id,
                    $compareCandidate->name,
                    $compareId,
                );
            }
        }

        return view('chat.show', compact('offre', 'candidate', 'conversation', 'messages', 'compareId', 'compareMessage'));
    }

    public function store(
        ChatMessageRequest $request,
        Offre $offre,
        Candidate $candidate,
    ): RedirectResponse {
        $this->authorizeAccess($offre, $candidate);

        $conversation = $this->conversationService->resolve($candidate);

        $conversation->messages()->create([
            'role' => 'user',
            'content' => $request->validated('message'),
        ]);

        $agent = new AnalyseCandidatAgent(
            conversation: $conversation,
            candidatureId: (int) $candidate->id,
            offreId: (int) $offre->id,
        );
        $response = $agent->prompt($request->validated('message'));
        $assistantText = trim((string) $response->text());

        if ($assistantText === '') {
            $assistantText = "Le modèle n'a pas renvoyé de réponse. Veuillez réessayer.";
        }

        $conversation->messages()->create([
            'role' => 'assistant',
            'content' => $assistantText,
        ]);

        return redirect()->route('chat.show', [$offre, $candidate])
            ->with('success', 'Réponse reçue.');
    }

    private function authorizeAccess(Offre $offre, Candidate $candidate): void
    {
        abort_if($offre->user_id !== auth()->id(), 403);
        abort_if($candidate->offre_id !== $offre->id, 403);
        abort_if(
            $candidate->status !== StatutCandidatureEnum::Completed,
            422,
            "L'analyse de cette candidature n'est pas encore terminée."
        );
    }
}
