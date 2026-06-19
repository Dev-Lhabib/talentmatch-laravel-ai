@extends("layouts.app")

@section("title", "Dashboard Candidats")

@section("content")
    @php
        $chatStoreUrl = $conversation && $selectedApp ? route('applications.chat', $selectedApp) : '#';
    @endphp

    @if($selectedApp)
        <div class="grid h-full min-h-0 grid-cols-2 gap-6">
            {{-- Left Panel: Candidate Analysis --}}
            <x-candidate-analysis-panel
                :application="$selectedApp"
                :all-applications="$applications"
                :offers="$offers"
            />

            {{-- Right Panel: AI Chat --}}
            <x-ai-chat-panel
                :application="$selectedApp"
                :messages="$messages"
                :chat-store-url="$chatStoreUrl"
            />
        </div>
    @else
        <div class="flex h-full items-center justify-center">
            <div class="text-center">
                <svg class="mx-auto h-12 w-12 text-text-secondary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
                <h3 class="mt-4 text-lg font-semibold text-white">Aucune candidature analysée</h3>
                <p class="mt-2 text-sm text-text-secondary">Soumettez un CV pour voir les résultats de l"analyse IA ici.</p>
                <a href="{{ route("offres.index") }}" class="mt-4 inline-flex items-center rounded-lg bg-accent px-4 py-2 text-sm font-medium text-white transition hover:bg-accent/80">
                    Voir les offres
                </a>
            </div>
        </div>
    @endif
@endsection
