@extends('layouts.app')

@section('content')
    <div class="mb-6">
        <h1 class="text-xl font-semibold text-white">Retour d'expérience</h1>
        <p class="mt-1 text-sm text-text-secondary">Partagez vos suggestions ou signalez un problème.</p>
    </div>

    @if(session('success'))
        <div class="mb-4 rounded-lg bg-success/10 p-3 text-sm text-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="rounded-xl border border-border bg-card p-6">
        <form method="POST" action="{{ route('feedback.store') }}" class="space-y-4">
            @csrf

            <div>
                <label for="subject" class="mb-1 block text-sm font-medium text-text-secondary">Sujet</label>
                <input type="text" id="subject" name="subject" value="{{ old('subject') }}" required maxlength="255"
                    placeholder="Ex: Amélioration du dashboard, bug detected..."
                    class="w-full rounded-lg border border-teal-dark bg-transparent px-4 py-2.5 text-sm text-white placeholder-text-secondary outline-none transition focus:border-teal focus:ring-1 focus:ring-teal">
                @error('subject')
                    <p class="mt-1 text-xs text-accent">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="message" class="mb-1 block text-sm font-medium text-text-secondary">Message</label>
                <textarea id="message" name="message" rows="6" required minlength="10" maxlength="2000"
                    placeholder="Décrivez votre retour en détail..."
                    class="w-full rounded-lg border border-teal-dark bg-transparent px-4 py-2.5 text-sm text-white placeholder-text-secondary outline-none transition focus:border-teal focus:ring-1 focus:ring-teal">{{ old('message') }}</textarea>
                @error('message')
                    <p class="mt-1 text-xs text-accent">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="rounded-lg bg-accent px-4 py-2.5 text-sm font-medium text-white transition hover:bg-accent/80">
                Envoyer le retour
            </button>
        </form>
    </div>
@endsection
