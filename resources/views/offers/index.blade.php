@extends('layouts.app')

@section('content')
    <div class="flex items-center justify-between">
        <h1 class="text-xl font-semibold text-white">Offres</h1>
        <a href="{{ route('offers.create') }}" class="rounded-lg bg-accent px-4 py-2 text-sm font-medium text-white transition hover:bg-accent/80">
            Nouvelle offre
        </a>
    </div>

    @if(session('success'))
        <div class="mt-4 rounded-lg bg-teal/10 p-3 text-sm text-teal">
            {{ session('success') }}
        </div>
    @endif

    @if($offers->isEmpty())
        <p class="mt-6 text-text-secondary">Aucune offre pour le moment.</p>
    @else
        <div class="mt-6 space-y-3">
            @foreach($offers as $offer)
                <a href="{{ route('offers.show', $offer) }}" class="block rounded-xl border border-border bg-card p-4 transition hover:bg-card-hover">
                    <div class="flex items-start justify-between">
                        <div class="min-w-0 flex-1">
                            <h2 class="text-base font-medium text-white">{{ $offer->title }}</h2>
                            <p class="mt-1 text-sm text-text-secondary">
                                {{ Str::limit(strip_tags($offer->description), 100) }}
                            </p>
                        </div>
                        <div class="ml-4 flex flex-col items-end gap-1.5">
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                                {{ $offer->status === 'open' ? 'bg-green-900/50 text-green-300' : '' }}
                                {{ $offer->status === 'closed' ? 'bg-red-900/50 text-red-300' : '' }}
                                {{ $offer->status === 'draft' ? 'bg-yellow-900/50 text-yellow-300' : '' }}">
                                {{ $offer->status === 'open' ? 'Ouvert' : ($offer->status === 'closed' ? 'Fermé' : 'Brouillon') }}
                            </span>
                            <span class="text-xs text-text-secondary">{{ $offer->created_at->format('d/m/Y') }}</span>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $offers->links() }}
        </div>
    @endif
@endsection
