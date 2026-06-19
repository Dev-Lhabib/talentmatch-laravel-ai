@extends("layouts.app")

@section("content")
    <div class="mb-4">
        <a href="{{ route("offres.show", $offre) }}" class="text-sm text-accent hover:underline">← Retour aux candidatures</a>
    </div>

    <div class="mb-8 text-center">
        <h1 class="text-xl font-semibold text-white">{{ $offre->titre }}</h1>
        <p class="mt-1 text-sm text-text-secondary">Analyse comparative IA</p>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        @php
            $winnerApp = $isWinner1 ? $app1 : $app2;
            $loserApp = $isWinner1 ? $app2 : $app1;
            $winnerVerdict = $isWinner1 ? $comparison->candidate1_verdict : $comparison->candidate2_verdict;
            $loserVerdict = $isWinner1 ? $comparison->candidate2_verdict : $comparison->candidate1_verdict;
            $winnerMatrix = $isWinner1 ? $skillMatrix1 : $skillMatrix2;
            $loserMatrix = $isWinner1 ? $skillMatrix2 : $skillMatrix1;
            $winnerExtra = $isWinner1 ? $extraSkills1 : $extraSkills2;
            $loserExtra = $isWinner1 ? $extraSkills2 : $extraSkills1;
        @endphp

        {{-- Winner column --}}
        <div class="rounded-xl border-2 border-teal/60 bg-card p-5 shadow-lg shadow-teal/5">
            <div class="mb-4 flex items-center gap-3">
                <span class="text-2xl">🥇</span>
                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-teal/20 text-sm font-bold text-teal">
                    {{ strtoupper(substr($winnerApp->candidate->name, 0, 1)) }}
                </div>
                <div>
                    <h2 class="text-lg font-bold text-white">{{ $winnerApp->candidate->name }}</h2>
                    <x-status-badge :status="$winnerApp->analyse->recommandation->value" />
                </div>
            </div>

            <div class="mb-4 flex justify-center">
                <x-donut-chart :score="$winnerApp->analyse->matching_score" size="120" strokeWidth="10" />
            </div>

            @if($offre->required_skills)
                <div class="mb-4">
                    <h3 class="mb-2 text-xs font-semibold uppercase tracking-wider text-text-secondary">Compétences requises</h3>
                    <div class="space-y-1">
                        @foreach($winnerMatrix as $item)
                            <div class="flex items-center gap-2 text-sm">
                                @if($item['has'])
                                    <span class="text-teal">✅</span>
                                @else
                                    <span class="text-accent">❌</span>
                                @endif
                                <span class="{{ $item['has'] ? 'text-white' : 'text-text-secondary' }}">{{ $item['skill'] }}</span>
                            </div>
                        @endforeach
                    </div>
                    @if(! empty($winnerExtra))
                        <div class="mt-2 flex flex-wrap gap-1">
                            @foreach($winnerExtra as $skill)
                                <span class="rounded-full bg-bg px-2 py-0.5 text-xs text-text-secondary">{{ $skill }}</span>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endif

            <div class="mb-4">
                <h3 class="mb-2 text-xs font-semibold uppercase tracking-wider text-text-secondary">Points forts</h3>
                <ul class="list-inside list-disc space-y-0.5 text-sm text-teal/90">
                    @foreach($winnerApp->analyse->points_forts ?? [] as $pf)
                        <li>{{ $pf }}</li>
                    @endforeach
                </ul>
            </div>

            <div class="mb-4">
                <h3 class="mb-2 text-xs font-semibold uppercase tracking-wider text-text-secondary">Lacunes</h3>
                <ul class="list-inside list-disc space-y-0.5 text-sm text-accent/80">
                    @foreach($winnerApp->analyse->lacunes ?? [] as $l)
                        <li>{{ $l }}</li>
                    @endforeach
                </ul>
                @if(empty($winnerApp->analyse->lacunes))
                    <p class="text-sm text-teal">Aucune lacune détectée</p>
                @endif
            </div>

            <div class="rounded-lg border border-teal/30 bg-teal/[0.04] p-3">
                <p class="text-xs font-semibold uppercase tracking-wider text-teal">💬 Verdict IA</p>
                <p class="mt-1 text-sm leading-relaxed text-text-secondary">{{ $winnerVerdict }}</p>
            </div>
        </div>

        {{-- Loser column --}}
        <div class="rounded-xl border border-border bg-card/60 p-5 opacity-80">
            <div class="mb-4 flex items-center gap-3">
                <span class="text-2xl">🥈</span>
                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-bg text-sm font-bold text-text-secondary">
                    {{ strtoupper(substr($loserApp->candidate->name, 0, 1)) }}
                </div>
                <div>
                    <h2 class="text-lg font-bold text-white/70">{{ $loserApp->candidate->name }}</h2>
                    <x-status-badge :status="$loserApp->analyse->recommandation->value" />
                </div>
            </div>

            <div class="mb-4 flex justify-center">
                <x-donut-chart :score="$loserApp->analyse->matching_score" size="120" strokeWidth="10" />
            </div>

            @if($offre->required_skills)
                <div class="mb-4">
                    <h3 class="mb-2 text-xs font-semibold uppercase tracking-wider text-text-secondary">Compétences requises</h3>
                    <div class="space-y-1">
                        @foreach($loserMatrix as $item)
                            <div class="flex items-center gap-2 text-sm">
                                @if($item['has'])
                                    <span class="text-teal">✅</span>
                                @else
                                    <span class="text-accent">❌</span>
                                @endif
                                <span class="{{ $item['has'] ? 'text-white' : 'text-text-secondary' }}">{{ $item['skill'] }}</span>
                            </div>
                        @endforeach
                    </div>
                    @if(! empty($loserExtra))
                        <div class="mt-2 flex flex-wrap gap-1">
                            @foreach($loserExtra as $skill)
                                <span class="rounded-full bg-bg px-2 py-0.5 text-xs text-text-secondary">{{ $skill }}</span>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endif

            <div class="mb-4">
                <h3 class="mb-2 text-xs font-semibold uppercase tracking-wider text-text-secondary">Points forts</h3>
                <ul class="list-inside list-disc space-y-0.5 text-sm text-teal/70">
                    @foreach($loserApp->analyse->points_forts ?? [] as $pf)
                        <li>{{ $pf }}</li>
                    @endforeach
                </ul>
            </div>

            <div class="mb-4">
                <h3 class="mb-2 text-xs font-semibold uppercase tracking-wider text-text-secondary">Lacunes</h3>
                <ul class="list-inside list-disc space-y-0.5 text-sm text-accent/60">
                    @foreach($loserApp->analyse->lacunes ?? [] as $l)
                        <li>{{ $l }}</li>
                    @endforeach
                </ul>
                @if(empty($loserApp->analyse->lacunes))
                    <p class="text-sm text-teal">Aucune lacune détectée</p>
                @endif
            </div>

            <div class="rounded-lg border border-border bg-card p-3">
                <p class="text-xs font-semibold uppercase tracking-wider text-text-secondary">💬 Verdict IA</p>
                <p class="mt-1 text-sm leading-relaxed text-text-secondary">{{ $loserVerdict }}</p>
            </div>
        </div>
    </div>

    {{-- Winner banner --}}
    <div class="mt-8 rounded-xl bg-green-900/20 border border-green-500/30 p-5 text-center">
        <p class="text-lg font-semibold text-green-400">
            🏆 Recommandation IA : <span class="text-white">{{ $winnerApp->candidate->name }}</span>
        </p>
        <p class="mt-1 text-sm text-green-300/80">{{ $comparison->winner_reason }}</p>
    </div>

    {{-- Actions --}}
    <div class="mt-6 flex items-center justify-center gap-4">
        <form method="POST" action="{{ route('applications.retry', $winnerApp) }}" class="inline">
            @csrf
            <button type="submit" class="rounded-lg bg-teal px-5 py-2.5 text-sm font-medium text-white transition hover:bg-teal/80">
                ✅ Convoquer {{ $winnerApp->candidate->name }}
            </button>
        </form>
        <a href="{{ route("offres.show", $offre) }}" class="rounded-lg border border-border px-5 py-2.5 text-sm font-medium text-text-secondary transition hover:bg-card-hover hover:text-white">
            Retour aux candidatures
        </a>
    </div>
@endsection
