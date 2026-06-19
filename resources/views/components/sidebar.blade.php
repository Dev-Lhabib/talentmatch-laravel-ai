@props([])

<aside class="fixed left-0 top-0 z-40 flex h-screen w-56 flex-col overflow-hidden bg-sidebar">
    {{-- Logo --}}
    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-4">
        <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-accent">
            <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
            </svg>
        </div>
        <h1 class="text-sm font-bold tracking-wide text-white">TALENTMATCH <span class="font-normal text-text-secondary">— AI Screening Assistant</span></h1>
    </a>

    {{-- Navigation --}}
    <nav class="mt-6 flex flex-1 flex-col gap-1 px-3">
        {{-- Dashboard / Grid --}}
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 {{ request()->routeIs('dashboard') && !request()->routeIs('dashboard.candidates') ? 'bg-accent text-white' : 'text-text-secondary hover:bg-card hover:text-white' }} transition">
            <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
            </svg>
            <span class="text-sm font-medium">Dashboard</span>
        </a>

        {{-- People / Candidats (analyse) --}}
        <a href="{{ url('/dashboard/candidates') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 {{ request()->routeIs('dashboard.candidates') ? 'bg-accent text-white' : 'text-text-secondary hover:bg-card hover:text-white' }} transition">
            <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
            <span class="text-sm font-medium">Analyses</span>
        </a>

        {{-- Feedback --}}
        <a href="{{ route('feedback.show') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 {{ request()->routeIs('feedback.*') ? 'bg-accent text-white' : 'text-text-secondary hover:bg-card hover:text-white' }} transition">
            <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="text-sm font-medium">Feedback</span>
        </a>

        {{-- Users / Candidats (standalone) --}}
        <a href="{{ route('candidates.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 {{ request()->routeIs('candidates.*') ? 'bg-accent text-white' : 'text-text-secondary hover:bg-card hover:text-white' }} transition">
            <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
            <span class="text-sm font-medium">Candidats</span>
        </a>

        {{-- Briefcase / Offres --}}
        <a href="{{ route('offres.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 {{ request()->routeIs('offres.*') ? 'bg-accent text-white' : 'text-text-secondary hover:bg-card hover:text-white' }} transition">
            <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
            <span class="text-sm font-medium">Offres</span>
        </a>
    </nav>

    {{-- Bottom: Help + Logout --}}
    <nav class="flex flex-col gap-1 px-3 pb-4">
        {{-- Help --}}
        <a href="{{ route('profile.show') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-text-secondary transition hover:bg-card hover:text-white">
            <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="text-sm">Aide</span>
        </a>

        {{-- Logout --}}
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="flex w-full items-center gap-3 rounded-lg px-3 py-2 text-text-secondary transition hover:bg-card hover:text-white">
                <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                <span class="text-sm">Déconnexion</span>
            </button>
        </form>
    </nav>
</aside>
