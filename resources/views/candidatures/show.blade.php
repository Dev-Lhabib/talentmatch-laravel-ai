@extends("layouts.app")

@section("content")
    <div class="mb-4">
        <a href="{{ route("offres.show", $offre) }}" class="text-sm text-accent hover:underline">← Retour à l"offre</a>
    </div>

    <div class="mb-6 flex items-start justify-between">
        <div>
            <h1 class="text-xl font-semibold text-white">{{ $application->candidate->name }}</h1>
            <p class="mt-1 text-sm text-text-secondary">
                Soumise le {{ $application->created_at->format("d/m/Y") }} pour l"offre « {{ $offre->titre }} »
            </p>
        </div>
        <form method="POST" action="{{ route("offres.candidatures.destroy", [$offre, $application]) }}" onsubmit="return confirm("Êtes-vous sûr de vouloir supprimer cette candidature ?");" class="inline">
            @csrf
            @method("DELETE")
            <button type="submit" class="rounded-lg bg-accent/80 px-3 py-1.5 text-sm text-white transition hover:bg-accent">
                Supprimer
            </button>
        </form>
    </div>

    <div class="mb-6 rounded-xl border border-border bg-card p-5">
        <h2 class="mb-3 text-sm font-semibold text-white">Statut</h2>
        <div class="flex items-center gap-3">
            @if($application->analyse)
                <x-status-badge :status="$application->analyse->recommandation->value" />
                <span class="text-lg font-bold text-white">{{ $application->analyse->matching_score }}/100</span>
            @elseif($application->status->value === "failed")
                <span class="rounded bg-accent/20 px-2 py-0.5 text-xs text-accent">⚠️ Analyse échouée</span>
            @else
                <span class="rounded bg-bg px-2 py-0.5 text-xs text-text-secondary border border-border">🔄 Analyse en cours</span>
            @endif
        </div>
    </div>

    @if($application->analyse)
        <div class="mb-6 rounded-xl border border-border bg-card p-5">
            <h2 class="mb-4 text-sm font-semibold text-white">Analyse IA</h2>

            <div class="mb-4">
                <h3 class="mb-1 text-sm font-medium text-white">Points forts</h3>
                @if(!empty($application->analyse->points_forts))
                    <ul class="space-y-0.5 pl-4 text-sm text-text-secondary">
                        @foreach($application->analyse->points_forts as $point)
                            <li class="list-disc">{{ $point }}</li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-sm text-text-secondary">Aucun point fort identifié.</p>
                @endif
            </div>

            <div class="mb-4">
                <h3 class="mb-1 text-sm font-medium text-white">Lacunes</h3>
                @if(!empty($application->analyse->lacunes))
                    <ul class="space-y-0.5 pl-4 text-sm text-text-secondary">
                        @foreach($application->analyse->lacunes as $lacune)
                            <li class="list-disc">{{ $lacune }}</li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-sm text-text-secondary">Aucune lacune identifiée.</p>
                @endif
            </div>

            <div class="mb-4">
                <h3 class="mb-1 text-sm font-medium text-white">Compétences manquantes</h3>
                @if(!empty($application->analyse->competences_manquantes))
                    <div class="flex flex-wrap gap-1.5">
                        @foreach($application->analyse->competences_manquantes as $competence)
                            <span class="inline-flex items-center rounded-full bg-accent/10 px-2.5 py-0.5 text-xs text-accent">
                                {{ $competence }}
                            </span>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-text-secondary">Aucune compétence manquante.</p>
                @endif
            </div>

            <div>
                <h3 class="mb-1 text-sm font-medium text-white">Justification</h3>
                <p class="text-sm leading-relaxed text-text-secondary">{{ $application->analyse->justification }}</p>
            </div>
        </div>
    @endif

    <div class="rounded-xl border border-border bg-card p-5">
        <h2 class="mb-2 text-sm font-semibold text-white">CV soumis</h2>
        <p class="whitespace-pre-wrap text-sm leading-relaxed text-text-secondary">{{ $application->cv_text ?? $application->candidate->cv_text }}</p>
    </div>
@endsection
