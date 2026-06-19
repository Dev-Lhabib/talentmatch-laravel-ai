@extends('layouts.app')

@section('content')
    <h1 class="mb-6 text-xl font-semibold text-white">Nouvelle offre</h1>

    @if($errors->any())
        <div class="mb-4 rounded-lg bg-accent/10 p-3 text-sm text-accent">
            <ul class="list-disc space-y-1 pl-4">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('offers.store') }}">
        @include('offers._form')

        <div class="mt-6 flex gap-2">
            <button type="submit" class="rounded-lg bg-accent px-4 py-2.5 text-sm font-medium text-white transition hover:bg-accent/80">
                Créer l'offre
            </button>
            <a href="{{ route('offers.index') }}" class="rounded-lg border border-border px-4 py-2.5 text-sm text-text-secondary transition hover:bg-card-hover hover:text-white">
                Annuler
            </a>
        </div>
    </form>
@endsection
