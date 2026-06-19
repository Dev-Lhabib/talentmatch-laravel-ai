<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\OffreRequest;
use App\Models\Competence;
use App\Models\Offre;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class OffreController extends Controller
{
    public function index(): View
    {
        $offres = Offre::where('user_id', Auth::id())
            ->withCount('candidates')
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

        $offre->loadCount('candidates');

        $offre->load([
            'candidates' => fn ($q) => $q->orderedByScore(),
            'candidates.analyse' => fn ($q) => $q->select('candidate_id', 'matching_score', 'recommandation'),
        ]);

        return view('offres.show', compact('offre'));
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
}
