<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\SoumettreCandidatureRequest;
use App\Jobs\AnalyseCandidatJob;
use App\Models\Candidature;
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

        $candidature = Candidature::create([
            'offre_id' => $offre->id,
            'user_id' => Auth::id(),
            'nom_candidat' => $validated['nom_candidat'],
            'cv_text' => $validated['cv_text'],
            'status' => 'pending',
        ]);

        if (class_exists(AnalyseCandidatJob::class)) {
            AnalyseCandidatJob::dispatch($candidature);
        }

        return redirect()->route('offres.show', $offre)
            ->with('success', 'Analyse en cours…');
    }

    public function show(Offre $offre, Candidature $candidature): View
    {
        $this->authorize('view', $candidature);

        $candidature->load('analyse');

        return view('candidatures.show', compact('offre', 'candidature'));
    }

    public function destroy(Offre $offre, Candidature $candidature): RedirectResponse
    {
        $this->authorize('delete', $candidature);

        $candidature->delete();

        return redirect()->route('offres.show', $offre);
    }
}
