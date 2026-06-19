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
            ->whereHas('offre', fn ($q) => $q->where('user_id', auth()->id()))
            ->with(['candidate', 'analyse', 'offre'])
            ->whereHas('analyse')
            ->get()
            ->sortByDesc(fn (Application $app) => $app->analyse?->matching_score ?? 0);

        $offers = $applications->pluck('offre')->unique('id')->sortBy('titre');

        $selectedApp = null;
        $conversation = null;
        $messages = collect();

        if ($applications->isNotEmpty()) {
            $selectedOffreId = (int) $request->integer('offre');
            $selectedCandidateId = (int) $request->integer('candidate');

            $offerApps = $selectedOffreId
                ? $applications->where('offre_id', $selectedOffreId)
                : collect();

            if ($selectedOffreId && $offerApps->isNotEmpty()) {
                $selectedApp = $selectedCandidateId
                    ? $offerApps->firstWhere('candidate_id', $selectedCandidateId)
                    : null;

                $selectedApp ??= $offerApps->first();
            }

            $selectedApp ??= $applications->first();

            if ($selectedApp) {
                $conversation = $this->conversationService->resolve($selectedApp);
                $messages = $conversation->messages()->orderBy('created_at')->get();
            }
        }

        return view('dashboard.candidates', compact('selectedApp', 'applications', 'offers', 'conversation', 'messages'));
    }
}
