@extends("layouts.app")

@section("content")
    <div class="mb-4">
        <a href="{{ route("offres.index") }}" class="text-sm text-accent hover:underline">← Retour à mes offres</a>
    </div>

    @if(session("success"))
        <div class="mb-4 rounded-lg bg-teal/10 p-3 text-sm text-teal">
            {{ session("success") }}
        </div>
    @endif

    @if(session("error"))
        <div class="mb-4 rounded-lg bg-accent/10 p-3 text-sm text-accent">
            {{ session("error") }}
        </div>
    @endif

    <div class="mb-6 flex items-start justify-between">
        <div>
            <h1 class="text-xl font-semibold text-white">{{ $offre->titre }}</h1>
            <p class="mt-1 text-sm text-text-secondary">
                Créée le {{ $offre->created_at->format("d/m/Y") }}
                @if($offre->experience_min)
                    · {{ $offre->experience_min }} an{{ $offre->experience_min > 1 ? "s" : "" }} min.
                @endif
            </p>
        </div>
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                {{ $offre->status === "open" ? "bg-green-900/50 text-green-300" : "" }}
                {{ $offre->status === "closed" ? "bg-red-900/50 text-red-300" : "" }}
                {{ $offre->status === "draft" ? "bg-yellow-900/50 text-yellow-300" : "" }}">
                {{ $offre->status === "open" ? "Ouvert" : ($offre->status === "closed" ? "Fermé" : "Brouillon") }}
            </span>
            <a href="{{ route("offres.edit", $offre) }}" class="rounded-lg border border-border px-3 py-1.5 text-sm text-text-secondary transition hover:bg-card-hover hover:text-white">
                Modifier
            </a>
            <form method="POST" action="{{ route("offres.destroy", $offre) }}" onsubmit="return confirm("Êtes-vous sûr de vouloir supprimer cette offre ?");" class="inline">
                @csrf
                @method("DELETE")
                <button type="submit" class="rounded-lg bg-accent/80 px-3 py-1.5 text-sm text-white transition hover:bg-accent">
                    Supprimer
                </button>
            </form>
        </div>
    </div>

    <div class="mb-6 rounded-xl border border-border bg-card p-5">
        <h2 class="mb-2 text-sm font-semibold text-white">Description</h2>
        <p class="text-sm leading-relaxed text-text-secondary">{{ $offre->description }}</p>

        <div class="mt-3">
            <span class="text-sm text-text-secondary">
                Expérience minimum : {{ $offre->experience_min }} an{{ $offre->experience_min > 1 ? "s" : "" }}
            </span>
        </div>
    </div>

    @if($offre->required_skills && count($offre->required_skills))
        <div class="mb-6 rounded-xl border border-border bg-card p-5">
            <h2 class="mb-3 text-sm font-semibold text-white">Compétences requises</h2>
            <div class="flex flex-wrap gap-1.5">
                @foreach($offre->required_skills as $skill)
                    <span class="inline-flex items-center rounded-full bg-bg px-2.5 py-0.5 text-xs text-text-secondary border border-border">
                        {{ $skill }}
                    </span>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Ajouter un candidat --}}
    <div class="mb-8 rounded-xl border border-border bg-card p-5">
        <h2 class="mb-4 text-base font-semibold text-white">Ajouter un candidat</h2>

        <form method="POST" action="{{ route("offres.assign", $offre) }}" class="flex items-end gap-3">
            @csrf
            <div class="flex-1">
                <select name="candidate_id" required
                    class="w-full rounded-lg border border-teal-dark bg-transparent px-4 py-2.5 text-sm text-white outline-none transition focus:border-teal focus:ring-1 focus:ring-teal">
                    <option value="" disabled selected>Choisir un candidat…</option>
                    @foreach($candidates as $candidate)
                        <option value="{{ $candidate->id }}" class="bg-card text-white">{{ $candidate->name }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="rounded-lg bg-accent px-4 py-2.5 text-sm font-medium text-white transition hover:bg-accent/80">
                Analyser
            </button>
        </form>

        @if($candidates->isNotEmpty())
            <form method="POST" action="{{ route("offres.analyse-all", $offre) }}" class="mt-3">
                @csrf
                <button type="submit" class="rounded-lg border border-teal px-4 py-2 text-sm font-medium text-teal transition hover:bg-teal/10">
                    Analyser tous les candidats ({{ $candidates->count() }})
                </button>
            </form>
        @endif
    </div>

    {{-- Processing overlay --}}
    <div x-data="{ processing: false }">
        <div x-show="processing"
             x-transition.opacity.duration.300ms
             class="fixed inset-0 z-50 flex flex-col items-center justify-center bg-bg/90 backdrop-blur-sm">
            <div class="h-12 w-12 animate-spin rounded-full border-4 border-teal/30 border-t-teal"></div>
            <p class="mt-6 text-lg font-semibold text-white">🔄 Comparaison en cours...</p>
            <p class="mt-2 text-sm text-text-secondary">L'IA analyse les deux profils pour <span class="text-white">{{ $offre->titre }}</span></p>
        </div>

    {{-- Candidatures existantes --}}
    <div x-data="{
        selected: [],
        get canCompare() { return this.selected.length === 2 },
        submitCompare() {
            if (this.selected.length !== 2) return;
            document.getElementById('app1_id').value = this.selected[0];
            document.getElementById('app2_id').value = this.selected[1];
            processing = true;
            $nextTick(() => document.getElementById('compare-form').submit());
        },
        toggle(id) {
            const idx = this.selected.indexOf(id);
            if (idx >= 0) { this.selected.splice(idx, 1); }
            else if (this.selected.length < 2) { this.selected.push(id); }
        }
    }">
        <form id="compare-form" method="POST" action="{{ route('comparisons.create') }}" class="hidden">
            @csrf
            <input type="hidden" name="application1_id" id="app1_id" value="">
            <input type="hidden" name="application2_id" id="app2_id" value="">
        </form>

        <div class="mb-4 flex items-center justify-between">
            <h2 class="text-base font-semibold text-white">
                Candidatures ({{ $applications->count() }})
            </h2>
            <button x-show="canCompare"
                    @click="submitCompare()"
                    x-transition
                    class="inline-flex items-center gap-1.5 rounded-lg bg-teal px-3 py-1.5 text-sm font-medium text-white transition hover:bg-teal/80">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                Comparer ces 2 candidats
            </button>
        </div>

        @if($applications->isEmpty())
            <p class="text-text-secondary">Aucune candidature pour le moment.</p>
        @else
            <div class="space-y-2">
                @php $rank = 0; @endphp
                @foreach($applications->sortByDesc(fn($a) => $a->analyse?->matching_score ?? 0) as $application)
                    @if($application->analyse)
                        @php $rank++; @endphp
                    @endif
                    <div class="flex items-center gap-3 rounded-xl border border-border bg-card p-4"
                         :class="{ 'border-teal/50 bg-teal/[0.03]': selected.includes({{ $application->id }}) }">
                        @if($application->analyse)
                            <label class="flex cursor-pointer items-center">
                                <input type="checkbox"
                                       value="{{ $application->id }}"
                                       :checked="selected.includes({{ $application->id }})"
                                       @change="toggle({{ $application->id }})"
                                       class="h-4 w-4 rounded border-border bg-bg text-teal focus:ring-teal">
                            </label>
                            <span class="min-w-[2rem] text-center text-sm font-bold
                                @if($rank === 1) text-yellow-500
                                @elseif($rank === 2) text-gray-400
                                @elseif($rank === 3) text-orange-700
                                @else text-text-secondary
                                @endif
                            ">
                                @if($rank === 1) 🥇
                                @elseif($rank === 2) 🥈
                                @elseif($rank === 3) 🥉
                                @else #{{ $rank }}
                                @endif
                            </span>
                        @else
                            <span class="min-w-[2rem]"></span>
                            <span class="w-4"></span>
                        @endif
                        <a href="{{ route("applications.show", $application) }}" class="flex-1 text-decoration-none">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-sm font-medium text-white">{{ $application->candidate->name }}</h3>
                                    <p class="text-xs text-text-secondary">
                                        Soumise le {{ $application->created_at->format("d/m/Y") }}
                                    </p>
                                </div>
                                <div class="text-right">
                                    @if($application->analyse && $application->status->value === 'completed')
                                        <span class="text-lg font-bold text-white">{{ $application->analyse->matching_score }}/100</span>
                                        <br>
                                        <x-status-badge :status="$application->analyse->recommandation->value" />
                                    @else
                                        <x-status-badge :status="$application->status->value" />
                                    @endif
                                </div>
                            </div>
                        </a>
                        <div class="ml-2 flex flex-col gap-1.5">
                            @if(in_array($application->status->value, ['pending', 'failed']))
                                <form method="POST" action="{{ route('applications.retry', $application) }}">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center gap-1 rounded bg-teal/10 px-2 py-1 text-xs text-teal transition hover:bg-teal/20">
                                        ▶ Analyser
                                    </button>
                                </form>
                            @elseif($application->status->value === 'processing')
                                <form method="POST" action="{{ route('applications.retry', $application) }}">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center gap-1 rounded bg-yellow-600/10 px-2 py-1 text-xs text-yellow-500 transition hover:bg-yellow-600/20">
                                        🔄 Réessayer
                                    </button>
                                </form>
                            @elseif($application->status->value === 'completed')
                                <form method="POST" action="{{ route('applications.retry', $application) }}">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center gap-1 rounded bg-teal/10 px-2 py-1 text-xs text-teal transition hover:bg-teal/20">
                                        🔄 Réanalyser
                                    </button>
                                </form>
                            @endif
                            <a href="{{ route('candidates.edit', $application->candidate) }}" class="inline-flex items-center gap-1 rounded bg-bg px-2 py-1 text-xs text-text-secondary transition hover:text-white">
                                ✏️ CV
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
    </div>
@endsection
