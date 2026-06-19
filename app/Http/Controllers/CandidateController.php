<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\CandidateRequest;
use App\Models\Candidate;
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
        return view('candidates.show', compact('candidate'));
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
}
