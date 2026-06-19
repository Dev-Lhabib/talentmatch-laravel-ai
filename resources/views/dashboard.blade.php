@extends('layouts.app')

@section('content')
    <div class="mb-6">
        <h1 class="text-xl font-semibold text-white">Dashboard</h1>
        <p class="mt-1 text-sm text-text-secondary">Bienvenue, {{ auth()->user()->name }}.</p>
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-lg border border-border bg-card p-4">
            <p class="text-sm text-text-secondary">Offres créées</p>
            <p class="mt-1 text-2xl font-bold text-white">{{ $totalOffres }}</p>
        </div>
        <div class="rounded-lg border border-border bg-card p-4">
            <p class="text-sm text-text-secondary">Candidatures reçues</p>
            <p class="mt-1 text-2xl font-bold text-white">{{ $totalCandidatures }}</p>
        </div>
        <div class="rounded-lg border border-border bg-card p-4">
            <p class="text-sm text-text-secondary">Analyses complétées</p>
            <p class="mt-1 text-2xl font-bold text-white">{{ $analysesCompleted }}</p>
        </div>
        <div class="rounded-lg border border-border bg-card p-4">
            <p class="text-sm text-text-secondary">Score moyen</p>
            <p class="mt-1 text-2xl font-bold text-teal">{{ $avgScore ? number_format($avgScore, 1) . '%' : '—' }}</p>
        </div>
    </div>

    @if ($totalOffres > 0)
        <div class="mt-6 grid grid-cols-1 gap-4 lg:grid-cols-3">
            <div class="rounded-lg border border-border bg-card p-4">
                <p class="text-sm text-text-secondary">À convoquer</p>
                <p class="mt-1 text-2xl font-bold text-green-400">{{ $recommandationCounts['convoquer'] }}</p>
            </div>
            <div class="rounded-lg border border-border bg-card p-4">
                <p class="text-sm text-text-secondary">En attente</p>
                <p class="mt-1 text-2xl font-bold text-yellow-400">{{ $recommandationCounts['attente'] }}</p>
            </div>
            <div class="rounded-lg border border-border bg-card p-4">
                <p class="text-sm text-text-secondary">À rejeter</p>
                <p class="mt-1 text-2xl font-bold text-red-400">{{ $recommandationCounts['rejeter'] }}</p>
            </div>
        </div>

        @if ($recentCandidatures->isNotEmpty())
            <div class="mt-6 rounded-lg border border-border bg-card">
                <div class="border-b border-border px-4 py-3">
                    <h2 class="text-sm font-semibold text-white">Dernières candidatures analysées</h2>
                </div>
                <div class="divide-y divide-border">
                    @foreach ($recentCandidatures as $candidature)
                        <a href="{{ route('offres.candidatures.show', [$candidature->offre, $candidature]) }}"
                           class="flex items-center justify-between px-4 py-3 transition hover:bg-bg/50">
                            <div>
                                <p class="text-sm font-medium text-white">{{ $candidature->nom_candidat }}</p>
                                <p class="text-xs text-text-secondary">{{ $candidature->offre->titre }}</p>
                            </div>
                            <div class="flex items-center gap-2">
                                @if ($candidature->analyse)
                                    <span class="text-sm font-semibold {{ $candidature->analyse->matching_score >= 70 ? 'text-green-400' : ($candidature->analyse->matching_score >= 40 ? 'text-yellow-400' : 'text-red-400') }}">
                                        {{ $candidature->analyse->matching_score }}%
                                    </span>
                                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium
                                        {{ $candidature->analyse->recommandation->value === 'convoquer' ? 'bg-green-900/50 text-green-300' : '' }}
                                        {{ $candidature->analyse->recommandation->value === 'attente' ? 'bg-yellow-900/50 text-yellow-300' : '' }}
                                        {{ $candidature->analyse->recommandation->value === 'rejeter' ? 'bg-red-900/50 text-red-300' : '' }}">
                                        {{ $candidature->analyse->recommandation->value }}
                                    </span>
                                @endif
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    @else
        <div class="mt-6 rounded-lg border border-border bg-card p-6 text-center">
            <p class="text-text-secondary">Commencez par créer votre première offre d'emploi.</p>
            <a href="{{ route('offres.create') }}" class="mt-3 inline-block rounded bg-accent px-4 py-2 text-sm font-medium text-white transition hover:bg-accent/90">
                Créer une offre
            </a>
        </div>
    @endif
@endsection
