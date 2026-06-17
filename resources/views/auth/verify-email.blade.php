<x-guest-layout>
    <div class="mb-4 text-sm text-text-secondary">
        Merci de vous être inscrit ! Avant de commencer, vérifiez votre email en cliquant sur le lien que nous venons de vous envoyer. Si vous ne l'avez pas reçu, nous vous en enverrons un autre.
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 text-sm font-medium text-success">
            Un nouveau lien de vérification a été envoyé à votre adresse email.
        </div>
    @endif

    <div class="mt-4 flex items-center justify-between">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf

            <div>
                <x-primary-button>
                    Renvoyer l'email de vérification
                </x-primary-button>
            </div>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button type="submit" class="rounded-md text-sm text-text-secondary underline underline-offset-4 transition hover:text-white">
                Déconnexion
            </button>
        </form>
    </div>
</x-guest-layout>
