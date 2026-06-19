@extends('layouts.app')

@section('content')
    <div class="mb-4">
        <a href="{{ route('offers.index') }}" class="text-sm text-accent hover:underline">← Retour aux offres</a>
    </div>

    @if(session('success'))
        <div class="mb-4 rounded-lg bg-teal/10 p-3 text-sm text-teal">
            {{ session('success') }}
        </div>
    @endif

    <div class="mb-6 flex items-start justify-between">
        <div>
            <h1 class="text-xl font-semibold text-white">{{ $offer->title }}</h1>
            <p class="mt-1 text-sm text-text-secondary">
                Créée le {{ $offer->created_at->format('d/m/Y') }}
                @if($offer->experience_min)
                    · {{ $offer->experience_min }} an{{ $offer->experience_min > 1 ? 's' : '' }} min.
                @endif
            </p>
        </div>
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium
                {{ $offer->status === 'open' ? 'bg-green-900/50 text-green-300' : '' }}
                {{ $offer->status === 'closed' ? 'bg-red-900/50 text-red-300' : '' }}
                {{ $offer->status === 'draft' ? 'bg-yellow-900/50 text-yellow-300' : '' }}">
                {{ $offer->status === 'open' ? 'Ouvert' : ($offer->status === 'closed' ? 'Fermé' : 'Brouillon') }}
            </span>
            <a href="{{ route('offers.edit', $offer) }}" class="rounded-lg border border-border px-3 py-1.5 text-sm text-text-secondary transition hover:bg-card-hover hover:text-white">
                Modifier
            </a>
            <form method="POST" action="{{ route('offers.destroy', $offer) }}" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette offre ?');" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="rounded-lg bg-accent/80 px-3 py-1.5 text-sm text-white transition hover:bg-accent">
                    Supprimer
                </button>
            </form>
        </div>
    </div>

    <div class="mb-6 rounded-xl border border-border bg-card p-5">
        <h2 class="mb-2 text-sm font-semibold text-white">Description</h2>
        <p class="text-sm leading-relaxed text-text-secondary">{{ $offer->description }}</p>
    </div>

    @if($offer->required_skills && count($offer->required_skills))
        <div class="mb-6 rounded-xl border border-border bg-card p-5">
            <h2 class="mb-3 text-sm font-semibold text-white">Compétences requises</h2>
            <div class="flex flex-wrap gap-1.5">
                @foreach($offer->required_skills as $skill)
                    <span class="inline-flex items-center rounded-full bg-bg px-2.5 py-0.5 text-xs text-text-secondary border border-border">
                        {{ $skill }}
                    </span>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Candidates placeholder --}}
    <div class="rounded-xl border border-dashed border-border bg-card/50 p-5">
        <h2 class="mb-2 text-sm font-semibold text-white">Candidats</h2>
        <p class="text-sm text-text-secondary">Aucun candidat associé pour le moment.</p>
    </div>
@endsection
