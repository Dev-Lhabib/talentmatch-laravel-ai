@props([
    'candidature',
    'analyse',
    'candidatures' => collect(),
])

<div class="flex h-full flex-col rounded-xl border border-border bg-card">
    {{-- Panel Header --}}
    <div class="flex flex-col gap-3 border-b border-border px-5 py-3">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h2 class="text-sm font-semibold text-white">Analyse du candidat</h2>
                <p class="text-xs text-text-secondary">
                    {{ $candidature->nom_candidat }} · Offre : {{ $candidature->offre->titre }}
                </p>
            </div>

            @if($candidatures->count() > 1)
                <div class="relative inline-block">
                    <select
                        onchange="window.location.href = this.value"
                        class="rounded-lg border border-border bg-card px-3 py-2 pr-8 text-sm text-white outline-none transition focus:border-accent focus:ring-1 focus:ring-accent"
                    >
                        @foreach($candidatures as $candidateOption)
                            <option
                                value="{{ route('dashboard.candidates', ['candidate' => $candidateOption->id]) }}"
                                @selected($candidateOption->id === $candidature->id)
                            >
                                {{ $candidateOption->nom_candidat }}
                            </option>
                        @endforeach
                    </select>
                    <span class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-text-secondary">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </span>
                </div>
            @endif
        </div>
    </div>

    {{-- Scrollable Content --}}
    <div class="flex-1 overflow-y-auto p-5">
        {{-- Candidate Card --}}
        <x-candidate-card :candidature="$candidature" />

        @if($analyse)
            {{-- Score + Skills Row --}}
            <div class="mt-5 flex gap-6">
                {{-- Donut Chart --}}
                <div class="flex-shrink-0">
                    <x-donut-chart :score="$analyse->matching_score" />
                </div>

                {{-- Skills Columns --}}
                <div class="flex min-w-0 flex-1 flex-col gap-3">
                    {{-- Competences --}}
                    <div>
                        <p class="mb-1 text-xs font-semibold uppercase tracking-wider text-text-secondary">Compétences</p>
                        <div class="flex flex-wrap gap-1.5">
                            @foreach($analyse->competences_extraites as $competence)
                                <x-tag-badge :label="$competence" />
                            @endforeach
                        </div>
                    </div>

                    {{-- Profil --}}
                    <div>
                        <p class="mb-1 text-xs font-semibold uppercase tracking-wider text-text-secondary">Profil</p>
                        <p class="text-sm text-text-secondary">{{ $analyse->annees_experience }} ans d'expérience — {{ $analyse->niveau_etudes }}</p>
                    </div>

                    {{-- Langues --}}
                    <div>
                        <p class="mb-1 text-xs font-semibold uppercase tracking-wider text-text-secondary">Langues</p>
                        <div class="flex flex-wrap gap-1.5">
                            @foreach($analyse->langues as $langue)
                                <x-tag-badge :label="$langue" />
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            {{-- Strengths + Gaps --}}
            <div class="mt-5 grid grid-cols-2 gap-4">
                {{-- Points Forts --}}
                <div>
                    <h4 class="mb-2 text-sm font-bold text-white">Points Forts</h4>
                    <ul class="space-y-1">
                        @foreach($analyse->points_forts as $point)
                            <li class="text-sm text-text-secondary">• {{ $point }}</li>
                        @endforeach
                    </ul>
                </div>

                {{-- Lacunes --}}
                <div>
                    <h4 class="mb-2 text-sm font-bold text-white">Lacunes</h4>
                    <ul class="space-y-1">
                        @foreach($analyse->lacunes as $lacune)
                            <li class="text-sm text-text-secondary">• {{ $lacune }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>

            {{-- Recommendation Banner --}}
            <div class="mt-5 flex items-center justify-between rounded-lg bg-success-bg px-4 py-3">
                <span class="text-sm text-text-secondary">Recommandation</span>
                <x-status-badge :status="$analyse->recommandation->value" />
            </div>
        @else
            <div class="mt-6 text-center text-text-secondary">
                <p class="text-sm">Analyse non disponible</p>
            </div>
        @endif
    </div>
</div>
