@extends("layouts.app")

@section("content")
    <div class="mb-4">
        <a href="{{ route("applications.show", $application) }}" class="text-sm text-accent hover:underline">← Retour à la candidature</a>
    </div>

    <div class="mb-6 rounded-xl border border-border bg-card p-5">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-base font-semibold text-white">Chat — {{ $application->candidate->name }}</h1>
                <p class="mt-1 text-sm text-text-secondary">
                    Offre : {{ $offre->titre }}
                </p>
            </div>
            @if($application->analyse)
                <div class="text-right">
                    <x-status-badge :status="$application->analyse->recommandation->value" />
                    <span class="ml-2 text-sm font-bold text-white">{{ $application->analyse->matching_score }}/100</span>
                </div>
            @endif
        </div>
    </div>

    <div class="mb-6 min-h-[400px] max-h-[600px] overflow-y-auto rounded-xl border border-border bg-card p-5">
        @if($messages->isEmpty())
            <p class="py-8 text-center text-text-secondary">Posez votre première question sur ce candidat.</p>
        @else
            <div class="space-y-4">
                @foreach($messages as $message)
                    <x-chat-message
                        :role="$message->role === "user" ? "user" : "assistant""
                        :content="$message->content"
                        :label="$message->role === "user" ? "HR Agent" : "AI""
                    />
                @endforeach
            </div>
        @endif
    </div>

    <div class="mb-4 flex flex-wrap gap-2">
        <button type="button" class="rounded-lg bg-card border border-border px-3 py-1.5 text-xs text-text-secondary transition hover:bg-card-hover hover:text-white"
            onclick="document.getElementById("message").value="Pourquoi ce score ?"; document.getElementById("chat-form").submit();">
            Pourquoi ce score ?
        </button>
        <button type="button" class="rounded-lg bg-card border border-border px-3 py-1.5 text-xs text-text-secondary transition hover:bg-card-hover hover:text-white"
            onclick="document.getElementById("message").value="Quelles questions poser en entretien ?"; document.getElementById("chat-form").submit();">
            Questions d"entretien ?
        </button>
        <button type="button" class="rounded-lg bg-card border border-border px-3 py-1.5 text-xs text-text-secondary transition hover:bg-card-hover hover:text-white"
            onclick="document.getElementById("message").value="Quels sont ses points faibles ?"; document.getElementById("chat-form").submit();">
            Points faibles ?
        </button>
    </div>

    <form id="chat-form" method="POST" action="{{ route("chat.store", [$offre, $application]) }}">
        @csrf
        <div class="flex gap-2">
            <textarea
                name="message"
                id="message"
                rows="2"
                placeholder="Votre question..."
                required
                minlength="2"
                maxlength="2000"
                class="flex-1 resize-none rounded-lg border border-teal-dark bg-transparent px-4 py-2.5 text-sm text-white placeholder-text-secondary outline-none transition focus:border-teal focus:ring-1 focus:ring-teal"
            >{{ old("message", $compareMessage ?? "") }}</textarea>
            <button type="submit" class="self-end rounded-lg bg-teal px-4 py-2.5 text-sm font-medium text-white transition hover:bg-teal/80">
                Envoyer
            </button>
        </div>
        @error("message")
            <p class="mt-1 text-xs text-accent">{{ $message }}</p>
        @enderror
    </form>
@endsection
