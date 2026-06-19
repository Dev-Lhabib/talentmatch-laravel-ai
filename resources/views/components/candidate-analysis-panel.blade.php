@props([
    "application",
    "allApplications" => collect(),
    "offers" => collect(),
])

@php
    $candidate = $application->candidate;
    $analyse = $application->analyse;
    $sameOfferApps = $allApplications->filter(fn($app) => $app->offre_id === $application->offre_id);
@endphp

<div class="flex h-full flex-col rounded-xl border border-border bg-card">
    {{-- Compact Header with Dropdowns --}}
    <div class="border-b border-border px-5 py-2.5">
        <div class="flex items-center gap-2">
            @if($offers->count() > 1)
                <select
                    class="min-w-[300px] rounded-lg border border-border bg-card px-2 py-1.5 text-sm text-white outline-none transition focus:border-teal"
                    onchange="window.location = this.value"
                >
                    @foreach($offers as $offre)
                        <option
                            value="{{ route("dashboard.candidates", ["offre" => $offre->id]) }}"
                            @selected($application->offre_id === $offre->id)
                        >
                            {{ $offre->titre }}
                        </option>
                    @endforeach
                </select>
            @endif

            @if($sameOfferApps->count() > 1)
                <select
                    onchange="window.location.href = this.value"
                    class="min-w-[160px] rounded-lg border border-border bg-card px-2 py-1.5 text-sm text-white outline-none transition focus:border-teal"
                >
                    @foreach($sameOfferApps as $app)
                        <option
                            value="{{ route("dashboard.candidates", ["offre" => $application->offre_id, "candidate" => $app->candidate_id]) }}"
                            @selected($app->candidate_id === $application->candidate_id)
                        >
                            {{ $app->candidate->name }}
                        </option>
                    @endforeach
                </select>
            @endif
        </div>

        <p class="mt-1.5 text-xs text-text-secondary">
            {{ $candidate->name }} · {{ $application->offre->titre }}
        </p>
    </div>

    {{-- Scrollable Content --}}
    <div class="flex-1 overflow-y-auto p-5">
        {{-- Candidate Card --}}
        <x-candidate-card :candidate="$candidate" />

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
                        <p class="text-sm text-text-secondary">{{ $analyse->annees_experience }} ans d"expérience — {{ $analyse->niveau_etudes }}</p>
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
