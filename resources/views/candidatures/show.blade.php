@extends('layouts.app')

@section('content')
    <div style="margin-bottom: 1.5rem;">
        <a href="{{ route('offres.show', $offre) }}" class="link" style="font-size: 0.875rem;">← Retour à l'offre</a>
    </div>

    <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1.5rem;">
        <div>
            <h1 style="font-size: 1.5rem; font-weight: 600;">{{ $candidature->nom_candidat }}</h1>
            <p style="font-size: 0.875rem; color: #6b7280; margin-top: 0.25rem;">
                Soumise le {{ $candidature->created_at->format('d/m/Y') }} pour l'offre « {{ $offre->titre }} »
            </p>
        </div>
        <form method="POST" action="{{ route('offres.candidatures.destroy', [$offre, $candidature]) }}" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette candidature ?');" style="display: inline;">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn" style="font-size: 0.875rem; background: #dc2626;">Supprimer</button>
        </form>
    </div>

    <div style="padding: 1rem; border: 1px solid #e3e3e0; border-radius: 2px; margin-bottom: 1.5rem;">
        <h2 style="font-size: 1rem; font-weight: 500; margin-bottom: 0.5rem;">Statut</h2>
        <div>
            @if($candidature->analyse)
                <span style="font-size: 0.875rem; padding: 0.25rem 0.5rem;
                    @if($candidature->analyse->recommandation->value === 'convoquer')
                        background: #dcfce7; color: #166534;
                    @elseif($candidature->analyse->recommandation->value === 'attente')
                        background: #fef9c3; color: #854d0e;
                    @else
                        background: #fee2e2; color: #991b1b;
                    @endif
                    border-radius: 2px;">
                    @if($candidature->analyse->recommandation->value === 'convoquer')
                        ✅ À convoquer
                    @elseif($candidature->analyse->recommandation->value === 'attente')
                        ⏳ En attente
                    @else
                        ❌ À rejeter
                    @endif
                </span>
                <span style="font-size: 1.25rem; font-weight: 600; margin-left: 0.5rem;">{{ $candidature->analyse->matching_score }}/100</span>
            @elseif($candidature->status->value === 'failed')
                <span style="font-size: 0.875rem; padding: 0.25rem 0.5rem; background: #fee2e2; color: #991b1b; border-radius: 2px;">⚠️ Analyse échouée</span>
            @else
                <span style="font-size: 0.875rem; padding: 0.25rem 0.5rem; background: #f3f4f6; color: #6b7280; border-radius: 2px;">🔄 Analyse en cours</span>
            @endif
        </div>
    </div>

    @if($candidature->analyse)
        <div style="padding: 1rem; border: 1px solid #e3e3e0; border-radius: 2px; margin-bottom: 1.5rem;">
            <h2 style="font-size: 1rem; font-weight: 500; margin-bottom: 0.75rem;">Analyse IA</h2>

            <div style="margin-bottom: 1rem;">
                <h3 style="font-size: 0.875rem; font-weight: 500; margin-bottom: 0.25rem;">Points forts</h3>
                @if($candidature->analyse->points_forts->isNotEmpty())
                    <ul style="font-size: 0.875rem; margin-left: 1.25rem;">
                        @foreach($candidature->analyse->points_forts as $point)
                            <li>{{ $point }}</li>
                        @endforeach
                    </ul>
                @else
                    <p style="font-size: 0.875rem; color: #6b7280;">Aucun point fort identifié.</p>
                @endif
            </div>

            <div style="margin-bottom: 1rem;">
                <h3 style="font-size: 0.875rem; font-weight: 500; margin-bottom: 0.25rem;">Lacunes</h3>
                @if($candidature->analyse->lacunes->isNotEmpty())
                    <ul style="font-size: 0.875rem; margin-left: 1.25rem;">
                        @foreach($candidature->analyse->lacunes as $lacune)
                            <li>{{ $lacune }}</li>
                        @endforeach
                    </ul>
                @else
                    <p style="font-size: 0.875rem; color: #6b7280;">Aucune lacune identifiée.</p>
                @endif
            </div>

            <div style="margin-bottom: 1rem;">
                <h3 style="font-size: 0.875rem; font-weight: 500; margin-bottom: 0.25rem;">Compétences manquantes</h3>
                @if($candidature->analyse->competences_manquantes->isNotEmpty())
                    <div style="display: flex; flex-wrap: wrap; gap: 0.5rem;">
                        @foreach($candidature->analyse->competences_manquantes as $competence)
                            <span style="font-size: 0.75rem; padding: 0.25rem 0.5rem; background: #fee2e2; color: #991b1b; border-radius: 2px;">{{ $competence }}</span>
                        @endforeach
                    </div>
                @else
                    <p style="font-size: 0.875rem; color: #6b7280;">Aucune compétence manquante.</p>
                @endif
            </div>

            <div>
                <h3 style="font-size: 0.875rem; font-weight: 500; margin-bottom: 0.25rem;">Justification</h3>
                <p style="font-size: 0.875rem; line-height: 1.6;">{{ $candidature->analyse->justification }}</p>
            </div>
        </div>
    @endif

    <div style="padding: 1rem; border: 1px solid #e3e3e0; border-radius: 2px;">
        <h2 style="font-size: 1rem; font-weight: 500; margin-bottom: 0.5rem;">CV soumis</h2>
        <p style="font-size: 0.875rem; line-height: 1.6; white-space: pre-wrap;">{{ $candidature->cv_text }}</p>
    </div>
@endsection
