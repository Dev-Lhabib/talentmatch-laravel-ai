<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\OffreRequest;
use App\Models\Competence;
use App\Models\Offre;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class OffreController extends Controller
{
    public function index(Request $request): View
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

        $offre = DB::transaction(function () use ($validated) {
            $offre = Offre::create([
                'user_id' => Auth::id(),
                'titre' => $validated['titre'],
                'description' => $validated['description'],
                'experience_min' => $validated['experience_min'] ?? 0,
            ]);

            $this->syncCompetences($offre, $validated['competences'] ?? []);

            return $offre;
        });

        return redirect()->route('offres.show', $offre);
    }

    public function show(Offre $offre): View
    {
        $this->authorize('view', $offre);

        $offre->load('competences')->loadCount('candidates');

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
        $selectedCompetenceIds = $offre->competences->pluck('id')->toArray();

        return view('offres.edit', compact('offre', 'competences', 'selectedCompetenceIds'));
    }

    public function update(OffreRequest $request, Offre $offre): RedirectResponse
    {
        $this->authorize('update', $offre);

        $validated = $request->validated();

        DB::transaction(function () use ($offre, $validated) {
            $offre->update([
                'titre' => $validated['titre'],
                'description' => $validated['description'],
                'experience_min' => $validated['experience_min'] ?? 0,
            ]);

            $this->syncCompetences($offre, $validated['competences'] ?? []);
        });

        return redirect()->route('offres.show', $offre);
    }

    public function destroy(Offre $offre): RedirectResponse
    {
        $this->authorize('delete', $offre);

        $offre->delete();

        return redirect()->route('offres.index');
    }

    private function syncCompetences(Offre $offre, array $competenceNames): void
    {
        $competenceIds = [];

        foreach ($competenceNames as $name) {
            $name = trim($name);
            if ($name === '') {
                continue;
            }

            $competence = Competence::firstOrCreate(
                ['nom' => $name],
                ['nom' => $name]
            );

            $competenceIds[] = $competence->id;
        }

        $offre->competences()->sync($competenceIds);
    }
}
