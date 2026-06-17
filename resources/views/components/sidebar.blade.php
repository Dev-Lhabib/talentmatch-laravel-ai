@props([])

<aside class="flex w-16 flex-col bg-sidebar">
    {{-- Logo --}}
    <div class="flex shrink-0 items-center justify-center py-4">
        <span class="text-lg font-bold leading-none text-accent">TM</span>
    </div>

    {{-- Navigation --}}
    <nav class="flex flex-1 flex-col items-center justify-center gap-4">
        {{-- Home --}}
        <a href="{{ route('dashboard') }}" class="flex h-10 w-10 items-center justify-center rounded-lg {{ request()->routeIs('dashboard') && !request()->routeIs('dashboard.candidates') ? 'bg-card text-white' : 'text-text-secondary' }} transition hover:bg-card hover:text-white" title="Dashboard">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
        </a>

        {{-- Candidates Dashboard --}}
        <a href="{{ url('/dashboard/candidates') }}" class="flex h-10 w-10 items-center justify-center rounded-lg {{ request()->routeIs('dashboard.candidates') ? 'bg-accent text-white' : 'text-text-secondary' }} transition hover:bg-card hover:text-white" title="Candidats">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
            </svg>
        </a>

        {{-- Offres --}}
        <a href="{{ route('offres.index') }}" class="flex h-10 w-10 items-center justify-center rounded-lg {{ request()->routeIs('offres.*') ? 'bg-accent text-white' : 'text-text-secondary' }} transition hover:bg-card hover:text-white" title="Offres">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
        </a>

        {{-- Retour d'expérience / Feedback --}}
        <a href="{{ route('feedback.show') }}" class="flex h-10 w-10 items-center justify-center rounded-lg {{ request()->routeIs('feedback.*') ? 'bg-accent text-white' : 'text-text-secondary' }} transition hover:bg-card hover:text-white" title="Retour d'expérience">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </a>
    </nav>

    {{-- Logout --}}
    <div class="flex shrink-0 items-center justify-center py-4">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="flex h-10 w-10 items-center justify-center rounded-lg text-text-secondary transition hover:bg-card hover:text-white" title="Déconnexion">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
            </button>
        </form>
    </div>
</aside>
