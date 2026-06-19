<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\StatutCandidatureEnum;
use App\Models\Candidate;
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
        $candidates = Candidate::where('status', StatutCandidatureEnum::Completed)
            ->with(['analyse', 'offre'])
            ->orderedByScore()
            ->get();

        $selectedCandidate = null;
        $conversation = null;
        $messages = collect();

        if ($candidates->isNotEmpty()) {
            $selectedId = $request->query('candidate');
            $selectedCandidate = $selectedId
                ? $candidates->firstWhere('id', (int) $selectedId)
                : $candidates->first();

            $selectedCandidate = $selectedCandidate ?? $candidates->first();
            $conversation = $this->conversationService->resolve($selectedCandidate);
            $messages = $conversation->messages()->orderBy('created_at')->get();
        }

        return view('dashboard.candidates', compact('selectedCandidate', 'candidates', 'conversation', 'messages'));
    }
}
