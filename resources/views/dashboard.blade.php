@extends('layouts.app')

@section('content')
    <div class="mb-6">
        <h1 class="text-xl font-semibold text-white">Dashboard</h1>
        <p class="mt-1 text-sm text-text-secondary">Bienvenue, {{ auth()->user()->name }}.</p>
    </div>

    {{-- Row 1: Stats cards --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-lg border border-border bg-card p-5">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-teal/10 text-teal">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-white">{{ $totalCandidats }}</p>
                    <p class="text-xs text-text-secondary">Total Candidats</p>
                </div>
            </div>
        </div>

        <div class="rounded-lg border border-border bg-card p-5">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-teal/10 text-teal">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-white">{{ $totalOffresGlobal }}</p>
                    <p class="text-xs text-text-secondary">Total Offres</p>
                </div>
            </div>
        </div>

        <div class="rounded-lg border border-border bg-card p-5">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-teal/10 text-teal">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-white">{{ $analysesCompleted }}</p>
                    <p class="text-xs text-text-secondary">Analyses complétées</p>
                </div>
            </div>
        </div>

        <div class="rounded-lg border border-border bg-card p-5">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-red/10 text-accent">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-white">{{ $analysesEnAttente }}</p>
                    <p class="text-xs text-text-secondary">Analyses en attente</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Row 2: Two columns --}}
    <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-2">
        {{-- Left: Recent offres --}}
        <div class="rounded-lg border border-border bg-card">
            <div class="flex items-center justify-between border-b border-border px-4 py-3">
                <h2 class="text-sm font-semibold text-white">Offres récentes</h2>
                <a href="{{ route('offres.index') }}" class="text-xs text-teal transition hover:text-teal/80">Voir toutes les offres</a>
            </div>
            @if ($recentOffres->isNotEmpty())
                <div class="divide-y divide-border">
                    @foreach ($recentOffres as $offre)
                        <a href="{{ route('offres.show', $offre) }}" class="flex items-center justify-between px-4 py-3 transition hover:bg-card-hover">
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-medium text-white">{{ $offre->titre }}</p>
                                <p class="mt-0.5 text-xs text-text-secondary">{{ $offre->created_at->isoFormat('D MMM YYYY') }}</p>
                            </div>
                            <div class="ml-3 flex items-center gap-3">
                                <span class="text-xs text-text-secondary">{{ $offre->applications_count }} candidature(s)</span>
                                @if ($offre->status === 'open')
                                    <span class="inline-flex items-center rounded-full bg-green-900/50 px-2 py-0.5 text-xs font-medium text-green-300">Ouverte</span>
                                @elseif ($offre->status === 'closed')
                                    <span class="inline-flex items-center rounded-full bg-red-900/50 px-2 py-0.5 text-xs font-medium text-red-300">Fermée</span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-yellow-900/50 px-2 py-0.5 text-xs font-medium text-yellow-300">Brouillon</span>
                                @endif
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="p-6 text-center">
                    <p class="text-sm text-text-secondary">Aucune offre pour le moment.</p>
                    <a href="{{ route('offres.create') }}" class="mt-2 inline-block text-xs text-teal transition hover:text-teal/80">Créer une offre</a>
                </div>
            @endif
        </div>

        {{-- Right: Recent analyses --}}
        <div class="rounded-lg border border-border bg-card">
            <div class="flex items-center justify-between border-b border-border px-4 py-3">
                <h2 class="text-sm font-semibold text-white">Analyses récentes</h2>
                <a href="{{ route('dashboard.candidates') }}" class="text-xs text-teal transition hover:text-teal/80">Voir toutes les analyses</a>
            </div>
            @if ($recentCompletedAnalyses->isNotEmpty())
                <div class="divide-y divide-border">
                    @foreach ($recentCompletedAnalyses as $application)
                        <a href="{{ route('dashboard.candidates', ['candidate' => $application->candidate_id]) }}" class="flex items-center justify-between px-4 py-3 transition hover:bg-card-hover">
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-medium text-white">{{ $application->candidate->name }}</p>
                                <p class="mt-0.5 truncate text-xs text-text-secondary">{{ $application->offre->titre }}</p>
                            </div>
                            <div class="ml-3 flex items-center gap-2">
                                @if ($application->analyse)
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold
                                        {{ $application->analyse->matching_score >= 70 ? 'bg-green-900/50 text-green-300' : '' }}
                                        {{ $application->analyse->matching_score >= 40 && $application->analyse->matching_score < 70 ? 'bg-yellow-900/50 text-yellow-300' : '' }}
                                        {{ $application->analyse->matching_score < 40 ? 'bg-red-900/50 text-red-300' : '' }}">
                                        {{ $application->analyse->matching_score }}%
                                    </span>
                                @endif
                                <span class="text-xs text-text-secondary">{{ $application->updated_at->isoFormat('D MMM YYYY') }}</span>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="p-6 text-center">
                    <p class="text-sm text-text-secondary">Aucune analyse pour le moment.</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Row 3: Quick actions --}}
    <div class="mt-6 rounded-lg border border-border bg-card p-4">
        <h2 class="mb-3 text-sm font-semibold text-white">Actions rapides</h2>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('candidates.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-accent px-4 py-2.5 text-sm font-medium text-white transition hover:bg-accent/90">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                </svg>
                Ajouter un candidat
            </a>
            <a href="{{ route('offres.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-accent px-4 py-2.5 text-sm font-medium text-white transition hover:bg-accent/90">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                Créer une offre
            </a>
            <a href="{{ route('feedback.show') }}" class="inline-flex items-center gap-2 rounded-lg border border-border px-4 py-2.5 text-sm font-medium text-text-secondary transition hover:bg-card-hover hover:text-white">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5" />
                </svg>
                Voir les feedbacks
            </a>
        </div>
    </div>
@endsection
