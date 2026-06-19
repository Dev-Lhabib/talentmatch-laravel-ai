<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" value="Email" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" value="Mot de passe" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="mt-4 block">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-border text-accent accent-accent" name="remember">
                <span class="ms-2 text-sm text-text-secondary">Se souvenir de moi</span>
            </label>
        </div>

        <div class="mt-4 flex items-center justify-between">
            <a class="rounded-md text-sm text-text-secondary underline underline-offset-4 transition hover:text-white" href="{{ route('password.request') }}">
                Mot de passe oublié ?
            </a>

            <x-primary-button>
                Connexion
            </x-primary-button>
        </div>

        <div class="mt-4 text-center">
            <span class="text-sm text-text-secondary">Pas encore de compte ?</span>
            <a class="ms-1 rounded-md text-sm text-teal underline underline-offset-4 transition hover:text-teal/80" href="{{ route('register') }}">
                S'inscrire
            </a>
        </div>
    </form>
</x-guest-layout>
