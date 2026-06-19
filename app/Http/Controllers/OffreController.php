<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\OffreRequest;
use App\Jobs\AnalyseCandidatJob;
use App\Models\Application;
use App\Models\Candidate;
use App\Models\Competence;
use App\Models\Offre;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class OffreController extends Controller
{
    public function index(): View
    {
        $offres = Offre::where('user_id', Auth::id())
            ->withCount('applications')
            ->latest()
            ->paginate(15);

        return view('offres.index', compact('offres'));
    }

    public function create(): View
    {
        $competences = Competence::orderBy('nom')->get();

        return view('offres.create', compact('competences'));
    }

    public function store(OffreRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $offre = Offre::create([
            'user_id' => Auth::id(),
            'titre' => $validated['titre'],
            'description' => $validated['description'],
            'experience_min' => $validated['experience_min'] ?? 0,
            'required_skills' => $validated['required_skills'] ?? [],
            'status' => $validated['status'] ?? 'open',
        ]);

        return redirect()->route('offres.show', $offre);
    }

    public function show(Offre $offre): View
    {
        $this->authorize('view', $offre);

        $offre->loadCount('applications');

        $offre->load([
            'applications' => fn ($q) => $q->with('candidate', 'analyse'),
        ]);

        $applications = $offre->applications;

        $linkedCandidateIds = $applications->pluck('candidate_id')->toArray();
        $candidates = Candidate::whereNotIn('id', $linkedCandidateIds)->orderBy('name')->get();

        return view('offres.show', compact('offre', 'applications', 'candidates'));
    }

    public function edit(Offre $offre): View
    {
        $this->authorize('update', $offre);

        $competences = Competence::orderBy('nom')->get();

        return view('offres.edit', compact('offre', 'competences'));
    }

    public function update(OffreRequest $request, Offre $offre): RedirectResponse
    {
        $this->authorize('update', $offre);

        $offre->update($request->validated());

        return redirect()->route('offres.show', $offre);
    }

    public function destroy(Offre $offre): RedirectResponse
    {
        $this->authorize('delete', $offre);

        $offre->delete();

        return redirect()->route('offres.index');
    }

    public function assign(Request $request, Offre $offre): RedirectResponse
    {
        $candidate = Candidate::findOrFail((int) $request->input('candidate_id'));

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

    public function analyseAll(Offre $offre): RedirectResponse
    {
        $linkedCandidateIds = Application::where('offre_id', $offre->id)
            ->pluck('candidate_id')
            ->toArray();

        $candidates = Candidate::whereNotIn('id', $linkedCandidateIds)->get();

        foreach ($candidates as $candidate) {
            $application = Application::create([
                'candidate_id' => $candidate->id,
                'offre_id' => $offre->id,
                'cv_text' => $candidate->cv_text,
                'status' => 'pending',
            ]);

            AnalyseCandidatJob::dispatch($application);
        }

        $count = $candidates->count();

        return redirect()->route('offres.show', $offre)
            ->with('success', "Analyse lancée pour {$count} candidat(s).");
    }
}
