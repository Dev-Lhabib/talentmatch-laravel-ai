<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Jobs\AnalyseCandidatJob;
use App\Models\Application;
use App\Models\Candidate;
use App\Models\Offre;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CandidatureController extends Controller
{
    public function store(Offre $offre): RedirectResponse
    {
        $candidateId = (int) request('candidate_id');
        $candidate = Candidate::findOrFail($candidateId);

        $existing = Application::where('candidate_id', $candidate->id)
            ->where('offre_id', $offre->id)
            ->exists();

        if ($existing) {
            return redirect()->route('offres.show', $offre)
                ->with('error', 'Ce candidat est déjà lié à cette offre.');
        }

        $application = Application::create([
            'candidate_id' => $candidate->id,
            'offre_id' => $offre->id,
            'cv_text' => $candidate->cv_text,
            'status' => 'pending',
        ]);

        AnalyseCandidatJob::dispatch($application);

        return redirect()->route('offres.show', $offre)
            ->with('success', "Analyse en cours\u2026");
    }

    public function show(Offre $offre, Application $application): View
    {
        $application->load(['candidate', 'analyse']);

        return view('candidatures.show', compact('offre', 'application'));
    }

    public function destroy(Offre $offre, Application $application): RedirectResponse
    {
        $application->delete();

        return redirect()->route('offres.show', $offre);
    }
}
