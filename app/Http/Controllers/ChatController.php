<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Ai\Agents\AnalyseCandidatAgent;
use App\Enums\StatutCandidatureEnum;
use App\Http\Requests\ChatMessageRequest;
use App\Models\Application;
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

    public function show(Request $request, Offre $offre, Application $application): View
    {
        $this->authorizeAccess($offre, $application);

        $conversation = $this->conversationService->resolve($application);
        $messages = $conversation->messages()->orderBy('created_at')->get();

        $compareId = null;
        $compareMessage = null;
        if ($request->has('compare')) {
            $compareId = (int) $request->input('compare');
            $compareApplication = Application::where('id', $compareId)
                ->where('offre_id', $offre->id)
                ->where('status', StatutCandidatureEnum::Completed)
                ->first();

            if (! $compareApplication || $compareApplication->id === $application->id) {
                $compareId = null;
            } else {
                $compareMessage = sprintf(
                    'Compare le candidat %s (app: %d) avec le candidat %s (app: %d) sur la même offre.',
                    $application->candidate->name,
                    $application->id,
                    $compareApplication->candidate->name,
                    $compareId,
                );
            }
        }

        return view('chat.show', compact('offre', 'application', 'conversation', 'messages', 'compareId', 'compareMessage'));
    }

    public function store(
        ChatMessageRequest $request,
        Offre $offre,
        Application $application,
    ): RedirectResponse {
        $this->authorizeAccess($offre, $application);

        $conversation = $this->conversationService->resolve($application);

        $conversation->messages()->create([
            'role' => 'user',
            'content' => $request->validated('message'),
        ]);

        $agent = new AnalyseCandidatAgent(
            conversation: $conversation,
            candidatureId: (int) $application->id,
            offreId: (int) $offre->id,
        );
        $response = $agent->prompt($request->validated('message'));
        $assistantText = trim((string) $response->text());

        if ($assistantText === '') {
            $assistantText = "Le modèle n\u2019a pas renvoyé de réponse. Veuillez réessayer.";
        }

        $conversation->messages()->create([
            'role' => 'assistant',
            'content' => $assistantText,
        ]);

        return redirect()->route('chat.show', [$offre, $application])
            ->with('success', 'Réponse reçue.');
    }

    private function authorizeAccess(Offre $offre, Application $application): void
    {
        abort_if($offre->user_id !== auth()->id(), 403);
        abort_if($application->offre_id !== $offre->id, 403);
        abort_if(
            $application->status !== StatutCandidatureEnum::Completed,
            422,
            "L\u2019analyse de cette candidature n\u2019est pas encore terminée."
        );
    }
}
