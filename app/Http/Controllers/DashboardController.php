<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\StatutCandidatureEnum;
use App\Models\Application;
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
        $applications = Application::where('status', StatutCandidatureEnum::Completed)
            ->with(['candidate', 'analyse', 'offre'])
            ->whereHas('analyse')
            ->get()
            ->sortByDesc(fn (Application $app) => $app->analyse?->matching_score ?? 0);

        $selectedApp = null;
        $conversation = null;
        $messages = collect();

        if ($applications->isNotEmpty()) {
            $selectedId = $request->query('candidate');
            $selectedApp = $selectedId
                ? $applications->firstWhere('candidate_id', (int) $selectedId)
                : $applications->first();

            $selectedApp = $selectedApp ?? $applications->first();
            $conversation = $this->conversationService->resolve($selectedApp);
            $messages = $conversation->messages()->orderBy('created_at')->get();
        }

        return view('dashboard.candidates', compact('selectedApp', 'applications', 'conversation', 'messages'));
    }
}
