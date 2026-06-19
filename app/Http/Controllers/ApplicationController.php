<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Ai\Agents\AnalyseCandidatAgent;
use App\Enums\StatutCandidatureEnum;
use App\Http\Requests\ChatMessageRequest;
use App\Jobs\AnalyseCandidatJob;
use App\Models\Application;
use App\Services\ConversationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ApplicationController extends Controller
{
    public function __construct(
        private readonly ConversationService $conversationService,
    ) {}

    public function show(Application $application): View
    {
        $application->load(['candidate', 'offre', 'analyse']);

        $candidateApplications = collect();

        if ($application->candidate) {
            $candidateApplications = Application::where('candidate_id', $application->candidate_id)
                ->where('status', StatutCandidatureEnum::Completed)
                ->with(['offre', 'analyse'])
                ->get();
        }

        $candidates = collect();

        if ($application->offre) {
            $candidates = Application::where('offre_id', $application->offre_id)
                ->where('status', StatutCandidatureEnum::Completed)
                ->with('candidate')
                ->get()
                ->pluck('candidate');
        }

        $conversation = null;
        $messages = collect();

        if ($application->status === StatutCandidatureEnum::Completed && $application->analyse) {
            try {
                $conversation = $this->conversationService->resolve($application);
                $messages = $this->conversationService->getMessages($conversation);
            } catch (\RuntimeException) {
            }
        }

        return view('applications.show', compact('application', 'conversation', 'messages', 'candidateApplications', 'candidates'));
    }

    public function retry(Application $application): RedirectResponse
    {
        $application->analyse?->delete();
        $application->update(['status' => StatutCandidatureEnum::Pending]);

        AnalyseCandidatJob::dispatch($application);

        return redirect()->back()->with('success', "Analyse relancée\u2026");
    }

    public function jsonChat(ChatMessageRequest $request, Application $application): JsonResponse
    {
        $application->load(['candidate', 'offre', 'analyse']);

        if ($application->status !== StatutCandidatureEnum::Completed) {
            return response()->json(['error' => "L\u2019analyse n\u2019est pas encore termin\u00e9e."], 422);
        }

        $conversation = $this->conversationService->resolve($application);

        $conversation->messages()->create([
            'role' => 'user',
            'content' => $request->validated('message'),
        ]);

        try {
            $agent = new AnalyseCandidatAgent(
                conversation: $conversation,
                candidatureId: (int) $application->id,
                offreId: (int) $application->offre_id,
            );

            $response = $agent->prompt($request->validated('message'));
            $assistantText = trim($response->text);

            if ($assistantText === '') {
                $assistantText = "Le modèle n\u2019a pas renvoyé de réponse. Veuillez réessayer.";
            }
        } catch (\Throwable $e) {
            return response()->json(['error' => "L\u2019assistant IA est temporairement indisponible. Veuillez réessayer plus tard."], 503);
        }

        $message = $conversation->messages()->create([
            'role' => 'assistant',
            'content' => $assistantText,
        ]);

        return response()->json([
            'role' => 'assistant',
            'content' => $assistantText,
            'created_at' => $message->created_at->diffForHumans(),
        ]);
    }
}
