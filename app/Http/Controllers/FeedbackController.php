<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreFeedbackRequest;
use App\Models\Candidate;
use App\Models\Feedback;
use App\Models\Offre;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class FeedbackController extends Controller
{
    public function show(): View
    {
        return view('feedback.show', [
            'offres' => Offre::where('user_id', auth()->id())->latest()->get(),
            'candidates' => Candidate::orderBy('name')->get(),
        ]);
    }

    public function store(StoreFeedbackRequest $request): RedirectResponse
    {
        Feedback::create([
            'user_id' => auth()->id(),
            'type' => $request->validated('type'),
            'offre_id' => $request->validated('offre_id'),
            'candidate_id' => $request->validated('candidate_id'),
            'sujet' => $request->validated('sujet'),
            'message' => $request->validated('message'),
            'priorite' => $request->validated('priorite', 'medium'),
        ]);

        return redirect()->route('feedback.show')->with('success', 'Merci pour votre retour !');
    }
}
