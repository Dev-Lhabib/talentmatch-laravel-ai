@extends('layouts.app')

@section('content')
    <div class="flex items-center justify-between">
        <h1 class="text-xl font-semibold text-white">Candidats</h1>
        <a href="{{ route('candidates.create') }}" class="rounded-lg bg-accent px-4 py-2 text-sm font-medium text-white transition hover:bg-accent/80">
            Nouveau candidat
        </a>
    </div>

    @if(session('success'))
        <div class="mt-4 rounded-lg bg-teal/10 p-3 text-sm text-teal">
            {{ session('success') }}
        </div>
    @endif

    <form method="GET" action="{{ route('candidates.index') }}" class="mt-6">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher par nom..." maxlength="255"
            class="w-full rounded-lg border border-teal-dark bg-transparent px-4 py-2.5 text-sm text-white placeholder-text-secondary outline-none transition focus:border-teal focus:ring-1 focus:ring-teal">
    </form>

    @if($candidates->isEmpty())
        <p class="mt-6 text-text-secondary">Aucun candidat pour le moment.</p>
    @else
        <div class="mt-6 space-y-3">
            @foreach($candidates as $candidate)
                <a href="{{ route('candidates.show', $candidate) }}" class="block rounded-xl border border-border bg-card p-4 transition hover:bg-card-hover">
                    <div class="flex items-start justify-between">
                        <div>
                            <h2 class="text-base font-medium text-white">{{ $candidate->name }}</h2>
                            @if($candidate->email)
                                <p class="mt-1 text-sm text-text-secondary">{{ $candidate->email }}</p>
                            @endif
                        </div>
                        <span class="rounded-full bg-bg px-2.5 py-0.5 text-xs text-text-secondary border border-border">
                            {{ $candidate->created_at->format('d/m/Y') }}
                        </span>
                    </div>
                </a>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $candidates->links() }}
        </div>
    @endif
@endsection
