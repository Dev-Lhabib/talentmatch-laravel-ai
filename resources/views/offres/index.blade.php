@extends('layouts.app')

@section('content')
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <h1 style="font-size: 1.5rem; font-weight: 600;">Mes offres</h1>
        <a href="{{ route('offres.create') }}" class="btn">Nouvelle offre</a>
    </div>

    @if($offres->isEmpty())
        <p style="color: #6b7280;">Aucune offre pour le moment. Créez votre première offre !</p>
    @else
        <div style="display: flex; flex-direction: column; gap: 1rem;">
            @foreach($offres as $offre)
                <a href="{{ route('offres.show', $offre) }}" style="display: block; padding: 1rem; border: 1px solid #e3e3e0; border-radius: 2px; text-decoration: none; color: inherit;">
                    <div style="display: flex; justify-content: space-between; align-items: start;">
                        <div>
                            <h2 style="font-size: 1.125rem; font-weight: 500;">{{ $offre->titre }}</h2>
                            <p style="font-size: 0.875rem; color: #6b7280; margin-top: 0.25rem;">
                                Créée le {{ $offre->created_at->format('d/m/Y') }}
                            </p>
                        </div>
                        <span style="font-size: 0.875rem; color: #6b7280;">
                            {{ $offre->candidatures_count }} candidature{{ $offre->candidatures_count > 1 ? 's' : '' }}
                        </span>
                    </div>
                </a>
            @endforeach
        </div>

        <div style="margin-top: 1.5rem;">
            {{ $offres->links() }}
        </div>
    @endif
@endsection
