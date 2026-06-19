<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\StatutCandidatureEnum;
use App\Models\Candidature;
use App\Services\ConversationService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        private readonly ConversationService $conversationService,
    ) {}

    public function candidates(Request $request): View
    {
        $candidatures = Candidature::where('status', StatutCandidatureEnum::Completed)
            ->with(['analyse', 'offre'])
            ->orderedByScore()
            ->get();

        $candidature = null;
        $conversation = null;
        $messages = collect();

        if ($candidatures->isNotEmpty()) {
            $selectedId = $request->query('candidate');
            $candidature = $selectedId
                ? $candidatures->firstWhere('id', (int) $selectedId)
                : $candidatures->first();

            $candidature = $candidature ?? $candidatures->first();
            $conversation = $this->conversationService->resolve($candidature);
            $messages = $conversation->messages()->orderBy('created_at')->get();
        }

        return view('dashboard.candidates', compact('candidature', 'candidatures', 'conversation', 'messages'));
    }
}
