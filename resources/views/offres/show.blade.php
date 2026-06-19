@extends("layouts.app")

@section("content")
    <div class="mb-4">
        <a href="{{ route("offres.index") }}" class="text-sm text-accent hover:underline">← Retour à mes offres</a>
    </div>

    @if(session('success'))
        <div class="mb-4 rounded-lg bg-teal/10 p-3 text-sm text-teal">
            {{ session('success') }}
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
                {{ $offre->status === 'open' ? 'bg-green-900/50 text-green-300' : '' }}
                {{ $offre->status === 'closed' ? 'bg-red-900/50 text-red-300' : '' }}
                {{ $offre->status === 'draft' ? 'bg-yellow-900/50 text-yellow-300' : '' }}">
                {{ $offre->status === 'open' ? 'Ouvert' : ($offre->status === 'closed' ? 'Fermé' : 'Brouillon') }}
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

    <div>
        <div class="mb-4 flex items-center justify-between">
            <h2 class="text-base font-semibold text-white">
                Candidatures ({{ $offre->candidates_count }})
            </h2>
            <button
                type="button"
                id="compare-btn"
                class="hidden rounded-lg bg-accent px-3 py-1.5 text-sm text-white transition hover:bg-accent/80"
                onclick="compareSelected()"
            >
                Comparer ces deux candidats
            </button>
        </div>

        @if($offre->candidates->isEmpty())
            <p class="text-text-secondary">Aucune candidature pour le moment.</p>
        @else
            <div class="space-y-2">
                @php $rank = 0; @endphp
                @foreach($offre->candidates as $candidate)
                    @if($candidate->analyse)
                        @php $rank++; @endphp
                    @endif
                    <div class="flex items-center gap-3 rounded-xl border border-border bg-card p-4">
                        <input
                            type="checkbox"
                            class="candidate-checkbox h-5 w-5 cursor-pointer accent-accent"
                            value="{{ $candidate->id }}"
                            data-completed="{{ $candidate->status->value === "completed" ? "1" : "0" }}"
                            @if($candidate->status->value !== "completed") disabled title="Analyse non terminée" @endif
                        >
                        @if($candidate->analyse)
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
                        <a href="{{ route("offres.candidatures.show", [$offre, $candidate]) }}" class="flex-1 text-decoration-none">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-sm font-medium text-white">{{ $candidate->name }}</h3>
                                    <p class="text-xs text-text-secondary">
                                        Soumise le {{ $candidate->created_at->format("d/m/Y") }}
                                    </p>
                                </div>
                                <div class="text-right">
                                    @if($candidate->analyse)
                                        <span class="text-lg font-bold text-white">{{ $candidate->analyse->matching_score }}/100</span>
                                        <br>
                                        <x-status-badge :status="$candidate->analyse->recommandation->value" />
                                    @elseif($candidate->status->value === "failed")
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

    <script>
        function compareSelected() {
            const checked = document.querySelectorAll(".candidate-checkbox:checked");
            if (checked.length === 2) {
                const id1 = checked[0].value;
                const id2 = checked[1].value;
                window.location.href = "{{ route("chat.show", [$offre, "__ID__"]) }}".replace("__ID__", id1) + "?compare=" + id2;
            }
        }

        document.querySelectorAll(".candidate-checkbox").forEach(function(cb) {
            cb.addEventListener("change", function() {
                const checked = document.querySelectorAll(".candidate-checkbox:checked");
                const btn = document.getElementById("compare-btn");
                btn.style.display = checked.length === 2 ? "inline-block" : "none";
            });
        });
    </script>

    <div class="mt-8 rounded-xl border border-border bg-card p-5">
        <h2 class="mb-4 text-base font-semibold text-white">Soumettre un CV</h2>

        @if($errors->any())
            <div class="mb-4 rounded-lg bg-accent/10 p-3 text-sm text-accent">
                <ul class="list-disc space-y-1 pl-4">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route("offres.candidatures.store", $offre) }}" class="space-y-4">
            @csrf

            <div>
                <label for="nom_candidat" class="mb-1 block text-sm font-medium text-text-secondary">Nom du candidat</label>
                <input type="text" id="nom_candidat" name="nom_candidat" value="{{ old("nom_candidat") }}" required maxlength="255"
                    class="w-full rounded-lg border border-teal-dark bg-transparent px-4 py-2.5 text-sm text-white placeholder-text-secondary outline-none transition focus:border-teal focus:ring-1 focus:ring-teal">
            </div>

            <div>
                <label for="cv_text" class="mb-1 block text-sm font-medium text-text-secondary">CV (texte brut, minimum 50 caractères)</label>
                <textarea id="cv_text" name="cv_text" rows="8" required minlength="50" maxlength="50000"
                    class="w-full rounded-lg border border-teal-dark bg-transparent px-4 py-2.5 text-sm text-white placeholder-text-secondary outline-none transition focus:border-teal focus:ring-1 focus:ring-teal"
                    placeholder="Collez le CV du candidat ici...">{{ old("cv_text") }}</textarea>
            </div>

            <button type="submit" class="rounded-lg bg-accent px-4 py-2.5 text-sm font-medium text-white transition hover:bg-accent/80">
                Soumettre la candidature
            </button>
        </form>
    </div>
@endsection
