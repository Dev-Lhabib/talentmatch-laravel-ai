@props([
    'candidature',
    'analyse',
])

<div class="flex h-full flex-col rounded-xl border border-border bg-card">
    {{-- Panel Header --}}
    <div class="flex items-center justify-between border-b border-border px-5 py-3">
        <h2 class="text-sm font-semibold text-white">Candidates Analysis</h2>
        <button class="flex items-center gap-1.5 rounded-lg border border-border px-3 py-1.5 text-xs text-text-secondary transition hover:bg-card-hover hover:text-white">
            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
            Profiles
            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
            </svg>
        </button>
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
                        <p class="mb-1 text-xs font-semibold uppercase tracking-wider text-text-secondary">Competences</p>
                        <div class="flex flex-wrap gap-1.5">
                            @foreach($analyse->competences_extraites as $competence)
                                <x-tag-badge :label="$competence" />
                            @endforeach
                        </div>
                    </div>

                    {{-- Relevant Skills --}}
                    <div>
                        <p class="mb-1 text-xs font-semibold uppercase tracking-wider text-text-secondary">Relevant Skills</p>
                        <p class="text-sm text-text-secondary">{{ $analyse->annees_experience }} ans d'expérience — {{ $analyse->niveau_etudes }}</p>
                    </div>

                    {{-- Languages --}}
                    <div>
                        <p class="mb-1 text-xs font-semibold uppercase tracking-wider text-text-secondary">Languages</p>
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
                <span class="text-sm text-text-secondary">Recommendation enum:</span>
                <x-status-badge :status="$analyse->recommandation->value" />
            </div>
        @else
            <div class="mt-6 text-center text-text-secondary">
                <p class="text-sm">Analyse non disponible</p>
            </div>
        @endif
    </div>
</div>
