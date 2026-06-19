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

    {{-- Candidatures existantes --}}
    <div>
        <div class="mb-4 flex items-center justify-between">
            <h2 class="text-base font-semibold text-white">
                Candidatures ({{ $applications->count() }})
            </h2>
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
                    <div class="flex items-center gap-3 rounded-xl border border-border bg-card p-4">
                        @if($application->analyse)
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
                                    @if($application->analyse)
                                        <span class="text-lg font-bold text-white">{{ $application->analyse->matching_score }}/100</span>
                                        <br>
                                        <x-status-badge :status="$application->analyse->recommandation->value" />
                                    @elseif($application->status->value === "failed")
                                        <span class="rounded bg-accent/20 px-2 py-0.5 text-xs text-accent">⚠️ Échouée</span>
                                    @else
                                        <span class="text-sm text-text-secondary">En attente</span>
                                    @endif
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection
