@props([])

<header class="flex h-16 items-center justify-between bg-gradient-to-r from-navbar-left to-navbar-right px-4">
    {{-- Left: Logo + Title --}}
    <div class="flex items-center gap-3">
        <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-accent">
            <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
            </svg>
        </div>
        <h1 class="text-sm font-bold tracking-wide text-white">TALENTMATCH <span class="font-normal text-text-secondary">— AI Screening Assistant</span></h1>
    </div>

    {{-- Right: Notifications + User --}}
    <div class="flex items-center gap-4">
        {{-- Notification Bell --}}
        <button class="relative p-2 text-text-secondary transition hover:text-white">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
            <span class="absolute right-1 top-1 h-2 w-2 rounded-full bg-accent"></span>
        </button>

        {{-- User Avatar with Dropdown --}}
        <div class="relative" x-data="{ open: false }">
            <button @click="open = !open" @click.outside="open = false" class="flex h-8 w-8 items-center justify-center rounded-full bg-accent text-xs font-bold text-white">
                {{ substr(auth()->user()->name ?? 'U', 0, 1) }}
            </button>
            <svg class="h-4 w-4 text-text-secondary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
            </svg>
            <div x-show="open" x-transition class="absolute right-0 top-full mt-2 w-48 rounded-xl border border-border bg-card py-2 shadow-lg">
                <a href="{{ route('profile.show') }}" class="block px-4 py-2 text-sm text-text-secondary transition hover:bg-card-hover hover:text-white">Mon profil</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="block w-full px-4 py-2 text-left text-sm text-text-secondary transition hover:bg-card-hover hover:text-white">Déconnexion</button>
                </form>
            </div>
        </div>
    </div>
</header>
