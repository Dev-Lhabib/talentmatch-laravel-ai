<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\StatutCandidatureEnum;
use App\Models\Candidature;
use App\Services\ConversationService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        private readonly ConversationService $conversationService,
    ) {}

    public function candidates(): View
    {
        $candidature = Candidature::where('status', StatutCandidatureEnum::Completed)
            ->with(['analyse', 'offre'])
            ->orderedByScore()
            ->first();

        $conversation = null;
        $messages = collect();

        if ($candidature) {
            $conversation = $this->conversationService->resolve($candidature);
            $messages = $conversation->messages()->orderBy('created_at')->get();
        }

        return view('dashboard.candidates', compact('candidature', 'conversation', 'messages'));
    }
}
