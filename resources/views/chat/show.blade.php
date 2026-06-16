@extends('layouts.app')

@section('content')
    <div style="margin-bottom: 1.5rem;">
        <a href="{{ route('offres.candidatures.show', [$offre, $candidature]) }}" class="link" style="font-size: 0.875rem;">← Retour à la candidature</a>
    </div>

    <div style="padding: 1rem; border: 1px solid #e3e3e0; border-radius: 2px; margin-bottom: 1.5rem;">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h1 style="font-size: 1.25rem; font-weight: 600;">Chat — {{ $candidature->nom_candidat }}</h1>
                <p style="font-size: 0.875rem; color: #6b7280; margin-top: 0.25rem;">
                    Offre : {{ $offre->titre }}
                </p>
            </div>
            @if($candidature->analyse)
                <div style="text-align: right;">
                    <span style="font-size: 0.875rem; padding: 0.25rem 0.5rem;
                        @if($candidature->analyse->recommandation->value === 'convoquer')
                            background: #dcfce7; color: #166534;
                        @elseif($candidature->analyse->recommandation->value === 'attente')
                            background: #fef9c3; color: #854d0e;
                        @else
                            background: #fee2e2; color: #991b1b;
                        @endif
                        border-radius: 2px;">
                        {{ $candidature->analyse->matching_score }}/100
                    </span>
                </div>
            @endif
        </div>
    </div>

    <div style="padding: 1rem; border: 1px solid #e3e3e0; border-radius: 2px; margin-bottom: 1.5rem; min-height: 400px; max-height: 600px; overflow-y: auto;">
        @if($messages->isEmpty())
            <p style="color: #6b7280; text-align: center; padding: 2rem;">Posez votre première question sur ce candidat.</p>
        @else
            @foreach($messages as $message)
                <div style="margin-bottom: 1rem; display: flex; {{ $message->role === 'user' ? 'justify-content: flex-end;' : 'justify-content: flex-start;' }}">
                    <div style="max-width: 70%; padding: 0.75rem 1rem; border-radius: 2px;
                        {{ $message->role === 'user'
                            ? 'background: #3b82f6; color: white;'
                            : 'background: #f3f4f6; color: #111827;'
                        }}">
                        <p style="font-size: 0.875rem; line-height: 1.5; white-space: pre-wrap;">{{ $message->content }}</p>
                        <p style="font-size: 0.75rem; margin-top: 0.5rem; opacity: 0.7;">
                            {{ $message->created_at->format('H:i') }}
                        </p>
                    </div>
                </div>
            @endforeach
        @endif
    </div>

    <div style="margin-bottom: 1rem;">
        <div style="display: flex; flex-wrap: wrap; gap: 0.5rem;">
            <button type="button" class="btn" style="font-size: 0.75rem; background: #e5e7eb; color: #374151;" onclick="document.getElementById('message').value='Pourquoi ce score ?'; this.closest('form').submit();">
                Pourquoi ce score ?
            </button>
            <button type="button" class="btn" style="font-size: 0.75rem; background: #e5e7eb; color: #374151;" onclick="document.getElementById('message').value='Quelles questions poser en entretien ?'; this.closest('form').submit();">
                Questions d'entretien ?
            </button>
            <button type="button" class="btn" style="font-size: 0.75rem; background: #e5e7eb; color: #374151;" onclick="document.getElementById('message').value='Quels sont ses points faibles ?'; this.closest('form').submit();">
                Points faibles ?
            </button>
        </div>
    </div>

    <form method="POST" action="{{ route('chat.store', [$offre, $candidature]) }}">
        @csrf
        <div style="display: flex; gap: 0.5rem;">
            <textarea
                name="message"
                id="message"
                rows="2"
                placeholder="Votre question..."
                required
                minlength="2"
                maxlength="2000"
                style="flex: 1; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 2px; font-size: 0.875rem; resize: none;"
            >{{ old('message') }}</textarea>
            <button type="submit" class="btn" style="align-self: flex-end;">Envoyer</button>
        </div>
        @error('message')
            <p style="color: #dc2626; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</p>
        @enderror
    </form>
@endsection
