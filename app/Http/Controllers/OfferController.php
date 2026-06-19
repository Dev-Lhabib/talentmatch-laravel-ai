<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\OfferRequest;
use App\Models\Offer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class OfferController extends Controller
{
    public function index(): View
    {
        $offers = Offer::where('user_id', Auth::id())
            ->latest()
            ->paginate(15);

        return view('offers.index', compact('offers'));
    }

    public function create(): View
    {
        return view('offers.create');
    }

    public function store(OfferRequest $request): RedirectResponse
    {
        $offer = Offer::create([
            'user_id' => Auth::id(),
            'title' => $request->validated('title'),
            'description' => $request->validated('description'),
            'experience_min' => $request->validated('experience_min'),
            'required_skills' => $request->validated('required_skills', []),
            'status' => $request->validated('status', 'open'),
        ]);

        return redirect()->route('offers.show', $offer)
            ->with('success', 'Offre créée avec succès.');
    }

    public function show(Offer $offer): View
    {
        $this->authorize('view', $offer);

        return view('offers.show', compact('offer'));
    }

    public function edit(Offer $offer): View
    {
        $this->authorize('update', $offer);

        return view('offers.edit', compact('offer'));
    }

    public function update(OfferRequest $request, Offer $offer): RedirectResponse
    {
        $this->authorize('update', $offer);

        $offer->update([
            'title' => $request->validated('title'),
            'description' => $request->validated('description'),
            'experience_min' => $request->validated('experience_min'),
            'required_skills' => $request->validated('required_skills', []),
            'status' => $request->validated('status', 'open'),
        ]);

        return redirect()->route('offers.show', $offer)
            ->with('success', 'Offre mise à jour avec succès.');
    }

    public function destroy(Offer $offer): RedirectResponse
    {
        $this->authorize('delete', $offer);

        $offer->delete();

        return redirect()->route('offers.index')
            ->with('success', 'Offre supprimée avec succès.');
    }
}
