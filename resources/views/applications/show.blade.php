@extends("layouts.app")

@section("content")
    @php
        $candidate = $application->candidate;
        $analyse = $application->analyse;
        $bio = $candidate ? trim(explode("\n", $candidate->cv_text)[0]) : "";
        $score = $analyse?->matching_score ?? 0;
        $scoreColor = $score >= 70 ? "text-green-400 stroke-green-400" : ($score >= 40 ? "text-yellow-400 stroke-yellow-400" : "text-red-400 stroke-red-400");
        $circumference = 2 * pi() * 42;
        $offset = $circumference - ($score / 100) * $circumference;
    @endphp

    <div class="mb-4 flex items-center justify-between gap-4">
        <a href="{{ route("offres.show", $application->offre) }}" class="text-sm text-accent hover:underline shrink-0">← Retour à l'offre</a>

        <div class="flex items-center gap-4">
            @if($candidates->count() > 0)
                <div class="flex items-center gap-2">
                    <label class="text-xs text-text-secondary">Candidat :</label>
                    <select
                        class="rounded-lg border border-border bg-card px-3 py-1.5 text-sm text-white outline-none transition focus:border-teal"
                        onchange="window.location = '/applications/' + this.value"
                    >
                        @foreach($candidates as $cand)
                            @php $candApp = $candidateApplications->firstWhere('candidate_id', $cand->id); @endphp
                            @if($candApp)
                                <option value="{{ $candApp->id }}" {{ $cand->id === $application->candidate_id ? 'selected' : '' }}>
                                    {{ $cand->name }}
                                </option>
                            @endif
                        @endforeach
                    </select>
                </div>
            @endif

            @if($candidateApplications->count() > 1)
                <div class="flex items-center gap-2">
                    <label class="text-xs text-text-secondary">Offre :</label>
                    <select
                        class="rounded-lg border border-border bg-card px-3 py-1.5 text-sm text-white outline-none transition focus:border-teal"
                        onchange="window.location = '/applications/' + this.value"
                    >
                        @foreach($candidateApplications as $app)
                            <option value="{{ $app->id }}" {{ $app->id === $application->id ? 'selected' : '' }}>
                                {{ $app->offre->titre }}
                                @if($app->analyse)
                                    ({{ $app->analyse->matching_score }}%)
                                @endif
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif
        </div>
    </div>

    @if(session("success"))
        <div class="mb-4 rounded-lg bg-teal/10 p-3 text-sm text-teal">{{ session("success") }}</div>
    @endif

    @if(session("error"))
        <div class="mb-4 rounded-lg bg-accent/10 p-3 text-sm text-accent">{{ session("error") }}</div>
    @endif

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        {{-- Left Column: Candidate Info + Analysis --}}
        <div class="space-y-6 lg:col-span-2">

            {{-- Candidate Header --}}
            <div class="flex items-start gap-5 rounded-xl border border-border bg-card p-6">
                <div class="flex h-16 w-16 flex-shrink-0 items-center justify-center rounded-full bg-accent/20 text-2xl font-bold text-accent">
                    {{ strtoupper(substr($candidate->name, 0, 1)) }}
                </div>
                <div class="min-w-0 flex-1">
                    <h1 class="text-xl font-semibold text-white">{{ $candidate->name }} <span class="text-base font-normal text-text-secondary">· Offre : {{ $application->offre->titre }}</span></h1>
                    <p class="mt-1 text-sm leading-relaxed text-text-secondary">{{ $bio }}</p>
                    <div class="mt-3 flex flex-wrap gap-3 text-xs text-text-secondary">
                        @if($candidate->email)
                            <span>{{ $candidate->email }}</span>
                        @endif
                        @if($candidate->phone)
                            <span>{{ $candidate->phone }}</span>
                        @endif
                        <span>Soumis le {{ $application->created_at->format("d/m/Y") }}</span>
                    </div>
                </div>
            </div>

            {{-- Status States --}}
            @if($application->status->value === "failed")
                <div class="rounded-xl border border-accent/30 bg-accent/5 p-5 text-center">
                    <p class="mb-3 text-sm font-medium text-accent">⚠️ Analyse échouée</p>
                    <form method="POST" action="{{ route("applications.retry", $application) }}">
                        @csrf
                        <button type="submit" class="rounded-lg bg-accent px-4 py-2 text-sm font-medium text-white transition hover:bg-accent/80">
                            Réessayer
                        </button>
                    </form>
                </div>
            @elseif($application->status->value === "analysing")
                <div class="flex items-center justify-center gap-3 rounded-xl border border-border bg-card p-8">
                    <svg class="h-6 w-6 animate-spin text-teal" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                    </svg>
                    <p class="text-sm text-text-secondary">Analyse en cours...</p>
                </div>
            @elseif($application->status->value === "pending" && !$analyse)
                <div class="rounded-xl border border-border bg-card p-8 text-center">
                    <p class="text-sm text-text-secondary">Pas encore analysé</p>
                </div>
            @endif

            {{-- Analysis Data --}}
            @if($analyse)
                {{-- Score Ring + Recommendation --}}
                <div class="flex items-center gap-8 rounded-xl border border-border bg-card p-6">
                    <div class="relative flex items-center justify-center">
                        <svg class="h-24 w-24 -rotate-90" viewBox="0 0 100 100">
                            <circle cx="50" cy="50" r="42" fill="none" stroke="#283142" stroke-width="8" />
                            <circle cx="50" cy="50" r="42" fill="none" class="{{ $scoreColor }}" stroke-width="8" stroke-dasharray="{{ $circumference }}" stroke-dashoffset="{{ $offset }}" stroke-linecap="round" />
                        </svg>
                        <span class="absolute text-2xl font-bold text-white">{{ $score }}%</span>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-white">Score de matching</p>
                        <div class="mt-2">
                            <x-status-badge :status="$analyse->recommandation->value" />
                        </div>
                        @if($analyse->justification)
                            <p class="mt-2 text-sm leading-relaxed text-text-secondary">{{ $analyse->justification }}</p>
                        @endif
                    </div>
                </div>

                {{-- Competences --}}
                @if(!empty($analyse->competences_extraites))
                    <div class="rounded-xl border border-border bg-card p-5">
                        <h2 class="mb-3 text-sm font-semibold text-white">Compétences</h2>
                        <div class="flex flex-wrap gap-1.5">
                            @foreach($analyse->competences_extraites as $skill)
                                <span class="inline-flex items-center rounded-full bg-teal/10 px-2.5 py-0.5 text-xs text-teal border border-teal/20">{{ $skill }}</span>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Langues --}}
                @if(!empty($analyse->langues))
                    <div class="rounded-xl border border-border bg-card p-5">
                        <h2 class="mb-3 text-sm font-semibold text-white">Langues</h2>
                        <div class="flex flex-wrap gap-1.5">
                            @foreach($analyse->langues as $langue)
                                <span class="inline-flex items-center rounded-full bg-card px-2.5 py-0.5 text-xs text-text-secondary border border-border">{{ $langue }}</span>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Points Forts & Lacunes --}}
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    @if(!empty($analyse->points_forts))
                        <div class="rounded-xl border border-border bg-card p-5">
                            <h2 class="mb-3 text-sm font-semibold text-green-400">Points forts</h2>
                            <ul class="space-y-1.5">
                                @foreach($analyse->points_forts as $point)
                                    <li class="flex items-start gap-2 text-sm text-text-secondary">
                                        <span class="mt-0.5 text-green-400">+</span>
                                        {{ $point }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if(!empty($analyse->lacunes))
                        <div class="rounded-xl border border-border bg-card p-5">
                            <h2 class="mb-3 text-sm font-semibold text-accent">Lacunes</h2>
                            <ul class="space-y-1.5">
                                @foreach($analyse->lacunes as $lacune)
                                    <li class="flex items-start gap-2 text-sm text-text-secondary">
                                        <span class="mt-0.5 text-accent">−</span>
                                        {{ $lacune }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            @endif
        </div>

        {{-- Right Column: Chat Panel --}}
        <div class="lg:col-span-1">
            @if($application->status->value === "completed" && $analyse)
                <div class="sticky top-4 h-[600px]">
                    <x-ai-chat-panel
                        :application="$application"
                        :messages="$messages"
                        chatStoreUrl="{{ route('applications.chat', $application) }}"
                    />
                </div>
            @else
                <div class="flex h-[200px] items-center justify-center rounded-xl border border-border bg-card">
                    <p class="text-sm text-text-secondary">Le chat sera disponible après l'analyse.</p>
                </div>
            @endif
        </div>
    </div>
@endsection
