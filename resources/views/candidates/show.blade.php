@extends("layouts.app")

@section("content")
    <div class="mb-4">
        <a href="{{ route("candidates.index") }}" class="text-sm text-accent hover:underline">← Retour à la liste</a>
    </div>

    @if(session("success"))
        <div class="mb-4 rounded-lg bg-teal/10 p-3 text-sm text-teal">
            {{ session("success") }}
        </div>
    @endif

    @if(session("error"))
        <div class="mb-4 rounded-lg bg-accent/10 p-3 text-sm text-accent">
            {{ session("error") }}
        </div>
    @endif

    <div class="mb-6 flex items-start justify-between">
        <div>
            <h1 class="text-xl font-semibold text-white">{{ $candidate->name }}</h1>
            <p class="mt-1 text-sm text-text-secondary">
                Créé le {{ $candidate->created_at->format("d/m/Y") }}
            </p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route("candidates.edit", $candidate) }}" class="rounded-lg border border-border px-3 py-1.5 text-sm text-text-secondary transition hover:bg-card-hover hover:text-white">
                Modifier
            </a>
            <form method="POST" action="{{ route("candidates.destroy", $candidate) }}" onsubmit="return confirm("Êtes-vous sûr de vouloir supprimer ce candidat ?");" class="inline">
                @csrf
                @method("DELETE")
                <button type="submit" class="rounded-lg bg-accent/80 px-3 py-1.5 text-sm text-white transition hover:bg-accent">
                    Supprimer
                </button>
            </form>
        </div>
    </div>

    <div class="space-y-4">
        @if($candidate->email)
            <div class="rounded-xl border border-border bg-card p-4">
                <h2 class="mb-1 text-sm font-semibold text-text-secondary">Email</h2>
                <p class="text-sm text-white">{{ $candidate->email }}</p>
            </div>
        @endif

        @if($candidate->phone)
            <div class="rounded-xl border border-border bg-card p-4">
                <h2 class="mb-1 text-sm font-semibold text-text-secondary">Téléphone</h2>
                <p class="text-sm text-white">{{ $candidate->phone }}</p>
            </div>
        @endif

        <div class="rounded-xl border border-border bg-card p-4">
            <h2 class="mb-2 text-sm font-semibold text-white">CV</h2>
            <p class="whitespace-pre-wrap text-sm leading-relaxed text-text-secondary">{{ $candidate->cv_text }}</p>
        </div>
    </div>

    {{-- Analyser pour une offre --}}
    <div class="mt-8 rounded-xl border border-border bg-card p-5">
        <h2 class="mb-4 text-base font-semibold text-white">Analyser pour une offre</h2>

        <form method="POST" action="{{ route("candidates.assign", $candidate) }}" class="flex items-end gap-3">
            @csrf
            <div class="flex-1">
                <select name="offre_id" required
                    class="w-full rounded-lg border border-teal-dark bg-transparent px-4 py-2.5 text-sm text-white outline-none transition focus:border-teal focus:ring-1 focus:ring-teal">
                    <option value="" disabled selected>Choisir une offre…</option>
                    @foreach($offres as $offre)
                        <option value="{{ $offre->id }}" class="bg-card text-white">{{ $offre->titre }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="rounded-lg bg-accent px-4 py-2.5 text-sm font-medium text-white transition hover:bg-accent/80">
                Analyser
            </button>
        </form>
    </div>
@endsection
