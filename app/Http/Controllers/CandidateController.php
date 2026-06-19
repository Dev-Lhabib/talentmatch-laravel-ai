<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\CandidateRequest;
use App\Jobs\AnalyseCandidatJob;
use App\Models\Application;
use App\Models\Candidate;
use App\Models\Offre;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CandidateController extends Controller
{
    public function index(Request $request): View
    {
        $query = Candidate::latest();

        if ($search = $request->query('search')) {
            $query->where('name', 'like', "%{$search}%");
        }

        $candidates = $query->paginate(15);

        return view('candidates.index', compact('candidates'));
    }

    public function create(): View
    {
        return view('candidates.create');
    }

    public function store(CandidateRequest $request): RedirectResponse
    {
        $candidate = Candidate::create($request->validated());

        return redirect()->route('candidates.show', $candidate)
            ->with('success', 'Candidat créé avec succès.');
    }

    public function show(Candidate $candidate): View
    {
        $offres = Offre::latest()->get();

        return view('candidates.show', compact('candidate', 'offres'));
    }

    public function edit(Candidate $candidate): View
    {
        return view('candidates.edit', compact('candidate'));
    }

    public function update(CandidateRequest $request, Candidate $candidate): RedirectResponse
    {
        $candidate->update($request->validated());

        return redirect()->route('candidates.show', $candidate)
            ->with('success', 'Candidat mis à jour avec succès.');
    }

    public function destroy(Candidate $candidate): RedirectResponse
    {
        $candidate->delete();

        return redirect()->route('candidates.index')
            ->with('success', 'Candidat supprimé avec succès.');
    }

    public function assign(Request $request, Candidate $candidate): RedirectResponse
    {
        $offre = Offre::findOrFail((int) $request->input('offre_id'));

        $existing = Application::where('candidate_id', $candidate->id)
            ->where('offre_id', $offre->id)
            ->exists();

        if ($existing) {
            return redirect()->route('candidates.show', $candidate)
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
}
