<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\SoumettreCandidatureRequest;
use App\Jobs\AnalyseCandidatJob;
use App\Models\Candidate;
use App\Models\Offre;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CandidatureController extends Controller
{
    public function store(SoumettreCandidatureRequest $request, Offre $offre): RedirectResponse
    {
        $this->authorize('store', $offre);

        $validated = $request->validated();

        $candidate = Candidate::create([
            'offre_id' => $offre->id,
            'user_id' => Auth::id(),
            'name' => $validated['nom_candidat'],
            'cv_text' => $validated['cv_text'],
            'status' => 'pending',
        ]);

        if (class_exists(AnalyseCandidatJob::class)) {
            AnalyseCandidatJob::dispatch($candidate);
        }

        return redirect()->route('offres.show', $offre)
            ->with('success', 'Analyse en cours…');
    }

    public function show(Offre $offre, Candidate $candidate): View
    {
        $this->authorize('view', $candidate);

        $candidate->load('analyse');

        return view('candidatures.show', compact('offre', 'candidate'));
    }

    public function destroy(Offre $offre, Candidate $candidate): RedirectResponse
    {
        $this->authorize('delete', $candidate);

        $candidate->delete();

        return redirect()->route('offres.show', $offre);
    }
}
