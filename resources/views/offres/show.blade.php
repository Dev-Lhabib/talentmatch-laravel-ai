@extends('layouts.app')

@section('content')
    <div style="margin-bottom: 1.5rem;">
        <a href="{{ route('offres.index') }}" class="link" style="font-size: 0.875rem;">← Retour à mes offres</a>
    </div>

    <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1.5rem;">
        <div>
            <h1 style="font-size: 1.5rem; font-weight: 600;">{{ $offre->titre }}</h1>
            <p style="font-size: 0.875rem; color: #6b7280; margin-top: 0.25rem;">
                Créée le {{ $offre->created_at->format('d/m/Y') }}
            </p>
        </div>
        <div style="display: flex; gap: 0.5rem;">
            <a href="{{ route('offres.edit', $offre) }}" class="btn" style="font-size: 0.875rem;">Modifier</a>
            <form method="POST" action="{{ route('offres.destroy', $offre) }}" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette offre ?');" style="display: inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn" style="font-size: 0.875rem; background: #dc2626;">Supprimer</button>
            </form>
        </div>
    </div>

    <div style="padding: 1rem; border: 1px solid #e3e3e0; border-radius: 2px; margin-bottom: 1.5rem;">
        <h2 style="font-size: 1rem; font-weight: 500; margin-bottom: 0.5rem;">Description</h2>
        <p style="font-size: 0.875rem; line-height: 1.6;">{{ $offre->description }}</p>

        <div style="margin-top: 1rem;">
            <span style="font-size: 0.875rem; color: #6b7280;">
                Expérience minimum : {{ $offre->experience_min }} an{{ $offre->experience_min > 1 ? 's' : '' }}
            </span>
        </div>

        @if($offre->competences->isNotEmpty())
            <div style="margin-top: 1rem;">
                <span style="font-size: 0.875rem; font-weight: 500;">Compétences requises :</span>
                <div style="display: flex; flex-wrap: wrap; gap: 0.5rem; margin-top: 0.25rem;">
                    @foreach($offre->competences as $competence)
                        <span style="font-size: 0.75rem; padding: 0.25rem 0.5rem; background: #f3f4f6; border-radius: 2px;">{{ $competence->nom }}</span>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    <div>
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <h2 style="font-size: 1.125rem; font-weight: 600;">
                Candidatures ({{ $offre->candidatures_count }})
            </h2>
            <button
                type="button"
                id="compare-btn"
                class="btn"
                style="font-size: 0.875rem; background: #7c3aed; color: white; display: none;"
                onclick="compareSelected()"
            >
                Comparer ces deux candidats
            </button>
        </div>

        @if($offre->candidatures->isEmpty())
            <p style="color: #6b7280;">Aucune candidature pour le moment.</p>
        @else
            <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                @php $rank = 0; @endphp
                @foreach($offre->candidatures as $candidature)
                    @if($candidature->analyse)
                        @php $rank++; @endphp
                    @endif
                    <div style="display: flex; align-items: center; gap: 0.75rem; padding: 1rem; border: 1px solid #e3e3e0; border-radius: 2px;">
                        <input
                            type="checkbox"
                            class="candidature-checkbox"
                            value="{{ $candidature->id }}"
                            data-completed="{{ $candidature->status->value === 'completed' ? '1' : '0' }}"
                            style="width: 1.25rem; height: 1.25rem; cursor: pointer; accent-color: #7c3aed;"
                            @if($candidature->status->value !== 'completed') disabled title="Analyse non terminée" @endif
                        >
                        @if($candidature->analyse)
                            <span style="font-size: 0.875rem; font-weight: 600; min-width: 2rem; text-align: center;
                                @if($rank === 1) color: #b45309;
                                @elseif($rank === 2) color: #6b7280;
                                @elseif($rank === 3) color: #92400e;
                                @else color: #9ca3af;
                                @endif
                            ">
                                @if($rank === 1) 🥇
                                @elseif($rank === 2) 🥈
                                @elseif($rank === 3) 🥉
                                @else #{{ $rank }}
                                @endif
                            </span>
                        @else
                            <span style="min-width: 2rem;"></span>
                        @endif
                        <a href="{{ route('offres.candidatures.show', [$offre, $candidature]) }}" style="flex: 1; text-decoration: none; color: inherit;">
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <div>
                                    <h3 style="font-size: 0.875rem; font-weight: 500;">{{ $candidature->nom_candidat }}</h3>
                                    <p style="font-size: 0.75rem; color: #6b7280;">
                                        Soumise le {{ $candidature->created_at->format('d/m/Y') }}
                                    </p>
                                </div>
                                <div style="text-align: right;">
                                    @if($candidature->analyse)
                                        <span style="font-size: 1.25rem; font-weight: 600;">{{ $candidature->analyse->matching_score }}/100</span>
                                        <br>
                                        <span style="font-size: 0.75rem; padding: 0.125rem 0.375rem;
                                            @if($candidature->analyse->recommandation->value === 'convoquer')
                                                background: #dcfce7; color: #166534;
                                            @elseif($candidature->analyse->recommandation->value === 'attente')
                                                background: #fef9c3; color: #854d0e;
                                            @else
                                                background: #fee2e2; color: #991b1b;
                                            @endif
                                            border-radius: 2px;">
                                            {{ ucfirst($candidature->analyse->recommandation->value) }}
                                        </span>
                                    @elseif($candidature->status->value === 'failed')
                                        <span style="font-size: 0.75rem; padding: 0.125rem 0.375rem; background: #fee2e2; color: #991b1b; border-radius: 2px;">⚠️ Échouée</span>
                                    @else
                                        <span style="font-size: 0.875rem; color: #6b7280;">En attente</span>
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
            const checked = document.querySelectorAll('.candidature-checkbox:checked');
            if (checked.length === 2) {
                const id1 = checked[0].value;
                const id2 = checked[1].value;
                window.location.href = '{{ route('chat.show', [$offre, '__ID__']) }}'.replace('__ID__', id1) + '?compare=' + id2;
            }
        }

        document.querySelectorAll('.candidature-checkbox').forEach(function(cb) {
            cb.addEventListener('change', function() {
                const checked = document.querySelectorAll('.candidature-checkbox:checked');
                const btn = document.getElementById('compare-btn');
                btn.style.display = checked.length === 2 ? 'inline-block' : 'none';
            });
        });
    </script>

    <div style="margin-top: 2rem; padding: 1rem; border: 1px solid #e3e3e0; border-radius: 2px;">
        <h2 style="font-size: 1.125rem; font-weight: 600; margin-bottom: 1rem;">Soumettre un CV</h2>

        @if($errors->any())
            <div class="errors">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('offres.candidatures.store', $offre) }}">
            @csrf

            <div class="form-group">
                <label for="nom_candidat">Nom du candidat</label>
                <input type="text" id="nom_candidat" name="nom_candidat" value="{{ old('nom_candidat') }}" required maxlength="255">
            </div>

            <div class="form-group">
                <label for="cv_text">CV (texte brut, minimum 50 caractères)</label>
                <textarea id="cv_text" name="cv_text" rows="8" required minlength="50" maxlength="50000" style="width: 100%; padding: 0.5rem 0.75rem; border: 1px solid #e3e3e0; border-radius: 2px; font-size: 0.875rem; font-family: inherit;">{{ old('cv_text') }}</textarea>
            </div>

            <button type="submit" class="btn">Soumettre la candidature</button>
        </form>
    </div>
@endsection
