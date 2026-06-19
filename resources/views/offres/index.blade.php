@extends('layouts.app')

@section('content')
    <div class="flex items-center justify-between">
        <h1 class="text-xl font-semibold text-white">Mes offres</h1>
        <a href="{{ route('offres.create') }}" class="rounded-lg bg-accent px-4 py-2 text-sm font-medium text-white transition hover:bg-accent/80">
            Nouvelle offre
        </a>
    </div>

    @if($offres->isEmpty())
        <p class="mt-6 text-text-secondary">Aucune offre pour le moment. Créez votre première offre !</p>
    @else
        <div class="mt-6 space-y-3">
            @foreach($offres as $offre)
                <a href="{{ route('offres.show', $offre) }}" class="block rounded-xl border border-border bg-card p-4 transition hover:bg-card-hover">
                    <div class="flex items-start justify-between">
                        <div>
                            <h2 class="text-base font-medium text-white">{{ $offre->titre }}</h2>
                            <p class="mt-1 text-sm text-text-secondary">
                                Créée le {{ $offre->created_at->format('d/m/Y') }}
                            </p>
                        </div>
                        <span class="rounded-full bg-bg px-2.5 py-0.5 text-xs text-text-secondary border border-border">
                            {{ $offre->candidates_count }} candidature{{ $offre->candidates_count > 1 ? 's' : '' }}
                        </span>
                    </div>
                </a>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $offres->links() }}
        </div>
    @endif
@endsection
